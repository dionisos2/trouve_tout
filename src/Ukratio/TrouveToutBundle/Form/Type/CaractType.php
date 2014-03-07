<?php

namespace Ukratio\TrouveToutBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

use Doctrine\ORM\EntityManager;

use Ukratio\ToolBundle\Service\Enum;
use Ukratio\ToolBundle\Service\DataChecking;
use Ukratio\ToolBundle\Form\Type\EnumType;

use Ukratio\TrouveToutBundle\Entity\Discriminator;
use Ukratio\TrouveToutBundle\Entity\ConceptRepository;
use Ukratio\TrouveToutBundle\Entity\CaractRepository;
use Ukratio\TrouveToutBundle\Entity\ElementRepository;
use Ukratio\TrouveToutBundle\Service\CaractTypeManager;
use Ukratio\TrouveToutBundle\Form\EventListener\CaractEventSubscriber;



class CaractType extends AbstractType
{
    protected $caractTypeManager;

    public function __construct(CaractTypeManager $caractTypeManager)
    {
        $this->caractTypeManager = $caractTypeManager;
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $this->caractTypeManager->buildCaractView($view, $form, $options);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->caractTypeManager->buildCaractForm($builder, $options);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ukratio\TrouveToutBundle\Entity\Caract',
            'display_type' => 'show',
            'parentType' => Discriminator::$Set,
        ))
                 ->setRequired(array('display_type'));
    }

    public function getName()
    {
        return 'TrouveTout_Caract';
    }
}
