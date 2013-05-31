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
use Ukratio\ToolBundle\Form\Type\EnumType;
use Ukratio\TrouveToutBundle\Form\EventListener\AddCategories;
use Ukratio\TrouveToutBundle\Entity\ConceptRepository;
use Ukratio\TrouveToutBundle\Entity\ElementRepository;
use Ukratio\TrouveToutBundle\Entity\CaractRepository;
use Ukratio\TrouveToutBundle\Service\ElementManager;


class ResearchType extends ConceptType
{

    public function __construct(ConceptRepository $conceptRepo, CaractRepository $caractRepo, ElementRepository $elementRepo, ElementManager $elementManager, FormFactoryInterface $formFactory)
    {
        parent::__construct($conceptRepo, $caractRepo, $elementRepo, Discriminator::$Research, $elementManager, $formFactory);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $linkableChoices = array('all', 'linkable', 'unlinkable');
        $linkableChoices = array_combine($linkableChoices, $linkableChoices);
        
        $builder
        ->add('researchedType', new EnumType('Ukratio\TrouveToutBundle\Entity\Discriminator'), array('label' => 'research.type'))
        ->add('name', 'text', array('required' => false, 'label' => 'concept.name'))
        ->add('researchedLinkable', 'choice', array('choices' => $linkableChoices, 'label' => 'research.linkable'))
        ->add('researchedNumber', 'text', array('required' => false, 'label' => 'research.number'))
        ->add('researchedName', 'text', array('required' => false, 'label' => 'research.name'));

    }

    public function getName()
    {
        return 'TrouveTout_Research';
    }
}
