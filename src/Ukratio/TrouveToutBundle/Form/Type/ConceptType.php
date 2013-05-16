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
use Ukratio\TrouveToutBundle\Entity\ConceptRepository;
use Ukratio\TrouveToutBundle\Entity\ElementRepository;
use Ukratio\TrouveToutBundle\Entity\CaractRepository;
use Ukratio\TrouveToutBundle\Service\ElementManager;
use Ukratio\TrouveToutBundle\Service\CaractTypeManager;

use Ukratio\ToolBundle\Service\DataChecking;

abstract class ConceptType extends AbstractType
{


    protected $discriminator;
    protected $conceptRepo;
    protected $elementRepo;
    protected $caractRepo;
    protected $elementManager;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->addCaracts($builder);
        $this->addPrototypes($builder);
            
        $builder->addEventSubscriber(new AddCategories($builder->getFormFactory(), $this->conceptRepo));
    }

    public function __construct(ConceptRepository $conceptRepo, CaractRepository $caractRepo, ElementRepository $elementRepo, Discriminator $discriminator = null, ElementManager $elementManager, FormFactoryInterface $formFactory)
    {
        $this->conceptRepo = $conceptRepo;
        $this->elementRepo = $elementRepo;
        $this->caractRepo = $caractRepo;
        $this->discriminator = $discriminator;
        $this->elementManager = $elementManager;
        $this->dataChecking = new DataChecking;
        $this->caractTypeManager = new CaractTypeManager($formFactory, $this->conceptRepo, $this->elementRepo, $this->elementManager);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ukratio\TrouveToutBundle\Entity\Concept',
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

        $builder->add('caracts', 'collection', array('type' => new CaractType($this->conceptRepo, $this->caractRepo, $this->elementRepo, $this->caractTypeManager, $this->dataChecking),
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
                                       'label' => 'element.specify',
                                       'required' => false);

        $optionsTextValue = array('choices' => array(),
                                  'label' => 'element.modify');


        $optionsElement = array('label' => ' ',
                                'read_only' => true,
                                'mapped' => false,
        );

        $prototypeOfChildValueName = $builder->create('__name__', 'Tool_ChoiceOrText', $optionsTextChildValue);
        $prototypeOfChildValueNumber = $builder->create('__name__', 'Tool_ChoiceOrText', $optionsTextChildValue);
        $prototypeOfChildValuePicture = $builder->create('__name__', 'choice', $optionsTextChildValue);
        $prototypeOfChildValueObject = $builder->create('__name__', 'Tool_ChoiceOrText', $optionsTextChildValue);
        $prototypeOfChildValueText = $builder->create('__name__', 'textarea', array('label' => 'element.specify'));

        $prototypeOfValueName = $builder->create('__name__', 'Tool_ChoiceOrText', $optionsTextValue);
        $prototypeOfValueNumber = $builder->create('__name__', 'Tool_ChoiceOrText', $optionsTextValue);
        $prototypeOfValuePicture = $builder->create('__name__', 'choice', $optionsTextValue);
        $prototypeOfValueObject = $builder->create('__name__', 'Tool_ChoiceOrText', $optionsTextValue);
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
