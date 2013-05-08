<?php

namespace Ukratio\TrouveToutBundle\Form\EventListener;

use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;
use Ukratio\TrouveToutBundle\Entity\Caract;
use Symfony\Component\Validator\ConstraintValidatorFactory;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints\MinLengthValidator;
use Doctrine\ORM\EntityManager;
use Ukratio\ToolBundle\Service\Enum;
use Ukratio\ToolBundle\Form\Type\EnumType;
use Ukratio\TrouveToutBundle\Entity\Type;
use Ukratio\TrouveToutBundle\Entity\Prefix;

class AddValueSubscriber implements EventSubscriberInterface
{
    private $factory;
    private $validatorFactory;

    public function __construct(FormFactoryInterface $factory)
    {
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
            $form->add($this->factory->createNamed('value', 'TrouveTout_Element', null, array('typeOfValue' => $data['type'])));
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

        /* if (($data->getValue() !== null) and ($data->getPrefix() != null)) { */
        /*     $ratio = Prefix::getEnumerator($data->getPrefix())->getValue(); */
        /*     $data->getValue()->setRatio($ratio); */
        /* } */

        $form->add($this->factory->createNamed('value', 'TrouveTout_Element', null, array('typeOfValue' => $valueType)));

        if (Type::getEnumerator($valueType) === Type::$number) {
            $form->add($this->factory->createNamed('prefix', new EnumType('Ukratio\TrouveToutBundle\Entity\Prefix')));

            $form->add($this->factory->createNamed('unit', new EnumType('Ukratio\TrouveToutBundle\Entity\Unit')));
        }
    }
}
