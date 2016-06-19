<?php
/**
 * Created by PhpStorm.
 * User: rod
 * Date: 13/06/16
 * Time: 11:35
 */

namespace WCS\EmployeeBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use WCS\CalendrierBundle\Service\Calendrier\ActivityType;

class ActivityControllerBase extends Controller
{
    /**
     * @param $activity
     * @return bool
     */
    protected function isDayOff($activity)
    {
        $activityType = [
            'cantine'       => ActivityType::CANTEEN,
            'tap'           => ActivityType::TAP,
            'garderie_matin'=> ActivityType::GARDERIE_MORNING,
            'garderie_soir' => ActivityType::GARDERIE_EVENING
        ];

        return $this->container->get('wcs.calendrierscolaire')->isDayOff(
            $activityType[$activity],
            $this->getDateDay()
        );
    }

    /**
     * @return \DateTimeImmutable the "date now" defined by the service
     */
    protected function getDateDay()
    {
        return $this->container->get('wcs.datenow')->getDate();
    }


}