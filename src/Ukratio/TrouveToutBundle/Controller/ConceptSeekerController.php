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
     * @Route("/create_research", name="create_research")
     * @Method({"GET"})
     * @Template("TrouveToutBundle:TrouveTout:createResearch.html.twig")
     */
    public function createResearchAction()
    {
        $cfc = $this->get('TrouveTout.ConceptFormManager');
        return $cfc->createConcept(Discriminator::$Research);
    }

    /**
     * @Route("/create_research")
     * @Method({"POST"})
     * @Template("TrouveToutBundle:TrouveTout:createResearch.html.twig")
     */
    public function runResearchAction(Request $request)
    {
        $cfc = $this->get('TrouveTout.ConceptFormManager');
        $research = new Concept();
        $type = Discriminator::$Research;

        $research->setType($type->getName());

        $form = $cfc->createForm($research);

        $form->bind($request);
        if ($form->isValid()) {
            $researchResults = $cfc->runResearch($research);

            return $cfc->arrayForTemplate($research, $form, $researchResults);
        } else {

            return $cfc->arrayForTemplate($research, $form);
        }

    }

}