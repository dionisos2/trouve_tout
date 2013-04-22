<?php

namespace Ukratio\TrouveToutBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Ukratio\TrouveToutBundle\Entity\ConceptRepository;
use Ukratio\TrouveToutBundle\Entity\Concept;
use Ukratio\TrouveToutBundle\Form\DataTransformer\ConceptToStringTransformer;

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
        $categories = $this->getSortedConceptsWithSpecificities($options['childConcept'])['categories'];
        $specificities = $this->getSortedConceptsWithSpecificities($options['childConcept'])['specificities'];

        $choices = array_map(function(Concept $category){return $category->getName();}, $categories);
        $choices_name = array_map(function($choice, $specificity){return $choice . '->' . round($specificity, 3);}, $choices, $specificities);
        $choices = array_combine($choices, $choices_name);

        $builder->add('name', 'choice', array('choices' => $choices,
                                              'label' => ' '));
        
        $builder->addModelTransformer(new ConceptToStringTransformer($categories));
        
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array('options' => array(),
        ));

        $resolver->setRequired(array('childConcept'));

        $resolver->setAllowedTypes(array('childConcept' => array('Ukratio\TrouveToutBundle\Entity\Concept', 'null'),
                                         'options' => 'array'));
    }

    public function getName()
    {
        return 'TrouveTout_SortedConcepts';
    }

    private function getSortedConceptsWithSpecificities(Concept $childConcept = null)
    {
        if ($childConcept !== null) {
            $findConnection = function (Concept $category) use ($childConcept) {
                $connection = 0;
                foreach ($childConcept->getCaracts() as $caract) {
                    $categoryCaract = $category->getCaract($caract->getName());
                    if ($categoryCaract !== null) {
                        $connection = $connection + $categoryCaract->getSpecificity() * (1 - $connection);
                    }
                }
            
                return $connection;
            };

            $cmpSpecificity = function (Concept $a, Concept $b) use ($findConnection)
            {
                if ($findConnection($a) == $findConnection($b)) {
                    return strcmp($a->getName(), $b->getName());
                } else {
                    return $findConnection($a) <= $findConnection($b);
                }
            };
        } else {
            $cmpSpecificity = function (Concept $a, Concept $b)
            {
                return strcmp($a->getName(), $b->getName());
            };
        }


        $categories = $this->conceptRepo->findAllCategories();

        usort($categories, $cmpSpecificity);
        
        return array('categories' => $categories, 
                     'specificities' => array_map($findConnection, $categories));
    }
}