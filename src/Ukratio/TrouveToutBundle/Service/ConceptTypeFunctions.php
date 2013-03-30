<?php

namespace Ukratio\TrouveToutBundle\Service;

use Symfony\Component\Form\FormBuilderInterface;

use Ukratio\TrouveToutBundle\Entity\Discriminator;
use Ukratio\TrouveToutBundle\Entity\Concept;

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

}