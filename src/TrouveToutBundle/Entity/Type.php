<?php
namespace Eud\TrouveToutBundle\Entity;
use Eud\ToolBundle\Service\Enum;

Enum::enum('Type', array('name', 'number', 'picture', 'object', 'text'), 'Eud\TrouveToutBundle\Entity');

class_alias('Eud\TrouveToutBundle\Entity\Type', 'Type');
