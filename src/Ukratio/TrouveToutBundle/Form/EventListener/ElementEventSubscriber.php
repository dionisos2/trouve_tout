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

class ElementEventSubscriber implements EventSubscriberInterface
{
    private $factory;
    private $type;
    private $caractTypeManager;

    public function __construct(FormFactoryInterface $factory, CaractTypeManager $caractTypeManager)
    {
        $this->factory = $factory;
        $this->caractTypeManager = $caractTypeManager;
    }

    public function setType($type)
    {
        $this->type = $type;
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

        $ownerElements = array();
        $index = 0;

        while (isset($data['element_'.$index])) {
            $ownerElements['element_'.$index] = $data['element_'.$index];
            $index++;
        }

        krsort($ownerElements);

        foreach ($ownerElements as $key => $ownerElement) {
            $form->add($this->factory->createNamed($key, 'text', $ownerElement, array('mapped' => false,
                                                                                      'label' => ' ',
                                                                                      'read_only' => true,
                                                                                      'auto_initialize' => false)));
        }


        if (isset($data['childValue'])) {
            unset($data['childValue']);
        }

        $event->setData($data); // don’t forget this

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

            $ownerElements = array_slice($data->getPath(), 1);

            $ownerElements = array_reverse($ownerElements);
            krsort($ownerElements);

            foreach ($ownerElements as $key => $pathElement) {
                $optionsElement = array('label' => ' ',
                                        'read_only' => true,
                                        'mapped' => false,
                                        'auto_initialize' => false,
                );

                $builder = $this->factory->createNamedBuilder("element_$key", 'text', $pathElement, $optionsElement);
                $form->add($builder->getForm());
            }


            $childElement = $this->caractTypeManager->createElementForm('childValue', $this->type, $data->getPath(), 'element.specify', false);

            if($childElement != null) {
                $form->add($childElement);
            }
        }

        $form->add($this->caractTypeManager->createElementForm('value', $this->type, $path));
    }

}
