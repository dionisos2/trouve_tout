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


class AddElementSubscriber implements EventSubscriberInterface
{
    private $factory;
    private $elementRepo;
    private $type;
    private $conceptRepo;
    
    public function __construct(FormFactoryInterface $factory, EntityManager $em, ConceptRepository $conceptRepo, Type $type)
    {
        $this->factory = $factory;
        $this->elementRepo = $em->getRepository('TrouveToutBundle:Element');
        $this->conceptRepo = $conceptRepo;
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
            $elementChoices = $this->elementRepo->findHeads();
        } else {
            $elementChoices = $this->elementRepo->findMoreSpecifics($general);
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
                $builder = $this->addObjectForm();
                break;
            case Type::$text:
                $builder = $this->factory->createNamedBuilder('value', 'textarea', null, array('label' => 'Modifier:',));
                break;
            default:
                throw new \Exception('impossible case with type = ' . $this->getType());
        }

        $form->add($builder->getForm());        
    }

    private function addObjectForm()
    {
        $choices1 = array_map(function (Concept $element) {return $element->getName();}, $this->conceptRepo->findNamedSet());

        $choices2 = array_map(function (Concept $element) {return $element->getId();}, $this->conceptRepo->findLinkableSet());


        $choices1 = array_combine($choices1, $choices1);
        $choices2 = array_combine($choices2, $choices2);

        $builder = $this->factory->createNamedBuilder('value', 'Tool_ChoiceOrText', null, array('label' => 'Modifier:',
                                                                                                'choices' => $choices1,
                                                                                                'textType' => 'choice',
                                                                                                'options' => array('choices' => $choices2),));

        return $builder;
    }
}
