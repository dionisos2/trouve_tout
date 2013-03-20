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

use Ukratio\TrouveToutBundle\Form\DataTransformer\TrueElementToElementTransformer;
use Ukratio\TrouveToutBundle\Entity\Element;
use Ukratio\TrouveToutBundle\Entity\Type;

use Doctrine\ORM\EntityManager;

class ElementType extends AbstractType
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $builder->addEventSubscriber(new AddElementSubscriber($builder->getFormFactory(), $this->em, Type::getEnumerator($options['typeOfValue'])));

        $builder->addEventSubscriber(new AddOwnerElementSubscriber($builder->getFormFactory(), $this->em));

        $builder->addEventSubscriber(new AddChildElementSubscriber($builder->getFormFactory(), $this->em));

        $builder->addModelTransformer(new TrueElementToElementTransformer($this->em));
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
