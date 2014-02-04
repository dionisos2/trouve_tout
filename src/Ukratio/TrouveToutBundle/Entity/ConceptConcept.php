<?php

namespace Ukratio\TrouveToutBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ukratio\TrouveToutBundle\Entity\Concept;

/**
 * ConceptConcept
 *
 * @ORM\Table(name="TrouveTout_ConceptConcept")
 * @ORM\Entity
 */
class ConceptConcept
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Ukratio\TrouveToutBundle\Entity\Concept
     *
     * @ORM\ManyToOne(targetEntity="Ukratio\TrouveToutBundle\Entity\Concept", inversedBy="moreSpecificConceptConcepts")
     */
    private $moreGeneral;

    /**
     * @var Ukratio\TrouveToutBundle\Entity\Concept
     *
     * @ORM\ManyToOne(targetEntity="Ukratio\TrouveToutBundle\Entity\Concept", inversedBy="moreGeneralConceptConcepts")
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


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }


}