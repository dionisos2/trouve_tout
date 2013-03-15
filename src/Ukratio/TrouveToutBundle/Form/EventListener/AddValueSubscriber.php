<?php

namespace Ukratio\TrouveToutBundle\Form\EventListener;

use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Event\DataEvent;
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
        // Tells the dispatcher that you want to listen on the form.post_bin
        // event and that the setBind method should be called.
        return array(FormEvents::PRE_SET_DATA => 'preSet');
    }

    public function preSet(DataEvent $event)
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
        $constraint = $data->getValueConstraint(); // à ajouter en pre_bind… TODO




        if (($data->getValue() !== null) and ($data->getPrefix() != null)) {
            $ratio = Prefix::getEnumerator($data->getPrefix())->getValue();
            $data->getValue()->setRatio($ratio);
        }

        $form->add($this->factory->createNamed('value', 'TrouveTout_Element', null, array('typeOfValue' => $valueType)));

        if (Type::getEnumerator($valueType) === Type::$number) {
            $form->add($this->factory->createNamed('prefix', new EnumType('Ukratio\TrouveToutBundle\Entity\Prefix')));

            $form->add($this->factory->createNamed('unit', new EnumType('Ukratio\TrouveToutBundle\Entity\Unit')));
        }
    }
}
