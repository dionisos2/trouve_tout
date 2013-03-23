<?php

namespace Ukratio\TrouveToutBundle\Form\EventListener;

use Symfony\Component\Form\Event\DataEvent;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;
use Doctrine\ORM\EntityManager;

use Ukratio\TrouveToutBundle\Entity\Element;
use Ukratio\TrouveToutBundle\Form\DataTransformer\TrueElementToElementTransformer;

class AddChildElementSubscriber implements EventSubscriberInterface
{
    private $factory;
    private $em;
    private $repo;
    
    public function __construct(FormFactoryInterface $factory, EntityManager $em)
    {
        $this->factory = $factory;
        $this->em = $em;
        $this->repo = $this->em->getRepository('TrouveToutBundle:Element');
    }

    public static function getSubscribedEvents()
    {
        return array(FormEvents::PRE_SET_DATA => 'preSetData');
    }

    public function preSetData(DataEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        if (! $data instanceof Element) {
            return;
        }
        
        $moreSpecifics = $this->repo->findMoreSpecifics($data);

        $choices = array_map(function(Element $element) { return $element->getValue();}, $moreSpecifics);

        $choices = array_combine($choices, $choices);


        $builder = $this->factory->createNamedBuilder('childElement', 'Tool_ChoiceOrText', null, array('mapped' => false,
                                                                                                       'choices' => $choices,
                                                                                                       'label' => 'element.specify'));
        $form->add($builder->getForm());
    }
}
