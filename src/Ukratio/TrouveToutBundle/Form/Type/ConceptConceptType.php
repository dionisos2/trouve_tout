<?php

namespace Ukratio\TrouveToutBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityManager;
use Ukratio\TrouveToutBundle\Form\EventListener\AddCaractsOfCategories;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

class ConceptConceptType extends AbstractType
{
    private $em;
        
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $options = array('label' => ' ',
                         'class' => 'TrouveToutBundle:Concept',
                         'property' => 'name',
                         'query_builder' => function(EntityRepository $er){return $er->QueryBuilderAllCategories();});

        $builder->add('moreGeneral', 'entity', $options);

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ukratio\TrouveToutBundle\Entity\ConceptConcept'
        ));
    }

    public function getName()
    {
        return 'TrouveTout_ConceptConcept';
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $conceptConcept = $form->getData();
        if ($conceptConcept !== null) {
            $category = $conceptConcept->getMoreGeneral();
            if ($category != null) {
                $categoryId = $category->getId();
                $categoryName = $category->getName();
                $view->vars = array_replace($view->vars, array(
                    'categoryName' => $categoryName,
                    'categoryId' => $categoryId,
                ));
            }
        }
    }
}
