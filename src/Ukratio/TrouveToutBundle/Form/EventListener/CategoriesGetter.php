<?php

namespace Ukratio\TrouveToutBundle\Form\EventListener;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;
use Doctrine\ORM\EntityManager;

use Ukratio\TrouveToutBundle\Entity\Concept;
use Ukratio\TrouveToutBundle\Entity\ConceptConcept;

class CategoriesGetter implements EventSubscriberInterface
{
    private $categories;

    public function __construct($categories, $conceptRepo, $entityManager)
    {
        $this->categories = $categories;
        $this->conceptRepo = $conceptRepo;
        $this->entityManager = $entityManager;
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
        $oldCategory = $this->conceptRepo->findOneById($category->getId());
        $this->entityManager->refresh($oldCategory);

        $trueCategories = array_filter($this->categories, function (Concept $category) use ($name) {return $category->getName() == $name;});

        if (count($trueCategories) !== 1) {
            throw new \Exception('More or less than one categorie with the same name:' . count($trueCategories));
        }

        $trueCategory = current($trueCategories);

        $event->setData($trueCategory);
    }

}

