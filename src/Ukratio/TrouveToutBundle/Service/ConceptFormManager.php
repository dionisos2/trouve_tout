<?php

namespace Ukratio\TrouveToutBundle\Service;

use Ukratio\TrouveToutBundle\Entity\Concept;
use Ukratio\TrouveToutBundle\Entity\Caract;
use Ukratio\TrouveToutBundle\Entity\ConceptRepository;
use Ukratio\TrouveToutBundle\Entity\Discriminator;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Ukratio\TrouveToutBundle\Form\Type\SetType;
use Ukratio\TrouveToutBundle\Form\Type\CategoryType;
use Doctrine\ORM\EntityManager;
use Ukratio\ToolBundle\debug\Message;
use Ukratio\TrouveToutBundle\Research\ResearchResults;

class ConceptFormManager
{
    protected $formFactory;
    protected $em;
    protected $conceptRepo;

    public function __construct(FormFactoryInterface $formFactory, EntityManager $em, ConceptRepository $conceptRepo)
    {
        $this->formFactory = $formFactory;
        $this->em = $em;
        $this->conceptRepo = $conceptRepo;
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
        $date = new \DateTime();
        $date->setTimezone(new \DateTimeZone('UTC'));

        $concept->setModifiedAt($date);
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

    public function deleteConcept(Concept $concept)
    {
        $this->em->remove($concept);
        $this->em->flush();
    }

    public function createForm(Concept $concept)
    {
        $options = array();

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

    public function arrayForTemplate(Concept $concept, FormInterface $form, $researchResults = null)
    {
        return array(
            'concept' => $concept,
            'form' => $form->createView(),
            'conceptType' => $concept->getType(),
        );
    }

    public function runResearch(Concept $research)
    {
        $arrayResults = $this->conceptRepo->findByResearch($research);

        $researchResults = new ResearchResults($research);
        $researchResults->setArrayResults($arrayResults);

        return $researchResults;
    }
}