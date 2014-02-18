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
use Ukratio\TrouveToutBundle\Entity\CaractRepository;
use Ukratio\TrouveToutBundle\Entity\ConceptRepository;
use Ukratio\TrouveToutBundle\Entity\ElementRepository;
use Ukratio\TrouveToutBundle\Service\CaractTypeManager;
use Ukratio\TrouveToutBundle\Form\Type\ElementType;
use Ukratio\TrouveToutBundle\Entity\Type;
use Ukratio\TrouveToutBundle\Entity\Prefix;

use Doctrine\ORM\EntityManager;

class AddValueSubscriber implements EventSubscriberInterface
{
    protected $factory;
    protected $validatorFactory;
    protected $conceptRepo;
    protected $elementRepo;
    protected $caractTypeManager;

    public function __construct(ConceptRepository $conceptRepo, ElementRepository $elementRepo, CaractTypeManager $caractTypeManager, FormFactoryInterface $factory)
    {
        $this->conceptRepo = $conceptRepo;
        $this->elementRepo = $elementRepo;
        $this->caractTypeManager = $caractTypeManager;
        $this->factory = $factory;
        $this->validatorFactory = new ConstraintValidatorFactory();
    }

    public static function getSubscribedEvents()
    {
        return array(FormEvents::PRE_SET_DATA => 'preSet',
                     FormEvents::PRE_BIND => 'preBind');
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
    }
}
