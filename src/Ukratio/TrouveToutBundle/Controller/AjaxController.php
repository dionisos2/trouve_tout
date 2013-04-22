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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;


class AjaxController extends ControllerWithTools
{    


    /**
     * @Route("/ajax_modify_caract", name="ajax_modify_caract")
     * @Method({"POST"})
     */
    public function createCategoryAction()
    {
        $elementList = $_POST['elementList'];
        $elementList["test"] = "anuritse";

        return new Response(json_encode($elementList));
    }

}
