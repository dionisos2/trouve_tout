<?php

namespace Ukratio\TrouveToutBundle\Tutorial;

use Symfony\Component\Translation\Translator;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

use Ukratio\TrouveToutBundle\Entity\Discriminator;
use Ukratio\TrouveToutBundle\Entity\Concept;
use Ukratio\TrouveToutBundle\Entity\ConceptRepository;

use Ukratio\ToolBundle\Service\ArrayHandling;


class ConceptTutorialRepository extends ConceptRepository
{
    protected $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    public function findAllCategories()
    {
        $category = new Concept($this->translator->trans('object'));
        $category->setType(Discriminator::$Category->getName());
        return array($category);
    }

    public function findNamedSet()
    {
        return array();
    }

    public function findLinkableSet()
    {
        return array();
    }

    public function findOneByName()
    {
        return null;
    }

    public function findOneById()
    {
        return null;
    }
}
