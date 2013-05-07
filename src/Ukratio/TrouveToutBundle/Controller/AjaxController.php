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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;


class AjaxController extends ControllerWithTools
{    


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

        if ($pathElement == 'empty') {
            $pathElement = array();
        }

        $choices = $caractTypeManager->getChoicesFor($type, $pathElement);

        if (in_array($type, array(Type::$name, Type::$number))) {
            $choices = array('other' => 'other') + $choices;
        }

        return new Response(json_encode($choices));
    }

}
