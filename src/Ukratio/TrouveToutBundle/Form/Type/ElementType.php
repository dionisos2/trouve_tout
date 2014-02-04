<?php

namespace Ukratio\TrouveToutBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Ukratio\TrouveToutBundle\Form\EventListener\AddOwnerElementSubscriber;
use Ukratio\TrouveToutBundle\Form\EventListener\AddChildElementSubscriber;
use Ukratio\TrouveToutBundle\Form\EventListener\AddElementSubscriber;

use Ukratio\TrouveToutBundle\Entity\Element;
use Ukratio\TrouveToutBundle\Entity\ElementRepository;
use Ukratio\TrouveToutBundle\Entity\Type;
use Ukratio\TrouveToutBundle\Entity\ConceptRepository;
use Ukratio\TrouveToutBundle\Entity\CaractRepository;
use Ukratio\TrouveToutBundle\Service\CaractTypeManager;

use Doctrine\ORM\EntityManager;

class ElementType extends AbstractType
{
    protected $conceptRepo;
    protected $elementRepo;
    protected $caractTypeManager;

    public function __construct(ConceptRepository $conceptRepo, ElementRepository $elementRepo, CaractTypeManager $caractTypeManager)
    {
        $this->conceptRepo = $conceptRepo;
        $this->elementRepo = $elementRepo;
        $this->caractTypeManager = $caractTypeManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $type = Type::getEnumerator($options['typeOfValue']);

        $builder->addEventSubscriber(new AddElementSubscriber($builder->getFormFactory(), $this->conceptRepo, $this->elementRepo, $type, $this->caractTypeManager));

        $builder->addEventSubscriber(new AddChildElementSubscriber($builder->getFormFactory(), $type, $this->caractTypeManager));

        $builder->addEventSubscriber(new AddOwnerElementSubscriber($builder->getFormFactory()));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ukratio\TrouveToutBundle\Entity\Element',
            'constraintOnValue' => false,
            'typeOfValue' => 'name',
        ));
    }

    public function getName()
    {
        return 'TrouveTout_Element';
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
    }
}
