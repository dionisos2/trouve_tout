<?php

namespace Ukratio\TrouveToutBundle\Service;

use Ukratio\TrouveToutBundle\Entity\Type;
use Ukratio\TrouveToutBundle\Entity\Element;
use Ukratio\TrouveToutBundle\Entity\ElementRepository;
use Ukratio\TrouveToutBundle\Entity\Concept;
use Ukratio\TrouveToutBundle\Entity\ConceptRepository;
use Symfony\Component\Form\FormFactoryInterface;
use Ukratio\TrouveToutBundle\Constant;

class CaractTypeManager
{
    private $conceptRepo;
    private $elementRepo;
    private $factory;
    private $elementManager;

    public function __construct(FormFactoryInterface $factory, ConceptRepository $conceptRepo, ElementRepository $elementRepo, ElementManager $elementManager)
    {
        $this->conceptRepo = $conceptRepo;
        $this->factory = $factory;
        $this->elementManager = $elementManager;
        $this->elementRepo = $elementRepo;
    }

    public function getFormTypeFor($type)
    {
        switch ($type) {
            case Type::$name:
                return 'Tool_ChoiceOrText';
            case Type::$number:
                return 'Tool_ChoiceOrText';
            case Type::$picture:
                return 'choice';
            case Type::$object:
                return 'Tool_ChoiceOrText';
            case Type::$text:
                return 'textarea';
            case Type::$date:
                return 'datetime';
            default:
                throw new \Exception('impossible case with type = ' . $this->getType());
        }
    }

    public function getChoicesFor(Type $type, $path, $isChildElement)
    {

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

        switch ($type) {
            case Type::$name:
                if ($isChildElement) {
                    $choices = array('other' => 'other') + $choices;
                }
                return $choices;
            case Type::$number:
                if ($isChildElement) {
                    $choices = array('other' => 'other') + $choices;
                }
                return $choices;
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
        $options = array('label' => $label, 'mapped' => $mapped, 'required' => $mapped);

        //TODO separate function for getting options

        if (is_array($path)) {
            if ($label == 'element.modify') {
                $choices = $this->getChoicesFor($type, $path, false);
            } else {
                $choices = $this->getChoicesFor($type, $path, true);
            }
        } else {
            if (in_array($type, array(Type::$text, Type::$date))) {
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

        $builder = $this->factory->createNamedBuilder($name, $this->getFormTypeFor($type), null, $options);

        return $builder->getForm();
    }

}
