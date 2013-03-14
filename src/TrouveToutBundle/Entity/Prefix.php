<?php

namespace Eud\TrouveToutBundle\Entity;

use Eud\ToolBundle\Service\Enum;

Enum::enum('Prefix', array('pico' => pow(10, -12), 
                           'nano' => pow(10, -9),
                           'micro' => pow(10, -6),
                           'milli' => pow(10, -3),
                           'centi' => pow(10, -2),
                           'déci' => pow(10, -1),
                           '∅' => 1,
                           'déca' => 10,
                           'hecto' => 100,
                           'kilo' => pow(10, 3),
                           'méga' => pow(10, 6),
                           'giga' => pow(10, 9),
                           'téra' => pow(10, 12),
                           'péta' => pow(10, 15),),
           'Eud\TrouveToutBundle\Entity');

class_alias('Eud\TrouveToutBundle\Entity\Prefix', 'Prefix');
