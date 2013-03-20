<?php

namespace Ukratio\TrouveToutBundle\Service;

use Ukratio\TrouveToutBundle\Entity\Concept;
use Ukratio\TrouveToutBundle\Entity\Discriminator;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Ukratio\TrouveToutBundle\Form\Type\SetType;
use Ukratio\TrouveToutBundle\Form\Type\CategoryType;
use Doctrine\ORM\EntityManager;
use Ukratio\ToolBundle\debug\Message;

class ConceptFormManager
{
    protected $formFactory;
    protected $em;

    public function __construct(FormFactoryInterface $formFactory, EntityManager $em)
    {
        $this->formFactory = $formFactory;
        $this->em = $em;
    }

    public function createConcept($type)
    {
        $concept = new Concept();
        $concept->setType($type->getName());

        $form = $this->createForm($concept);
        return $this->arrayForTemplate($concept, $form);
    }

    public function saveConcept(Concept $concept)
    {

        $this->em->persist($concept);
        $this->em->flush($concept);
     
        foreach ($concept->getMoreGeneralConceptConcepts() as $conceptConcept) {
            $this->em->persist($conceptConcept);
            $this->em->flush($conceptConcept);
        }
   
        // /!\ caract are not flush because it is the owner of the relation
        foreach ($concept->getCaracts() as $caract) {
            $this->em->persist($caract);
            $this->em->flush($caract);
        }
        
    }

    public function createForm(Concept $concept)
    {
        $options = array();
        $concept->initPaths();   

        $this->em->persist($concept);

        switch (Discriminator::getEnumerator($concept->getType())) {
            case Discriminator::$Set:
                $form = $this->formFactory->create('TrouveTout_Set', $concept, $options);
                break;
            case Discriminator::$Category:
                $form = $this->formFactory->create('TrouveTout_Category', $concept, $options);
                break;
            case Discriminator::$Research:
                $form = $this->formFactory->create('TrouveTout_Research', $concept, $options);
                break;
            default:
                throw new \Exception('impossible case with discriminator = ' . $concept->getType());
        }
        
        return $form;
    }

    public function arrayForTemplate(Concept $concept, FormInterface $form, ResearchConcept $researchResults = null)
    {
        return array(
            'concept' => $concept,
            'form' => $form->createView(),
            'conceptType' => $concept->getType(),
            'researchResults' => $researchResults,
        );
    }
}