<?php

namespace CatalogueBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use CoreBundle\Form\ImageType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

/**
 * Class FeatureValueType
 */
class FeatureValueType extends AbstractType
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
            ->add(
                $builder->create('image', ImageType::class, $conf)
            )
            ->add('removeImage', HiddenType::class, array('required' => false, 'attr' => array(
                'class' => 'remove-image'
                )))
            ;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CatalogueBundle\Entity\FeatureValue',
            'uploadDir' => null
        ));
    }

}
