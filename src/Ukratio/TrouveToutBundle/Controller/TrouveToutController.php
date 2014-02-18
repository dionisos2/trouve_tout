<?php

namespace Ukratio\TrouveToutBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;

use JMS\SecurityExtraBundle\Annotation\Secure;

use Ukratio\TrouveToutBundle\Entity\Concept;
use Ukratio\TrouveToutBundle\Entity\ConceptConcept;
use Ukratio\TrouveToutBundle\Entity\Element;
use Ukratio\TrouveToutBundle\Service\Tools;

use Ukratio\ToolBundle\Form\Type\ChoiceOrTextType;

class TrouveToutController extends ControllerWithTools
{


    /**
     * @Route("/login", name="login")
     * @Template()
     */
	public function loginAction()
	{
        if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('home'));
        }

        $request = $this->getRequest();
        $session = $request->getSession();

        // On vérifie s'il y a des erreurs d'une précédente soumission du formulaire
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        return array(
            // Valeur du précédent nom d'utilisateur entré par l'internaute
            'last_username' => $session->get(SecurityContext::LAST_USERNAME),
            'error'         => $error,
        );
	}

    /**
     * @Route("/", name="home")
     * @Method({"GET"})
     */
	public function homeAction(Request $request)
	{
        return $this->cachedResponse($request, 'TrouveToutBundle:TrouveTout:home.html.twig');
    }

    /**
     * @Route("/upload", name="upload")
     * @Template()
     * @Secure(roles="ROLE_USER")
     * @Method({"GET"})
     */
	public function uploadAction(Request $request)
	{
        $form = $this->createFormBuilder()
                     ->add('image', 'file', array('label' => 'upload_picture.picture'))
                     ->add('category', 'TrouveTout_ConceptConcept', array('label' => 'upload_picture.category'))
                     ->getForm();

        return array('form' => $form->createView());
	}

    /**
     * @Route("/upload", name="do_upload")
     * @Template("TrouveToutBundle:TrouveTout:upload.html.twig")
     * @Secure(roles="ROLE_USER")
     * @Method({"POST"})
     */
	public function doUploadAction(Request $request)
	{
        $form = $this->createFormBuilder()
                     ->add('image', 'file', array('label' => 'upload_picture.picture'))
                     ->add('category', 'TrouveTout_ConceptConcept', array('label' => 'upload_picture.category'))
                     ->getForm();

        $form->bind($request);
        $image = $form->getData()['image'];
        $category = $form->getData()['category']->getMoreGeneral();

        if (substr($image->getMimeType(), 0, 5) == 'image') {
            $subDir = $category->getName();

            $webPath = $this->get('kernel')->getRootDir() . '/../web/img/';

            $imagePath = Tools::stripAccents('picture/' . $subDir . '/');
            $imageName = Tools::stripAccents($image->getClientOriginalName());

            //TOSEE php problem with accent in start of string
            $image->move($webPath . $imagePath, $imageName);
            return array('form' => $form->createView(),
                         'imageName' => "$imageName",
                         'imagePath' => "$imagePath");
        } else {
            return array('form' => $form->createView(),
                         'invalideType' => $image->getMimeType());
        }
    }

    /**
     * @Route("/tools", name="tools")
     * @Template()
     * @Secure(roles="ROLE_USER")
     * @Method({"GET"})
     */
	public function toolsAction()
	{
        return array();
	}

    /**
     * @Route("/tools_ok/{type}/{number}", requirements={"number" = "\d+", "type" = "elements|researches|specificities"}, name="tools_ok")
     * @Template()
     * @Secure(roles="ROLE_USER")
     * @Method({"GET"})
     * @Cache(smaxage=604800)
     */
	public function toolsOkAction($type, $number)
	{
        return array('type' => $type,
                     'number' => $number);
	}


    /**
     * @Route("/delete_unamed_researches", name="delete_unamed_researches")
     * @Secure(roles="ROLE_USER")
     * @Method({"POST"})
     */
	public function deleteUnamedResearchesAction()
	{
        $number = $this->get('TrouveTout.Tools')->deleteUnamedResearches();
        return $this->redirect($this->generateUrl('tools_ok', array('type' => 'researches',
                                                                    'number' => $number)));
	}

    /**
     * @Route("/delete_orphan_elements", name="delete_orphan_elements")
     * @Secure(roles="ROLE_USER")
     * @Method({"POST"})
     */
	public function deleteOrphanElementsAction()
	{
        $number = $this->get('TrouveTout.Tools')->deleteOrphanElements();
        return $this->redirect($this->generateUrl('tools_ok', array('type' => 'elements',
                                                                    'number' => $number)));
	}

    /**
     * @Route("/compute_specificities", name="compute_specificities")
     * @Secure(roles="ROLE_USER")
     * @Method({"POST"})
     */
	public function computeSpecificitiesAction()
	{
        $number = $this->get('TrouveTout.Tools')->computeSpecificities();
        return $this->redirect($this->generateUrl('tools_ok', array('type' => 'specificities',
                                                                    'number' => $number)));
	}


    /**
     * @Route("/test", name="test")
     * @Template()
     */
	public function testAction(Request $request)
	{
        return array(
            'name' => "plop"
        );
	}

}

