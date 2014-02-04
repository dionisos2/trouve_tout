<?php

namespace Ukratio\TrouveToutBundle\Form\EventListener;

use Symfony\Component\Form\Event\DataEvent;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;
use Doctrine\ORM\EntityManager;

use Ukratio\TrouveToutBundle\Entity\Element;
use Ukratio\TrouveToutBundle\Entity\Concept;

class AddCaractsOfCategories implements EventSubscriberInterface
{
    public function __construct()
    {
    }

    public static function getSubscribedEvents()
    {
        return array(FormEvents::POST_BIND => 'postBind');
    }

    public function postBind(DataEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();
        
        if ($data === null) {
            return;
        }

        if (! $data instanceof Concept) {//theorically impossible
            return;
        }

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
}

