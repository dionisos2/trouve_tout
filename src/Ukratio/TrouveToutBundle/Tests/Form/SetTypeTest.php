<?php

namespace Ukratio\TrouveToutBundle\Tests\Form\Type;

use Ukratio\TrouveToutBundle\Form\Type\SetType;
use Ukratio\TrouveToutBundle\Form\Type\CaractType;
use Ukratio\TrouveToutBundle\Form\Type\ElementType;
use Ukratio\TrouveToutBundle\Entity\Concept;
use Symfony\Component\Form\Tests\Extension\Core\Type\TypeTestCase;
use Symfony\Bridge\Doctrine\Tests\DoctrineOrmTestCase;

/* class SetTypeTest extends TypeTestCase */
/* { */

/*     private $em; */

/*     public function setUp() */
/*     { */
/*         /\* $this->em = DoctrineOrmTestCase::createTestEntityManager(); *\/ */

/*         /\* parent::setUp(); *\/ */

/*         /\* $this->factory->addType(new CaractType($this->em)); *\/ */
/*         /\* $this->factory->addType(new ElementType($this->em)); *\/ */
/*     } */

/*     /\** */
/*      * @dataProvider getValidTestData */
/*      *\/ */
/*     public function testBindValidData($data) */
/*     { */

/*         $type = new SetType($this->em); */
/*         $form = $this->factory->create($type); */

/*         $set = new Concept(); */
/*         $set->setType(Discriminator::$Set); */

/*         $set->fromArray($data); */

/*         $form->bind($data); */

/*         $this->assertTrue($form->isSynchronized()); */
/*         $this->assertEquals($set, $form->getData()); */

/*         $view = $form->createView(); */
/*         $children = $view->children; */

/*         foreach (array_keys($data) as $key) { */
/*             $this->assertArrayHasKey($key, $children); */
/*         } */
/*     } */

/*     public function getValidTestData() */
/*     { */
/*         return array( */
/*             array( */
/*                 'data' => array( */
/*                     'test' => 'test', */
/*                     'test2' => 'test2', */
/*                 ), */
/*             ), */
/*             array( */
/*                 'data' => array(), */
/*             ), */
/*             array( */
/*                 'data' => array( */
/*                     'test' => null, */
/*                     'test2' => null, */
/*                 ), */
/*             ), */
/*         ); */
/*     } */
/* } */

