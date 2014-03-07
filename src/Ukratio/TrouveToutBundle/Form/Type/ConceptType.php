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

use Ukratio\TrouveToutBundle\Form\EventListener\ConceptEventSubscriber;
use Ukratio\TrouveToutBundle\Entity\Discriminator;
use Ukratio\TrouveToutBundle\Entity\Type;
use Ukratio\TrouveToutBundle\Entity\ConceptRepository;
use Ukratio\TrouveToutBundle\Entity\ElementRepository;
use Ukratio\TrouveToutBundle\Entity\CaractRepository;
use Ukratio\TrouveToutBundle\Service\ElementManager;
use Ukratio\TrouveToutBundle\Service\PrototypeManager;

use Ukratio\TrouveToutBundle\Constant;

use Ukratio\ToolBundle\Service\DataChecking;

abstract class ConceptType extends AbstractType
{
    protected $discriminator;
    protected $caractType;
    protected $prototypeManager;
    protected $conceptEventSubscriber;

    public function __construct(ConceptEventSubscriber $conceptEventSubscriber, CaractType $caractType, PrototypeManager $prototypeManager, Discriminator $discriminator = null)
    {
        $this->discriminator = $discriminator;
        $this->caractType = $caractType;
        $this->prototypeManager = $prototypeManager;
        $this->conceptEventSubscriber = $conceptEventSubscriber;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->addCaracts($builder);
        $this->prototypeManager->addPrototypes($builder);

        $builder->addEventSubscriber($this->conceptEventSubscriber);
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
        $this->prototypeManager->addPrototypesToView($view, $form, $options);
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
