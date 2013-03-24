<?php

namespace Ukratio\TrouveToutBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityManager;
use Ukratio\TrouveToutBundle\Form\EventListener\AddCaractsOfCategories;
use Doctrine\ORM\EntityRepository;
use Ukratio\TrouveToutBundle\Service\ConceptTypeFunctions;

class SetType extends AbstractType
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

        $builder->add('type', 'text', array('read_only' => true))
                ->add('name', 'text', array('required' => false))
                ->add('linkable', 'checkbox', array('required' => false))
                ->add('number', 'integer', array('invalid_message' => 'concept.integer.invalid'));

        $this->ctf->addCaracts($builder);

        $this->ctf->addCategories($builder);



        $builder->addEventSubscriber(new AddCaractsOfCategories($builder->getFormFactory()));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ukratio\TrouveToutBundle\Entity\Concept'
        ));
    }

    public function getName()
    {
        return 'TrouveTout_Set';
    }
}
