<?php

namespace Ukratio\TrouveToutBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

use Doctrine\ORM\EntityManager;

use Ukratio\ToolBundle\Service\Enum;
use Ukratio\ToolBundle\Service\DataChecking;
use Ukratio\TrouveToutBundle\Entity\Discriminator;
use Ukratio\TrouveToutBundle\Entity\ConceptRepository;
use Ukratio\ToolBundle\Form\Type\EnumType;
use Ukratio\TrouveToutBundle\Form\EventListener\AddValueSubscriber;
use Ukratio\TrouveToutBundle\Form\EventListener\SpecifyCaractSubscriber;


class CaractType extends AbstractType
{

    private $conceptRepo;
    private $caractRepo;
    private $dc;

    public function __construct(EntityManager $em, ConceptRepository $conceptRepo, DataChecking $dataChecking)
    {
        $this->em = $em;
        $this->dc = $dataChecking;
        $this->conceptRepo = $conceptRepo;
        $this->caractRepo = $em->getRepository('TrouveToutBundle:Caract');
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $caract = $form->getData();
        if (($caract !== null) and ($caract->getValue() !== null)) {
            if ($caract->getType() == 'picture') {
                $image = implode('/', array_reverse($caract->getValue()->getAllValues()));
                $view->vars['image'] = $image;
            }

            if ($caract->getType() == 'object') {
                $objectNames = $caract->getValue()->getAllValues();

                $view->vars['objects'] = array();
                foreach ($objectNames as $objectName) {
                    if ($this->dc->isNumbers($objectName)) {
                        $object = $this->conceptRepo->findOneById($objectName);
                    } else {
                        $object = $this->conceptRepo->findOneByName($objectName);
                    }
                    if ($object != null) {
                        $objectId = $object->getId();
                        $view->vars['objects'][] = array('name' => $objectName,
                                                         'id' => $objectId);
                    }
                }
            }
        }
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('name', 'text');

        $attr = array();

        if ($options['parentType'] === Discriminator::$Set) {
            $builder->add('selected', 'checkbox', array('required' => false,
                                                        'attr' => $attr,
                                                        'label' => 'caract.selected'));
        }
        
        if ($options['parentType'] === Discriminator::$Category) {
            $builder->add('selected', 'checkbox', array('required' => false,
                                                        'attr' => $attr,
                                                        'label' => 'caract.selected'));
            $builder->add('byDefault', 'checkbox', array('required' => false,
                                                         'attr' => $attr,
                                                         'label' => 'caract.byDefault'));
            $builder->add('specificity', null, array('required' => false,
                                                     'read_only' => true,
                                                     'label' => 'caract.specificity'));
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
