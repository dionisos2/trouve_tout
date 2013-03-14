<?php
namespace Ukratio\TrouveToutBundle\Tests\Entity;

use Ukratio\TrouveToutBundle\Entity\ElementRepository;
use Ukratio\TrouveToutBundle\Entity\Element;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ElementRepositoryTest extends WebTestCase
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;
    
    private $repo;
    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getEntityManager('test')
        ;

        $this->repo = $this->em->getRepository('TrouveToutBundle:Element');
    }

    private function findMoreSpecificsId(Element $element = null)
    {
        $childs = $this->repo->findMoreSpecifics($element);
        return $this->elementsToIds($childs);
    }

    private function elementsToIds($elements)
    {
        return array_map(function (Element $element){ return $element->getId();}, $elements);
    }

    public function testFindHeads()
    {
        $headsId = $this->elementsToIds($this->repo->findHeads());
        $this->assertCount(5, $headsId);
        $this->assertContains(1, $headsId);
        $this->assertContains(2, $headsId);
        $this->assertContains(3, $headsId);
        $this->assertContains(7, $headsId);
        $this->assertContains(8, $headsId);
    }

    public function testFindMoreSpecifics()
    {
        $element1 = $this->repo->findOneById(1);
        $childsId = $this->findMoreSpecificsId($element1);
        $this->assertCount(1, $childsId);
        $this->assertContains(4, $childsId);

        $element2 = $this->repo->findOneById(2);
        $childsId = $this->findMoreSpecificsId($element2);
        $this->assertCount(0, $childsId);

        $element3 = $this->repo->findOneById(3);
        $childsId = $this->findMoreSpecificsId($element3);
        $this->assertCount(0, $childsId);

        $element4 = $this->repo->findOneById(4);
        $childsId = $this->findMoreSpecificsId($element4);
        $this->assertCount(2, $childsId);
        $this->assertContains(5, $childsId);
        $this->assertContains(6, $childsId);

        $element5 = $this->repo->findOneById(5);
        $childsId = $this->findMoreSpecificsId($element5);
        $this->assertCount(0, $childsId);

        $element6 = $this->repo->findOneById(6);
        $childsId = $this->findMoreSpecificsId($element6);
        $this->assertCount(0, $childsId);
    }

    public function testFindByPathComplete()
    {
        $element1 = $this->repo->findByPath(array('a'));
        $this->assertEquals(1, $element1->getId());

        $element2 = $this->repo->findByPath(array('b'));
        $this->assertEquals(2, $element2->getId());

        $element3 = $this->repo->findByPath(array('c'));
        $this->assertEquals(3, $element3->getId());

        $element4 = $this->repo->findByPath(array('a', 'a'));
        $this->assertEquals(4, $element4->getId());

        $element5 = $this->repo->findByPath(array('a', 'a', 'a'));
        $this->assertEquals(5, $element5->getId());

        $element6 = $this->repo->findByPath(array('b', 'a', 'a'));
        $this->assertEquals(6, $element6->getId());
    }

    public function testFindByPathWithInt()
    {
        $element7 = $this->repo->findByPath(array(4));
    }

    public function testFindByPathNotComplete()
    {

        $element5 = $this->repo->findByPath(array('a', 'a', 'a'), false)[0];
        $this->assertEquals(5, $element5->getId());

        $element4and5 = $this->repo->findByPath(array('a', 'a'), false);
        $element4and5 = $this->queryResultToIdArray($element4and5);

        $this->assertCount(2, $element4and5);
        $this->assertContains(4, $element4and5);
        $this->assertContains(5, $element4and5);

        $element1and4and5 = $this->repo->findByPath(array('a'), false);
        $element1and4and5 = $this->queryResultToIdArray($element1and4and5);

        $this->assertCount(3, $element1and4and5);
        $this->assertContains(1, $element1and4and5);
        $this->assertContains(4, $element1and4and5);
        $this->assertContains(5, $element1and4and5);

        $element3 = $this->repo->findByPath(array('c'), false)[0];
        $this->assertEquals(3, $element3->getId());
    }

    private function queryResultToIdArray($queryResult)
    {
        $idArray = array();
        foreach ($queryResult as $result) {
            $idArray[] = $result->getId();
        }
        return $idArray;
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
    }

}
