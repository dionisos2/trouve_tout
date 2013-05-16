<?php

namespace Ukratio\TrouveToutBundle\Form\EventListener;

use Symfony\Component\Form\Event\DataEvent;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;
use Doctrine\ORM\EntityManager;


use Ukratio\TrouveToutBundle\Entity\Element;
use Ukratio\TrouveToutBundle\Entity\Concept;
use Ukratio\TrouveToutBundle\Entity\ConceptRepository;
use Ukratio\TrouveToutBundle\Form\DataTransformer\TrueElementToElementTransformer;
use Ukratio\TrouveToutBundle\Form\Type\ConceptConceptType;

class AddCategories implements EventSubscriberInterface
{
    private $factory;
    private $conceptRepo;

    public function __construct(FormFactoryInterface $factory,ConceptRepository $conceptRepo)
    {
        $this->factory = $factory;
        $this->conceptRepo = $conceptRepo;
    }

    public static function getSubscribedEvents()
    {
        return array(FormEvents::PRE_SET_DATA => 'doAction');
    }

    public function doAction(DataEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();
        
        $options = array('label' => ' ',
                         'childConcept' => $data
        );

        $named = $this->factory->createNamed('moreGeneralConceptConcepts', 'collection', null, array('type' => new ConceptConceptType($this->conceptRepo), 
                                                                                                     'label' => ' ',
                                                                                                     'allow_add' => true,
                                                                                                     'allow_delete' => true,
                                                                                                     'by_reference' => false,
                                                                                                     'options' => $options));
        $form->add($named);
        
    }

}

