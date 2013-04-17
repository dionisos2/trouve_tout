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
     * @Route("/run_research/{id}", name="run_research", requirements={"id" = "\d+"})
     * @Method({"GET"})
     * @Template("TrouveToutBundle:TrouveTout:runResearch.html.twig")
     */
    public function runResearchAction(Request $request, Concept $research)
    {
        $cfc = $this->get('TrouveTout.ConceptFormManager');
        $researchResults = $cfc->runResearch($research);
        return array('researchResults' => $researchResults,
                     'research' => $research,
        );
    }

}