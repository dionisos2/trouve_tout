<?php

namespace Ukratio\TrouveToutBundle\Service;

use Ukratio\TrouveToutBundle\Entity\Discriminator;
use Ukratio\TrouveToutBundle\Entity\Type;
use Ukratio\TrouveToutBundle\Entity\Element;
use Ukratio\TrouveToutBundle\Entity\ElementRepository;
use Ukratio\TrouveToutBundle\Entity\Concept;
use Ukratio\TrouveToutBundle\Entity\ConceptRepository;

use Ukratio\TrouveToutBundle\Constant;

use Ukratio\TrouveToutBundle\Form\EventListener\CaractEventSubscriber;

use Ukratio\ToolBundle\Service\DataChecking;
use Ukratio\ToolBundle\Form\Type\EnumType;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormBuilderInterface;

class PrototypeManager
{
    public function __construct()
    {
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

        $optionsFor = array();

        $optionsFor['value'] = array();
        $optionsFor['value']['name'] = $optionsTextValue;
        $optionsFor['value']['number'] = array('label' => 'element.modify');
        $optionsFor['value']['picture'] = $optionsTextValue;
        $optionsFor['value']['object'] = $optionsTextValue;
        $optionsFor['value']['text'] = array('label' => 'element.modify');
        $optionsFor['value']['date'] = array('label' => 'element.modify', 'input' => 'timestamp', 'widget' => 'single_text', 'required' => true, 'format' => Constant::DATEFORMAT);

        $optionsFor['childValue'] = array();
        $optionsFor['childValue']['name'] = $optionsTextChildValue;
        $optionsFor['childValue']['number'] = array('label' => 'element.modify');
        $optionsFor['childValue']['picture'] = $optionsTextChildValue;
        $optionsFor['childValue']['object'] = $optionsTextChildValue;
        $optionsFor['childValue']['text'] = array('label' => 'element.specify');
        $optionsFor['childValue']['date'] = array('label' => 'element.specify', 'input' => 'timestamp', 'widget' => 'single_text', 'required' => true, 'format' => Constant::DATEFORMAT);

        $prototypeOfChildValue = array();
        $prototypeOfValue = array();

        $prototypeOfOwnerElement = $builder->create('__name__', 'text',  $optionsElement);

        $prototypeOfImprecision = $builder->create('__name__', 'number', array('label' => 'caract.imprecision'));
        $prototypeOfPrefix = $builder->create('__name__', new EnumType('Ukratio\TrouveToutBundle\Entity\Prefix'), array('label' => 'caract.prefix'));
        $prototypeOfUnit = $builder->create('__name__', 'text', array('label' => 'caract.unit', 'required' => false));
        $prototypeOfChoosePicture = $builder->create('__name__', 'file', array('label' => 'caract.choose_picture', 'required' => false));

        foreach(Type::getListOfElement() as $element)
        {
            $prototypeOfValue[$element] = $builder->create('__name__', CaractTypeManager::getFormTypeFor(Type::getEnumerator($element)), $optionsFor['value'][$element]);
            $prototypeOfChildValue[$element] = $builder->create('__name__', CaractTypeManager::getFormTypeFor(Type::getEnumerator($element)), $optionsFor['childValue'][$element]);
            $elementUpper = $element;
            $elementUpper[0] = strtoupper($elementUpper[0]);

            $builder->setAttribute("prototypeOfChildValue$elementUpper", $prototypeOfChildValue["$element"]->getForm());
            $builder->setAttribute("prototypeOfValue$elementUpper", $prototypeOfValue["$element"]->getForm());
        }

        $builder->setAttribute('prototypeOfOwnerElement', $prototypeOfOwnerElement->getForm());
        $builder->setAttribute('prototypeOfImprecision', $prototypeOfImprecision->getForm());
        $builder->setAttribute('prototypeOfPrefix', $prototypeOfPrefix->getForm());
        $builder->setAttribute('prototypeOfUnit', $prototypeOfUnit->getForm());
        $builder->setAttribute('prototypeOfChoosePicture', $prototypeOfChoosePicture->getForm());
    }

    public function addPrototypesToView(FormView $view, FormInterface $form, array $options)
    {
        //TODO maybe some refactorization to do
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
        $view->vars['prototypeOfChoosePicture'] = $form->getConfig()->getAttribute('prototypeOfChoosePicture')->createView($view);
    }
}