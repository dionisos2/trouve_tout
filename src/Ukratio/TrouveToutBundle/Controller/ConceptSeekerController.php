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


class ConceptSeekerController extends ControllerWithTools
{

    /**
     * POST action on ConceptController
     *
     * @Route("/create_research", name="create_research")
     * @Method({"GET"})
     * @Template("TrouveToutBundle:TrouveTout:createConcept.html.twig")
     */
    public function createResearchAction()
    {
        $cfc = $this->get('TrouveTout.ConceptFormManager');
        return $cfc->createConcept(Discriminator::$Research);
    }


    /**
     * @Route("/run_with_id_research/{id}", name="run_with_id_research", requirements={"id" = "\d+"})
     * @Method({"GET"})
     * @Template("TrouveToutBundle:TrouveTout:runResearch.html.twig")
     */
    public function runAndSaveResearchAction(Request $request, Concept $research)
    {
        $cfc = $this->get('TrouveTout.ConceptFormManager');
        $researchResults = $cfc->runResearch($research);
        return array('researchResults' => $researchResults,
                     'research' => $research,
        );
    }

    /**
     * @Route("/run_research/", name="run_research")
     * @Method({"POST"})
     * @Template("TrouveToutBundle:TrouveTout:runResearch.html.twig")
     */
    public function runResearchAction(Concept $research)
    {
        $cfc = $this->get('TrouveTout.ConceptFormManager');
        $researchResults = $cfc->runResearch($research);
        return array('researchResults' => $researchResults,
                     'research' => $research,
        );
    }

    /**
     * @Route("/create_research", name="save_or_run_research")
     * @Method({"POST"})
     * @Template("TrouveToutBundle:TrouveTout:createConcept.html.twig")
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
                return $this->forward('TrouveToutBundle:ConceptSeeker:runResearch', array('research'  => $concept));
            }
        } else {

            return $cfc->arrayForTemplate($concept, $form);
        }

    }

}