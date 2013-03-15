<?php

namespace Ukratio\TrouveToutBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Ukratio\ToolBundle\Form\Type\EnumType;
use Ukratio\TrouveToutBundle\Form\EventListener\AddValueSubscriber;
use Ukratio\TrouveToutBundle\Form\EventListener\SpecifyCaractSubscriber;
use Doctrine\ORM\EntityManager;
use Ukratio\ToolBundle\Service\Enum;
use Ukratio\ToolBundle\Service\DataChecking;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Ukratio\TrouveToutBundle\Entity\Discriminator;

class CaractType extends AbstractType
{

    private $em;
    private $dc;

    public function __construct(EntityManager $em, DataChecking $dataChecking)
    {
        $this->em = $em;
        $this->dc = $dataChecking;
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $caract = $form->getData();
        if (($caract !== null) and ($caract->getValue() !== null)) {
            if ($caract->getType() == 'picture') {
                $image = $caract->getValue()->getValue();
                $view->vars = array_replace($view->vars, array(
                    'image'          => $image,
                ));
            }

            if ($caract->getType() == 'object') {
                $objectName = $caract->getValue()->getValue();

                if ($this->dc->isNumbers($objectName)) {
                    $object = $this->em->getRepository('TrouveToutBundle:Concept')->findOneById($objectName);
                } else {
                    $object = $this->em->getRepository('TrouveToutBundle:Concept')->findOneByName($objectName);
                }
                if ($object != null) {
                    $objectId = $object->getId();
                    $view->vars = array_replace($view->vars, array(
                        'objectName' => $objectName,
                        'objectId' => $objectId,
                    ));
                }
            }
        }
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('name', 'text');
        $builder->add('selected', 'checkbox', array('required' => false));
        
        if ($options['parentType'] === Discriminator::$Category) {
            $builder->add('byDefault', 'checkbox', array('required' => false));
            $builder->add('specificity');
        }

        if ($options['display_type'] == 'show') {
            $builder->add('type', 'text', array('disabled' => true));
        }

        
        if ($options['display_type'] == 'edit') {
            $builder->add('type', new EnumType('Ukratio\TrouveToutBundle\Entity\Type'));
        }

        $builder->addEventSubscriber(new AddValueSubscriber($builder->getFormFactory()));
        $builder->addEventSubscriber(new SpecifyCaractSubscriber($builder->getFormFactory(), $this->em));

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ukratio\TrouveToutBundle\Entity\Caract',
            'display_type' => 'show',
            'parentType' => Discriminator::$Set,
        ))
                 ->setRequired(array('display_type'));
    }

    public function getName()
    {
        return 'TrouveTout_Caract';
    }
}
