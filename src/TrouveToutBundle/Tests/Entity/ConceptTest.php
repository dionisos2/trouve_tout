<?php

namespace Eud\TrouveToutBundle\Tests\Entity;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Eud\TrouveToutBundle\Entity\Concept;
use Eud\TrouveToutBundle\Entity\Caract;
use Doctrine\Common\Collections\ArrayCollection;
use Eud\TrouveToutBundle\Constant;
use Eud\ToolBundle\Tests\ValidatorAwareTestCase;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

class ConceptTest extends ValidatorAwareTestCase
{

    protected function createInstance()
    {
        static $nbr = 10;
        $newConcept = new Concept();
        $newConcept->SetId($nbr);
        $nbr++;
        return $newConcept;
    }

    protected function setUp()
    {
        $this->concept = $this->createInstance();
    }

    /**
     * @covers Concept::getCaract
     */
    public function testGetCaract()
    {
        $concept = $this->createInstance();
        $caract1 = new Caract();
        $caract1->setName('caract1');
        $caract2 = new Caract();
        $caract2->setName('caract2');
        $caract3 = new Caract();
        $caract3->setName('caract3');


        $concept->addCaract($caract1);
        $concept->addCaract($caract2);
        $concept->addCaract($caract3);

        $this->assertEquals($caract1, $concept->getCaract('caract1'));
        $this->assertEquals($caract2, $concept->getCaract('caract2'));
        $this->assertEquals($caract3, $concept->getCaract('caract3'));
        $this->assertEquals(null, $concept->getCaract('unknown'));

    }


    /**
     * @covers Concept::getCaract
     * @expectedException RuntimeException
     */
    public function testGetCaractImpossible()
    {
        $concept = $this->createInstance();
        $caract1 = new Caract();
        $caract1->setName('caract1');
        $caract2 = new Caract();
        $caract2->setName('caract1');

        $concept->addCaract($caract1);
        $concept->addCaract($caract2);
        
        $concept->getCaract('caract1');
    }

    /**
     * @covers Concept::__construct
     */
    public function testConceptByDefault()
    {
        $conceptByDefault = $this->createInstance();
        $this->assertInstanceOf('ArrayCollection', $conceptByDefault->getCaracts());
        $this->assertInstanceOf('ArrayCollection', $conceptByDefault->getMoreGeneralConcepts());
        $this->assertInstanceOf('ArrayCollection', $conceptByDefault->getMoreSpecificConcepts());
        $this->assertEquals('Set', $conceptByDefault->getType());
        $this->assertEquals(1, $conceptByDefault->getNumber());
        $this->assertEquals(null, $conceptByDefault->getName());
    }

    /**
     * @covers Concept::setNumber
     * @covers Concept::getNumber
     */
    public function testSetAndGetNumber()
    {
        $this->concept->setNumber(10);
        $this->assertEquals(10, $this->concept->getNumber());
        /* $this->valide($this->concept); */
    }

    /**
     * @covers Concept::setNumber
     * @covers Concept::getNumber
     */
    public function testSetNumberWithWrongType()
    {
        $this->concept->setNumber(true);
        /* $this->notValide($this->concept, 'number'); */
    }

    /**
     * @covers Concept::setName
     * @covers Concept::getName
     */
    public function testSetAndGetName()
    {
        $this->concept->setName('conceptName');
        $this->assertEquals('conceptName', $this->concept->getName());
        /* $this->valide($this->concept); */
    }

    /**
     * @covers Concept::setName
     * @covers Concept::getName
     */
    public function testSetNameWithWrongType()
    {
        $this->concept->setName(true);
        /* $this->notValide($this->concept, 'name'); */
    }

    /**
     * @covers Concept::getId
     */
    public function testGetId()
    {
        $this->concept->getId();
    }

    /**
     * @covers Concept::setType
     */
    public function testSetTypeWithWrongType()
    {
        $this->concept->setType('unknowType');
        /* $this->notValide($this->concept, 'type'); */
    }

    /**
     * @covers Concept::setType
     * @covers Concept::getType
     */
    public function testSetTypeAndGetTypeWithCategory()
    {
        $this->concept->setType('Category');
        $this->assertEquals('Category', $this->concept->getType());
        /* $this->valide($this->concept); */
    }

    /**
     * @covers Concept::setType
     * @covers Concept::getType
     */
    public function testSetTypeAndGetTypeWithSet()
    {
        $this->concept->setType('Set');
        $this->assertEquals('Set', $this->concept->getType());
        /* $this->valide($this->concept); */
    }

    /**
     * @covers Concept::addMoreGeneralConcept
     * @covers Concept::getMoreGeneralConcepts
     */
    public function testAddMoreGeneralConcept()
    {
        $concept1 = $this->createInstance();
        $concept2 = $this->createInstance();
        $concept3 = $this->createInstance();

        $concept1->addMoreGeneralConcept($concept2);
        $concept1->addMoreGeneralConcept($concept3);

        $mgc = $concept1->getMoreGeneralConcepts();
        $this->assertCount(2, $mgc);
        $this->assertContains($concept2, $mgc);
        $this->assertContains($concept3, $mgc);
    }

    /**
     * @covers Concept::addMoreSpecificConcept
     * @covers Concept::getMoreSpecificConcepts
     */
    public function testAddMoreSpecificConcept()
    {
        $concept1 = $this->createInstance();
        $concept2 = $this->createInstance();
        $concept3 = $this->createInstance();

        $concept1->addMoreSpecificConcept($concept2);
        $concept1->addMoreSpecificConcept($concept3);

        $mgc = $concept1->getMoreSpecificConcepts();
        $this->assertCount(2, $mgc);
        $this->assertContains($concept2, $mgc);
        $this->assertContains($concept3, $mgc);
    }

     /**
     * @covers Concept::removeMoreGeneralConcepts
     * @depends testAddMoreGeneralConcept
     */
    public function testRemoveMoreGeneralConcept()
    {
        $concept1 = $this->createInstance();
        $concept2 = $this->createInstance();
        $concept3 = $this->createInstance();

        $concept1->addMoreGeneralConcept($concept2);
        $concept1->addMoreGeneralConcept($concept3);
        
        $mgc = $concept1->getMoreGeneralConcepts();
        $this->assertCount(2, $mgc);
        $this->assertContains($concept2, $mgc);
        $this->assertContains($concept3, $mgc);

        $concept1->removeMoreGeneralConcept($concept2);
        $mgc = $concept1->getMoreGeneralConcepts();
        $this->assertCount(1, $mgc);
        $this->assertContains($concept3, $mgc);
    }

     /**
     * @covers Concept::removeMoreSpecificConcepts
     * @depends testAddMoreSpecificConcept
     */
    public function testRemoveMoreSpecificConcept()
    {
        $concept1 = $this->createInstance();
        $concept2 = $this->createInstance();
        $concept3 = $this->createInstance();
        $concept4 = $this->createInstance();

        $concept1->addMoreSpecificConcept($concept2);
        $concept1->addMoreSpecificConcept($concept3);
        $concept1->addMoreSpecificConcept($concept4);
        
        $mgc = $concept1->getMoreSpecificConcepts();
        $this->assertCount(3, $mgc);
        $this->assertContains($concept2, $mgc);
        $this->assertContains($concept3, $mgc);
        $this->assertContains($concept4, $mgc);

        $concept1->removeMoreSpecificConcept($concept3);
        $mgc = $concept1->getMoreSpecificConcepts();
        $this->assertCount(2, $mgc);
        $this->assertContains($concept2, $mgc);
        $this->assertContains($concept4, $mgc);
    }

}