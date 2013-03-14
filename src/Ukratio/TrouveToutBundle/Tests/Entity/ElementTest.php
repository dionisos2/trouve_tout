<?php
namespace Ukratio\TrouveToutBundle\Tests\Entity;

use Ukratio\TrouveToutBundle\Entity\Element;
use Ukratio\ToolBundle\Tests\ValidatorAwareTestCase;
use Doctrine\Common\Collections\ArrayCollection;

class_alias('Doctrine\Common\Collections\ArrayCollection', 'ArrayCollection');

class ElementTest extends ValidatorAwareTestCase
{

    /**
     * Create an instance of Element
     */
    protected function createInstance()
    {
        $newElement = new Element();
        return $newElement;
    }

    protected function setUp()
    {
        $this->element0 = $this->createInstance();
    }

    protected function tearDown()
    {
        unset($this->element0);
    }

    /**
     * @covers Element::__construct
     */
    public function testElementByDefault()
    {
        $elementByDefault = $this->createInstance();
        $this->assertEquals("", $elementByDefault->getValue());
        $this->assertInstanceOf("ArrayCollection", $elementByDefault->getMoreSpecifics());
        $this->assertEquals(null, $elementByDefault->getMoreGeneral());
    }

    /**
     * @covers Element::getId
     */
    public function testGetId()
    {
        $this->assertNotNull($this->element0);
        $this->element0->getId();
    }

    /**
     * @covers Element::getValue
     * @covers Element::setValue
     */
    public function testGetAndSetValue()
    {
        $this->element0->setValue("value1");
        $this->assertEquals("value1", $this->element0->getValue());
        $this->valide($this->element0);
    }

    /**
     * @covers Element::setValue
     */
    public function testSetValueWithIntConversion()
    {
        $this->element0->setValue(10);
        $this->valide($this->element0);
    }

    /**
     * @covers Element::getMoreGeneral
     * @covers Element::setMoreGeneral
     */
    public function testGetAndSetMoreGeneral()
    {
        $moreGeneral = $this->getMockBuilder('Ukratio\TrouveToutBundle\Entity\Element')->disableOriginalConstructor()->getMock();
        $this->element0->setMoreGeneral($moreGeneral);
        $this->assertEquals($moreGeneral, $this->element0->getMoreGeneral());
    }
}
