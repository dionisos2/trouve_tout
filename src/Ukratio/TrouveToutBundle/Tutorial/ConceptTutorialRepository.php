<?php

namespace Ukratio\TrouveToutBundle\Tutorial;

use Symfony\Component\Translation\Translator;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

use Ukratio\TrouveToutBundle\Entity\Discriminator;
use Ukratio\TrouveToutBundle\Entity\Concept;
use Ukratio\TrouveToutBundle\Entity\ConceptRepository;

use Ukratio\ToolBundle\Service\ArrayHandling;


class ConceptTutorialRepository extends ConceptRepository
{
    protected $translator;
    protected $concepts;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
        $this->concepts = array();
    }

    public function setConcepts($concepts)
    {
        $this->concepts = $concepts;
    }
    
    public function getConcepts()
    {
        return $this->concepts;
    }

    public function setConceptsByProperties($conceptsProperties)
    {

        $this->concepts = array();
        foreach($conceptsProperties as $properties) {
            $concept = new Concept();
            if (isset($properties['name'])) {
                $concept->setName($properties['name']);
            }

            if (isset($properties['discriminator'])) {
                $concept->setType($properties['discriminator']->getName());
            }
            $this->concepts[] = $concept;
        }
        
        return $this;
    }

    public function findAllCategories()
    {
        return $this->concepts;
    }

    public function findNamedSet()
    {
        return array();
    }

    public function findLinkableSet()
    {
        return array();
    }

    public function findOneByName()
    {
        return null;
    }

    public function findOneById()
    {
        return null;
    }
}
