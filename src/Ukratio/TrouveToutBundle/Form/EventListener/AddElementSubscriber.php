<?php

namespace Ukratio\TrouveToutBundle\Form\EventListener;

use Symfony\Component\Form\Event\DataEvent;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;

use Ukratio\TrouveToutBundle\Entity\Element;
use Ukratio\TrouveToutBundle\Entity\Concept;
use Ukratio\TrouveToutBundle\Entity\ConceptRepository;
use Ukratio\TrouveToutBundle\Entity\Type;
use Ukratio\TrouveToutBundle\Form\DataTransformer\TrueElementToElementTransformer;
use Ukratio\TrouveToutBundle\Service\CaractTypeManager;

class AddElementSubscriber implements EventSubscriberInterface
{
    private $factory;
    private $elementRepo;
    private $type;
    private $conceptRepo;
    private $caractTypeManager;
    
    public function __construct(FormFactoryInterface $factory, EntityManager $em, ConceptRepository $conceptRepo, Type $type, CaractTypeManager $caractTypeManager)
    {
        $this->factory = $factory;
        $this->elementRepo = $em->getRepository('TrouveToutBundle:Element');
        $this->conceptRepo = $conceptRepo;
        $this->type = $type;
        $this->caractTypeManager = $caractTypeManager;
    }

    public static function getSubscribedEvents()
    {
        // Tells the dispatcher that you want to listen on the form.pre_set_data
        // event and that the preSetData method should be called.
        return array(FormEvents::PRE_SET_DATA => 'preSetData');
    }

    public function preSetData(DataEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        if (! $data instanceof Element) {
            $data = new Element();
        }

        $general = $data->getMoreGeneral();
        if ($general === null) {
            $elementChoices = $this->elementRepo->findHeads();
        } else {
            $elementChoices = $this->elementRepo->findMoreSpecifics($general);
        }

        $builder = $this->caractTypeManager->getValueForm($data, $this->type, $elementChoices);

        $form->add($builder->getForm());        
    }

}
