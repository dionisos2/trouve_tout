<?php

namespace Ukratio\TrouveToutBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityManager;
use Ukratio\TrouveToutBundle\Form\EventListener\AddCaractsOfCategories;
use Ukratio\TrouveToutBundle\Form\EventListener\AddCategories;
use Doctrine\ORM\EntityRepository;
use Ukratio\TrouveToutBundle\Entity\Concept;
use Ukratio\TrouveToutBundle\Entity\Discriminator;

class SetType extends ConceptType
{
    public function __construct(EntityManager $em)
    {
        parent::__construct($em, Discriminator::$Set);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('name', 'text', array('required' => false))
                ->add('linkable', 'checkbox', array('required' => false,))
                ->add('number', 'integer', array('invalid_message' => 'concept.integer.invalid'));


        $builder->addEventSubscriber(new AddCaractsOfCategories($builder->getFormFactory()));
    }

    public function getName()
    {
        return 'TrouveTout_Set';
    }
}
