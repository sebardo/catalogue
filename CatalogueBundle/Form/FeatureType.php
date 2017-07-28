<?php

namespace CatalogueBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use CoreBundle\Form\ImageType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

/**
 * Class FeatureType
 */
class FeatureType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $conf = array();
        if(isset($options['uploadDir'])){
            $conf['uploadDir'] = $options['uploadDir'];
        }
        
        $builder
            ->add('name')
            ->add('category', EntityType::class, array(
                'class' => 'CatalogueBundle:Category',
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('c');
                        //->where('c.family IS NOT NULL');
                },
                'required' => false
            ))
            ->add(
                $builder->create('image', ImageType::class, $conf)
            )
            ->add('removeImage', HiddenType::class, array('required' => false, 'attr' => array(
                'class' => 'remove-image'
                )))
            //->add('filtrable', 'checkbox', array(
            //    'required' => false
            //))
            //->add('rangeable', 'checkbox', array(
            //    'required' => false
            //))
           
            ;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CatalogueBundle\Entity\Feature',
            'uploadDir' => null
        ));
    }

}
