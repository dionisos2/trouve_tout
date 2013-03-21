<?php
namespace Ukratio\TrouveToutBundle\Tests\Service;

use Ukratio\TrouveToutBundle\Entity\ElementRepository;
use Ukratio\TrouveToutBundle\Entity\Element;
use Ukratio\TrouveToutBundle\Entity\Caract;
use Ukratio\TrouveToutBundle\Entity\Concept;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ConceptFormManagerTest extends WebTestCase
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;
    private $repo;
    private $conceptFormManager;

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
        $this->repo = static::$kernel->getContainer()->get('TrouveTout.repository.concept');

        $this->conceptFormManager = static::$kernel->getContainer()->get('TrouveTout.ConceptFormManager');
    }

    public function testCreateForm()
    {
        /* $set1 = $this->loadSet1(); */
        /* $form = $this->conceptFormManager->createForm($set1); */
        
        /* $this->assertEquals(array('a'), $set1->getCaract('caract1')->getValue()->getPath()); */
        /* $this->assertEquals(array('a', 'a'), $set1->getCaract('caract2')->getValue()->getPath()); */
        /* $this->assertEquals(array('a', 'a', 'a'), $set1->getCaract('caract3')->getValue()->getPath()); */
        /* $this->assertEquals(array('c'), $set1->getCaract('caract4')->getValue()->getPath()); */
        /* $this->assertEquals(array('b', 'a', 'a'), $set1->getCaract('caract5')->getValue()->getPath()); */
    }


    private function loadSet1()
    {
        $set1 = $this->repo->findOneById(1);

        return $set1;
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
    }

}
