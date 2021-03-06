<?php

namespace Ukratio\TrouveToutBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormFactoryInterface;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;

use Ukratio\TrouveToutBundle\Form\EventListener\ConceptEventSubscriber;
use Ukratio\TrouveToutBundle\Entity\Concept;
use Ukratio\TrouveToutBundle\Entity\ConceptRepository;
use Ukratio\TrouveToutBundle\Entity\ElementRepository;
use Ukratio\TrouveToutBundle\Entity\CaractRepository;
use Ukratio\TrouveToutBundle\Entity\Discriminator;
use Ukratio\TrouveToutBundle\Service\ElementManager;
use Ukratio\TrouveToutBundle\Service\PrototypeManager;

class SetType extends ConceptType
{

    public function __construct(ConceptEventSubscriber $conceptEventSubscriber, CaractType $caractType, PrototypeManager $prototypeManager)
    {
        parent::__construct($conceptEventSubscriber, $caractType, $prototypeManager, Discriminator::$Set);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('name', 'text', array('required' => false, 'label' => 'concept.name'))
                ->add('linkable', 'checkbox', array('required' => false, 'label' => 'concept.linkable'))
                ->add('number', 'integer', array('invalid_message' => 'concept.integer.invalid', 'label' => 'concept.number'))
                ->add('numberImprecision', 'integer', array('invalid_message' => 'concept.integer.invalid', 'label' => 'concept.number_imprecision', 'required' => false));


        $builder->addEventSubscriber($this->conceptEventSubscriber);
    }

    public function getName()
    {
        return 'TrouveTout_Set';
    }
}
