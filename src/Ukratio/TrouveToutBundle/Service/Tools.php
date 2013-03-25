<?php

namespace Ukratio\TrouveToutBundle\Service;

use Doctrine\ORM\EntityManager;

use Ukratio\TrouveToutBundle\Entity\Concept;
use Ukratio\TrouveToutBundle\Entity\ConceptRepository;
use Ukratio\TrouveToutBundle\Entity\Discriminator;
use Ukratio\TrouveToutBundle\Form\Type\SetType;
use Ukratio\TrouveToutBundle\Form\Type\CategoryType;
use Ukratio\ToolBundle\debug\Message;
use Ukratio\TrouveToutBundle\Research\ResearchResults;

class Tools
{
    
    protected $em;
    protected $conceptRepo;
    protected $elementRepo;

    public function __construct(EntityManager $em, ConceptRepository $conceptRepo)
    {
        $this->em = $em;
        $this->conceptRepo = $conceptRepo;
        $this->elementRepo = $this->em->getRepository('TrouveToutBundle:Element');
    }

    public function deleteUnamedResearches()
    {
        $unamedResearches = $this->conceptRepo->findUnamedResearches();
        $number = count($unamedResearches);
        
        foreach ($unamedResearches as $research) {
            $this->em->remove($research);
        }

        $this->em->flush();
        return $number;
    }

    public function deleteOrphanElements()
    {
        $orphanElements = $this->elementRepo->findOrphanElements();
        $number = count($orphanElements);
        
        foreach ($orphanElements as $research) {
            $this->em->remove($research);
        }

        $this->em->flush();
        return $number;
    }

    public function computeSpecificities()
    {
        return 5;
    }
}