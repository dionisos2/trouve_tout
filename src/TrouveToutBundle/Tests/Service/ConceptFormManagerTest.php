<?php
namespace Eud\TrouveToutBundle\Tests\Service;

use Eud\TrouveToutBundle\Entity\ElementRepository;
use Eud\TrouveToutBundle\Entity\Element;
use Eud\TrouveToutBundle\Entity\Caract;
use Eud\TrouveToutBundle\Entity\Concept;
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
        $this->repo = $this->em->getRepository('TrouveToutBundle:Concept');

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
