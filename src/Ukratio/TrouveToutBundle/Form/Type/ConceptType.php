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
        $this->caractTypeManager->addPrototypes($builder);

        $builder->addEventSubscriber(new AddCategories($builder->getFormFactory(), $this->conceptRepo, $this->entityManager));
    }

    public function __construct(ConceptRepository $conceptRepo, CaractRepository $caractRepo, ElementRepository $elementRepo, Discriminator $discriminator = null, ElementManager $elementManager, FormFactoryInterface $formFactory, EntityManager $entityManager)
    {
        $this->conceptRepo = $conceptRepo;
        $this->entityManager = $entityManager;
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
        $view->vars['prototypeOfImprecision'] = $form->getConfig()->getAttribute('prototypeOfImprecision')->createView($view);
        $view->vars['prototypeOfPrefix'] = $form->getConfig()->getAttribute('prototypeOfPrefix')->createView($view);
        $view->vars['prototypeOfUnit'] = $form->getConfig()->getAttribute('prototypeOfUnit')->createView($view);

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
