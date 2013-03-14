<?php

namespace Eud\TrouveToutBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Eud\TrouveToutBundle\Form\EventListener\AddOwnerElementSubscriber;
use Eud\TrouveToutBundle\Form\EventListener\AddChildElementSubscriber;
use Eud\TrouveToutBundle\Form\EventListener\AddElementSubscriber;

use Eud\TrouveToutBundle\Form\DataTransformer\TrueElementToElementTransformer;
use Eud\TrouveToutBundle\Entity\Element;
use Eud\TrouveToutBundle\Entity\Type;

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
            'data_class' => 'Eud\TrouveToutBundle\Entity\Element',
            'constraintOnValue' => false,
            'allow_modif' => true,
            'typeOfValue' => 'name',
        ));
    }

    public function getName()
    {
        return 'TrouveTout_Element';
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars = array_replace($view->vars, array(
            'allow_modif'          => $options['allow_modif'],
        ));
    }
}
