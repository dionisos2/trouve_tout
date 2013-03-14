<?php

namespace Eud\TrouveToutBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Eud\ToolBundle\Service\Enum;
use Eud\ToolBundle\debug\Message;
use Eud\TrouveToutBundle\Entity\Concept;
use Eud\TrouveToutBundle\Entity\Caract;
use Eud\TrouveToutBundle\Entity\Element;
use Eud\TrouveToutBundle\Entity\Discriminator;


class ConceptSeekerController extends ControllerWithTools
{    
    
    /**
     * @Route("/find_set", name="find_set")
     * @Method({"GET"})
     * @Template("TrouveToutBundle:TrouveTout:createConcept.html.twig")
     */
    public function createSetAction(Request $request)
    {
        return $this->createConcept($request, Discriminator::$Set);
    }

}