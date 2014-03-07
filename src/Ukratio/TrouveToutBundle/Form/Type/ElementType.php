<?php

namespace Ukratio\TrouveToutBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Ukratio\TrouveToutBundle\Form\EventListener\ElementEventSubscriber;

use Ukratio\TrouveToutBundle\Entity\Element;
use Ukratio\TrouveToutBundle\Entity\ElementRepository;
use Ukratio\TrouveToutBundle\Entity\Type;
use Ukratio\TrouveToutBundle\Entity\ConceptRepository;
use Ukratio\TrouveToutBundle\Entity\CaractRepository;
use Ukratio\TrouveToutBundle\Service\CaractTypeManager;

use Doctrine\ORM\EntityManager;

class ElementType extends AbstractType
{
    protected $elementEventSubscriber;

    public function __construct(ElementEventSubscriber $elementEventSubscriber)
    {
        $this->elementEventSubscriber = $elementEventSubscriber;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $type = Type::getEnumerator($options['typeOfValue']);
        $this->elementEventSubscriber->setType($type);

        $builder->addEventSubscriber($this->elementEventSubscriber);
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
