<?php

namespace Ukratio\TrouveToutBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Ukratio\TrouveToutBundle\Entity\Concept;
use Ukratio\TrouveToutBundle\Entity\ConceptConcept;
use Ukratio\TrouveToutBundle\Entity\Element;
use Ukratio\ToolBundle\Form\Type\ChoiceOrTextType;

class TrouveToutController extends ControllerWithTools
{    

    /**
     * @Route("/", name="home")
     * @Template()
     */
	public function homeAction()
	{
        return array();
	}

    /**
     * @Route("/upload", name="upload")
     * @Template()
     */
	public function uploadAction(Request $request)
	{
        $form = $this->createFormBuilder()
                     ->add('image', 'file')
                     ->getForm();

        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            $image = $form->getData()['image'];

            if (substr($image->getMimeType(), 0, 5) == 'image') {
                $webPath = $this->get('kernel')->getRootDir() . '/../web/img';
                $imageName = $image->getClientOriginalName();
                $image->move($webPath, $imageName);
                return array('form' => $form->createView(),
                             'imageName' => "$imageName");
            } else {
                return array('form' => $form->createView(),
                             'invalideType' => $image->getMimeType());
            }
        } else {
            return array('form' => $form->createView());
        }
	}


    /**
     * @Route("/tools", name="tools")
     * @Template()
     */
	public function toolsAction()
	{
        return array();
	}

    /**
     * @Route("/tools_ok", name="tools_ok")
     * @Template()
     */
	public function toolsOkAction()
	{
        return array();
	}


    /**
     * @Route("/delete_unamed_researches", name="delete_unamed_researches")
     */
	public function deleteUnamedResearchesAction()
	{
        return $this->redirect($this->generateUrl('tools_ok'));
	}

    /**
     * @Route("/delete_orphan_elements", name="delete_orphan_elements")
     */
	public function deleteOrphanElementsAction()
	{
        return $this->redirect($this->generateUrl('tools_ok'));
	}

    /**
     * @Route("/compute_specificities", name="compute_specificities")
     */
	public function computeSpecificitiesAction()
	{
        return $this->redirect($this->generateUrl('tools_ok'));
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