<?php

namespace Ukratio\TrouveToutBundle\Service;

use Ukratio\TrouveToutBundle\Entity\Type;
use Ukratio\TrouveToutBundle\Entity\Element;
use Ukratio\TrouveToutBundle\Entity\Concept;
use Ukratio\TrouveToutBundle\Entity\ConceptRepository;
use Symfony\Component\Form\FormFactoryInterface;

class CaractTypeManager
{
    private $conceptRepo;
    private $factory;
    private $elementManager;

    public function __construct(FormFactoryInterface $factory, ConceptRepository $conceptRepo, ElementManager $elementManager)
    {
        $this->conceptRepo = $conceptRepo;
        $this->factory = $factory;
        $this->elementManager = $elementManager;
    }

    public function getValueForm(Element $element, $type, $elementChoices, $mapped = true, $label = 'element.modify')
    {
        if ($mapped) {
            $name = 'value';
        } else {
            $name = 'childElement';
        }

        $choices = array_map(function(Element $element) { return $element->getValue();}, $elementChoices);

        $choices = array_combine($choices, $choices);

        switch ($type) {
            case Type::$name:
                $builder = $this->factory->createNamedBuilder($name, 'Tool_ChoiceOrText', null, array('label' => $label,
                                                                                                        'choices' => $choices,
                                                                                                        'mapped' => $mapped));
                break;
            case Type::$number:
                $builder = $this->factory->createNamedBuilder($name, 'Tool_ChoiceOrText', null, array('label' => $label,               
                                                                                                      'property_path' => 'standardValue',
                                                                                                      'choices' => $choices,
                                                                                                      'invalid_message' => 'elemoment.number.invalid',
                                                                                                      'mapped' => $mapped));
                break;
            case Type::$picture:
                $builder = $this->addPictureForm($element, $mapped, $label, $name);
                break;
            case Type::$object:
                $builder = $this->addObjectForm($mapped, $label, $name);
                break;
            case Type::$text:
                $builder = $this->factory->createNamedBuilder($name, 'textarea', null, array('label' => $label,
                                                                                               'mapped' => $mapped));
                break;
            default:
                throw new \Exception('impossible case with type = ' . $this->getType());
        }

        return $builder;
    }

    private function addPictureForm(Element $element, $mapped, $label, $name)
    {
        $choices = array_map(function (Element $element) {return $element->getValue();}, $this->elementManager->filesIn($element, $name == 'childElement'));

        $choices = array('' => '') + $choices;
        $choices = array_combine($choices, $choices);

        $builder = $this->factory->createNamedBuilder($name, 'choice', null, array('label' => $label,
                                                                                   'choices' => $choices,
                                                                                   'mapped' => $mapped,
                                                                                   'required' => false));

        return $builder;
    }

    private function addObjectForm($mapped, $label, $name)
    {
        $choices1 = array_map(function (Concept $element) {return $element->getName();}, $this->conceptRepo->findNamedSet());

        $choices2 = array_map(function (Concept $element) {return $element->getId();}, $this->conceptRepo->findLinkableSet());


        $choices1 = array_combine($choices1, $choices1);
        $choices2 = array_combine($choices2, $choices2);

        $builder = $this->factory->createNamedBuilder($name, 'Tool_ChoiceOrText', null, array('label' => $label,
                                                                                              'choices' => $choices1,
                                                                                              'textType' => 'choice',
                                                                                              'options' => array('choices' => $choices2),
                                                                                              'mapped' => $mapped,
));

        return $builder;
    }
}