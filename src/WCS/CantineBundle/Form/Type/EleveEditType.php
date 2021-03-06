<?php

namespace WCS\CantineBundle\Form\Type;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WCS\CantineBundle\Entity\Eleve;
use WCS\CantineBundle\Form\DataTransformer\LunchToStringTransformer;


class EleveEditType extends AbstractType
{
    private $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', 'text', array(
                'disabled' => true
            ))
            ->add('prenom', 'text', array(
                'disabled' => true
            ))
            ->add('dateDeNaissance', 'date', array(
                'format' => 'dd-MMMM-yyyy',
                'years' =>  range(\date("Y") - 11, \date("Y") - 2),
                'disabled' => true
            ))
            ->add('division', 'entity', array(
                'class' => 'WCSCantineBundle:Division'))
            ->add('regimeSansPorc', 'checkbox', array('required'=>false))
            ->add('allergie', 'text', array('label' =>'allergie', 'required'=>false))
            ->add('habits', null, array('required'=>false))
            ->add('habits', 'choice', array(
                'choices'   => Eleve::getHabitDaysLabels(),
                'expanded' => true,
                'multiple' => true,
                'required'  => false
            ))

            ->add('lunches', 'text', array(
                'invalid_message' => 'That is not valid dates for lunches',
                'required'  => false
            ));

        $builder->get('lunches')
            ->addModelTransformer(new LunchToStringTransformer($this->manager, $builder->getData()));
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'WCS\CantineBundle\Entity\Eleve'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'WCS_cantinebundle_eleve';
    }
}
