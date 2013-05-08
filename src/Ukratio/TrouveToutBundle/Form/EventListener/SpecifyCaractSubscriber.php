<?php

namespace Ukratio\TrouveToutBundle\Form\EventListener;

use Symfony\Component\Form\Event\DataEvent;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;
use Doctrine\ORM\EntityManager;

use Ukratio\TrouveToutBundle\Entity\Caract;
use Ukratio\TrouveToutBundle\Entity\Element;
use Ukratio\TrouveToutBundle\Entity\Type;

use Ukratio\ToolBundle\Debug\Message;

class SpecifyCaractSubscriber implements EventSubscriberInterface
{
    private $factory;
    private $em;
    private $elementRepo;
    
    public function __construct(FormFactoryInterface $factory, EntityManager $em)
    {
        $this->factory = $factory;
        $this->em = $em;
        $this->elementRepo = $this->em->getRepository('TrouveToutBundle:Element');
    }

    public static function getSubscribedEvents()
    {
        return array(FormEvents::POST_BIND => 'postBind');
    }

    public function postBind(DataEvent $event)
    {
        $caract = $event->getData();
        $form = $event->getForm();

        if (! $caract instanceof Caract) {
            return;
        }

        if ($form->get('value') === null) {
            return;
        }
        
        $element = $form->get('value')->getData();

        $ownerElements = array();
        $index = 0;
        while ($form->get('value')->has('element_'.$index)) {
            $ownerElements['element_'.$index] = $form->get('value')->get('element_'.$index)->getData();
            $index++;
        }

        $ownerElements = array_filter($ownerElements, function ($elementValue) {return $elementValue !== null;});

        $pathElement = array_values($ownerElements);

        if ($element->getValue() != null) {
            $pathElement[] = $element->getValue();
        } else {
            $caract->setValue(null);
            return;
        }

        $pathElement = array_reverse($pathElement);
        
        $trueElement = $this->elementRepo->findByPath($pathElement, true);

        if ($trueElement !== null) {
            $caract->setValue($trueElement);
        } else {//creation
            $trueElement = $this->createElementByPath($pathElement);
            $caract->setValue($trueElement);
        }
    }

    public function createElementByPath($pathElement)
    {
        $value = $pathElement[0];

        $pathElement = array_slice($pathElement, 1);

        $newElement = new Element($value);
        if (count($pathElement) != 0) {
            $element = $this->elementRepo->findByPath($pathElement, true);
            if ($element === null) {
                $element = $this->createElementByPath($pathElement);
            }
            $newElement->setMoreGeneral($element);
        }

        return $newElement;
    }
}
