<?php

namespace Eud\TrouveToutBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Eud\ToolBundle\Service\AssertData;
use Eud\TrouveToutBundle\Constant;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\MaxLength;
use Symfony\Component\Validator\Constraints\Regex;
use Eud\ToolBundle\Validator as MyAssert;
use Eud\TrouveToutBundle\Entity\Prefix;
use Eud\TrouveToutBundle\Entity\Unit;
use Eud\TrouveToutBundle\Entity\Type;


/**
 * Caract
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Eud\TrouveToutBundle\Entity\CaractRepository")
 */
class Caract
{

    /**
     * Dependance injection for AssertData
     */
    private static $ad = null;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var boolean
     *
     * @ORM\Column(name="selected", type="boolean")
     * @Assert\Type(type="bool")
     */
    private $selected;

    /**
     * @var string
     *
     * @ORM\Column(name="unit", type="string", length=255, nullable=true)
     * @MyAssert\TypeEnum(enumName="Eud\TrouveToutBundle\Entity\Unit")
     */
    private $unit;

    /**
     * @var string
     *
     * @ORM\Column(name="prefix", type="string", length=255, nullable=true)
     * @MyAssert\TypeEnum(enumName="Eud\TrouveToutBundle\Entity\Prefix")
     */
    private $prefix;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\Type(type="string")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     * @MyAssert\TypeEnum(enumName="Eud\TrouveToutBundle\Entity\Type")
     */
    private $type;

    /**
     * @var boolean
     *
     * @ORM\Column(name="byDefault", type="boolean")
     * @Assert\Type(type="bool")
     */
    private $byDefault;

    /**
     * @var float
     *
     * @ORM\Column(name="specificity", type="float")
     * @Assert\Type(type="numeric")
     */
    private $specificity;


    /**
     * @var Eud\TrouveToutBundle\Entity\Concept
     *
     * @ORM\ManyToOne(targetEntity="Eud\TrouveToutBundle\Entity\Concept", inversedBy="caracts")
     */
    private $ownerConcept;

    /**
     * @var Eud\TrouveToutBundle\Entity\Element
     *
     * @ORM\ManyToOne(targetEntity="Eud\TrouveToutBundle\Entity\Element", cascade={"persist", "detach", "merge"})
     * @Assert\Valid(traverse=true)
     */
    private $value;


    public function __toString()
    {
        $lineEnd = '<br>';

        $str = 'Caract id: ' . $this->getId() . $lineEnd;
        $str .= 'Name: ' . $this->getName() . $lineEnd;
        if ($this->getValue() !== null) {
            $str .= 'Element: ' . (string)$this->getValue() . $lineEnd;
        } else {
            $str .= 'No element' . $lineEnd;
        }
        return $str;
    }

    public function initPaths()
    {
        if ($this->value !== null) {
            $this->value->initPaths();
        }
    }

    public function __construct()
    {
        if (static::$ad === null) {
            static::$ad = new AssertData();
        }
        
        $this->value = null;
        $this->ownerConcept = null;
        $this->selected = true;
        $this->unit = '∅';
        $this->prefix = '∅';
        $this->type = Type::$name->getName();
        $this->byDefault = true;
        $this->specificity = 0;
        $this->name = Constant::UNDEFINED;
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

    /**
     * Set selected
     *
     * @param boolean $selected
     * @return Caract
     */
    public function setSelected($selected)
    {
        $this->selected = $selected;    
        return $this;
    }

    /**
     * Get selected
     *
     * @return boolean 
     */
    public function getSelected()
    {
        return $this->selected;
    }

    /**
     * Set unit
     *
     * @param string $unit
     * @return Caract
     */
    public function setUnit($unit)
    {
        $this->unit = $unit;
        return $this;
    }

    /**
     * Get unit
     *
     * @return string 
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Caract
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
     * Set byDefault
     *
     * @param boolean $byDefault
     * @return Caract
     */
    public function setByDefault($byDefault)
    {
        $this->byDefault = $byDefault;
    
        return $this;
    }

    /**
     * Get byDefault
     *
     * @return boolean 
     */
    public function getByDefault()
    {
        return $this->byDefault;
    }

    /**
     * Set specificity
     *
     * @param float $specificity
     * @return Caract
     */
    public function setSpecificity($specificity)
    {
        $this->specificity = $specificity;
        return $this;
    }

    /**
     * Get specificity
     *
     * @return float 
     */
    public function getSpecificity()
    {
        return $this->specificity;
    }

    /**
     * Set ownerConcept
     *
     * @param \Eud\TrouveToutBundle\Entity\Concept $ownerConcept
     * @return Caract
     */
    public function setOwnerConcept(\Eud\TrouveToutBundle\Entity\Concept $ownerConcept = null)
    {
        $this->ownerConcept = $ownerConcept;
        return $this;
    }

    /**
     * Get ownerConcept
     *
     * @return \Eud\TrouveToutBundle\Entity\Concept 
     */
    public function getOwnerConcept()
    {
        return $this->ownerConcept;
    }

    /**
     * Set value
     *
     * @param \Eud\TrouveToutBundle\Entity\Element $value
     * @return Caract
     */
    public function setValue(\Eud\TrouveToutBundle\Entity\Element $value = null)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Get value
     *
     * @return \Eud\TrouveToutBundle\Entity\Element 
     */
    public function getValue()
    {
        return $this->value;
    }


    /**
     * Set name
     *
     * @param string $name
     * @return Caract
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

    public function getValueConstraint()
	{
        switch (Type::getEnumerator($this->getType())) {
            case Type::$name:
                return new MaxLength(30);
            case Type::$number:
                return new Regex("/^(\d*|[ni])$/");
            case Type::$picture:
                return new Image();
            case Type::$object:
                return null;
            case Type::$text:
                return null;
            default:
                throw new \Exception('impossible case with type = ' . $this->getType());
        }
	}

    /**
     * Set prefix
     *
     * @param string $prefix
     * @return Caract
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    
        return $this;
    }

    /**
     * Get prefix
     *
     * @return string 
     */
    public function getPrefix()
    {
        return $this->prefix;
    }
}