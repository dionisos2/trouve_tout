<?php

namespace Ukratio\TrouveToutBundle\Form\EventListener;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;
use Doctrine\ORM\EntityManager;

use Ukratio\TrouveToutBundle\Entity\Element;
use Ukratio\TrouveToutBundle\Entity\ElementRepository;
use Ukratio\TrouveToutBundle\Entity\Type;
use Ukratio\TrouveToutBundle\Form\DataTransformer\TrueElementToElementTransformer;
use Ukratio\TrouveToutBundle\Service\CaractTypeManager;
use Ukratio\ToolBundle\Form\DataTransformer\StringToChoiceOrTextTransformer;

class AddChildElementSubscriber implements EventSubscriberInterface
{
    private $factory;
    private $type;
    private $caractTypeManager;

    public function __construct(FormFactoryInterface $factory, Type $type, CaractTypeManager $caractTypeManager)
    {
        $this->type = $type;
        $this->factory = $factory;
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

        if (isset($data['childValue'])) {
            unset($data['childValue']);
        }

        $event->setData($data); // don’t forget this
    }

    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        if (! $data instanceof Element) {
            return;
        }
        $childElement = $this->caractTypeManager->createElementForm('childValue', $this->type, $data->getPath(), 'element.specify', false);

        if($childElement != null) {
            $form->add($childElement);
        }
    }
}
