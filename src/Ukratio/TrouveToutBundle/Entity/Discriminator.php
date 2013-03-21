<?php

namespace Ukratio\TrouveToutBundle\Entity;
use Ukratio\ToolBundle\Service\Enum;


Enum::enum('Discriminator', array('Category', 'Set', 'Research'), 'Ukratio\TrouveToutBundle\Entity');

class_alias('Ukratio\TrouveToutBundle\Entity\Discriminator', 'Discriminator');
