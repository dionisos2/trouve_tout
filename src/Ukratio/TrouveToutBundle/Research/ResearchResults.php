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
    private $columnGroups;

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

    public function getColumnGroups()
    {
        return $this->columnGroups;
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
        $transformsLine = array('research.header.name' => function (Concept $concept)
            {
                return $concept->getName();
            });


        if ($this->research->getResearchedType() == 'Set') {
            $transformsLine['linkable'] = function (Concept $concept)
            {
                if ($concept->getLinkable()) {
                    return 'o';
                } else {
                    return 'X';
                }
            };
        }


        if (in_array($this->research->getResearchedType(), array('Set','Research'))) {
            $transformsLine['number'] = function(Concept $concept)
            {
                return $concept->getNumber();
            };
        }

        $this->columnGroups = array(count($transformsLine));

        foreach($this->arrayResults as $conceptResult) {
            foreach($conceptResult->getCaracts() as $caract) {
                $name = $caract->getName();
                $transformsLine[$name] = function (Concept $concept) use ($name)
                {
                    $caract = $concept->getCaract($name);
                    if ($caract != null) {
                        $type = $caract->getType();
                        $element = $caract->getValue();
                        if ($element != null) {
                            if (in_array($type, array('simple','picture','object'))) {
                                $path = $element->getPath();
                                $path = array_reverse($path);
                                $path = implode('/', $path);
                                return array($path, $type);
                            } else {
                                return array($element->getValue(), $type);
                            }
                        } else {
                            return array('o', $type);
                        }
                    } else {
                        return array('X', 'no type');
                    }
                };
            }
        }
        $this->columnGroups[] = count($transformsLine) - $this->columnGroups[0];

        foreach($this->arrayResults as $conceptResult) {
            foreach($conceptResult->getMoreGeneralConcepts() as $category) {
                $name = $category->getName();
                $transformsLine[$name] = function (Concept $concept) use ($name)
                {
                    $category = $concept->getMoreGeneralConceptByName($name);
                    if ($category != null) {
                        return 'o';
                    } else {
                        return 'X';
                    }
                };
            }
        }
        $this->columnGroups[] = count($transformsLine) - $this->columnGroups[0] - $this->columnGroups[1];


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