<?php

namespace Eud\TrouveToutBundle\Service;

use Eud\TrouveToutBundle\Entity\Concept;
use Eud\TrouveToutBundle\Entity\Discriminator;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Eud\TrouveToutBundle\Form\Type\SetType;
use Eud\TrouveToutBundle\Form\Type\CategoryType;
use Doctrine\ORM\EntityManager;
use Eud\ToolBundle\debug\Message;

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

        // concept have to be flush before conceptConcept
        foreach ($concept->getMoreGeneralConceptConcepts() as $conceptConcept) {
            $this->em->persist($conceptConcept);
        }

        $this->em->persist($concept);
        $this->em->flush($concept);
        
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