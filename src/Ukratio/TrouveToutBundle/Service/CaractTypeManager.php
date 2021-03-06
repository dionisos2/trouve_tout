<?php

namespace Ukratio\TrouveToutBundle\Service;

use Ukratio\TrouveToutBundle\Entity\Discriminator;
use Ukratio\TrouveToutBundle\Entity\Type;
use Ukratio\TrouveToutBundle\Entity\Element;
use Ukratio\TrouveToutBundle\Entity\ElementRepository;
use Ukratio\TrouveToutBundle\Entity\Concept;
use Ukratio\TrouveToutBundle\Entity\ConceptRepository;

use Ukratio\TrouveToutBundle\Constant;

use Ukratio\TrouveToutBundle\Form\EventListener\CaractEventSubscriber;

use Ukratio\ToolBundle\Service\DataChecking;
use Ukratio\ToolBundle\Form\Type\EnumType;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormBuilderInterface;

class CaractTypeManager
{
    private $conceptRepo;
    private $elementRepo;
    private $factory;
    private $elementManager;
    private $dataChecking;
    private $rootDir;

    public function __construct(FormFactoryInterface $factory, ConceptRepository $conceptRepo, ElementRepository $elementRepo, ElementManager $elementManager, DataChecking $dataChecking, $rootDir)
    {
        $this->conceptRepo = $conceptRepo;
        $this->factory = $factory;
        $this->elementManager = $elementManager;
        $this->elementRepo = $elementRepo;
        $this->dataChecking = $dataChecking;
        $this->rootDir = $rootDir;
    }

    public function buildCaractView(FormView $view, FormInterface $form, array $options)
    {
        $caract = $form->getData();
        if (($caract !== null) and ($caract->getValue() !== null)) {
            if ($caract->getType() == 'picture') {
                $image = implode('/', array_reverse($caract->getValue()->getPath()));
                $view->vars['image'] = $image;
            }

            if ($caract->getType() == 'object') {
                $objectNames = $caract->getValue()->getPath();

                $view->vars['objects'] = array();
                foreach ($objectNames as $objectName) {
                    if ($this->dataChecking->isNumbers($objectName)) {
                        $object = $this->conceptRepo->findOneById($objectName);
                    } else {
                        $object = $this->conceptRepo->findOneByName($objectName);
                    }
                    if ($object != null) {
                        $objectId = $object->getId();
                        $view->vars['objects'][] = array('name' => $objectName,
                                                         'id' => $objectId);
                    }
                }
            }
        }
    }

    public function buildCaractForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', array('label' => 'caract.name'));

        $attr = array();

        if ($options['parentType'] === Discriminator::$Set) {
            $builder->add('selected', 'checkbox', array('required' => false,
                                                        'attr' => $attr,
                                                        'label' => 'caract.selected'));
        }

        if ($options['parentType'] === Discriminator::$Category) {
            $builder->add('selected', 'checkbox', array('required' => false,
                                                        'attr' => $attr,
                                                        'label' => 'caract.selected'));
            $builder->add('byDefault', 'checkbox', array('required' => false,
                                                         'attr' => $attr,
                                                         'label' => 'caract.byDefault'));
            $builder->add('specificity', null, array('required' => false,
                                                     'read_only' => true,
                                                     'label' => 'caract.specificity'));
        }

        if ($options['display_type'] == 'show') {
            $builder->add('type', 'text', array('disabled' => true, 'label' => 'caract.type'));
        }


        if ($options['display_type'] == 'edit') {
            $builder->add('type', new EnumType('Ukratio\TrouveToutBundle\Entity\Type'), array('label' => 'caract.type'));
        }

        $builder->addEventSubscriber(new CaractEventSubscriber($this->elementRepo, $this, $builder->getFormFactory(), $this->rootDir));
    }


    public static function getFormTypeFor($type)
    {
        switch ($type) {
            case Type::$name:
                return 'Tool_ChoiceOrText';
            case Type::$number:
                return 'number';
            case Type::$picture:
                return 'choice';
            case Type::$object:
                return 'Tool_ChoiceOrText';
            case Type::$text:
                return 'textarea';
            case Type::$date:
                return 'datetime';
            default:
                throw new \Exception('impossible case with type = ' . $type->getName());
        }
    }

    public function getChoicesFor(Type $type, $path, $isChildElement)
    {
        if (in_array($type, array(Type::$name))) {
            if ($path == null) { //TODO should be in the repo
                $elementChoices = $this->elementRepo->findHeads();
            } else {
                $element = $this->elementRepo->findByPath($path, true);

                if($element !== null) {
                    $elementChoices = $this->elementRepo->findMoreSpecifics($element);
                } else {
                    $elementChoices = array();
                }
            }


            $choices = array_map(function(Element $element) { return (string) $element->getValue();}, $elementChoices);

            $choices = array_combine($choices, $choices);
        }

        switch ($type) {
            case Type::$name:
                if ($isChildElement) {
                    $choices = array('other' => 'other') + $choices;
                }
                return $choices;
            case Type::$number:
                return null;
            case Type::$picture:
                $choices = array_map(function (Element $element) {return $element->getValue();}, $this->elementManager->filesIn($path));

                $choices = array_combine($choices, $choices);

                if ($isChildElement) {
                    $choices = array('' => '') + $choices;
                }
                    return $choices;
            case Type::$object:
                $choices1 = array_map(function (Concept $element) {return $element->getName();}, $this->conceptRepo->findNamedSet());

                $choices2 = array_map(function (Concept $element) {return $element->getId();}, $this->conceptRepo->findLinkableSet());

                $choices1 = array_combine($choices1, $choices1);
                $choices2 = array_combine($choices2, $choices2);
                $choices = $choices1 + $choices2;

                if ($isChildElement) {
                    $choices = array('other' => 'other') + $choices;
                }

                return $choices;
            case Type::$text:
                return null;
            case Type::$date:
                return null;
            default:
                throw new \Exception('impossible case with type = ' . $this->getType());
        }
    }


    public function createElementForm($name, Type $type, $path, $label = 'element.modify', $mapped = true)
    {
        $options = array('label' => $label, 'mapped' => $mapped, 'required' => $mapped, 'auto_initialize' => false);

        //TODO separate function for getting options

        if (($label == 'element.specify') and (in_array($type, array(Type::$date, Type::$number, Type::$text)))) {
            return null;
        }

        if (is_array($path)) {
            if ($label == 'element.modify') {
                $choices = $this->getChoicesFor($type, $path, false);
            } else {
                $choices = $this->getChoicesFor($type, $path, true);
            }
        } else {
            if (in_array($type, array(Type::$text,Type::$number, Type::$date))) {
                $choices = null;
            } else {
                $choices = array($path => $path);
            }
        }


        if ($choices !== null) {
            if(isset($choices['choices1']) and is_array($choices['choices1'])) {
                $options = $options + array('textType' => 'choice',
                                            'choices' => $choices['choices1'],
                                            'options' => array('choices' => $choices['choices2']));
            } else {
                $options = $options + array('choices' => $choices);
            }
        }

        if ($type === Type::$date) {
            if ($label == 'element.modify') {
                $options = $options + array('input' => 'timestamp', 'widget' => 'single_text', 'required' => false, 'format' => Constant::DATEFORMAT);
            } else {
                $options = $options + array('input' => 'timestamp', 'widget' => 'single_text', 'format' => Constant::DATEFORMAT);
            }
        }

        $builder = $this->factory->createNamedBuilder($name, CaractTypeManager::getFormTypeFor($type), null, $options);

        return $builder->getForm();
    }

}
