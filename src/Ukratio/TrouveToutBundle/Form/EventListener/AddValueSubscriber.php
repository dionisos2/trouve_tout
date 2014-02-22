<?php

namespace Ukratio\TrouveToutBundle\Form\EventListener;

use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\ConstraintValidatorFactory;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints\MinLengthValidator;

use Ukratio\ToolBundle\Service\Enum;
use Ukratio\ToolBundle\Form\Type\EnumType;

use Ukratio\TrouveToutBundle\Entity\Caract;
use Ukratio\TrouveToutBundle\Entity\Element;
use Ukratio\TrouveToutBundle\Entity\CaractRepository;
use Ukratio\TrouveToutBundle\Entity\ConceptRepository;
use Ukratio\TrouveToutBundle\Entity\ElementRepository;
use Ukratio\TrouveToutBundle\Service\CaractTypeManager;
use Ukratio\TrouveToutBundle\Form\Type\ElementType;
use Ukratio\TrouveToutBundle\Entity\Type;
use Ukratio\TrouveToutBundle\Entity\Prefix;
use Ukratio\TrouveToutBundle\Service\Tools;

use Doctrine\ORM\EntityManager;

class AddValueSubscriber implements EventSubscriberInterface
{
    protected $factory;
    protected $validatorFactory;
    protected $conceptRepo;
    protected $elementRepo;
    protected $caractTypeManager;
    protected $rootDir;

    public function __construct(ConceptRepository $conceptRepo, ElementRepository $elementRepo, CaractTypeManager $caractTypeManager, FormFactoryInterface $factory, $rootDir)
    {
        $this->conceptRepo = $conceptRepo;
        $this->elementRepo = $elementRepo;
        $this->caractTypeManager = $caractTypeManager;
        $this->factory = $factory;
        $this->validatorFactory = new ConstraintValidatorFactory();
        $this->rootDir = $rootDir;
    }

    public static function getSubscribedEvents()
    {
        return array(FormEvents::PRE_SET_DATA => 'preSet',
                     FormEvents::PRE_BIND => 'preBind',
                     FormEvents::POST_BIND => 'postSubmit');
    }

    public function postSubmit(FormEvent $event)
    {
        $caract = $event->getData();
        $form = $event->getForm();

        if (! $caract instanceof Caract) {
            return;
        }

        $findPicture = false;
        if (Type::getEnumerator($caract->getType()) === Type::$picture) {
            $conceptConcepts = $form->getParent()->getParent()->get('moreGeneralConceptConcepts')->getData();

            if(count($conceptConcepts) > 0) {
                // TODO get the current category, and don’t call a mysql request
                $category = $conceptConcepts[0]->getMoreGeneral();

                $picture = $form->get('choosePicture')->getData();

                if(($picture != null) and (substr($picture->getMimeType(), 0, 5) == 'image')) {
                    $subDir = Tools::stripAccents($category->getName());
                    $webPath = $this->rootDir . '/../web/img/';
                    $picturePath = 'picture/' . $subDir . '/';
                    $pictureName = Tools::stripAccents($picture->getClientOriginalName());
                    $picture->move($webPath . $picturePath, $pictureName);
                    $pathElement = array($pictureName, $subDir, 'picture');
                    $trueElement = $this->elementRepo->findByPath($pathElement, true);
                    if ($trueElement !== null) {
                        $caract->setValue($trueElement);
                    } else {//creation
                        $trueElement = $this->createElementByPath($pathElement);
                        $caract->setValue($trueElement);
                    }
                    $findPicture = true;
                }
            }

            if(!$findPicture) {
                $caract->setValue(null);
            }

            $event->setData($caract);
        }
    }

    public function preBind(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        if (isset($data['type'] )) {
            $form->add($this->factory->createNamed('value', new ElementType($this->conceptRepo, $this->elementRepo, $this->caractTypeManager), null, array('typeOfValue' => $data['type'], 'auto_initialize' => false)));
            if (Type::getEnumerator($data['type']) === Type::$number) {
                $form->add($this->factory->createNamed('imprecision', 'number', null, array('label' => 'caract.imprecision', 'auto_initialize' => false)));

                $form->add($this->factory->createNamed('prefix', new EnumType('Ukratio\TrouveToutBundle\Entity\Prefix'), null, array('label' => 'caract.prefix', 'auto_initialize' => false)));

                $form->add($this->factory->createNamed('unit', 'text', null, array('label' => 'caract.unit', 'auto_initialize' => false, 'required' => false)));
            }

            if (Type::getEnumerator($data['type']) === Type::$picture) {
                $form->add($this->factory->createNamed('choosePicture', 'file', null, array('label' => 'caract.choose_picture',
                                                                                             'required' => false,
                                                                                             'mapped' => false,
                                                                                             'auto_initialize' => false)));
            }
        }
    }

    public function preSet(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        if( $data == null) {
            return;
        }

        if (! $data instanceof Caract) {
            throw new UnexpectedTypeException($data, 'Caract');
        }

        $valueType = $data->getType();

        $form->add($this->factory->createNamed('value', new ElementType($this->conceptRepo, $this->elementRepo, $this->caractTypeManager), null, array('typeOfValue' => $valueType, 'auto_initialize' => false)));

        if (Type::getEnumerator($valueType) === Type::$number) {
            $form->add($this->factory->createNamed('imprecision', 'number', null, array('label' => 'caract.imprecision', 'auto_initialize' => false)));

            $form->add($this->factory->createNamed('prefix', new EnumType('Ukratio\TrouveToutBundle\Entity\Prefix'), null, array('label' => 'caract.prefix', 'auto_initialize' => false)));

            $form->add($this->factory->createNamed('unit', 'text', null, array('label' => 'caract.unit', 'auto_initialize' => false, 'required' => false)));
        }

        if (Type::getEnumerator($data->getType()) === Type::$picture) {
            $form->add($this->factory->createNamed('choosePicture', 'file', null, array('label' => 'caract.choose_picture',
                                                                                         'required' => false,
                                                                                         'auto_initialize' => false,
                                                                                         'mapped' => false)));
        }
    }

    public function createElementByPath($pathElement)
    {
        $value = $pathElement[0];

        $pathElement = array_slice($pathElement, 1);

        $newElement = new Element($value);
        if (count($pathElement) != 0) {
            $element = $this->elementRepo->findByPath($pathElement, true);
            if ($element === null) {
                $element = $this->createElementByPath($pathElement);
            }
            $newElement->setMoreGeneral($element);
        }

        return $newElement;
    }
}
