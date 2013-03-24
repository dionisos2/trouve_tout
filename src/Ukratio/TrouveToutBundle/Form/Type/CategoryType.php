<?php

namespace Ukratio\TrouveToutBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityManager;
use Ukratio\TrouveToutBundle\Entity\Discriminator;
use Doctrine\ORM\EntityRepository;
use Ukratio\TrouveToutBundle\Service\ConceptTypeFunctions;

class CategoryType extends AbstractType
{

    private $em;
    private $ctf;
        
    public function __construct(EntityManager $em, ConceptTypeFunctions $ctf)
    {
        $this->em = $em;
        $this->ctf = $ctf;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', array('required' => true));

        $this->ctf->addCaracts($builder, Discriminator::$Category);
        $this->ctf->addCategories($builder);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ukratio\TrouveToutBundle\Entity\Concept'
        ));
    }

    public function getName()
    {
        return 'TrouveTout_Category';
    }
}
