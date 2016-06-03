<?php
namespace WCS\CalendrierBundle\Service;

interface DaysOffInterface
{
    public function __construct(
        \Doctrine\ORM\EntityManager $em
    );

    /**
     * @return array of dates
     */
    public function findDatesWithin(
        \WCS\CalendrierBundle\Service\Periode\Periode $periode
    );
}
