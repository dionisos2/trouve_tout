<?php

namespace Ukratio\TrouveToutBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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

    public function getAd()
    {
        return $this->ad;
    }
}