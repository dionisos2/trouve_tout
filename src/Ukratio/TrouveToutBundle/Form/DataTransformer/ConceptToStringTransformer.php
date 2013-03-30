<?php

namespace Ukratio\TrouveToutBundle\Form\DataTransformer;

use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\DataTransformerInterface;
use Ukratio\TrouveToutBundle\Entity\Concept;
use Doctrine\ORM\EntityManager;

class ConceptToStringTransformer implements DataTransformerInterface
{
    private $concepts;

    public function __construct($concepts)
    {
        $this->concepts = $concepts;
    }

    /**
     *
     * @param Concept $concept
     *
     * @return string
     */
    public function transform($concept)
    {
        if ($concept !== null) {
            return array('name', $concept->getName());
        } else {
            return null;
        }
    }

    /**
     *
     * @param string $name
     *
     * @return Concept
     */
    public function reverseTransform($name)
    {
        $name = $name['name'];
        $concepts = array_filter($this->concepts, function (Concept $concepts) use ($name) {return $concepts->getName() == $name;});

        if (count($concepts) !== 1) {
            throw new \Exception('More than one concept with the same name:' . count($concepts));
        }

        return current($concepts);
    }
}
