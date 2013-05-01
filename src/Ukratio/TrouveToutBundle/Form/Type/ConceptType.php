<?php

namespace Ukratio\TrouveToutBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

use Doctrine\ORM\EntityManager;
use Ukratio\TrouveToutBundle\Entity\Discriminator;
use Doctrine\ORM\EntityRepository;
use Ukratio\TrouveToutBundle\Form\EventListener\AddCaractsOfCategories;
use Ukratio\TrouveToutBundle\Form\EventListener\AddCategories;

abstract class ConceptType extends AbstractType
{

    protected $em;
    protected $discriminator;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->addCaracts($builder);
        $this->addPrototypes($builder);
    }

    public function __construct(EntityManager $em, Discriminator $discriminator = null)
    {
        $this->em = $em;
        $this->discriminator = $discriminator;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ukratio\TrouveToutBundle\Entity\Concept'
        ));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {

        $view->vars['prototype_specify_name'] = $form->getConfig()->getAttribute('prototype_specify_name')->createView($view);
        $view->vars['prototype_specify_number'] = $form->getConfig()->getAttribute('prototype_specify_number')->createView($view);
        $view->vars['prototype_specify_picture'] = $form->getConfig()->getAttribute('prototype_specify_picture')->createView($view);
        $view->vars['prototype_specify_object'] = $form->getConfig()->getAttribute('prototype_specify_object')->createView($view);
        $view->vars['prototype_specify_text'] = $form->getConfig()->getAttribute('prototype_specify_text')->createView($view);

    }

    public function addCaracts(FormBuilderInterface $builder)
    {

        $builder->add('caracts', 'collection', array('type' => 'TrouveTout_Caract',
                                                     'label' => ' ',
                                                     'allow_add' => true,
                                                     'allow_delete' => true,
                                                     'by_reference' => false,
                                                     'options' => array('display_type' => 'edit',
                                                                        'parentType' => $this->getDiscriminator())));
    }

    public function addPrototypes(FormBuilderInterface $builder) 
    {

        $optionsLabel = array('label' => 'element.specify');
        $optionsText = array('choices' => array(),
                             'label' => 'element.specify');

        $optionsChoice = array('choices' => array(),
                               'label' => 'element.specify',
                               'textType' => 'choice');

        $prototype_specify_name = $builder->create('childElement', 'Tool_ChoiceOrText', $optionsText);
        $prototype_specify_number = $builder->create('childElement', 'Tool_ChoiceOrText', $optionsText);
        $prototype_specify_picture = $builder->create('childElement', 'choice', $optionsText);
        $prototype_specify_object = $builder->create('childElement', 'Tool_ChoiceOrText', $optionsChoice);
        $prototype_specify_text = $builder->create('childElement', 'textarea', $optionsLabel);

        $builder->setAttribute('prototype_specify_name', $prototype_specify_name->getForm());
        $builder->setAttribute('prototype_specify_number', $prototype_specify_number->getForm());
        $builder->setAttribute('prototype_specify_picture', $prototype_specify_picture->getForm());
        $builder->setAttribute('prototype_specify_object', $prototype_specify_object->getForm());
        $builder->setAttribute('prototype_specify_text', $prototype_specify_text->getForm());
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
