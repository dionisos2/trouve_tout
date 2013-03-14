<?php

namespace Eud\TrouveToutBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityManager;
use Eud\TrouveToutBundle\Form\EventListener\AddCaractsOfCategories;
use Doctrine\ORM\EntityRepository;

class SetType extends AbstractType
{
    private $em;
        
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('type', 'text', array('disabled' => true))
                ->add('name', 'text', array('required' => false))
                ->add('number', 'integer')
                ->add('caracts', 'collection', array('type' => 'TrouveTout_Caract',
                                                     'label' => ' ',
                                                     'allow_add' => true,
                                                     'allow_delete' => true,
                                                     'by_reference' => false,
                                                     'options' => array('display_type' => 'edit')));

        $options = array('label' => ' ',
        );

        $builder->add('moreGeneralConceptConcepts', 'collection', array('type' => 'TrouveTout_ConceptConcept',
                                                                 'label' => ' ',
                                                                 'allow_add' => true,
                                                                 'allow_delete' => true,
                                                                 'by_reference' => false,
                                                                 'options' => $options));

        $builder->addEventSubscriber(new AddCaractsOfCategories($builder->getFormFactory(), $this->em));
        /* $builder->addEventSubscriber(new FlushBeforeBindData($builder->getFormFactory(), $this->em)); */

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Eud\TrouveToutBundle\Entity\Concept'
        ));
    }

    public function getName()
    {
        return 'TrouveTout_Set';
    }
}
