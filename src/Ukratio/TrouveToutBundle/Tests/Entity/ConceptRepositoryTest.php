<?php
namespace Ukratio\TrouveToutBundle\Tests\Entity;

use Ukratio\TrouveToutBundle\Entity\ConceptRepository;
use Ukratio\TrouveToutBundle\Entity\Concept;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ConceptRepositoryTest extends WebTestCase
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
        if ($this->em == null) {
            throw new Exception('problem with database test');
        }
        $this->repo = $this->em->getRepository('TrouveToutBundle:Concept');
    }

    public function testCount()
    {
        $number = $this->repo->count();
        $this->AssertEquals(6, $number);
    }

    public function testFindMoreGeneralCategories()
    {
        $set1 = $this->repo->findOneById(1);
        $generalCategories = $this->repo->findMoreGeneralCategories($set1);
        $this->AssertCount(2, $generalCategories);
        $this->AssertEquals('category1', $generalCategories[0]->getName());
        $this->AssertEquals('category2', $generalCategories[1]->getName());
    }

    public function testFindAllCategories()
    {
        $categories = $this->repo->findAllCategories();
        $this->AssertCount(3, $categories);
        $this->AssertEquals('category1', $categories[0]->getName());
        $this->AssertEquals('category2', $categories[1]->getName());
        $this->AssertEquals('category3', $categories[2]->getName());
    }

    public function testFindNamedSet()
    {
        $namedSet = $this->repo->findNamedSet();
        $this->AssertCount(2, $namedSet);
        $this->AssertEquals('set1', $namedSet[0]->getName());
        $this->AssertEquals('set2', $namedSet[1]->getName());
    }

    
    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
    }

}
