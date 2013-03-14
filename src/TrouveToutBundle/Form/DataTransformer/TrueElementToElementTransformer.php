<?php

namespace Eud\TrouveToutBundle\Form\DataTransformer;

use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\DataTransformerInterface;
use Eud\TrouveToutBundle\Entity\Element;
use Doctrine\ORM\EntityManager;

class TrueElementToElementTransformer implements DataTransformerInterface
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * just a identity function
     *
     * @param Element $element
     *
     * @return Element
     */
    public function transform($element)
    {
        if (($element !== null) && (! $element instanceof Element)) {
            throw new \Exception("transform, wrong type: " . get_class($element));
        }

        if ($element === null) {
            $element = new Element('');
        }

        return $element;
    }

    /**
     * return Element with good id
     *
     * @param Element $element
     *
     * @return Element
     */
    public function reverseTransform($element)
    {
        if (! $element instanceof Element) {
            throw new \Exception("reverseTransform, wrong type: " . gettype($element));
        }

        if ($element->getValue() == '') {
            return null;
        }

        $path = $element->getPath();
        $path[0] = $element->getValue();

        $trueElement = $this->em->getRepository('TrouveToutBundle:Element')
                            ->findByPath($path);

        if ($trueElement !== null) {
            return $trueElement;
        } else {//creation
            return $element->softClone();
        }
    }
}
