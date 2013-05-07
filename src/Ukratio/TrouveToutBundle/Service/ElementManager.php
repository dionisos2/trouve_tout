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

    public function filesIn($elementPath)
    {
        $finder = new Finder();

        $elementPath = array_reverse($elementPath);
        $imagesPath = $this->rootDir . '/../web/img/' . implode('/', $elementPath);

        $imageFile = new \SplFileInfo($imagesPath);

        if (file_exists($imagesPath)) {
            if (! $imageFile->isDir()) {
                $choices = array();
            } else {
                $finder->in($imagesPath)->depth("== 0");
                $choices = array();
                foreach ($finder as $file) {
                    $choices[] = new Element($file->getBasename());
                }
            }
        } else {
            $choices = array();
        }

        
        return $choices;
    }
}