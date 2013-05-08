<?php

namespace Ukratio\TrouveToutBundle\Form\EventListener;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;
use Doctrine\ORM\EntityManager;

use Ukratio\TrouveToutBundle\Entity\Concept;
use Ukratio\TrouveToutBundle\Entity\ConceptConcept;
use Ukratio\TrouveToutBundle\Form\DataTransformer\TrueElementToElementTransformer;

class CategoriesGetter implements EventSubscriberInterface
{
    private $categories;

    public function __construct($categories)
    {
        $this->categories = $categories;
    }

    public static function getSubscribedEvents()
    {
        return array(FormEvents::BIND => 'bind');
    }

    public function bind(FormEvent $event)
    {
        $category = $event->getData();
        $form = $event->getForm();
        
        if (! $category instanceof Concept) {
            return;
        }
        $name = $category->getName();

        $trueCategories = array_filter($this->categories, function (Concept $category) use ($name) {return $category->getName() == $name;});

        if (count($trueCategories) !== 1) {
            throw new \Exception('More or less than one concept with the same name:' . count($concepts));
        }

        $trueCategory = current($trueCategories);

        $event->setData($trueCategory);
    }

}

