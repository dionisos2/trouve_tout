<?php

namespace Ukratio\TrouveToutBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ukratio\TrouveToutBundle\Entity\Concept;

/**
 * ConceptConcept
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class ConceptConcept
{

    /**
     * @var Ukratio\TrouveToutBundle\Entity\Concept
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Ukratio\TrouveToutBundle\Entity\Concept", inversedBy="moreSpecificConcepts")
     */
    private $moreGeneral;

    /**
     * @var Ukratio\TrouveToutBundle\Entity\Concept
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Ukratio\TrouveToutBundle\Entity\Concept", inversedBy="moreGeneralConcepts")
     */
    private $moreSpecific;


    public function linkConcepts(Concept $moreSpecific, Concept $moreGeneral)
    {
        $this->setMoreGeneral($moreGeneral);
        $this->setMoreSpecific($moreSpecific);
    }

    /**
     * Set moreGeneral
     *
     * @param \Ukratio\TrouveToutBundle\Entity\Concept $moreGeneral
     * @return ConceptConcept
     *
     * @codeCoverageIgnore
     */
    public function setMoreGeneral(Concept $moreGeneral)
    {
        $this->moreGeneral = $moreGeneral;
    
        return $this;
    }

    /**
     * Get moreGeneral
     *
     * @return \Ukratio\TrouveToutBundle\Entity\Concept
     *
     * @codeCoverageIgnore
     */
    public function getMoreGeneral()
    {
        return $this->moreGeneral;
    }

    /**
     * Set moreSpecific
     *
     * @param \Ukratio\TrouveToutBundle\Entity\Concept $moreSpecific
     * @return ConceptConcept
     *
     * @codeCoverageIgnore
     */
    public function setMoreSpecific(Concept $moreSpecific)
    {
        $this->moreSpecific = $moreSpecific;
        
        return $this;
    }

    /**
     * Get moreSpecific
     *
     * @return \Ukratio\TrouveToutBundle\Entity\Concept
     *
     * @codeCoverageIgnore
     */
    public function getMoreSpecific()
    {
        return $this->moreSpecific;
    }

}