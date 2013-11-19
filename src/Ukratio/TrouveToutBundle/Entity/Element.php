<?php

namespace Ukratio\TrouveToutBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ukratio\ToolBundle\Service\AssertData;
use Ukratio\ToolBundle\Service\ArrayHandling;
use Ukratio\ToolBundle\Service\DataChecking;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Ukratio\TrouveToutBundle\Entity\Type;
use Ukratio\TrouveToutBundle\Entity\Caract;


/**
 * Element
 *
 * @ORM\Table(name="TrouveTout_Element")
 * @ORM\Entity(repositoryClass="Ukratio\TrouveToutBundle\Entity\ElementRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Element
{

    /**
     * Dependance injection for AssertData
     */
    protected static $assertData = null;

    /**
     * Dependance injection for ArrayHandling
     */
    protected static $arrayHandling = null;
    protected static $dataChecking = null;

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
     * @Assert\NotBlank()
     */
    private $value;


    /**
     * @var Ukratio\TrouveToutBundle\Entity\Element
     *
     * @ORM\ManyToOne(targetEntity="Ukratio\TrouveToutBundle\Entity\Element", inversedBy="moreSpecifics", cascade={"persist", "detach", "merge"})
     */
    private $moreGeneral;

    /**
     * @var Ukratio\TrouveToutBundle\Entity\Element
     *
     * @ORM\OneToMany(targetEntity="Ukratio\TrouveToutBundle\Entity\Element", mappedBy="moreGeneral")
     */
    private $moreSpecifics;

    /**
     * @var Ukratio\TrouveToutBundle\Entity\Caract
     *
     * @ORM\OneToMany(targetEntity="Ukratio\TrouveToutBundle\Entity\Caract", mappedBy="value")
     */
    private $ownerCaracts;

    public function equals(Element $element)
    {
        return $element->getPath() === $this->getPath();
    }

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
        $str .= 'realPath: ' . implode('/', $this->getPath()) . ' | ';

        return $str;
    }

    public function getPath()
    {
        $path = array($this->getValue());

        if ($this->getMoreGeneral() !== null) {
            $path = array_merge($path, $this->moreGeneral->getPath());
        }

        return $path;
    }


    /**
     * @ORM\PostLoad
     */
    public function initialize()
    {

        if (static::$dataChecking === null) {
            static::$dataChecking = new DataChecking();
        }

        if (static::$assertData === null) {
            static::$assertData = new AssertData();
        }

        if (static::$arrayHandling === null) {
            static::$arrayHandling = new ArrayHandling(static::$assertData);
        }
    }

    public function __construct($value = '')
    {
        $this->initialize();

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
    /* public function setStandardValue($standardValue) */
    /* { */
    /*     if (static::$dataChecking->isFloatOrInt($standardValue, true)) { */
    /*         $this->value = (string)round((float)$standardValue * $this->ratio, 5); */
    /*     } else { */
    /*         $this->setValue($standardValue); */
    /*     } */

    /*     return $this; */
    /* } */

    /**
     * Get standardValue
     *
     * @return string
     */
    /* public function getStandardValue() */
    /* { */
    /*     if (static::$dataChecking->isFloatOrInt($this->value, true)) { */
    /*         return (string)round((float)$this->value / $this->ratio, 5); */
    /*     } else { */
    /*         return $this->getValue(); */
    /*     } */
    /* } */

    /**
     * SetmoreGeneral
     *
     * @param \Ukratio\TrouveToutBundle\Entity\Element $moreGeneral
     * @return Element
     *
     * @codeCoverageIgnore
     */
    public function setMoreGeneral(\Ukratio\TrouveToutBundle\Entity\Element $moreGeneral = null)
    {
        $this->moreGeneral = $moreGeneral;

        return $this;
    }

    /**
     * Get moreGeneral
     *
     * @return \Ukratio\TrouveToutBundle\Entity\Element
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
     * @param \Ukratio\TrouveToutBundle\Entity\Element $moreSpecific
     * @return Element
     *
     * @codeCoverageIgnore
     */
    public function addMoreSpecific(\Ukratio\TrouveToutBundle\Entity\Element $moreSpecific)
    {
        $this->moreSpecifics[] = $moreSpecifics;

        return $this;
    }

    /**
     * Remove moreSpecific
     *
     * @param \Ukratio\TrouveToutBundle\Entity\Element $moreSpecific
     *
     * @codeCoverageIgnore
     */
    public function removeMoreSpecific(\Ukratio\TrouveToutBundle\Entity\Element $moreSpecific)
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

    public function getAllMoreSpecifics($deph = 0)
    {
        $deph++;
        $getChilds = function (Element $element)
        {
            return $element->getMoreSpecifics()->toArray();
        };

        return Static::$arrayHandling->getValuesRecursively(array($this), $getChilds, $deph);
    }


    /**
     * Add ownerCaracts
     *
     * @param \Ukratio\TrouveToutBundle\Entity\Caract $ownerCaracts
     * @return Element
     */
    public function addOwnerCaract(\Ukratio\TrouveToutBundle\Entity\Caract $ownerCaracts)
    {
        $this->ownerCaracts[] = $ownerCaracts;

        return $this;
    }

    /**
     * Remove ownerCaracts
     *
     * @param \Ukratio\TrouveToutBundle\Entity\Caract $ownerCaracts
     */
    public function removeOwnerCaract(\Ukratio\TrouveToutBundle\Entity\Caract $ownerCaracts)
    {
        $this->ownerCaracts->removeElement($ownerCaracts);
    }

    /**
     * Get ownerCaracts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOwnerCaracts()
    {
        return $this->ownerCaracts;
    }
}