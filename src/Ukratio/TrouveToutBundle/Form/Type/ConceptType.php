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

        $view->vars['prototypeOfChildValueName'] = $form->getConfig()->getAttribute('prototypeOfChildValueName')->createView($view);
        $view->vars['prototypeOfChildValueNumber'] = $form->getConfig()->getAttribute('prototypeOfChildValueNumber')->createView($view);
        $view->vars['prototypeOfChildValuePicture'] = $form->getConfig()->getAttribute('prototypeOfChildValuePicture')->createView($view);
        $view->vars['prototypeOfChildValueObject'] = $form->getConfig()->getAttribute('prototypeOfChildValueObject')->createView($view);
        $view->vars['prototypeOfChildValueText'] = $form->getConfig()->getAttribute('prototypeOfChildValueText')->createView($view);

        $view->vars['prototypeOfValueName'] = $form->getConfig()->getAttribute('prototypeOfValueName')->createView($view);
        $view->vars['prototypeOfValueNumber'] = $form->getConfig()->getAttribute('prototypeOfValueNumber')->createView($view);
        $view->vars['prototypeOfValuePicture'] = $form->getConfig()->getAttribute('prototypeOfValuePicture')->createView($view);
        $view->vars['prototypeOfValueObject'] = $form->getConfig()->getAttribute('prototypeOfValueObject')->createView($view);
        $view->vars['prototypeOfValueText'] = $form->getConfig()->getAttribute('prototypeOfValueText')->createView($view);

        $view->vars['prototypeOfOwnerElement'] = $form->getConfig()->getAttribute('prototypeOfOwnerElement')->createView($view);

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
        $optionsTextChildValue = array('choices' => array(),
                                       'label' => 'element.specify');

        $optionsChoiceChildValue = array('choices' => array(),
                                         'label' => 'element.specify',
                                         'textType' => 'choice');

        $optionsTextValue = array('choices' => array(),
                                  'label' => 'element.modify');

        $optionsChoiceValue = array('choices' => array(),
                                         'label' => 'element.modify',
                                         'textType' => 'choice');

        $optionsElement = array('label' => ' ',
                                'read_only' => true,
                                'mapped' => false,
        );

        $prototypeOfChildValueName = $builder->create('__name__', 'Tool_ChoiceOrText', $optionsTextChildValue);
        $prototypeOfChildValueNumber = $builder->create('__name__', 'Tool_ChoiceOrText', $optionsTextChildValue);
        $prototypeOfChildValuePicture = $builder->create('__name__', 'choice', $optionsTextChildValue);
        $prototypeOfChildValueObject = $builder->create('__name__', 'Tool_ChoiceOrText', $optionsChoiceChildValue);
        $prototypeOfChildValueText = $builder->create('__name__', 'textarea', array('label' => 'element.specify'));

        $prototypeOfValueName = $builder->create('__name__', 'Tool_ChoiceOrText', $optionsTextValue);
        $prototypeOfValueNumber = $builder->create('__name__', 'Tool_ChoiceOrText', $optionsTextValue);
        $prototypeOfValuePicture = $builder->create('__name__', 'choice', $optionsTextValue);
        $prototypeOfValueObject = $builder->create('__name__', 'Tool_ChoiceOrText', $optionsChoiceValue);
        $prototypeOfValueText = $builder->create('__name__', 'textarea', array('label' => 'element.modify'));

        $prototypeOfOwnerElement = $builder->create('__name__', 'text',  $optionsElement);

        $builder->setAttribute('prototypeOfChildValueName', $prototypeOfChildValueName->getForm());
        $builder->setAttribute('prototypeOfChildValueNumber', $prototypeOfChildValueNumber->getForm());
        $builder->setAttribute('prototypeOfChildValuePicture', $prototypeOfChildValuePicture->getForm());
        $builder->setAttribute('prototypeOfChildValueObject', $prototypeOfChildValueObject->getForm());
        $builder->setAttribute('prototypeOfChildValueText', $prototypeOfChildValueText->getForm());

        $builder->setAttribute('prototypeOfValueName', $prototypeOfValueName->getForm());
        $builder->setAttribute('prototypeOfValueNumber', $prototypeOfValueNumber->getForm());
        $builder->setAttribute('prototypeOfValuePicture', $prototypeOfValuePicture->getForm());
        $builder->setAttribute('prototypeOfValueObject', $prototypeOfValueObject->getForm());
        $builder->setAttribute('prototypeOfValueText', $prototypeOfValueText->getForm());

        $builder->setAttribute('prototypeOfOwnerElement', $prototypeOfOwnerElement->getForm());
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
