<?php

namespace Ukratio\TrouveToutBundle\Form\EventListener;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Doctrine\ORM\EntityManager;


use Ukratio\TrouveToutBundle\Entity\Element;
use Ukratio\TrouveToutBundle\Entity\Concept;
use Ukratio\TrouveToutBundle\Entity\ConceptRepository;
use Ukratio\TrouveToutBundle\Form\Type\ConceptConceptType;

class AddCategories implements EventSubscriberInterface
{
    private $factory;
    private $conceptRepo;

    public function __construct(FormFactoryInterface $factory,ConceptRepository $conceptRepo, EntityManager $entityManager)
    {
        $this->factory = $factory;
        $this->conceptRepo = $conceptRepo;
        $this->entityManager = $entityManager;
    }

    public static function getSubscribedEvents()
    {
        return array(FormEvents::PRE_SET_DATA => 'doAction',
                     FormEvents::POST_BIND => 'postBind');
    }

    public function postBind(FormEvent $event)
    {

    }

    public function doAction(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        $options = array('label' => ' ',
                         'childConcept' => $data
        );

        $named = $this->factory->createNamed('moreGeneralConceptConcepts', 'collection', null, array('type' => new ConceptConceptType($this->conceptRepo, $this->entityManager),
                                                                                                     'label' => ' ',
                                                                                                     'allow_add' => true,
                                                                                                     'allow_delete' => true,
                                                                                                     'by_reference' => false,
                                                                                                     'options' => $options,
                                                                                                     'auto_initialize' => false));
        $form->add($named);

    }

}

