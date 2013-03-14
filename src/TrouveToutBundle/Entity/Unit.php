<?php

namespace Ukratio\TrouveToutBundle\Entity;

use Ukratio\ToolBundle\Service\Enum;

Enum::enum('Unit', array('∅',
                         'mètre',
                         'gramme',
                         'seconde',
                         'newton',
                         'watt',
                         'joule',
                         'pascal',
                         'litre',
                         'ampère',
                         'volt',
                         'farad',
                         'henry',
                         'coulomb',),
           'Ukratio\TrouveToutBundle\Entity');

class_alias('Ukratio\TrouveToutBundle\Entity\Unit', 'Unit');
