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
use Ukratio\TrouveToutBundle\Entity\Caract;
use Ukratio\TrouveToutBundle\Entity\Type;
use Ukratio\ToolBundle\Form\Type\ChoiceOrTextType;
use Ukratio\TrouveToutBundle\Entity\Discriminator;

class TutorialController extends ControllerWithTools
{    

    
    /**
     * @Route("/tutorial/{introduction}", requirements={"introduction" = "(introduction||)"}, defaults={"introduction" = "introduction"}, name="tutorial_introduction")
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
     * @Route("/tutorial/add_category2", name="tutorial_add_category2")
     * @Template()
     */
	public function addCategory2Action()
	{
        $conceptFormManager = $this->get('TrouveTout.ConceptFormManager');
        $concept = $this->getObjectCategory();
        $form = $conceptFormManager->createForm($concept);
        return $conceptFormManager->arrayForTemplate($concept, $form);
	}

    /**
     * @Route("/tutorial/create_category_dishes", name="tutorial_create_category_dishes")
     * @Method({"GET"})
     * @Template()
     */
	public function createCategoryDishesAction()
	{
        $conceptFormManager = $this->get('TrouveTout.ConceptFormManager');

        $concept = new Concept();
        $concept->setType(Discriminator::$Category->getName());
        $tutorialCategoryType = $this->get('TrouveTout.tutorial.form.category');
        
        $form = $this->createForm($tutorialCategoryType, $concept);

        return $conceptFormManager->arrayForTemplate($concept, $form);
	}

    /**
     * @Route("/tutorial/create_category_object", name="tutorial_create_category_object")
     * @Method({"GET"})
     * @Template()
     */
	public function createCategoryObjectAction()
	{
        $conceptFormManager = $this->get('TrouveTout.ConceptFormManager');
        return $conceptFormManager->createConcept(Discriminator::$Category);
	}

    /**
     * @Route("/tutorial/congratulation/{route}", name="tutorial_congratulation")
     * @Method({"GET"})
     * @Template()
     */
	public function congratulationAction($route)
	{
        return array('route' => $route);
	}

    /**
     * @Route("/tutorial/create_category_object", name="tutorial_verify_category_object")
     * @Method({"POST"})
     * @Template("TrouveToutBundle:Tutorial:createCategoryObject.html.twig")
     */
	public function verifyCategoryObjectAction(Request $request)
	{
        $conceptFormManager = $this->get('TrouveTout.ConceptFormManager');
        $translator = $this->get('translator');
        $concept = new Concept();

        $concept->setType(Discriminator::$Category->getName());
        $form = $conceptFormManager->createForm($concept);

        $form->bind($request);

        
        if ($form->isValid()) {
            $valid = $concept->equals($this->getObjectCategory());

            if ($valid) {
                return $this->redirect($this->generateUrl('tutorial_congratulation', array('route' => 'tutorial_add_category2')));
            } else {
                return $conceptFormManager->arrayForTemplate($concept, $form) + array('error' => 'tutorial.miss');
            }
        } else {
            return $conceptFormManager->arrayForTemplate($concept, $form);
        }
	}

    private function getObjectCategory()
    {
        $translator = $this->get('translator');

        $concept = new Concept();
        $concept->setType(Discriminator::$Category->getName());

        $concept->setName($translator->trans('object'));
        $caractPicture = new Caract();
        $caractPicture->setName($translator->trans('picture'));
        $caractPicture->setType(Type::$picture->getName());
        $caractPicture->setValue(new Element('picture'));
        $caractObject = new Caract();
        $caractObject->setName($translator->trans('localization'));
        $caractObject->setType(Type::$object->getName());

        $concept->addCaract($caractPicture);
        $concept->addCaract($caractObject);
        
        return $concept;
    }

}
