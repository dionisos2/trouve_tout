<?php
namespace Ukratio\TrouveToutBundle\Entity;
use Ukratio\ToolBundle\Service\Enum;

Enum::enum('Type', array('name', 'number', 'picture', 'object', 'text', 'date'), 'Ukratio\TrouveToutBundle\Entity');

class_alias('Ukratio\TrouveToutBundle\Entity\Type', 'Type');
