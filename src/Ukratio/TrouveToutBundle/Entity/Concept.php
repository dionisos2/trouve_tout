<?php

namespace Ukratio\TrouveToutBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ukratio\ToolBundle\Service;
use Ukratio\ToolBundle\Service\Enum;
use Ukratio\ToolBundle\Service\AssertData;
use Ukratio\ToolBundle\Service\ArrayHandling;
use Doctrine\Common\Collections\ArrayCollection;
use Ukratio\TrouveToutBundle\Constant;
use Ukratio\TrouveToutBundle\Entity\Discriminator;
use Ukratio\TrouveToutBundle\Entity\Element;
use Ukratio\TrouveToutBundle\Entity\Caract;
use Ukratio\TrouveToutBundle\Entity\ConceptConcept;
use Symfony\Component\Validator\Constraints as Assert;
use Ukratio\ToolBundle\Validator as MyAssert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\ExecutionContextInterface;


/**
 * Ukratio\TrouveToutBundle\Entity\Concept
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Ukratio\TrouveToutBundle\Entity\ConceptRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity("name")
 * @Assert\Callback(methods={"validateConcept"})
 */
class Concept
{
    /**
     * Dependance injection for AssertData
     */
    protected static $assertData = null;

    /**
     * Dependance injection for ArrayHandling
     */
    protected static $arrayHandling = null;

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var boolean
     *
     * @ORM\Column(name="linkable", type="boolean")
     * @Assert\Type(type="bool")
     */
    private $linkable;

    /**
     * @var string
     *
     * @ORM\Column(name="researchedLinkable", type="string", length=255, nullable=true)
     * @Assert\Type(type="string")
     */
    private $researchedLinkable;

    /**
     * @var string $type
     *
     * @ORM\Column(name="type", type="string", length=255)
     * @MyAssert\TypeEnum(enumName="Ukratio\TrouveToutBundle\Entity\Discriminator")
     */
    private $type;

    /**
     * @var string $researchedType
     *
     * @ORM\Column(name="researchedType", type="string", length=255, nullable=true)
     * @MyAssert\TypeEnum(enumName="Ukratio\TrouveToutBundle\Entity\Discriminator")
     */
    private $researchedType;

    /**
     * @var string $number
     *
     * @ORM\Column(name="number", type="string", length=16, nullable=true)
     * @Assert\Type(message="auieeua", type="integer")
     */
    private $number;

    /**
     * @var string $researchedNumber
     *
     * @ORM\Column(name="researchedNumber", type="string", length=16, nullable=true)
     * @Assert\Type(type="string")
     */
    private $researchedNumber;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true, unique=true)
     * @Assert\Type(type="string")
     * @Assert\Regex(pattern="/^\d*$/", match=false, message="concept.name.not.number")$
     */
    private $name;

    /**
     * @var string $researchedName
     *
     * @ORM\Column(name="researchedName", type="string", length=255, nullable=true)
     * @Assert\Type(type="string")
     */
    private $researchedName;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Ukratio\TrouveToutBundle\Entity\ConceptConcept", mappedBy="moreSpecific", cascade={"persist", "merge", "remove"}, orphanRemoval=true)
     * @Assert\Valid(traverse=true)
     */
    private $moreGeneralConceptConcepts;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Ukratio\TrouveToutBundle\Entity\ConceptConcept", mappedBy="moreGeneral", cascade={"persist", "merge", "remove"}, orphanRemoval=true)
     * @Assert\Valid(traverse=true)
     */
    private $moreSpecificConceptConcepts;

    /**
     * @var ArrayCollection $caracts
     *
     * @ORM\OneToMany(targetEntity="Ukratio\TrouveToutBundle\Entity\Caract", mappedBy="ownerConcept", cascade={"persist", "detach", "merge"}, orphanRemoval=true)
     * @Assert\Valid(traverse=true)
     */
    private $caracts;


    public function validateConcept(ExecutionContextInterface $context)
    {
        $this->noRecursionOnConcept($context);

        if ($context->getViolations()->count() > 0) {
            return;//moreGeneralConceptUnique can’t work with cycle
        }

        $this->moreGeneralConceptUnique($context);

    }

    public function noRecursionOnConcept(ExecutionContextInterface $context)
    {
        $getChilds = function (Concept $concept)
        {
            return $concept->getMoreGeneralConcepts()->toArray();
        };

        $hasCycles = Static::$arrayHandling->hasTrueCycle(array($this), $getChilds);

        if ($hasCycles) {
            $context->addViolationAt('moreGeneralConceptConcepts', 'You have a cycle in your category', array(), null);
        }

    }

    public function moreGeneralConceptUnique(ExecutionContextInterface $context)
    {
        $concepts = $this->getMoreGeneralConcepts()->toArray();
        $allMoreGeneralConcepts = $this->getAllMoreGeneralConcepts(1);

        foreach($concepts as $key => $concept) {
            $concepts2 = array_slice($concepts, $key + 1); //TOSEE
            foreach($concepts2 as $key2 => $concept2) {
                if($concept == $concept2) {
                    $context->addViolationAt('moreGeneralConceptConcepts', 'You can’t add two time the same category', array(), null);
                }
            }

            if (in_array($concept, $allMoreGeneralConcepts))
            {
                $context->addViolationAt('moreGeneralConceptConcepts', $concept->getName() . ' is already in a more general Category', array(), null);
            }
        }
    }

    private function Names($concepts)
    {
        return array_map(function($x){return $x->getName();}, $concepts);
    }

    public function getAllMoreGeneralConcepts($deph = 0)
    {
        $deph++;
        $getChilds = function (Concept $concept)
        {
            return $concept->getMoreGeneralConcepts()->toArray();
        };
        
        return Static::$arrayHandling->getValuesRecursively(array($this), $getChilds, $deph);
    }

    public function getAllMoreSpecificConcepts($deph = 0)
    {
        $deph++;
        $getChilds = function (Concept $concept)
        {
            return $concept->getMoreSpecificConcepts()->toArray();
        };
        
        return Static::$arrayHandling->getValuesRecursively(array($this), $getChilds, $deph);
    }

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

    /**
     * @ORM\PostLoad
     */
    public function initialize()
    {
        if (static::$assertData === null) {
            static::$assertData = new AssertData();
        }

        if (static::$arrayHandling === null) {
            static::$arrayHandling = new ArrayHandling(static::$assertData);
        }
    }

    public function __construct()
    {
        $this->initialize();

        $this->caracts = new ArrayCollection();
		$this->moreGeneralConceptConcepts = new ArrayCollection();
		$this->moreSpecificConceptConcepts = new ArrayCollection();
        $this->type = Discriminator::$Set->getName();
        $this->number = 1;
        $this->name = null;
        $this->linkable = false;
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

    public function setMoreGeneralConceptConcepts(ArrayCollection $moreGeneralConceptConcepts)
    {
        throw new Exception("ok");
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

    public function getMoreGeneralConceptByName($name)
    {
        $concepts = array_filter($this->getMoreGeneralConcepts()->toArray(),
                                function ($concept) use ($name)
                                {return $concept->getName() == $name;});

        if (count($concepts) > 1) {
            throw new \RuntimeException("The concept have two concept of name $name, that can’t be possible…");
        } elseif (count($concepts) === 1) {
            return current($concepts);
        } else {
            return null;
        }
        
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

    /**
     * Set linkable
     *
     * @param boolean $linkable
     * @return Concept
     */
    public function setLinkable($linkable)
    {
        $this->linkable = $linkable;
    
        return $this;
    }

    /**
     * Get linkable
     *
     * @return boolean 
     */
    public function getLinkable()
    {
        return $this->linkable;
    }

    /**
     * Add moreSpecificConceptConcepts
     *
     * @param \Ukratio\TrouveToutBundle\Entity\ConceptConcept $moreSpecificConceptConcepts
     * @return Concept
     */
    public function addMoreSpecificConceptConcept(\Ukratio\TrouveToutBundle\Entity\ConceptConcept $moreSpecificConceptConcepts)
    {
        $this->moreSpecificConceptConcepts[] = $moreSpecificConceptConcepts;
    
        return $this;
    }

    /**
     * Get moreSpecificConceptConcepts
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMoreSpecificConceptConcepts()
    {
        return $this->moreSpecificConceptConcepts;
    }

    /**
     * Set researchedType
     *
     * @param string $researchedType
     * @return Concept
     */
    public function setResearchedType($researchedType)
    {
        $this->researchedType = $researchedType;
    
        return $this;
    }

    /**
     * Get researchedType
     *
     * @return string 
     */
    public function getResearchedType()
    {
        return $this->researchedType;
    }

    /**
     * Set researchedNumber
     *
     * @param string $researchedNumber
     * @return Concept
     */
    public function setResearchedNumber($researchedNumber)
    {
        $this->researchedNumber = $researchedNumber;
    
        return $this;
    }

    /**
     * Get researchedNumber
     *
     * @return string 
     */
    public function getResearchedNumber()
    {
        return $this->researchedNumber;
    }

    /**
     * Set researchedName
     *
     * @param string $researchedName
     * @return Concept
     */
    public function setResearchedName($researchedName)
    {
        $this->researchedName = $researchedName;
    
        return $this;
    }

    /**
     * Get researchedName
     *
     * @return string 
     */
    public function getResearchedName()
    {
        return $this->researchedName;
    }

    /**
     * Set researchedLinkable
     *
     * @param string $researchedLinkable
     * @return Concept
     */
    public function setResearchedLinkable($researchedLinkable)
    {
        $this->researchedLinkable = $researchedLinkable;
    
        return $this;
    }

    /**
     * Get researchedLinkable
     *
     * @return string 
     */
    public function getResearchedLinkable()
    {
        return $this->researchedLinkable;
    }
}