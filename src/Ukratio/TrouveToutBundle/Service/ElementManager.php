<?php

namespace Ukratio\TrouveToutBundle\Service;

use Ukratio\TrouveToutBundle\Entity\Type;
use Ukratio\TrouveToutBundle\Entity\Element;
use Ukratio\TrouveToutBundle\Entity\Concept;
use Ukratio\TrouveToutBundle\Entity\ConceptRepository;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Finder\Finder;

class ElementManager
{
    private $rootDir;
    private $imageError;

    public function __construct($rootDir)
    {
        $this->rootDir = $rootDir;
        $this->imageError = $this->rootDir . '/../web/img/error.jpeg';
    }

    public function filesIn(Element $element, $isChild)
    {
        $finder = new Finder();

        $values = $element->getAllValues();
        $values = array_reverse($values);

        if (! $isChild) {
            $values = array_slice($values, 0, -1);
        }

        $imagesPath = $this->rootDir . '/../web/img/' . implode('/', $values);

        $imageFile = new \SplFileInfo($imagesPath);

        if (! $imageFile->isDir()) {
            $values = array_slice($values, 0, -1);
            $imagesPath = $this->rootDir . '/../web/img/' . implode('/', $values);
        }
        
        if (file_exists($imagesPath)) {
            $finder->in($imagesPath)->depth("== 0");
        } else {
            $finder = array();
        }


        $choices = array();
        foreach ($finder as $file) {
            $choices[] = new Element($file->getBasename());
        }
        
        return $choices;
    }
}