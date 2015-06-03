<?php

namespace Ukratio\TrouveToutBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;

use JMS\SecurityExtraBundle\Annotation\Secure;

use Ukratio\ToolBundle\Service\Enum;
use Ukratio\ToolBundle\debug\Message;
use Ukratio\TrouveToutBundle\Entity\Concept;
use Ukratio\TrouveToutBundle\Entity\Caract;
use Ukratio\TrouveToutBundle\Entity\Element;
use Ukratio\TrouveToutBundle\Entity\Discriminator;



class ConceptController extends ControllerWithTools
{

    /**
     * @Route("/create_category", name="create_category")
     * @Method({"GET"})
     * @Template("TrouveToutBundle:TrouveTout:createConcept.html.twig")
     * @Secure(roles="ROLE_USER")
     * @Cache(smaxage=604800, maxage=3600, public=true)
     */
    public function createCategoryAction()
    {
        $cfc = $this->get('TrouveTout.ConceptFormManager');
        return $cfc->createConcept(Discriminator::$Category);
    }

    /**
     * @Route("/create_set", name="create_set")
     * @Method({"GET"})
     * @Template("TrouveToutBundle:TrouveTout:createConcept.html.twig")
     * @Secure(roles="ROLE_USER")
     * @Cache(smaxage=604800, maxage=3600, public=true)
     */
    public function createSetAction()
    {
        $cfc = $this->get('TrouveTout.ConceptFormManager');
        return $cfc->createConcept(Discriminator::$Set);
    }

    /**
     * @Route("/create_{type}", requirements={"type" = "set|category"}, name="save_concept")
     * @Method({"POST"})
     * @Secure(roles="ROLE_USER")
     * @Template("TrouveToutBundle:TrouveTout:createConcept.html.twig")
     */
    public function saveConcept(Request $request, $type)
    {
        $cfc = $this->get('TrouveTout.ConceptFormManager');
        $concept = new Concept();

        if ($type == 'category') {
            $type = Discriminator::$Category;
            $concept->setNumber(null);
        } elseif($type == 'set') {
            $type = Discriminator::$Set;
        } else {
            $type = Discriminator::$Research;
        }

        $concept->setType($type->getName());

        $form = $cfc->createForm($concept);

        $form->bind($request);

        if ($form->isValid()) {
            $cfc->saveConcept($concept);

            return $this->redirect($this->generateUrl('edit_concept', array('conceptId' => $concept->getId())));
        } else {

            return $cfc->arrayForTemplate($concept, $form);
        }

    }

    /**
     * @Route("/modify/{conceptId}", name="edit_concept", requirements={"conceptId" = "\d+"})
     * @Method({"GET"})
     */
    public function editConceptAction($conceptId, Request $request)
    {
        $response = new Response();

        if ($this->get('security.context')->isGranted('ROLE_USER')) {
            $response->setETag("connected");
        } else {
            $response->setETag("unconnected");
        }

        $conceptRepo = $this->getDoctrine()
                            ->getRepository('TrouveToutBundle:Concept');
        if (in_array($response->getEtag(), $request->getEtags())) {
            $date = $conceptRepo->getModifiedAt($conceptId);
            if($date == null) {
                $date = new \DateTime();
                $date->setTimezone(new \DateTimeZone('UTC'));
            }
            $response->setLastModified($date);
        }

        if ($response->isNotModified($request)) {
            return $response;
        } else {
            $concept = $conceptRepo->findByIdWithCaract($conceptId);
            $cfc = $this->get('TrouveTout.ConceptFormManager');
            $form = $cfc->createForm($concept);
            $options = $cfc->arrayForTemplate($concept, $form);
            return $this->render('TrouveToutBundle:TrouveTout:modifyConcept.html.twig', $options, $response);
        }

    }


    /**
     * @Route("/modify/{id}", name="update_concept", requirements={"id" = "\d+"})
     * @Method({"POST"})
     * @Secure(roles="ROLE_USER")
     * @ParamConverter("concept", options={"repository_method": "findByIdWithCaract"})
     * @Template("TrouveToutBundle:TrouveTout:modifyConcept.html.twig")
     */
    public function updateAction(Request $request, Concept $concept)
    {
        $cfc = $this->get('TrouveTout.ConceptFormManager');

        $form = $cfc->createForm($concept);

        echo "<br>bind<br>";
        $form->bind($request);
        echo "<br>fin bind<br>";

        $type = Discriminator::getEnumerator($concept->getType());

        if ($form->isValid()) {
            $cfc->saveConcept($concept);

            if ($type == Discriminator::$Research) {
                return $this->redirect($this->generateUrl('run_with_id_research', array('id' => $concept->getId())));
            } else {
                return $this->redirect($this->generateUrl('edit_concept', array('conceptId' => $concept->getId())));
            }
        } else {
            return $cfc->arrayForTemplate($concept, $form);
        }

    }


    /**
     * @Route("/delete/{id}", name="confirm_delete", requirements={"id" = "\d+"})
     * @Secure(roles="ROLE_USER")
     * @Method({"GET"})
     * @Template("TrouveToutBundle:TrouveTout:confirm_delete.html.twig")
     */
    public function confirmDeleteConcept(Request $request, Concept $concept)
    {
        //TODO afficher le concept dans la page
        return array('conceptId' => $concept->getId());
    }

    /**
     * @Route("/delete/{id}", name="delete_concept", requirements={"id" = "\d+"})
     * @Secure(roles="ROLE_USER")
     * @Method({"POST"})
     * @Template("TrouveToutBundle:TrouveTout:deletion_ok.html.twig")
     */
    public function deleteConcept(Request $request, Concept $concept)
    {
        $conceptId = $concept->getId();
        $cfc = $this->get('TrouveTout.ConceptFormManager');
        $cfc->deleteConcept($concept);

        return array('conceptId' => $conceptId);
    }

}
