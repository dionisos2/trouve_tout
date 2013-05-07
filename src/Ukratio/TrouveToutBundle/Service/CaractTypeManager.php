<?php

namespace Ukratio\TrouveToutBundle\Service;

use Ukratio\TrouveToutBundle\Entity\Type;
use Ukratio\TrouveToutBundle\Entity\Element;
use Ukratio\TrouveToutBundle\Entity\ElementRepository;
use Ukratio\TrouveToutBundle\Entity\Concept;
use Ukratio\TrouveToutBundle\Entity\ConceptRepository;
use Symfony\Component\Form\FormFactoryInterface;

class CaractTypeManager
{
    private $conceptRepo;
    private $elementRepo;
    private $factory;
    private $elementManager;

    public function __construct(FormFactoryInterface $factory, ConceptRepository $conceptRepo, ElementRepository $elementRepo, ElementManager $elementManager)
    {
        $this->conceptRepo = $conceptRepo;
        $this->factory = $factory;
        $this->elementManager = $elementManager;
        $this->elementRepo = $elementRepo;
    }

    public function getFormTypeFor($type) 
    {
        switch ($type) {
            case Type::$name:
                return 'Tool_ChoiceOrText';
            case Type::$number:
                return 'Tool_ChoiceOrText';
            case Type::$picture:
                return 'choice';
            case Type::$object:
                return 'Tool_ChoiceOrText';
            case Type::$text:
                return 'textarea';
            default:
                throw new \Exception('impossible case with type = ' . $this->getType());
        }
    }

    public function getChoicesFor($type, $path)
    {

        if ($path == null) { //TODO should be in the repo
            $elementChoices = $this->elementRepo->findHeads();
        } else {
            $element = $this->elementRepo->findByPath($path, true);

            if($element !== null) {
                $elementChoices = $this->elementRepo->findMoreSpecifics($element);
            } else {
                $elementChoices = array();
            }
        }


        $choices = array_map(function(Element $element) { return $element->getValue();}, $elementChoices);

        $choices = array_combine($choices, $choices);

        switch ($type) {
            case Type::$name:
                return $choices;
            case Type::$number:
                return $choices;
            case Type::$picture:
                $choices = array_map(function (Element $element) {return $element->getValue();}, $this->elementManager->filesIn($path));

                $choices = array_combine($choices, $choices);
                $choices = array('' => '') + $choices;
                return $choices;
            case Type::$object:
                $choices1 = array_map(function (Concept $element) {return $element->getName();}, $this->conceptRepo->findNamedSet());

                $choices2 = array_map(function (Concept $element) {return $element->getId();}, $this->conceptRepo->findLinkableSet());

                $choices1 = array_combine($choices1, $choices1);
                $choices2 = array_combine($choices2, $choices2);
                $choices2 = array('' => '') + $choices2;

                return array('choices1' => $choices1, 'choices2' => $choices2);
            case Type::$text:
                return null;
            default:
                throw new \Exception('impossible case with type = ' . $this->getType());
        }
    }

    public function createElementForm($name, $type, $path, $label = 'element.modify', $mapped = true)
    {
        $options = array('label' => $label, 'mapped' => $mapped, 'required' => $mapped);

        if (is_array($path)) {
            $choices = $this->getChoicesFor($type, $path);
        } else {
            $choices = array($path => $path);
        }


        if ($choices !== null) {
            if(isset($choices['choices1']) and is_array($choices['choices1'])) {
                $options = $options + array('textType' => 'choice',
                                            'choices' => $choices['choices1'],
                                            'options' => array('choices' => $choices['choices2']));
            } else {
                $options = $options + array('choices' => $choices);
            }
        }

        $builder = $this->factory->createNamedBuilder($name, $this->getFormTypeFor($type), null, $options);

        return $builder->getForm();
    }

}
