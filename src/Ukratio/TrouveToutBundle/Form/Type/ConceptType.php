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
        $this->caractTypeManager = new CaractTypeManager($formFactory, $this->conceptRepo, $this->elementRepo, $this->elementManager, $this->dataChecking);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ukratio\TrouveToutBundle\Entity\Concept',
        ));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {

        foreach(Type::getListOfElement() as $element)
        {
            $element[0] = strtoupper($element[0]);
            $view->vars["prototypeOfChildValue$element"] = $form->getConfig()->getAttribute("prototypeOfChildValue$element")->createView($view);
            $view->vars["prototypeOfValue$element"] = $form->getConfig()->getAttribute("prototypeOfValue$element")->createView($view);
        }

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

        $prototypeOfChildValue = array();
        $prototypeOfChildValue["Name"] = $builder->create('__name__', 'Tool_ChoiceOrText', $optionsTextChildValue);
        $prototypeOfChildValue["Number"] = $builder->create('__name__', 'Tool_ChoiceOrText', $optionsTextChildValue);
        $prototypeOfChildValue["Picture"] = $builder->create('__name__', 'choice', $optionsTextChildValue);
        $prototypeOfChildValue["Object"] = $builder->create('__name__', 'Tool_ChoiceOrText', $optionsTextChildValue);
        $prototypeOfChildValue["Text"] = $builder->create('__name__', 'textarea', array('label' => 'element.specify'));
        $prototypeOfChildValue["Date"] = $builder->create('__name__', 'datetime', array('label' => 'element.specify', 'input' => 'timestamp', 'widget' => 'single_text', 'required' => false, 'format' => Constant::DATEFORMAT));

        $prototypeOfValue = array();
        $prototypeOfValue["Name"] = $builder->create('__name__', 'Tool_ChoiceOrText', $optionsTextValue);
        $prototypeOfValue["Number"] = $builder->create('__name__', 'Tool_ChoiceOrText', $optionsTextValue);
        $prototypeOfValue["Picture"] = $builder->create('__name__', 'choice', $optionsTextValue);
        $prototypeOfValue["Object"] = $builder->create('__name__', 'Tool_ChoiceOrText', $optionsTextValue);
        $prototypeOfValue["Text"] = $builder->create('__name__', 'textarea', array('label' => 'element.modify'));
        $prototypeOfValue["Date"] = $builder->create('__name__', 'datetime', array('label' => 'element.modify', 'input' => 'timestamp', 'widget' => 'single_text', 'required' => true, 'format' => Constant::DATEFORMAT));

        $prototypeOfOwnerElement = $builder->create('__name__', 'text',  $optionsElement);

        foreach(Type::getListOfElement() as $element)
        {
            $element[0] = strtoupper($element[0]);
            $builder->setAttribute("prototypeOfChildValue$element", $prototypeOfChildValue["$element"]->getForm());
            $builder->setAttribute("prototypeOfValue$element", $prototypeOfValue["$element"]->getForm());
        }

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
