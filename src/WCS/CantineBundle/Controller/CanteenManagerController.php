<?php
// src/WCS/CantineBundle/Controller/CanteenManagerController.php

namespace WCS\CantineBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use WCS\CantineBundle\Form\Type\LunchType;
use WCS\CantineBundle\Entity\Lunch;
use WCS\CantineBundle\Entity\Eleve;
use WCS\CantineBundle\Entity\School;

/**
 * List controller.
 *
 */
class CanteenManagerController extends Controller
{

    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $schools = $em->getRepository('WCSCantineBundle:School')->findAll();

        return $this->render('WCSCantineBundle:CanteenManager:index.html.twig', array(
            'schools' => $schools,
        ));
    }

    /**
     * Lists all Eleve entities.
     *
     */
    public function todayListAction(Request $request, $schoolId)
    {
        $dateNow = new \DateTime();

        $em = $this->getDoctrine()->getManager();
        $lunches = $em->getRepository('WCSCantineBundle:Lunch')->findBy(array(
            'date' => $dateNow,
        ));
        $school = $em->getRepository('WCSCantineBundle:School')->find($schoolId);

        $lunch = new Lunch();
        $form = $this->createForm(new LunchType(), $lunch);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $lunch->setDate($dateNow);
            $em->persist($lunch);
            $em->flush();

            return $this->redirect($this->generateUrl('wcs_cantine_todayList', array('schoolId' => $schoolId)));

        }

        return $this->render('WCSCantineBundle:Eleve:todayList.html.twig', array(
            'lunches' => $lunches,
            'ecole' => $school,
            'form' => $form->createView(),
        ));

    }

    public function deleteAction($id, $schoolId)
    {
        $dateNow = new \DateTime();
        $em = $this->getDoctrine()->getManager();
        $lunches = $em->getRepository('WCSCantineBundle:Lunch')->findBy(array(
            'eleve' => $id,
            'date' => $dateNow
        ));
        foreach ($lunches as $lunch) {
            $em->remove($lunch);
        }
        $em->flush();

        return $this->redirect($this->generateUrl('wcs_cantine_todayList', array('schoolId' => $schoolId)));
    }

    public function commandeAction(Request $request)
    {
        $date = new \DateTime();

        $em = $this->getDoctrine()->getManager();
        $school = $em->getRepository('WCSCantineBundle:School')->findAll();
        $lunches = $em->getRepository('WCSCantineBundle:Lunch')->findBy(array(
                'date' => $date,
            )
        );

        $lunch = new Lunch();
        $form = $this->createForm(new LunchType(), $lunch);
        $form->handleRequest($request);

        return $this->render('WCSCantineBundle:Eleve:commande.html.twig', array(
            'ecole'=> $school,
            'lunches' => $lunches,
        ));
    }
}
