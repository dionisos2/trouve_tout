<?php

namespace Ukratio\TrouveToutBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use JMS\SecurityExtraBundle\Annotation\Secure;

use Ukratio\TrouveToutBundle\Entity\Concept;
use Ukratio\TrouveToutBundle\Entity\ConceptConcept;
use Ukratio\TrouveToutBundle\Entity\Element;
use Ukratio\TrouveToutBundle\Entity\Type;
use Ukratio\ToolBundle\Form\Type\ChoiceOrTextType;
use Ukratio\TrouveToutBundle\Entity\Discriminator;

class TutorialController extends ControllerWithTools
{    

    
    /**
     * @Route("/tutorial/introduction", name="tutorial_introduction")
     * @Template()
     */
	public function introductionAction()
	{
        return array();
	}

    /**
     * @Route("/tutorial/add_category", name="tutorial_add_category")
     * @Template()
     */
	public function addCategoryAction()
	{
        return array();
	}

    /**
     * @Route("/tutorial/create_category_object", name="create_category_object")
     * @Method({"GET"})
     * @Template()
     */
	public function createCategoryObjectAction()
	{
        $cfc = $this->get('TrouveTout.ConceptFormManager');
        return $cfc->createConcept(Discriminator::$Category);
	}

    /**
     * @Route("/tutorial/create_category_object", name="verify_category_object")
     * @Method({"POST"})
     * @Template("TrouveToutBundle:Tutorial:createCategoryObject.html.twig")
     */
	public function verifyCategoryObjectAction(Request $request)
	{
        $cfc = $this->get('TrouveTout.ConceptFormManager');
        $translator = $this->get('translator');
        $concept = new Concept();

        $concept->setType(Discriminator::$Category->getName());
        $form = $cfc->createForm($concept);

        $form->bind($request);

        
        if ($form->isValid()) {
            $valid = ($translator->trans('object') == $concept->getName());
            $valid = $valid && $this->hasCaract($concept, 'picture', Type::$picture);
            $valid = $valid && $this->hasCaract($concept, 'localization', Type::$object);

            if ($valid) {
                echo 'Bien joué !';
            } else {
                echo 'Raté !';
            }
            return $cfc->arrayForTemplate($concept, $form);
        } else {
            return $cfc->arrayForTemplate($concept, $form);
        }
	}

    private function hasCaract(Concept $concept, $caractName, Type $type = null, $value = null)
    {
        $translator = $this->get('translator');
        $caract = $concept->getCaract($translator->trans($caractName));

        if ($caract === null) {
            return false;
        } else {
            $valid = true;
        }

        if ($type !== null) {
            $valid = $valid && $caract->getType() == $type->getName();
        }

        return $valid;
    }
}
