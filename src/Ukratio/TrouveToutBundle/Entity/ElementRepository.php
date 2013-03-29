<?php

namespace Ukratio\TrouveToutBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * ElementRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ElementRepository extends EntityRepository
{

    public function findOrphanElements()
    {
        $queryBuilder = $this->createQueryBuilder('element')
                             ->leftJoin('element.moreSpecifics', 'moreSpecifics')
                             ->where('moreSpecifics IS NULL')
                             ->leftJoin('element.ownerCaracts', 'caracts')
                             ->andWhere('caracts.value IS NULL');

        return $queryBuilder->getQuery()->getResult();
    }

    public function findHeads()
    {
        $queryBuilder = $this->createQueryBuilder('head')
                             ->where('head.moreGeneral is NULL');
        
        return $queryBuilder->getQuery()->getResult();
    }

    public function findMoreSpecifics(Element $element)
    {
        $queryBuilder = $this->createQueryBuilder('child')
                             ->leftJoin('child.moreGeneral', 'element')
                             ->where('element.id = :id')
                             ->setParameter('id', $element->getId());
        
        return $queryBuilder->getQuery()->getResult();
    }

    public function findByPath($path, $complete = true)
    {
        if (empty($path)) {
            throw new \RuntimeException('in findByPath($path), $path can’t be empty');
        }

        $queryBuilder = $this->createQueryBuilder('element')
                             ->where('element.value = :value')
                             ->setParameter('value', (string)$path[0]);

        $alias = 'element';
        $aliasGeneral = 'elementGeneral';

        foreach (array_slice($path, 1) as $value) {
            $queryBuilder->leftJoin("$alias.moreGeneral", "$aliasGeneral")
                         ->andWhere("$aliasGeneral.value = :" . $aliasGeneral . "value")
                         ->setParameter($aliasGeneral . "value", (string)$value);
            $alias = $aliasGeneral;
            $aliasGeneral .= 'General';
        }

        if ($complete) {
            $queryBuilder->andWhere("$alias.moreGeneral IS NULL");

            $result = $queryBuilder->getQuery()->getResult();
            if (count($result) > 1) {
                var_dump($queryBuilder->getQuery()->getParameters());
                var_dump($result);
                throw new \RuntimeException('impossible case in Element::findByPath');
            }
            $result = empty($result)?null:$result[0];
        } else {
            $result = $queryBuilder->getQuery()->getResult();
        }
        
        return $result;
    }
}
