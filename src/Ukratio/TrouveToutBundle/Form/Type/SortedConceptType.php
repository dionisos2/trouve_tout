<?php

namespace Ukratio\TrouveToutBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Ukratio\TrouveToutBundle\Entity\ConceptRepository;
use Ukratio\TrouveToutBundle\Entity\Concept;

use Ukratio\ToolBundle\Form\DataTransformer\StringToChoiceOrTextTransformer;

class SortedConceptType extends AbstractType
{

    private $conceptRepo;
        
    public function __construct(ConceptRepository $conceptRepo)
    {
        $this->conceptRepo = $conceptRepo;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $childConcept = $options['childConcept'];

        $findConnection = function (Concept $category) use ($childConcept)
        {
            $connection = 0;
            foreach ($childConcept->getCaracts() as $caract) {
                $categoryCaract = $category->getCaract($caract->getName());
                if ($categoryCaract !== null) {
                    $connection = $connection + $categoryCaract->getSpecificity() * (1 - $connection);
                }
            }

            return $connection;
        };

        $cmp = function (Concept $a, Concept $b) use ($findConnection)
        {
            return $findConnection($a) < $findConnection($b);
        };

        $categories = $this->conceptRepo->findAllCategories();

        usort($categories, $cmp);
        
        $choices = array_map(function(Concept $category){return $category->getName();}, $categories);
        $choices = array_combine($choices, $choices);

        $builder->add('choice', 'choice', array('choices' => $choices, 
                                                'label' => ' '));

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array('options' => array()));

        $resolver->setRequired(array('childConcept'));

        $resolver->setAllowedTypes(array('childConcept' => 'Ukratio\TrouveToutBundle\Entity\Concept',
                                         'options' => 'array'));
    }

    public function getName()
    {
        return 'TrouveTout_SortedConcepts';
    }
}
