<?php

namespace Ukratio\TrouveToutBundle\Form\EventListener;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;
use Doctrine\ORM\EntityManager;

use Ukratio\TrouveToutBundle\Entity\Element;
use Ukratio\TrouveToutBundle\Form\DataTransformer\TrueElementToElementTransformer;

class AddOwnerElementSubscriber implements EventSubscriberInterface
{
    private $factory;

    public function __construct(FormFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public static function getSubscribedEvents()
    {
        return array(FormEvents::PRE_SET_DATA => 'preSetData',
                     FormEvents::PRE_BIND => 'preBind');
    }

    public function preBind(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        $ownerElements = array();
        $index = 0;

        while (isset($data['element_'.$index])) {
            $ownerElements['element_'.$index] = $data['element_'.$index];
            $index++;
        }

        krsort($ownerElements);

        foreach ($ownerElements as $key => $ownerElement) {
            $form->add($this->factory->createNamed($key, 'text', $ownerElement, array('mapped' => false,
                                                                                      'label' => ' ',
                                                                                      'read_only' => true)));
        }
    }

    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        if (! $data instanceof Element) {
            return;
        }


        $ownerElements = array_slice($data->getPath(), 1);

        $ownerElements = array_reverse($ownerElements);
        krsort($ownerElements);

        foreach ($ownerElements as $key => $pathElement) {
            $optionsElement = array('label' => ' ',
                             'read_only' => true,
                             'mapped' => false,
            );

            $builder = $this->factory->createNamedBuilder("element_$key", 'text', $pathElement, $optionsElement);
            $form->add($builder->getForm());
        }

    }
}
