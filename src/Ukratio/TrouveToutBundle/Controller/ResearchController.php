<?php

namespace Ukratio\TrouveToutBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Ukratio\ToolBundle\Service\Enum;
use Ukratio\ToolBundle\debug\Message;
use Ukratio\TrouveToutBundle\Entity\Concept;
use Ukratio\TrouveToutBundle\Entity\Caract;
use Ukratio\TrouveToutBundle\Entity\Element;
use Ukratio\TrouveToutBundle\Entity\Discriminator;


class ResearchController extends ControllerWithTools
{

    /**
     * POST action on ConceptController
     *
     * @Route("/create_research", name="create_research")
     * @Method({"GET"})
     */
    public function createResearchAction(Request $request)
    {
        $response = new Response();

        if ($this->get('security.context')->isGranted('ROLE_USER')) {
            $response->setETag("connected");
        } else {
            $response->setETag("unconnected");
        }

        if ($response->isNotModified($request)) {
            return $response;
        } else {
            $cfc = $this->get('TrouveTout.ConceptFormManager');
            $options = $cfc->createConcept(Discriminator::$Research);
            return $this->render('TrouveToutBundle:TrouveTout:createConcept.html.twig', $options, $response);
        }
    }


    /**
     * @Route("/run_with_id_research/{id}", name="run_with_id_research", requirements={"id" = "\d+"})
     * @Method({"GET"})
     * @Template("TrouveToutBundle:TrouveTout:runResearch.html.twig")
     */
    public function runResearchAction(Request $request, Concept $research)
    {
        $cfc = $this->get('TrouveTout.ConceptFormManager');
        $researchResults = $cfc->runResearch($research);
        $form = $cfc->createForm($research);

        $returnArray = $cfc->arrayForTemplate($research, $form);
        $returnArray += array('researchResults' => $researchResults, 'research' => $research);

        return $returnArray;
    }

    /**
     * @Route("/run_with_id_research/{id}", name="save_research", requirements={"id" = "\d+"})
     * @Method({"POST"})
     * @Template("TrouveToutBundle:TrouveTout:runResearch.html.twig")
     */
    public function saveResearchAction(Request $request, Concept $research)
    {
        $cfc = $this->get('TrouveTout.ConceptFormManager');
        $form = $cfc->createForm($research);

        $form->bind($request);

        if ($form->isValid()) {
            $cfc->saveConcept($research);
            return $this->redirect($this->generateUrl('run_with_id_research', array('id' => $research->getId())));
        } else {
            return $cfc->arrayForTemplate($research, $form);
        }

        return $returnArray;
    }

    /**
     * @Route("/create_research", name="save_or_run_research")
     * @Method({"POST"})
     * @Template("TrouveToutBundle:TrouveTout:runResearch.html.twig")
     */
    public function saveOrRunResearch(Request $request)
    {
        $cfc = $this->get('TrouveTout.ConceptFormManager');
        $concept = new Concept();

        $type = Discriminator::$Research;

        $concept->setType($type->getName());

        $form = $cfc->createForm($concept);

        $form->bind($request);

        if ($form->isValid()) {

            if (($request->request->get('save') != null)and($this->get('security.context')->isGranted('ROLE_USER'))) {
                $cfc->saveConcept($concept);
                return $this->redirect($this->generateUrl('run_with_id_research', array('id' => $concept->getId())));
            } else {
                $returnArray = $cfc->arrayForTemplate($concept, $form);
                $researchResults = $cfc->runResearch($concept);
                $returnArray += array('researchResults' => $researchResults, 'research' => $concept);
                return $returnArray;
                /* return $this->forward('TrouveToutBundle:Research:runResearch', array('research'  => $concept)); */
            }
        } else {
            return $cfc->arrayForTemplate($concept, $form);
        }

    }

}