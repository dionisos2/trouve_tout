<?php

namespace Ukratio\TrouveToutBundle\Research;

use Ukratio\TrouveToutBundle\Entity\Concept;
use Ukratio\TrouveToutBundle\Entity\ConceptRepository;
use Ukratio\TrouveToutBundle\Entity\Discriminator;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Ukratio\TrouveToutBundle\Form\Type\SetType;
use Ukratio\TrouveToutBundle\Form\Type\CategoryType;
use Doctrine\ORM\EntityManager;
use Ukratio\ToolBundle\debug\Message;


class ResearchResults
{
    private $arrayResults;
    private $headers;
    private $conceptLines;
    private $research;
    private $transformsLine;

    public function __construct(Concept $research)
    {
        $this->research = $research;
    }

    public function setArrayResults($arrayResults)
    {
        $this->arrayResults = $arrayResults;
        $this->initialize();
        return $this;
    }

    public function initialize()
    {

        $this->transformsLine = $this->getTransformsLine();
        $this->headers = array_keys($this->transformsLine);
        $this->conceptLines = array();
        foreach($this->arrayResults as $conceptResult) {
            $this->conceptLines[] = $this->getConceptLine($conceptResult);
        }
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function getConceptLines()
    {
        return $this->conceptLines;
    }

    private function getTransformsLine()
    {
        $transformsLine = array('name' => function (Concept $concept)
            {
                return $concept->getName();
            },
                                      'linkable' => function (Concept $concept)
            {
                return $concept->getLinkable();
            }
        );


        if (in_array($this->research->getType(), array('Set','Category'))) {
            $transformsLine['number'] = function(Concept $concept)
            {
                return $concept->getNumber();
            };
        }

        foreach($this->arrayResults as $conceptResult) {
            foreach($conceptResult->getCaracts() as $caract) {
                $name = $caract->getName();
                $transformsLine[$name] = function (Concept $concept) use ($name)
                {
                    $caract = $concept->getCaract($name);
                    if ($caract != null) {
                        $element = $caract->getValue();
                        if ($element != null) {
                            return $element->getValue();
                        } else {
                            return 'o';
                        }
                    } else {
                        return 'X';
                    }
                };
            }
        }

        foreach($this->arrayResults as $conceptResult) {
            $index = 1;
            foreach($conceptResult->getMoreGeneralConcepts() as $category) {
                $name = $category->getName();
                $transformsLine["category_$index"] = function (Concept $concept) use ($name) 
                {
                    $category = $concept->getMoreGeneralConceptByName($name);
                    if ($category != null) {
                        return $category->getName();
                    } else {
                        return "X";
                    }
                };
            }
        }

        return $transformsLine;
    }

    private function getConceptLine(Concept $conceptResult)
    {
        $line = array();
        foreach ($this->transformsLine as $key => $callback) {
            $line[] = $callback($conceptResult);
        }

        $result = array('concept' => $conceptResult,
                        'line' => $line
        );

        return $result;
    }
}