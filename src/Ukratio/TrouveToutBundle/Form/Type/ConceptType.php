<?php

namespace Ukratio\TrouveToutBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormFactoryInterface;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

use Ukratio\TrouveToutBundle\Form\EventListener\AddCaractsOfCategories;
use Ukratio\TrouveToutBundle\Form\EventListener\AddCategories;
use Ukratio\TrouveToutBundle\Entity\Discriminator;
use Ukratio\TrouveToutBundle\Entity\Type;
use Ukratio\TrouveToutBundle\Entity\ConceptRepository;
use Ukratio\TrouveToutBundle\Entity\ElementRepository;
use Ukratio\TrouveToutBundle\Entity\CaractRepository;
use Ukratio\TrouveToutBundle\Service\ElementManager;
use Ukratio\TrouveToutBundle\Service\CaractTypeManager;
use Ukratio\TrouveToutBundle\Constant;

use Ukratio\ToolBundle\Service\DataChecking;

abstract class ConceptType extends AbstractType
{
    protected $discriminator;
    protected $conceptRepo;
    protected $caractType;
    protected $caractTypeManager;
    protected $entityManager;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->addCaracts($builder);
        $this->caractTypeManager->addPrototypes($builder);

        $builder->addEventSubscriber(new AddCategories($builder->getFormFactory(), $this->conceptRepo, $this->entityManager));
    }

    public function __construct(ConceptRepository $conceptRepo, Discriminator $discriminator = null,EntityManager $entityManager, CaractType $caractType, CaractTypeManager $caractTypeManager)
    {
        $this->conceptRepo = $conceptRepo;
        $this->discriminator = $discriminator;
        $this->entityManager = $entityManager;
        $this->caractType = $caractType;
        $this->caractTypeManager = $caractTypeManager;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ukratio\TrouveToutBundle\Entity\Concept',
            'csrf_protection'   => false,
        ));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $this->caractTypeManager->buildConceptView($view, $form, $options);
    }

    public function addCaracts(FormBuilderInterface $builder)
    {

        $builder->add('caracts', 'collection', array('type' => $this->caractType,
                                                     'label' => ' ',
                                                     'allow_add' => true,
                                                     'allow_delete' => true,
                                                     'by_reference' => false,
                                                     'options' => array('display_type' => 'edit',
                                                                        'parentType' => $this->getDiscriminator())));
    }

    public function getDiscriminator()
    {
        return $this->discriminator;
    }

    public function setDiscriminator(Discriminator $discriminator)
    {
        $this->discriminator = $discriminator;
        return $this;
    }
}
