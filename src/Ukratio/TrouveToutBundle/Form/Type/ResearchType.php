<?php

namespace Ukratio\TrouveToutBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityManager;
use Ukratio\TrouveToutBundle\Entity\Discriminator;
use Doctrine\ORM\EntityRepository;
use Ukratio\TrouveToutBundle\Service\ConceptTypeFunctions;
use Ukratio\ToolBundle\Form\Type\EnumType;

class ResearchType extends AbstractType
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
        $linkableChoices = array('all', 'linkable', 'unlinkable');
        $linkableChoices = array_combine($linkableChoices, $linkableChoices);
        
        $builder->add('type', 'text', array('disabled' => true))
                ->add('name', 'text', array('required' => false))
                ->add('number', 'integer', array('read_only' => true))
                ->add('researchedLinkable', 'choice', array('choices' => $linkableChoices))
                ->add('researchedType', new EnumType('Ukratio\TrouveToutBundle\Entity\Discriminator'))
                ->add('researchedNumber', 'text', array('required' => false))
                ->add('researchedName', 'text', array('required' => false));

        $this->ctf->addCaracts($builder, Discriminator::$Research);
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
        return 'TrouveTout_Research';
    }
}
