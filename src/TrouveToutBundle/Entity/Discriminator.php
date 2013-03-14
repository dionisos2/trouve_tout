<?php

namespace Eud\TrouveToutBundle\Entity;
use Eud\ToolBundle\Service\Enum;


Enum::enum("Discriminator", array("Category", "Set"), 'Eud\TrouveToutBundle\Entity');

class_alias('Eud\TrouveToutBundle\Entity\Discriminator', 'Discriminator');
