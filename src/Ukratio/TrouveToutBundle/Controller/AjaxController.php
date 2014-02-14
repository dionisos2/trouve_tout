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
use Ukratio\TrouveToutBundle\Entity\Type;

use Ukratio\TrouveToutBundle\Form\Type\SortedConceptType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;


class AjaxController extends ControllerWithTools
{

    /**
     * @Route("/ajax_get_owner_category", name="ajax_get_owner_category")
     * @Method({"POST"})
     */
    public function ajaxGetOwnerCategoryUrl()
    {
        $conceptRepo = $this->getDoctrine()->getRepository('TrouveToutBundle:Concept');

        $conceptId = $_POST['conceptId'];
        if ($conceptId == 'empty') {
            $conceptId = null;
        }
        $concept = $conceptRepo->findOneById($conceptId);

        $sortedConceptType = $this->get('TrouveTout.form.sorted_concept');
        $choices = $sortedConceptType->getChoicesAndCategories($concept)['choices'];

        return new Response(json_encode($choices));
    }

    /**
     * @Route("/ajax_modify_caract", name="ajax_modify_caract")
     * @Method({"POST"})
     */
    public function ajaxModifyCaract()
    {
        $elementRepo = $this->getDoctrine()->getRepository('TrouveToutBundle:Element');
        $caractTypeManager = $this->get('TrouveTout.CaractTypeManager');

        $pathElement = $_POST['completeElement'];
        $type = Type::getEnumerator($_POST['type']);
        $isChildElement = $_POST['isChildElement'] == 'true';

        if ($pathElement == 'empty') {
            $pathElement = array();
        }


        $choices = $caractTypeManager->getChoicesFor($type, $pathElement, $isChildElement);

        if (in_array($type, array(Type::$name, Type::$object))) {
            $choices = array('other' => 'other') + $choices;
        }

        $ordered_choices = array();

        if ($choices != null) {
            foreach($choices as $key => $value) {
                $ordered_choices[] = array($key, $value);
            }
        }

        return new Response(json_encode($ordered_choices));
    }

}
