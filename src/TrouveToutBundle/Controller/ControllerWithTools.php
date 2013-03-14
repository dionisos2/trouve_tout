<?php

namespace Eud\TrouveToutBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Eud\ToolBundle\Service\AssertData;

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

    public function getAd()
    {
        return $this->ad;
    }
}