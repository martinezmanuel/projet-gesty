<?php
namespace WCS\EmployeeBundle\Controller\ViewBuilder;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use WCS\CantineBundle\Entity\School;
use WCS\EmployeeBundle\Controller\Mapper\ActivityMapperInterface;
use WCS\EmployeeBundle\Form\Type\ActivityEleveType;


class ListViewBuilder extends ViewBuilderAbstract
{
    private $mapper;

    /**
     * RegisterController constructor.
     * @param ActivityMapperInterface $mapper
     * @param ContainerInterface $container
     */
    public function __construct(ActivityMapperInterface $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * @param Request $request
     * @param School $school
     * @param $activity
     * @return array
     */
    public function buildView(Request $request, School $school, $activity)
    {
        // prepare local variables

        $entityClass    = $this->mapper->getEntityClassName();
        $entity         = new $entityClass;
        $redirectTo     = '';


        // merge all options to transmit to the getActivityDayList repository's method

        $options = array_merge(
            $this->mapper->getDayListAdditionalOptions(),
            array(
                'school'    => $school,
                'date_day'  => $this->getDateDay()
            )
        );


        // get the list of pupils registered today in the activity

        $entities = $this->getRepository($this->mapper->getEntityClassName())->getActivityDayList($options);


        // create the form

        $form = $this->createForm(
            new ActivityEleveType($entityClass),
            $entity,
            [ 'additional_options' => [
                'school_id' => $school->getId(),
                'date_day'  => $this->getDateDay(),
                'activity_type' => $this->mapper->getActivityType()
                ]
            ]
        );


        // process the form if any POST

        if ($this->processForm($request, $form, $entity)) {
            $redirectTo = $this->generateUrl(
                'wcs_employee_daylist',
                array('activity'=>$activity, 'id'=>$school->getId())
            );
        }

        // return the array with all data
        return array(
            'entities'      => $entities,
            'form_register' => $form->createView(),
            'redirect_to'    => $redirectTo
        );
    }


    /**
     * @param Request $request
     * @param Form $form
     * @param $entity
     * @return bool
     */
    private function processForm(Request $request, Form $form, $entity)
    {
        $form->handleRequest($request);

        if ($form->isValid()) {

            $this->mapper->updateEntity(
                $entity, $this->getDateDay()
            );

            $em = $this->getDoctrineManager();
            $em->persist($entity);
            $em->flush();
            return true;
        }

        return false;
    }
}
