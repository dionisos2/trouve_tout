<?php
 
namespace Eud\TrouveToutBundle\DataFixtures\ORM;
 
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Eud\TrouveToutBundle\Entity\Concept;
use Eud\TrouveToutBundle\Entity\Element;
use Eud\TrouveToutBundle\Entity\Caract;
use Eud\TrouveToutBundle\Entity\ConceptConcept;
 
class TestDataBase implements FixtureInterface
{
    private $concepts;
    private $elements;
    private $caracts;

    public function load(ObjectManager $manager)
    {
        $this->createConcepts($manager);
        $this->createElements($manager);
        $this->createCaracts($manager);

        $manager->flush();
    }

    private function createConcepts(ObjectManager $manager)
    {
        $names = array('set1' => 'Set', 'set2' => 'Set', 'category1' => 'Category', 'category2' => 'Category', 'category3' => 'Category');

        $this->concepts = array();
        foreach($names as $name => $type)
        {
            $this->concepts[$name] = new Concept();
            $this->concepts[$name]->setName($name);
            $this->concepts[$name]->setType($type);
            
            $manager->persist($this->concepts[$name]);
        }

        $this->concepts['unamed'] = new Concept();
        $manager->persist($this->concepts['unamed']);

        $manager->flush();

        $conceptConcept = new ConceptConcept();
        $conceptConcept->linkConcepts($this->concepts['set1'], $this->concepts['category1']);
        $manager->persist($conceptConcept);

        $conceptConcept = new ConceptConcept();
        $conceptConcept->linkConcepts($this->concepts['set1'], $this->concepts['category2']);
        $manager->persist($conceptConcept);
    }

    private function createElements(ObjectManager $manager)
    {
        $this->elements = array();

        $values = array('a', 'b', 'c', 'a', 'a', 'b', '10', '10 e');

        $this->elements = array();
        foreach($values as $index => $value)
        {
            $this->elements[$value . $index] = new Element();
            $this->elements[$value . $index]->setValue($value);
 
            $manager->persist($this->elements[$value . $index]);
        }

        $this->elements['a3']->setMoreGeneral($this->elements['a0']);
        $this->elements['a4']->setMoreGeneral($this->elements['a3']);
        $this->elements['b5']->setMoreGeneral($this->elements['a3']);
    }

    private function createCaracts(ObjectManager $manager)
    {
        $namesAndValues = array('caract1' => 'a0', 'caract2' => 'a3', 'caract3' => 'a4', 'caract4' => 'c2', 'caract5' => 'b5');

        $this->caracts = array();
        foreach($namesAndValues as $name => $value)
        {
            $this->caracts[$name] = new Caract();
            $this->caracts[$name]->setName($name);
            $this->caracts[$name]->setValue($this->elements[$value]);
            $this->caracts[$name]->setOwnerConcept($this->concepts['set1']);
            $this->caracts[$name]->setSelected(true);
            $this->caracts[$name]->setByDefault(true);
            $this->caracts[$name]->setSpecificity(0);
 
            $manager->persist($this->caracts[$name]);
        }        
        
        $this->caracts['caract4']->setSelected(false);
        $this->caracts['caract5']->setSelected(false);
        $this->caracts['caract4']->setByDefault(false);
        $this->caracts['caract5']->setByDefault(false);
    }
    
}