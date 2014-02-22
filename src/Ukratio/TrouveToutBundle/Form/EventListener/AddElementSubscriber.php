<?php

namespace Ukratio\TrouveToutBundle\Form\EventListener;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;

use Ukratio\TrouveToutBundle\Entity\Element;
use Ukratio\TrouveToutBundle\Entity\ElementRepository;
use Ukratio\TrouveToutBundle\Entity\Concept;
use Ukratio\TrouveToutBundle\Entity\ConceptRepository;
use Ukratio\TrouveToutBundle\Entity\Type;
use Ukratio\TrouveToutBundle\Service\CaractTypeManager;

class AddElementSubscriber implements EventSubscriberInterface
{
    private $factory;
    private $elementRepo;
    private $type;
    private $conceptRepo;
    private $caractTypeManager;

    public function __construct(FormFactoryInterface $factory, ConceptRepository $conceptRepo, ElementRepository $elementRepo , Type $type, CaractTypeManager $caractTypeManager)
    {
        $this->factory = $factory;
        $this->elementRepo = $elementRepo;
        $this->conceptRepo = $conceptRepo;
        $this->type = $type;
        $this->caractTypeManager = $caractTypeManager;
    }

    public static function getSubscribedEvents()
    {
        return array(FormEvents::PRE_SET_DATA => 'preSetData',
                     FormEvents::PRE_BIND => 'preBind');
    }

    public function preBind(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        if (! isset($data['value'])) {
            $form->add($this->caractTypeManager->createElementForm('value', $this->type, null));
            return;
        }

        if (is_array($data['value'])) {
            if ($data['value']['choice'] == 'other') { //TOSEE
                $possibleValues = $data['value']['text'];
            } else {
                $possibleValues = $data['value']['choice'];
            }
        } else {
            $possibleValues = $data['value'];
            if ( $this->type == Type::$date) {
                if (preg_match( '#^\d\d/\d\d/\d\d\d\d$#' , $possibleValues) == 1) {
                    $possibleValues .= ' 12:00';
                    $data['value'] = $possibleValues;
                    $event->setData($data);
                }

            }

        }

        $form->add($this->caractTypeManager->createElementForm('value', $this->type, $possibleValues));

    }

    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        if (! $data instanceof Element) {
            $path = array();
        } else {
            $path = array_slice($data->getPath(), 1);
        }

        $form->add($this->caractTypeManager->createElementForm('value', $this->type, $path));
    }

}
