<?php

namespace Ukratio\TrouveToutBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormFactoryInterface;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;

use Ukratio\TrouveToutBundle\Form\EventListener\AddCaractsOfCategories;
use Ukratio\TrouveToutBundle\Form\EventListener\AddCategories;
use Ukratio\TrouveToutBundle\Entity\Concept;
use Ukratio\TrouveToutBundle\Entity\ConceptRepository;
use Ukratio\TrouveToutBundle\Entity\ElementRepository;
use Ukratio\TrouveToutBundle\Entity\CaractRepository;
use Ukratio\TrouveToutBundle\Entity\Discriminator;
use Ukratio\TrouveToutBundle\Service\ElementManager;

class SetType extends ConceptType
{
    public function __construct(ConceptRepository $conceptRepo, CaractRepository $caractRepo, ElementRepository $elementRepo, ElementManager $elementManager, FormFactoryInterface $formFactory)
    {
        parent::__construct($conceptRepo, $caractRepo, $elementRepo, Discriminator::$Set, $elementManager, $formFactory);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('name', 'text', array('required' => false, 'label' => 'concept.name'))
                ->add('linkable', 'checkbox', array('required' => false, 'label' => 'concept.linkable'))
                ->add('number', 'integer', array('invalid_message' => 'concept.integer.invalid', 'label' => 'concept.number'));


        $builder->addEventSubscriber(new AddCaractsOfCategories($builder->getFormFactory()));
    }

    public function getName()
    {
        return 'TrouveTout_Set';
    }
}
