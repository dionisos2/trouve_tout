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

class ConceptEventSubscriber implements EventSubscriberInterface
{
    protected $factory;
    protected $conceptRepo;
    protected $entityManager;
    protected $conceptConceptType;

    public function __construct(FormFactoryInterface $factory,ConceptRepository $conceptRepo, EntityManager $entityManager, ConceptConceptType $conceptConceptType)
    {
        $this->factory = $factory;
        $this->conceptRepo = $conceptRepo;
        $this->entityManager = $entityManager;
        $this->conceptConceptType = $conceptConceptType;
    }

    public static function getSubscribedEvents()
    {
        return array(FormEvents::PRE_SET_DATA => 'preSetData');
        return array(FormEvents::POST_SUBMIT => 'postSubmit');
    }

    public function postSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        if ($data === null) {
            return;
        }

        if (! $data instanceof Concept) {//theorically impossible
            return;
        }
        echo "uanierstuarnietrsunateiuie";
        throw new UnexpectedTypeException($data, 'Caract');
        $this->addCaractsForAllCategories($data);
    }

    public function addCaractsForAllCategories(Concept $concept)
    {
        foreach($concept->getMoreGeneralConcepts() as $generalCategory) {
            $this->addCaractsOfCategory($concept, $generalCategory);
        }
    }

    private function addCaractsOfCategory(Concept $concept, Concept $category)
    {
        foreach($category->getCaracts() as $caract) {
            $name = $caract->getName();
            if ($caract->getSelected() and ($concept->getCaract($name) == null)) {
                $caract->setSelected($caract->getByDefault());
                $caract = clone $caract;
                $concept->addCaract($caract);
            }
        }
    }

    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        $options = array('label' => ' ',
                         'childConcept' => $data
        );

        $named = $this->factory->createNamed('moreGeneralConceptConcepts', 'collection', null, array('type' => $this->conceptConceptType,
                                                                                                     'label' => ' ',
                                                                                                     'allow_add' => true,
                                                                                                     'allow_delete' => true,
                                                                                                     'by_reference' => false,
                                                                                                     'options' => $options,
                                                                                                     'auto_initialize' => false));
        $form->add($named);

    }

}

