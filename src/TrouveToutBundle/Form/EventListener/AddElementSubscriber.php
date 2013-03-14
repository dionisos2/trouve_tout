<?php

namespace Eud\TrouveToutBundle\Form\EventListener;

use Symfony\Component\Form\Event\DataEvent;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;

use Eud\TrouveToutBundle\Entity\Element;
use Eud\TrouveToutBundle\Entity\Type;
use Eud\TrouveToutBundle\Form\DataTransformer\TrueElementToElementTransformer;


class AddElementSubscriber implements EventSubscriberInterface
{
    private $factory;
    private $em;
    private $repo;
    private $type;
    private $conceptRepo;
    
    public function __construct(FormFactoryInterface $factory, EntityManager $em, Type $type)
    {
        $this->factory = $factory;
        $this->em = $em;
        $this->repo = $this->em->getRepository('TrouveToutBundle:Element');
        $this->conceptRepo = $this->em->getRepository('TrouveToutBundle:Concept');
        $this->type = $type;
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
            $elementChoices = $this->repo->findHeads();
        } else {
            $elementChoices = $this->repo->findMoreSpecifics($general);
        }

        $choices = array_map(function(Element $element) { return $element->getValue();}, $elementChoices);

        $choices = array_combine($choices, $choices);

        switch ($this->type) {
            case Type::$name:
                $builder = $this->factory->createNamedBuilder('value', 'Tool_ChoiceOrText', null, array('label' => 'Modifier:',
                                                                                                        'choices' => $choices));
                break;
            case Type::$number:
                $builder = $this->factory->createNamedBuilder('value', 'number', null, array('label' => 'Modifier:',               
                                                                                                        'property_path' => 'standardValue'));
                break;
            case Type::$picture:
                $builder = $this->factory->createNamedBuilder('value', 'Tool_ChoiceOrText', null, array('label' => 'Modifier:',
                                                                                                        'choices' => $choices));
                break;
            case Type::$object:
                $builder = $this->factory->createNamedBuilder('value', 'entity', null, array('label' => 'Modifier:',
                                                                                             'class' => 'TrouveToutBundle:Concept',
                                                                                             'property' => 'name',
                                                                                             'query_builder' => function(EntityRepository $er) { return $er->QueryBuilderNamedSet();}));
                break;
            case Type::$text:
                $builder = $this->factory->createNamedBuilder('value', 'textarea', null, array('label' => 'Modifier:',));
                break;
            default:
                throw new \Exception('impossible case with type = ' . $this->getType());
        }

        $form->add($builder->getForm());        
    }
}
