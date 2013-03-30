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
                     ->add('category', 'TrouveTout_ConceptConcept')
                     ->getForm();

        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            $image = $form->getData()['image'];
            $category = $form->getData()['category']->getMoreGeneral();
            
            if (substr($image->getMimeType(), 0, 5) == 'image') {
                $subDir = array_map(function($x){return $x->getName();},  $category->getAllMoreGeneralConcepts(-1));
                $subDir = array_reverse($subDir);

                $webPath = $this->get('kernel')->getRootDir() . '/../web/img/';

                $imagePath = 'picture/' . implode('/', $subDir) . '/';
                $imageName = $image->getClientOriginalName();
                $image->move($webPath . $imagePath, $imageName);
                return array('form' => $form->createView(),
                             'imageName' => "$imageName",
                             'imagePath' => "$imagePath");
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
     * @Route("/tools_ok/{type}/{number}", requirements={"number" = "\d+", "type" = "elements|researches|specificities"}, name="tools_ok")
     * @Template()
     */
	public function toolsOkAction($type, $number)
	{
        return array('type' => $type,
                     'number' => $number);
	}


    /**
     * @Route("/delete_unamed_researches", name="delete_unamed_researches")
     */
	public function deleteUnamedResearchesAction()
	{
        $number = $this->get('TrouveTout.Tools')->deleteUnamedResearches();
        return $this->redirect($this->generateUrl('tools_ok', array('type' => 'researches',
                                                                    'number' => $number)));
	}

    /**
     * @Route("/delete_orphan_elements", name="delete_orphan_elements")
     */
	public function deleteOrphanElementsAction()
	{
        $number = $this->get('TrouveTout.Tools')->deleteOrphanElements();
        return $this->redirect($this->generateUrl('tools_ok', array('type' => 'elements',
                                                                    'number' => $number)));
	}

    /**
     * @Route("/compute_specificities", name="compute_specificities")
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