<?php

namespace Ukratio\TrouveToutBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormFactoryInterface;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

use Ukratio\TrouveToutBundle\Entity\Discriminator;
use Ukratio\TrouveToutBundle\Service\ConceptTypeFunctions;
use Ukratio\TrouveToutBundle\Form\EventListener\AddCaractsOfCategories;
use Ukratio\TrouveToutBundle\Entity\ConceptRepository;
use Ukratio\TrouveToutBundle\Entity\ElementRepository;
use Ukratio\TrouveToutBundle\Entity\CaractRepository;
use Ukratio\TrouveToutBundle\Service\ElementManager;
use Ukratio\TrouveToutBundle\Service\CaractTypeManager;

class CategoryType extends ConceptType
{

    public function __construct(ConceptRepository $conceptRepo,EntityManager $entityManager, CaractType $caractType, CaractTypeManager $caractTypeManager)
    {
        parent::__construct($conceptRepo, Discriminator::$Category, $entityManager, $caractType, $caractTypeManager);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->add('name', 'text', array('required' => true, 'label' => 'concept.name'));

        $builder->addEventSubscriber(new AddCaractsOfCategories($builder->getFormFactory()));
    }

    public function getName()
    {
        return 'TrouveTout_Category';
    }
}
