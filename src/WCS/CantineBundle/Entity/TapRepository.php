<?php

namespace WCS\CantineBundle\Entity;

/**
 * TapRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TapRepository extends ActivityRepositoryAbstract
{
    public function count()
    {
        return $this->createQueryBuilder('a')
            ->select('COUNT(a)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param \WCS\CantineBundle\Entity\School $school
     * @return array
     */
    public function getDayList($options)
    {
        $school = $options['school'];
        $day    = $options['date_day']->format('Y-m-d');

        // Request pupils to the database from a certain date
        return $this->getEntityManager()
            ->createQuery(
                'SELECT t FROM WCSCantineBundle:Tap t 
                 JOIN t.eleve e 
                 JOIN e.division d 
                 WHERE t.date LIKE :day 
                    AND d.school = :school
                 ORDER BY d.grade, d.headTeacher, e.nom'
            )
            ->setParameter(':day', "%".$day."%")
            ->setParameter(':school', $school)
            ->getResult();
    }

}
