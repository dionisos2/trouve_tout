<?php
namespace Ukratio\TrouveToutBundle\Tests\Entity;

use Ukratio\TrouveToutBundle\Entity\Caract;
use Ukratio\TrouveToutBundle\Entity\Type;
use Ukratio\TrouveToutBundle\Constant;
use Ukratio\ToolBundle\Tests\ValidatorAwareTestCase;


class CaractTest extends ValidatorAwareTestCase
{

    protected function createInstance()
    {
        $newCaract = new Caract();
        return $newCaract;
    }

    protected function setUp()
    {
        $this->caract0 = $this->createInstance();
    }

    protected function tearDown()
    {
        unset($this->caract0);
    }



    /**
     * @covers Caract::__construct
     */
    public function testCaractByDefault()
    {
        $caractByDefault = $this->createInstance();
        $this->assertEquals(null, $caractByDefault->getOwnerConcept());
        $this->assertEquals(null, $caractByDefault->getValue());
        $this->assertEquals(true, $caractByDefault->getSelected());
        $this->assertEquals('∅', $caractByDefault->getUnit());
        $this->assertEquals(Type::$name->getName(), $caractByDefault->getType());
        $this->assertEquals(true, $caractByDefault->getByDefault());
        $this->assertEquals(0, $caractByDefault->getSpecificity());
        $this->assertEquals(Constant::UNDEFINED, $caractByDefault->getName());
        $this->valide($caractByDefault);
    }

    /**
     * @covers Caract::getId
     */
    public function testGetId()
    {
        $this->caract0->getId();
    }

    /**
     * @covers Caract::getName
     * @covers Caract::setName
     */
    public function testGetAndSetName()
    {
        $this->caract0->setName('oneName');
        $this->assertEquals('oneName', $this->caract0->getName());
        $this->caract0->setName('anOtherName');
        $this->assertEquals('anOtherName', $this->caract0->getName());
        $this->valide($this->caract0);
    }

    /**
     * @covers Caract::setName
     */
    public function testSetNameWithWrongType()
    {
        $this->caract0->setName(10);
        $this->notValide($this->caract0, 'name');
    }

    /**
     * @covers Caract::getSelected
     * @covers Caract::setSelected
     */
    public function testGetAndSetSelected()
    {
        $this->caract0->setSelected(true);
        $this->assertEquals(true, $this->caract0->getSelected());
        $this->caract0->setSelected(false);
        $this->assertEquals(false, $this->caract0->getSelected());
        $this->valide($this->caract0);
    }

    /**
     * @covers Caract::setSelected
     */
    public function testSetSelectedWithWrongType()
    {
        $this->caract0->setSelected(10);
        $this->notValide($this->caract0, 'selected');
    }

    /**
     * @covers Caract::getUnit
     * @covers Caract::setUnit
     */
    public function testGetAndSetUnit()
    {
        $this->caract0->setUnit("plop");
        $this->assertEquals('mètre', $this->caract0->getUnit());
        $this->caract0->setUnit("plop");
        $this->assertEquals('∅', $this->caract0->getUnit());
        $this->valide($this->caract0);
    }

    /**
     * @covers Caract::setUnit
     */
    public function testSetUnitWithWrongType()
    {
        $this->caract0->setUnit(10);
        $this->notValide($this->caract0, 'unit');
    }

    /**
     * @covers Caract::getType
     * @covers Caract::setType
     */
    public function testGetAndSetType()
    {
        $this->caract0->setType('name');
        $this->assertEquals('name', $this->caract0->getType());
        $this->valide($this->caract0);
        $this->caract0->setType('number');
        $this->assertEquals('number', $this->caract0->getType());
        $this->valide($this->caract0);
        $this->caract0->setType('picture');
        $this->assertEquals('picture', $this->caract0->getType());
        $this->valide($this->caract0);
        $this->caract0->setType('object');
        $this->assertEquals('object', $this->caract0->getType());
        $this->valide($this->caract0);
        $this->caract0->setType('text');
        $this->assertEquals('text', $this->caract0->getType());
        $this->valide($this->caract0);
    }


    /**
     * @covers Caract::setType
     */
    public function testSetTypeWithWrongType()
    {
        $this->caract0->setType(10);
        $this->notValide($this->caract0, 'type');
    }

    /**
     * @covers Caract::setType
     */
    public function testSetTypeWithWrongDomain()
    {
        $this->caract0->setType('unknowType');
        $this->notValide($this->caract0, 'type');
    }

    /**
     * @covers Caract::getByDefault
     * @covers Caract::setByDefault
     */
    public function testGetAndSetByDefault()
    {
        $this->caract0->setByDefault(true);
        $this->assertEquals(true, $this->caract0->getByDefault());
        $this->caract0->setByDefault(false);
        $this->assertEquals(false, $this->caract0->getByDefault());
        $this->valide($this->caract0);
    }


    /**
     * @covers Caract::setByDefault
     */
    public function testSetByDefaultWithWrongType()
    {
        $this->caract0->setByDefault(10);
        $this->notValide($this->caract0, 'byDefault');
    }

    /**
     * @covers Caract::getSpecificity
     * @covers Caract::setSpecificity
     */
    public function testGetAndSetSpecificity()
    {
        $this->caract0->setSpecificity(0.5);
        $this->assertEquals(0.5, $this->caract0->getSpecificity());
        $this->caract0->setSpecificity(1);
        $this->assertEquals(1, $this->caract0->getSpecificity());
        $this->valide($this->caract0);
    }

    /**
     * @covers Caract::setSpecificity
     */
    public function testSetSpecificityWithWrongType()
    {
        $this->caract0->setSpecificity(true);
        $this->notValide($this->caract0, 'specificity');
    }

    /**
     * @covers Caract::getOwnerConcept
     * @covers Caract::setOwnerConcept
     */
    public function testGetAndSetOwnerConcept()
    {
        $ownerConcept = $this->getMockBuilder('Ukratio\TrouveToutBundle\Entity\Concept')->disableOriginalConstructor()->getMock();
        $this->caract0->setOwnerConcept($ownerConcept);
        $this->assertEquals($ownerConcept, $this->caract0->getOwnerConcept());
    }

    /**
     * @covers Caract::setOwnerConcept
     * @expectedException PHPUnit_Framework_Error
     */
    public function testSetOwnerConceptWithWrongType()
    {
        $this->caract0->setOwnerConcept(true);
    }

    /**
     * @covers Caract::getValue
     * @covers Caract::setValue
     */
    public function testGetAndSetValue()
    {
        $value = $this->getMockBuilder('Ukratio\TrouveToutBundle\Entity\Element')->disableOriginalConstructor()->getMock();
        $this->caract0->setValue($value);
        $this->assertEquals($value, $this->caract0->getValue());
    }

    /**
     * @covers Caract::setValue
     * @expectedException PHPUnit_Framework_Error
     */
    public function testValueWithWrongType()
    {
        $this->caract0->setValue(true);
    }
}

