<?php

namespace Eud\TrouveToutBundle\Entity;

use Eud\ToolBundle\Service\Enum;

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
           'Eud\TrouveToutBundle\Entity');

class_alias('Eud\TrouveToutBundle\Entity\Unit', 'Unit');
