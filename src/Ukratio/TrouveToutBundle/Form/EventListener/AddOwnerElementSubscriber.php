<?php

namespace Ukratio\TrouveToutBundle\Form\EventListener;

use Symfony\Component\Form\Event\DataEvent;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;
use Doctrine\ORM\EntityManager;

use Ukratio\TrouveToutBundle\Entity\Element;
use Ukratio\TrouveToutBundle\Form\DataTransformer\TrueElementToElementTransformer;

class AddOwnerElementSubscriber implements EventSubscriberInterface
{
    private $factory;
    private $em;
    
    public function __construct(FormFactoryInterface $factory, EntityManager $em)
    {
        $this->factory = $factory;
        $this->em = $em;
    }

    public static function getSubscribedEvents()
    {
        // Tells the dispatcher that you want to listen on the form.pre_set_data
        // event and that the preSetData method should be called.
        return array(FormEvents::PRE_SET_DATA => 'preSetData');
    }

    public function preSetData(DataEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        // During form creation setData() is called with null as an argument
        // by the FormBuilder constructor. You're only concerned with when
        // setData is called with an actual Entity object in it (whether new
        // or fetched with Doctrine). This if statement lets you skip right
        // over the null condition.
        if (! $data instanceof Element) {
            return;
        }

        $index = 0;
        $choices = array(0);
        if ($data->getPath() === null) {
            $data->initPaths();
        }

        foreach (array_slice($data->getPath(), 1) as $pathElement) {
            $optionsElement = array('label' => ' ',
                             'disabled' => true,
                             'mapped' => false,
            );

            
            $builder = $this->factory->createNamedBuilder("element_$index", 'text', $pathElement, $optionsElement);
            $form->add($builder->getForm());
            $index += 1;
            $choices[] = $index;
        }

        if ($index > 0) {
            $builder = $this->factory->createNamedBuilder('generalize', 'choice', null, array('mapped' => false,
                                                                                              'choices' => $choices,
                                                                                              'label' => 'generaliser'));
            $form->add($builder->getForm());
        }
    }
}
