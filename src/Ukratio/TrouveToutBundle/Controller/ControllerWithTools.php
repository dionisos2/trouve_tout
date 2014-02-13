<?php

namespace Ukratio\TrouveToutBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Ukratio\ToolBundle\Service\AssertData;

class ControllerWithTools extends Controller
{
    protected $ad;

    public function __construct($ad = null)
    {
        if ($ad === null) {
            $this->ad = new AssertData();
        } else {
            $this->ad = $ad;
        }
    }

    public function cachedResponse(Request $request, $template, $getOptions = null)
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
            if ($getOptions == null) {
                return $this->render($template, array(), $response);
            } else {
                return $this->render($template, $getOptions(), $response);
            }
        }
    }

    public function getAd()
    {
        return $this->ad;
    }
}