<?php

namespace Ukratio\TrouveToutBundle\Form\EventListener;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;
use Doctrine\ORM\EntityManager;

use Ukratio\TrouveToutBundle\Entity\Element;
use Ukratio\TrouveToutBundle\Entity\Type;
use Ukratio\TrouveToutBundle\Form\DataTransformer\TrueElementToElementTransformer;
use Ukratio\TrouveToutBundle\Service\CaractTypeManager;
use Ukratio\ToolBundle\Form\DataTransformer\StringToChoiceOrTextTransformer;

class AddChildElementSubscriber implements EventSubscriberInterface
{
    private $factory;
    private $em;
    private $type;
    private $repo;
    private $caractTypeManager;

    public function __construct(FormFactoryInterface $factory, EntityManager $em, Type $type, CaractTypeManager $caractTypeManager)
    {
        $this->type = $type;
        $this->factory = $factory;
        $this->em = $em;
        $this->repo = $this->em->getRepository('TrouveToutBundle:Element');
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

        $dataTransformer = new StringToChoiceOrTextTransformer(array());
        if (isset($data['childElement'])) {
            if (is_array($data['childElement'])) {
                $choices = array($data['childElement']['choice'] => $data['childElement']['choice']); // avoid TransformationFailedException exception…
                $builder = $this->factory->createNamedBuilder('childElement', 'Tool_ChoiceOrText', null, array('label' => 'element.specify',
                                                                                                               'choices' => $choices,
                                                                                                               'mapped' => false));
            } else {
                $builder = $this->factory->createNamedBuilder('childElement', 'textarea', null, array('label' => 'element.specify',
                                                                                                      'mapped' => false,
                                                                                                      'required' => false));
            }


            $form->add($builder->getForm());
        }
    }

    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        if (! $data instanceof Element) {
            return;
        }
        
        $moreSpecifics = $this->repo->findMoreSpecifics($data);

        $builder = $this->caractTypeManager->getValueForm($data, $this->type, $moreSpecifics, false, 'element.specify');

        $form->add($builder->getForm());
    }
}
