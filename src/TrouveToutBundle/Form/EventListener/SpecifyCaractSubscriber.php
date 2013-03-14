<?php

namespace Eud\TrouveToutBundle\Form\EventListener;

use Symfony\Component\Form\Event\DataEvent;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;
use Doctrine\ORM\EntityManager;

use Eud\TrouveToutBundle\Entity\Caract;
use Eud\TrouveToutBundle\Entity\Element;
use Eud\TrouveToutBundle\Entity\Type;

use Eud\ToolBundle\Debug\Message;

class SpecifyCaractSubscriber implements EventSubscriberInterface
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
        return array(FormEvents::POST_BIND => 'postBind');
    }

    public function postBind(DataEvent $event)
    {
        $caract = $event->getData();
        $form = $event->getForm();

        if (! $caract instanceof Caract) {
            return;
        }

        if (($caract->getValue() === null)||(! $form->get('value')->has('childElement'))) {
            return;
        }
        
        $element = $form->get('value')->getData();
        $childElementName = $form->get('value')->get('childElement')->getData();

        if ($form->get('value')->has('generalize')) {
            $generalize = $form->get('value')->get('generalize')->getData();
        } else {
            $generalize = 0;
        }

        if ($element->getPath() != null) {
            if ($childElementName != null) {
                $this->specify($caract, $element, $childElementName);
            } elseif ($generalize != 0) {
                $this->generalize($caract, $element, $generalize);
            } //else modification
        }// else creation
    }
    
    private function specify(Caract $caract, Element $element, $childElementName)
    {

        $path = array_merge(array($childElementName) ,$element->getPath());

        $childElement = $this->repo->findByPath($path);

        if ($childElement === null) { //création
            $childElement = new Element();
            $childElement->setValue($childElementName);
            $childElement->setMoreGeneral($element);
        }
            
        $caract->setType(Type::$name->getName());
        $caract->setValue($childElement);
    }

    private function generalize(Caract $caract, Element $element, $generalize)
    {
        $path = array_slice($element->getPath(), $generalize);
        $parentElement = $this->repo->findByPath($path);
            
        if ($parentElement === null) {
            throw new \RuntimeException('generalized element not found, this situation is theoretically impossible');
        }

        $caract->setType(Type::$name->getName());
        $caract->setValue($parentElement);        
    }
}
