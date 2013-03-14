<?php

namespace Eud\TrouveToutBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Eud\TrouveToutBundle\Entity\Concept;

/**
 * ConceptConcept
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class ConceptConcept
{

    /**
     * @var Eud\TrouveToutBundle\Entity\Concept
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Eud\TrouveToutBundle\Entity\Concept", inversedBy="moreSpecificConcepts")
     */
    private $moreGeneral;

    /**
     * @var Eud\TrouveToutBundle\Entity\Concept
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Eud\TrouveToutBundle\Entity\Concept", inversedBy="moreGeneralConcepts")
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
     * @param \Eud\TrouveToutBundle\Entity\Concept $moreGeneral
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
     * @return \Eud\TrouveToutBundle\Entity\Concept
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
     * @param \Eud\TrouveToutBundle\Entity\Concept $moreSpecific
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
     * @return \Eud\TrouveToutBundle\Entity\Concept
     *
     * @codeCoverageIgnore
     */
    public function getMoreSpecific()
    {
        return $this->moreSpecific;
    }

}