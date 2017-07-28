<?php

namespace CatalogueBundle\Form;

use Doctrine\ORM\EntityRepository;
use CatalogueBundle\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

/**
 * Class ProductAttributesType
 */
class ProductAttributesType extends AbstractType
{

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    
        $category = $options['category'];
        
        $builder
            ->add('attributeValues', EntityType::class, array(
                'class'         => 'CatalogueBundle:AttributeValue',
                'group_by'      => 'attribute.name',
                'multiple'      => true,
                'query_builder' => function(EntityRepository $er) use ($category) {
                    return $er->createQueryBuilder('av')
                        ->join('av.attribute', 'a')
                        ->where('a.category = :category')
                        ->setParameter('category', $category)
                        ;
                }
            ));
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CatalogueBundle\Entity\Product',
            'category' => null,
        ));
    }

}
