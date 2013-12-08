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
        $sum = 0;
        do {
            $orphanElements = $this->elementRepo->findOrphanElements();
            $number = count($orphanElements);
            $sum += $number;
            var_dump($number);
            foreach ($orphanElements as $orphan) {
                $this->em->remove($orphan);
                $this->em->flush();
            }
        }
        while($number > 0);

        return $sum;
    }

    public function computeSpecificities()
    {
        $categories = $this->conceptRepo->findAllCategories();

        $caractsNumber = array();
        foreach ($categories as $category) {
            foreach ($category->getCaracts() as $caract) {
                if (! isset($caractsNumber[$caract->getName()])) {
                    $caractsNumber[$caract->getName()] = 0;
                }
                $caractsNumber[$caract->getName()]++;
            }
        }

        $number = 0;
        $numberOfCategories = count($categories);

        foreach ($categories as $category) {
            foreach ($category->getCaracts() as $caract) {
                $specificity = ($numberOfCategories + 1 - $caractsNumber[$caract->getName()]) / $numberOfCategories;
                $caract->setSpecificity($specificity);
                $number++;
            }
        }

        $this->em->flush();
        return $number;
    }
}