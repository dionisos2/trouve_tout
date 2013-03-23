<?php

namespace Ukratio\TrouveToutBundle\Service;

use Symfony\Component\Form\FormBuilderInterface;
use Ukratio\TrouveToutBundle\Entity\Discriminator;

class ConceptTypeFunctions
{
    public function addCaracts(FormBuilderInterface $builder, Discriminator $parentType = null)
    {
        if ($parentType === null) {
            $parentType = Discriminator::$Set;
        }
        
        $builder->add('caracts', 'collection', array('type' => 'TrouveTout_Caract',
                                                     'label' => ' ',
                                                     'allow_add' => true,
                                                     'allow_delete' => true,
                                                     'by_reference' => false,
                                                     'options' => array('display_type' => 'edit',
                                                                        'parentType' => $parentType)));
    }

    public function addCategories(FormBuilderInterface $builder)
    {
        $options = array('label' => ' ',
        );

        $builder->add('moreGeneralConceptConcepts', 'collection', array('type' => 'TrouveTout_ConceptConcept',
                                                                 'label' => ' ',
                                                                 'allow_add' => true,
                                                                 'allow_delete' => true,
                                                                 'by_reference' => false,
                                                                 'options' => $options));

    }
}