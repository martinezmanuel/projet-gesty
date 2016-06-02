<?php

namespace WCS\CantineBundle\Entity;

/**
 * VoyageRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class VoyageRepository extends \Doctrine\ORM\EntityRepository
{
    public function findByDivisionsAnneeScolaire($params)
    {
        $division = $params["division"];

        // récupère la date du jour
        $now = new \DateTime();


        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('v')
            ->from('WCSCantineBundle:Voyage', 'v')
            ->join('v.divisions', 'd')
            ->where("v.estAnnule = FALSE
                AND d = :division AND v.date_debut >= :now")
            ->orderBy('v.date_debut')
            ->setParameter(':division', $division)
            ->setParameter(':now', $now);

        return $qb;
    }


    public function findByDivisions()
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('d')
            ->from('WCSCantineBundle:Division', 'd')
            ->join('d.school', 's')
            ->where("s.active_voyage = TRUE");

        return $qb;

    }

}
