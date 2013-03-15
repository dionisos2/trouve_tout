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

    public function saveConcept(Concept $concept)
    {

        $this->em->persist($concept);
        $this->em->flush($concept);
     
        foreach ($concept->getMoreGeneralConceptConcepts() as $conceptConcept) {
            $this->em->persist($conceptConcept);
            $this->em->flush($conceptConcept);
        }
   
        // /!\ caract are not flush because it is the owner of the relationt
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
        if ($concept->getType() === Discriminator::$Set->getName()) {
            $form = $this->formFactory->create('TrouveTout_Set', $concept, $options);
        }

        if ($concept->getType() === Discriminator::$Category->getName()) {
            $form = $this->formFactory->create('TrouveTout_Category', $concept, $options);
        }
        
        return $form;
    }

    public function arrayForTemplate(Concept $concept, FormInterface $form)
    {
        return array(
            'concept' => $concept,
            'form' => $form->createView(),
            'conceptType' => $concept->getType(),
        );
    }
}