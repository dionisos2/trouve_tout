<?php

namespace Ukratio\TrouveToutBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ukratio\ToolBundle\Service\AssertData;
use Ukratio\TrouveToutBundle\Constant;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;
use Ukratio\ToolBundle\Validator as MyAssert;
use Ukratio\TrouveToutBundle\Entity\Prefix;
use Ukratio\TrouveToutBundle\Entity\Unit;
use Ukratio\TrouveToutBundle\Entity\Type;


/**
 * Caract
 *
 * @ORM\Table(name="TrouveTout_Caract")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="Ukratio\TrouveToutBundle\Entity\CaractRepository")
 */
class Caract
{

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
     * @MyAssert\TypeEnum(enumName="Ukratio\TrouveToutBundle\Entity\Unit")
     */
    private $unit;

    /**
     * @var string
     *
     * @ORM\Column(name="prefix", type="string", length=255, nullable=true)
     * @MyAssert\TypeEnum(enumName="Ukratio\TrouveToutBundle\Entity\Prefix")
     */
    private $prefix;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\Type(type="string")
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     * @MyAssert\TypeEnum(enumName="Ukratio\TrouveToutBundle\Entity\Type")
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
     * @var Ukratio\TrouveToutBundle\Entity\Concept
     *
     * @ORM\ManyToOne(targetEntity="Ukratio\TrouveToutBundle\Entity\Concept", inversedBy="caracts")
     */
    private $ownerConcept;

    /**
     * @var Ukratio\TrouveToutBundle\Entity\Element
     *
     * @ORM\ManyToOne(targetEntity="Ukratio\TrouveToutBundle\Entity\Element", cascade={"persist", "detach", "merge"}, inversedBy="ownerCaracts")
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


    /**
     * @ORM\PostLoad
     */
    public function initialize()
    {
        
    }

    public function __construct($name = null)
    {
        $this->initialize();
        $this->value = null;
        $this->ownerConcept = null;
        $this->unit = '∅';
        $this->prefix = '∅';
        $this->selected = true;
        $this->byDefault = true;
        $this->specificity = 0;
        $this->type = Type::$name->getName();
        if ($name !== null) {
            $this->name = $name;
        } else {
            $this->name = Constant::UNDEFINED;
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
        if ($specificity == null) {
            $this->specificity = 0;
        } else {
            $this->specificity = $specificity;
        }
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
     * @param \Ukratio\TrouveToutBundle\Entity\Concept $ownerConcept
     * @return Caract
     */
    public function setOwnerConcept(\Ukratio\TrouveToutBundle\Entity\Concept $ownerConcept = null)
    {
        $this->ownerConcept = $ownerConcept;
        return $this;
    }

    /**
     * Get ownerConcept
     *
     * @return \Ukratio\TrouveToutBundle\Entity\Concept 
     */
    public function getOwnerConcept()
    {
        return $this->ownerConcept;
    }

    /**
     * Set value
     *
     * @param \Ukratio\TrouveToutBundle\Entity\Element $value
     * @return Caract
     */
    public function setValue(\Ukratio\TrouveToutBundle\Entity\Element $value = null)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Get value
     *
     * @return \Ukratio\TrouveToutBundle\Entity\Element 
     */
    public function getValue()
    {
        return $this->value;
    }

    public function getAllMoreSpecificValues($deph = 0)
    {
        if ($this->value != null) {
            return $this->value->getAllMoreSpecifics($deph);
        } else {
            return null;
        }
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
                return new Length(30);
            case Type::$number:
                return new Regex("/^(\d*|[ni])$/");
            case Type::$picture:
                return new Image();
            case Type::$object:
                return null;
            case Type::$text:
                return null;
            case Type::$date:
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