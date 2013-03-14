<?php
namespace Eud\TrouveToutBundle\Tests\Entity;


use Eud\TrouveToutBundle\Form\DataTransformer\TrueElementToElementTransformer;
use Eud\TrouveToutBundle\Entity\Element;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TrueElementToElementTransformerTest extends WebTestCase
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;
    private $transformer;
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
        $this->transformer = new TrueElementToElementTransformer($this->em);
    }


    public function Transform()
    {
        $element = new Element('element');
        $element_transformed = $this->transformer($element);

        $this->AssertEqual($element, $element_transformed);
    }

    public function testReverseTransform()
    {
        $elements = array();
        $elements['a'] = $this->repo->findOneById(1);
        $elements['b'] = $this->repo->findOneById(2);
        $elements['c'] = $this->repo->findOneById(3);
        $elements['a/a'] = $this->repo->findOneById(4);
        $elements['a/a/a'] = $this->repo->findOneById(5);
        $elements['b/a/a'] = $this->repo->findOneById(6);

        foreach ($elements as $key => $element) {
            $element->initPaths();
            $this->assertEquals($key, implode('/', $element->getPath()));
        }

        $elementsTransformed = array();

        $elementsTransformed['a'] = $elements['a']->softClone();
        $elementsTransformed['a/a'] = $elements['a/a']->softClone();
        $elementsTransformed['a']->setValue('a_altered');
        $elementsTransformed['a/a']->setValue('a_a_altered');
        
        $elementsTransformed['a'] = $this->transformer->reverseTransform($elementsTransformed['a']);

        $elementsTransformed['a/a'] = $this->transformer->reverseTransform($elementsTransformed['a/a']);

        $this->assertEquals(array('a_altered'), $elementsTransformed['a']->getRealPath());
        $this->assertEquals(array('a_a_altered', 'a'), $elementsTransformed['a/a']->getRealPath());
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
    }

}
