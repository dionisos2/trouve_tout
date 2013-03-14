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


class ConceptController extends ControllerWithTools
{    


    /**
     * @Route("/create_category", name="create_category")
     * @Method({"GET"})
     * @Template("TrouveToutBundle:TrouveTout:createConcept.html.twig")
     */
    public function createCategoryAction(Request $request)
    {
        return $this->createConcept($request, Discriminator::$Category);
    }

    /**
     * @Route("/create_set", name="create_set")
     * @Method({"GET"})
     * @Template("TrouveToutBundle:TrouveTout:createConcept.html.twig")
     */
    public function createSetAction(Request $request)
    {
        return $this->createConcept($request, Discriminator::$Set);
    }

    private function createConcept(Request $request, $type)
    {
        $cfc = $this->get('TrouveTout.ConceptFormManager');
        $concept = new Concept();
        $concept->setType($type->getName());

        $form = $cfc->createForm($concept);
        return $cfc->arrayForTemplate($concept, $form);
    }

    /**
     * @Route("/create_{type}", requirements={"type" = "set|category"}, name="save_concept")
     * @Method({"POST"})
     * @Template("TrouveToutBundle:TrouveTout:createConcept.html.twig")
     */
    public function saveConcept(Request $request, $type)
    {
        $cfc = $this->get('TrouveTout.ConceptFormManager');
        $concept = new Concept();
        if ($type == "category") {
            $type = Discriminator::$Category;
            $concept->setNumber(null);
        } else {
            $type = Discriminator::$Set;
        }

        $concept->setType($type->getName());

        $form = $cfc->createForm($concept);

        $form->bind($request);
        if ($form->isValid()) {
            $cfc->saveConcept($concept);

            return $this->redirect($this->generateUrl('edit_concept', array('id' => $concept->getId())));
        } else {

            return $cfc->arrayForTemplate($concept, $form);
        }

    }
    
    /**
     * @Route("/modify/{id}", name="edit_concept", requirements={"id" = "\d+"})
     * @Method({"GET"})
     * @Template("TrouveToutBundle:TrouveTout:modifyConcept.html.twig")
     */
    public function editConceptAction(Concept $concept)
    {
        $cfc = $this->get('TrouveTout.ConceptFormManager');
        $form = $cfc->createForm($concept);
        return $cfc->arrayForTemplate($concept, $form);
    }


    /**
     * @Route("/modify/{id}", name="update_concept", requirements={"id" = "\d+"})
     * @Method({"POST"})
     * @Template("TrouveToutBundle:TrouveTout:modifyConcept.html.twig")
     */
    public function updateAction(Request $request, Concept $concept)
    {
        $cfc = $this->get('TrouveTout.ConceptFormManager');
        $form = $cfc->createForm($concept);

        $form->bind($request);
        if ($form->isValid()) {
            $cfc->saveConcept($concept);

            return $this->redirect($this->generateUrl('edit_concept', array('id' => $concept->getId())));
        } else {
            return $cfc->arrayForTemplate($concept, $form);
        }
            
    }

}