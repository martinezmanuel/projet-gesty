<?php

namespace WCS\CantineBundle\Block\Service;


use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Admin\Pool;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\BlockBundle\Block\BaseBlockService;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityManager;

class StatElevesBlockService extends BaseBlockService
{
    /**
     * @var \Symfony\Component\Security\Core\SecurityContextInterface
     */
    protected $securityContext;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var \WCS\CalendrierBundle\Service\DateNow
     */
    private $date_now_service;

    public function __construct( $name, ContainerInterface $container )
    {
        parent::__construct($name, $container->get('templating'));

        $this->date_now_service     = $container->get('wcs.datenow');
        $this->pool                 = $container->get('sonata.admin.pool');
        $this->em                   = $container->get('doctrine.orm.entity_manager');
        $this->securityContext      = $container->get('security.context');
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Statistiques';
    }

    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        // Totals stats
        $tots = array(
            'users'     => $this->em->getRepository('ApplicationSonataUserBundle:User')->count(),
            'children'  => $this->em->getRepository('WCSCantineBundle:Eleve')->count(),
            'tap'       => $this->em->getRepository('WCSCantineBundle:Tap')->count(),
            'gardery'   => $this->em->getRepository('WCSCantineBundle:Garderie')->count(),
            'meals'     => $this->em->getRepository('WCSCantineBundle:Lunch')->count(),
            'schools'   => $this->em->getRepository('WCSCantineBundle:School')->count()
        );

        $options['date_day'] = $this->date_now_service->getDate();

        $repoLunch = $this->em->getRepository('WCSCantineBundle:Lunch');

        // stats lunch : current week
        $options['enable_next_week']    = false;

        $options['without_pork']        = true;
        $currentWeekMealsNoPork         = $repoLunch->getWeekMeals( $options );
        $options['without_pork']        = false;
        $currentWeekMeals               = $repoLunch->getWeekMeals( $options );

        // stats lunch : next week
        $options['enable_next_week']    = true;

        $options['without_pork']        = true;
        $nextWeekMealsNoPork            = $repoLunch->getWeekMeals( $options );
        $options['without_pork']        = false;
        $nextWeekMeals                  = $repoLunch->getWeekMeals( $options );

        return $this->renderResponse($blockContext->getTemplate(), array(
            'block'                     => $blockContext->getBlock(),
            'base_template'             => $this->pool->getTemplate('WCSCantineBundle:Block:stateleves.html.twig'),
            'settings'                  => $blockContext->getSettings(),
            'currentWeekMeals'          => $currentWeekMeals,
            'currentWeekMealsNoPork'    => $currentWeekMealsNoPork,
            'nextWeekMeals'             => $nextWeekMeals,
            'nextWeekMealsNoPork'       => $nextWeekMealsNoPork,
            'tots'                      => $tots

        ), $response);
    }


    /**
     * {@inheritdoc}
     */
    public function setDefaultSettings(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'title'    => 'Mes informations',
            'template' => 'WCSCantineBundle:Block:stateleves.html.twig' // Le template à render dans execute()
        ));
    }

    /**
     * @return array
     */
    public function getDefaultSettings()
    {
        return array();
    }

    /**
     * {@inheritdoc}
     */
    public function validateBlock(ErrorElement $errorElement, BlockInterface $block)
    {
    }
    
    /**
     * {@inheritdoc}
     */
    public function buildEditForm(FormMapper $formMapper, BlockInterface $block)
    {
    }
}

