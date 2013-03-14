<?php

namespace Eud\TrouveToutBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Eud\ToolBundle\Service\AssertData;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Eud\TrouveToutBundle\Entity\Type;


/**
 * Element
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Eud\TrouveToutBundle\Entity\ElementRepository")
 */
class Element
{

    /**
     * Dependance injection for AssertData
     */
    private static $ad = null;

    private $path;
    private $ratio;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="string", length=255)
     * @Assert\Type(type="string")
     */
    private $value;


    /**
     * @var Eud\TrouveToutBundle\Entity\Element
     *
     * @ORM\ManyToOne(targetEntity="Eud\TrouveToutBundle\Entity\Element", inversedBy="moreSpecific", cascade={"persist", "detach", "merge"})
     */
    private $moreGeneral;

    /**
     * @var Eud\TrouveToutBundle\Entity\Element
     *
     * @ORM\OneToMany(targetEntity="Eud\TrouveToutBundle\Entity\Element", mappedBy="moreGeneral")
     */
    private $moreSpecifics;

    public function setRatio($ratio)
    {
        $this->ratio = $ratio;
        return $this;
    }

    public function getRatio()
    {
        return $this->ratio;
    }

    public function __toString()
    {
        $str = 'id: ' . $this->getId() . ' | ';
        $str .= 'value: ' . $this->getValue() . ' | ';
        if ($this->getPath()) {
            $str .= 'path: ' . implode('/', $this->getPath()) . ' | ';
        } else {
            $str .= 'no path | ';
        }
        $str .= 'realPath: ' . implode('/', $this->getRealPath()) . ' | ';

        return $str;
    }

    public function initPaths()
    {
        $this->path = array($this->getValue());

        if ($this->getMoreGeneral() !== null) {
            $this->path = array_merge($this->path, $this->moreGeneral->initPaths());
        }
        
        return $this->path;
    }

    public function getRealPath()
    {
        $path = array($this->getValue());

        if ($this->getMoreGeneral() !== null) {
            $path = array_merge($path, $this->moreGeneral->getRealPath());
        }
        
        return $path;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function __construct($value = '')
    {
        if (static::$ad === null) {
            static::$ad = new AssertData();
        }
        
        $this->moreGeneral = null;
        $this->moreSpecifics = new ArrayCollection();
        $this->value = $value;
        $this->path = null;
        $this->ratio = 1;
    }

    public function softClone()
    {
        $element = new Element();
        $element->setValue($this->getValue());
        if ($this->getMoreGeneral() !== null) {
            $element->setMoreGeneral($this->getMoreGeneral());
        }
        return $element;
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
     * Set value
     *
     * @param string $value
     * @return Element
     */
    public function setValue($value)
    {
        $this->value = (string)$value;
        return $this;
    }

    /**
     * Get value
     *
     * @return string 
     */
    public function getValue()
    {
        return (string)$this->value;
    }


    /**
     * Set standardValue
     *
     * @param string $standardValue
     * @return Element
     */
    public function setStandardValue($standardValue)
    {
        $this->value = (string)round((float)$standardValue * $this->ratio, 5);
        return $this;
    }

    /**
     * Get standardValue
     *
     * @return string 
     */
    public function getStandardValue()
    {
        return (string)round((float)$this->value / $this->ratio, 5);
    }

    /**
     * SetmoreGeneral
     *
     * @param \Eud\TrouveToutBundle\Entity\Element $moreGeneral
     * @return Element
     *
     * @codeCoverageIgnore
     */
    public function setMoreGeneral(\Eud\TrouveToutBundle\Entity\Element $moreGeneral = null)
    {
        $this->moreGeneral = $moreGeneral;
    
        return $this;
    }

    /**
     * Get moreGeneral
     *
     * @return \Eud\TrouveToutBundle\Entity\Element 
     *
     * @codeCoverageIgnore
     */
    public function getMoreGeneral()
    {
        return $this->moreGeneral;
    }

    /**
     * Add moreSpecific
     *
     * @param \Eud\TrouveToutBundle\Entity\Element $moreSpecific
     * @return Element
     *
     * @codeCoverageIgnore
     */
    public function addMoreSpecific(\Eud\TrouveToutBundle\Entity\Element $moreSpecific)
    {
        $this->moreSpecifics[] = $moreSpecifics;
    
        return $this;
    }

    /**
     * Remove moreSpecific
     *
     * @param \Eud\TrouveToutBundle\Entity\Element $moreSpecific
     *
     * @codeCoverageIgnore
     */
    public function removeMoreSpecific(\Eud\TrouveToutBundle\Entity\Element $moreSpecific)
    {
        $this->moreSpecifics->removeElement($moreSpecifics);
    }

    /**
     * Get moreSpecifics
     *
     * @return \Doctrine\Common\Collections\Collection 
     *
     * @codeCoverageIgnore
     */
    public function getMoreSpecifics()
    {
        return $this->moreSpecifics;
    }

}