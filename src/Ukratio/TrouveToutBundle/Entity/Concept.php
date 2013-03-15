<?php

namespace Ukratio\TrouveToutBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ukratio\ToolBundle\Service;
use Ukratio\ToolBundle\Service\Enum;
use Ukratio\ToolBundle\Service\AssertData;
use Doctrine\Common\Collections\ArrayCollection;
use Ukratio\TrouveToutBundle\Constant;
use Ukratio\TrouveToutBundle\Entity\Discriminator;
use Ukratio\TrouveToutBundle\Entity\Element;
use Ukratio\TrouveToutBundle\Entity\Caract;
use Ukratio\TrouveToutBundle\Entity\ConceptConcept;
use Symfony\Component\Validator\Constraints as Assert;
use Ukratio\ToolBundle\Validator as MyAssert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * Ukratio\TrouveToutBundle\Entity\Concept
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Ukratio\TrouveToutBundle\Entity\ConceptRepository")
 * @UniqueEntity("name")
 */
class Concept
{
    /**
     * Dependance injection for AssertData
     */
    private static $ad = null;

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $type
     *
     * @ORM\Column(name="type", type="string", length=255)
     * @MyAssert\TypeEnum(enumName="Ukratio\TrouveToutBundle\Entity\Discriminator")
     */
    private $type;

    /**
     * @var string $number
     *
     * @ORM\Column(name="number", type="string", length=16, nullable=true)
     * @Assert\Type(type="int")
     */
    private $number;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true, unique=true)
     * @Assert\Type(type="string")
     */
    private $name;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Ukratio\TrouveToutBundle\Entity\ConceptConcept", mappedBy="moreSpecific", cascade={"persist"}, orphanRemoval=true)
     */
    private $moreGeneralConceptConcepts;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Ukratio\TrouveToutBundle\Entity\ConceptConcept", mappedBy="moreGeneral", cascade={"persist"}, orphanRemoval=true)
     */
    private $moreSpecificConceptConcepts;

    /**
     * @var string $caracts
     *
     * @ORM\OneToMany(targetEntity="Ukratio\TrouveToutBundle\Entity\Caract", mappedBy="ownerConcept", cascade={"persist", "detach", "merge"}, orphanRemoval=true)
     * @Assert\Valid(traverse=true)
     */
    private $caracts;

    public function __toString()
    {
        return $this->getName();
    }

    public function toString()
    {
        $lineEnd = '<br>';
        $str = 'Concept id: ' . $this->getId() . $lineEnd;
        $str .= 'Type: ' . $this->getType() . $lineEnd;

        if ($this->getName() !== null) {
            $str .= 'Name: ' . $this->getName() . $lineEnd;
        }

        if ($this->getNumber() !== null) {
            $str .= 'Number: ' . $this->getNumber() . $lineEnd;
        }

        $str .= 'CARACTS' . $lineEnd;
        foreach ($this->getCaracts() as $caract) {
            $str .= (string)$caract;
        }

        return $str;
    }

    
    public function __construct()
    {
        if (static::$ad === null) {
            static::$ad = new AssertData();
        }

        $this->caracts = new ArrayCollection();
		$this->moreGeneralConceptConcepts = new ArrayCollection();
		$this->moreSpecificConceptConcepts = new ArrayCollection();
        $this->type = Discriminator::$Set->getName();
        $this->number = 1;
        $this->name = null;
    }

    public function initPaths()
    {
        foreach ($this->caracts as $caract) {
            $caract->initPaths();
        }
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


    /*
     * Only for debug
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Concept
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set number
     *
     * @param string $number
     * @return Concept
     */
    public function setNumber($number)
    {
        $this->number = $number;
        return $this;
    }

    /**
     * Get number
     *
     * @return string 
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Concept
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add moreGeneralConcepts
     *
     * @param \Ukratio\TrouveToutBundle\Entity\Concept $moreGeneralConcepts
     * @return Concept
     *
     */
    public function addMoreGeneralConcept(Concept $moreGeneralConcept)
    {
        $conceptConcept = new ConceptConcept();
        $conceptConcept->linkConcepts($this, $moreGeneralConcept);

        $this->moreGeneralConceptConcepts[] = $conceptConcept;
        return $this;
    }

    /**
     * Add moreGeneralConceptConcepts
     *
     * @param \Ukratio\TrouveToutBundle\Entity\ConceptConcept $moreGeneralConceptConcepts
     * @return ConceptConcept
     *
     */
    public function addMoreGeneralConceptConcept(ConceptConcept $moreGeneralConceptConcept)
    {
        $moreGeneralConceptConcept->setMoreSpecific($this);
        $this->moreGeneralConceptConcepts[] = $moreGeneralConceptConcept;
        return $this;
    }

    /**
     * Remove moreGeneralConcept
     *
     * @param \Ukratio\TrouveToutBundle\Entity\Concept $moreGeneralConcepts
     *
     */
    public function removeMoreGeneralConcept(Concept $moreGeneralConcept)
    {
        $self = $this;
        $conceptConcept = $this->moreGeneralConceptConcepts->filter(
            function($conceptConcept) use ($self, $moreGeneralConcept)
        {
            return ($conceptConcept->getMoreSpecific()->getId() == $self->getId()) and ($conceptConcept->getMoreGeneral()->getId() == $moreGeneralConcept->getId());
        })->first();

        
        $conceptConcept->getMoreGeneral()->removeMoreGeneralConceptConcept($conceptConcept);
        $this->moreGeneralConceptConcepts->removeElement($conceptConcept);
    }

    public function removeMoreGeneralConceptConcept(ConceptConcept $moreGeneralConceptConcept)
    {
        $this->moreGeneralConceptConcepts->removeElement($moreGeneralConceptConcept);
    }

    public function removeMoreSpecificConceptConcept(ConceptConcept $moreSpecificConceptConcept)
    {
        $this->moreSpecificConceptConcepts->removeElement($moreSpecificConceptConcept);
    }

    /**
     * Get moreGeneralConcepts
     *
     * @return \Doctrine\Common\Collections\Collection
     *
     */
    public function getMoreGeneralConcepts()
    {
        return $this->moreGeneralConceptConcepts->map(function (ConceptConcept $x) {return $x->getMoreGeneral();});
    }

    /**
     * Get moreGeneralConceptConcepts
     *
     * @return \Doctrine\Common\Collections\Collection
     *
     */
    public function getMoreGeneralConceptConcepts()
    {
        return $this->moreGeneralConceptConcepts;
    }

    /**
     * Add moreSpecificConcepts
     *
     * @param \Ukratio\TrouveToutBundle\Entity\Concept $moreSpecificConcepts
     * @return Concept
     *
     */
    public function addMoreSpecificConcept(Concept $moreSpecificConcepts)
    {
        $conceptConcept = new ConceptConcept();
        $conceptConcept->linkConcepts($moreSpecificConcepts, $this);

        $this->moreSpecificConceptConcepts[] = $conceptConcept;
        return $this;
    }

    /**
     * Remove moreSpecificConcepts
     *
     * @param \Ukratio\TrouveToutBundle\Entity\Concept $moreSpecificConcepts
     *
     */
    public function removeMoreSpecificConcept(Concept $moreSpecificConcept)
    {
        $self = $this;
        $conceptConcept = $this->moreSpecificConceptConcepts->filter(
            function($conceptConcept) use ($moreSpecificConcept, $self)
        {
            return $conceptConcept->getMoreSpecific() == $moreSpecificConcept and $conceptConcept->getMoreGeneral() == $self;
        })->first();

        $conceptConcept->getMoreSpecific()->removeMoreSpecificConceptConcept($conceptConcept);
        $this->moreSpecificConceptConcepts->removeElement($conceptConcept);
    }

    /**
     * Get moreSpecificConcepts
     *
     * @return \Doctrine\Common\Collections\Collection
     *
     */
    public function getMoreSpecificConcepts()
    {
        return $this->moreSpecificConceptConcepts->map(function (ConceptConcept $x){return $x->getMoreSpecific();});
    }

    /**
     * Add caracts
     *
     * @param \Ukratio\TrouveToutBundle\Entity\Caract $caracts
     * @return Concept
     *
     */
    public function addCaract(Caract $caract)
    {
        $caract->setOwnerConcept($this);
        $this->caracts[] = $caract;
        return $this;
    }

    /**
     * Remove caracts
     *
     * @param \Ukratio\TrouveToutBundle\Entity\Caract $caracts
     */
    public function removeCaract(Caract $caract)
    {
        $caract->setOwnerConcept(null);
        $this->caracts->removeElement($caract);
    }

    /**
     * Get caracts
     *
     * @return \Doctrine\Common\Collections\Collection
     *
     */
    public function getCaracts()
    {
        return $this->caracts;
    }

    public function setCaracts(ArrayCollection $caracts)
    {
        foreach ($caracts as $caract) {
            $caract->setOwnerConcept($this);
        }

        $this->caracts = $caracts;
    }

    public function getCaract($name)
    {
        $caracts = array_filter($this->getCaracts()->toArray(),
                                function ($caract) use ($name)
                                {return $caract->getName() == $name;});

        if (count($caracts) > 1) {
            throw new \RuntimeException("The concept have two caract of name $name, that can’t be possible…");
        } elseif (count($caracts) === 1) {
            return current($caracts);
        } else {
            return null;
        }
    }
}