<?php

namespace Ukratio\TrouveToutBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

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
use Ukratio\TrouveToutBundle\Form\EventListener\AddCaractsOfCategories;
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
        return $conceptFormManager->arrayForTemplate($concept, $form) + array('tutorial' => true);
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

        return $conceptFormManager->arrayForTemplate($concept, $form) + array('tutorial' => true);
	}

    /**
     * @Route("/tutorial/create_category_dishes", name="tutorial_verify_category_dishes")
     * @Method({"POST"})
     * @Template("TrouveToutBundle:Tutorial:createCategoryDishes.html.twig")
     */
	public function verifyCategoryDishesAction(Request $request)
	{
        return $this->verify($request, Discriminator::$Category, $this->getDishesCategoryBegin(), 'tutorial_modify_category_dishes', true);
	}

    /**
     * @Route("/tutorial/modify_category_dishes", name="tutorial_modify_category_dishes")
     * @Method({"GET"})
     * @Template()
     */
	public function modifyCategoryDishesAction()
	{
        $conceptFormManager = $this->get('TrouveTout.ConceptFormManager');

        $concept = $this->getDishesCategoryBegin();

        $subscriber = new AddCaractsOfCategories;
        $subscriber->addCaractsForAllCategories($concept);

        $tutorialCategoryType = $this->get('TrouveTout.tutorial.form.category');

        $form = $this->createForm($tutorialCategoryType, $concept);


        return $conceptFormManager->arrayForTemplate($concept, $form) + array('tutorial' => true);
	}

    /**
     * @Route("/tutorial/modify_category_dishes", name="tutorial_verify_modified_category_dishes")
     * @Method({"POST"})
     * @Template("TrouveToutBundle:Tutorial:modifyCategoryDishes.html.twig")
     */
	public function verifyModifiedCategoryDishesAction(Request $request)
	{
        return $this->verify($request, Discriminator::$Category, $this->getDishesCategoryModified(), 'tutorial_upload_picture', true);
	}

    /**
     * @Route("/tutorial/upload_picture", name="tutorial_upload_picture")
     * @Method({"GET"})
     * @Template()
     */
    public function uploadPictureAction()
    {
        $conceptFormManager = $this->get('TrouveTout.ConceptFormManager');

        $concept = $this->getDishesCategoryModified();
        $tutorialCategoryType = $this->get('TrouveTout.tutorial.form.category');

        $form = $this->createForm($tutorialCategoryType, $concept);


        return $conceptFormManager->arrayForTemplate($concept, $form) + array('tutorial' => true);
    }

    /**
     * @Route("/tutorial/create_category_object", name="tutorial_create_category_object")
     * @Method({"GET"})
     * @Template()
     */
	public function createCategoryObjectAction()
	{
        $conceptFormManager = $this->get('TrouveTout.ConceptFormManager');

        $concept = new Concept();
        $concept->setType(Discriminator::$Category->getName());

        $tutorialCategoryType = $this->get('TrouveTout.tutorial.form.category');

        $form = $this->createForm($tutorialCategoryType, $concept);


        return $conceptFormManager->arrayForTemplate($concept, $form) + array('tutorial' => true);
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
     * @Route("/tutorial/end", name="tutorial_end")
     * @Method({"GET"})
     * @Template()
     */
	public function endAction()
	{
        return array();
	}

    /**
     * @Route("/tutorial/create_category_object", name="tutorial_verify_category_object")
     * @Method({"POST"})
     * @Template("TrouveToutBundle:Tutorial:createCategoryObject.html.twig")
     */
	public function verifyCategoryObjectAction(Request $request)
	{
        return $this->verify($request, Discriminator::$Category, $this->getObjectCategory(), 'tutorial_add_category2', true);
	}

    private function trans($sentence)
    {
        $translator = $this->get('translator');
        return $translator->trans($sentence, array(), 'tutorial');
    }

    private function getObjectCategory()
    {
        $concept = new Concept();
        $concept->setType(Discriminator::$Category->getName());

        $concept->setName($this->trans('tutorial.input.object'));
        $caractPicture = new Caract();
        $caractPicture->setName($this->trans('tutorial.input.picture'));
        $caractPicture->setType(Type::$picture->getName());
        $caractPicture->setValue(new Element('picture'));
        $caractObject = new Caract();
        $caractObject->setName($this->trans('tutorial.input.localization'));
        $caractObject->setType(Type::$object->getName());

        $concept->addCaract($caractPicture);
        $concept->addCaract($caractObject);
        
        return $concept;
    }

    private function getDishesCategoryModified()
    {
        $concept = $this->getDishesCategoryBegin();
        $subscriber = new AddCaractsOfCategories;
        $subscriber->addCaractsForAllCategories($concept);
        
        $matter = new Element($this->trans('tutorial.input.matter'));
        $metal = new Element($this->trans('tutorial.input.metal'));
        $stainless = new Element($this->trans('tutorial.input.stainless'));

        $metal->setMoreGeneral($matter);
        $stainless->setMoreGeneral($metal);

        $composition = new Caract($this->trans('tutorial.input.composition'));
        $composition->setType(Type::$name->getName());
        $composition->setValue($stainless);

        $concept->addCaract($composition);

        return $concept;
    }

    private function getDishesCategoryBegin()
    {
        $concept = new Concept();
        $concept->setType(Discriminator::$Category->getName());

        $concept->setName($this->trans('tutorial.input.dishes'));
        $objectCategory = $this->getObjectCategory();
        $concept->addMoreGeneralConcept($objectCategory);

        return $concept;
    }

    private function verify(Request $request, Discriminator $discriminator, Concept $goodConcept, $route, $withCongratulation)
    {
        $conceptFormManager = $this->get('TrouveTout.ConceptFormManager');
        $concept = new Concept();

        $concept->setType($discriminator->getName());
        
        if ($discriminator === Discriminator::$Category) {
            $conceptType = $this->get('TrouveTout.tutorial.form.category');
        }

        $form = $this->createForm($conceptType, $concept);


        $form->bind($request);

        if ($form->isValid()) {
            $valid = $concept->equals($goodConcept);

            if ($valid) {
                if ($withCongratulation) {
                    return $this->redirect($this->generateUrl('tutorial_congratulation', array('route' => $route)));
                } else {
                    return $this->redirect($this->generateUrl($route));
                }
            } else {
                return $conceptFormManager->arrayForTemplate($concept, $form) + array('error' => 'tutorial.miss') + array('tutorial' => true);
            }
        } else {
            return $conceptFormManager->arrayForTemplate($concept, $form) + array('tutorial' => true);
        }
    }
}
