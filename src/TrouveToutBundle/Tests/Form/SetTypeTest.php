<?php

namespace Eud\TrouveToutBundle\Tests\Form\Type;

use Eud\TrouveToutBundle\Form\Type\SetType;
use Eud\TrouveToutBundle\Form\Type\CaractType;
use Eud\TrouveToutBundle\Form\Type\ElementType;
use Eud\TrouveToutBundle\Entity\Concept;
use Symfony\Component\Form\Tests\Extension\Core\Type\TypeTestCase;

class SetTypeTest extends TypeTestCase
{

    public function setUp()
    {
        /* parent::setUp(); */
        /* $this->factory->addType(new CaractType()); */
        /* $this->factory->addType(new ElementType()); */
    }

    /**
     * @dataProvider getValidTestData
     */
    public function testBindValidData($data)
    {

        /* $type = new SetType(); */
        /* $form = $this->factory->create($type); */

        /* $set = new Concept(); */
        /* $set->setType(Discriminator::$Set); */

        /* $set->fromArray($data); */

        /* $form->bind($data); */

        /* $this->assertTrue($form->isSynchronized()); */
        /* $this->assertEquals($set, $form->getData()); */

        /* $view = $form->createView(); */
        /* $children = $view->children; */

        /* foreach (array_keys($data) as $key) { */
        /*     $this->assertArrayHasKey($key, $children); */
        /* } */
    }

    public function getValidTestData()
    {
        return array(
            array(
                'data' => array(
                    'test' => 'test',
                    'test2' => 'test2',
                ),
            ),
            array(
                'data' => array(),
            ),
            array(
                'data' => array(
                    'test' => null,
                    'test2' => null,
                ),
            ),
        );
    }
}

