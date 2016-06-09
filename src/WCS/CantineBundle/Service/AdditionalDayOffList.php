<?php
/**
 * Created by PhpStorm.
 * User: rod
 * Date: 30/05/16
 * Time: 17:06
 */

namespace WCS\CantineBundle\Service;

use WCS\CalendrierBundle\Service\DaysOffInterface;

class AdditionalDayOffList implements DaysOffInterface
{
    public function __construct(\Doctrine\ORM\EntityManagerInterface $em)
    {
        $this->repoFeries = $em->getRepository('WCSCantineBundle:Feries');
    }

    /**
     * @return array of \DateTime
     */
    public function findDatesWithin(\WCS\CalendrierBundle\Service\Periode\Periode $periode)
    {
        $dayOffArray = $this->repoFeries->findListDateTimes(
            $periode->getFin()->format('Y')
        );
        
        if (is_null($dayOffArray)) {
            return array();
        }

        return $dayOffArray;
    }

    /**
     * @inheritdoc
     */
    public function isOff(\DateTimeInterface $date)
    {
        $daysOffArray = $this->repoFeries->findListDateTimes($date->format('Y'));
        return (in_array($date, $daysOffArray));
    }

    private $repoFeries;
}