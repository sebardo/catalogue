<?php

namespace CatalogueBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use CatalogueBundle\Form\Model\Search;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

class SearchCategoryType extends AbstractType
{
   

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $config = array();
        $config['category'] = array();
        $formConfig = $options['formConfig'];
        $category = $formConfig['category'];
        
        if(isset($formConfig['category'])){
            
            $config['category'] = array(
                    'class' => 'CatalogueBundle:Category',
                    'query_builder' => function(EntityRepository $er)use($category) {
                        return $er->createQueryBuilder('c')
                              ->where('c.active = true')
                              ->where('c.parentCategory = :category')
                             ->setParameter('category', $category)
                              ->orderBy('c.order', 'ASC')
                              
                            ;
                    },
                    'expanded'  => true,
                    'multiple'  => true,
                    'data' => array($category)
            );
        }
        
        $config['brand'] = array(
                'class' => 'CatalogueBundle:Brand',
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('b')
                         ->where('b.available = true')
                        ->orderBy('b.name', 'ASC');
                },
                'expanded'  => false,
                'multiple'  => true,
                'required' => false
        );
                
        if(isset($formConfig['brand'])){
            $config['brand'] = array_merge($config['brand'], array('data' => array($formConfig['brand'] )));
        }
        
        if(isset($formConfig['features'])){
            foreach ($formConfig['features'] as $feature) {
                $value = $feature->getSlug();
                if(isset($formConfig[$value])){
                    if(count(explode(',', $formConfig[$value])) > 1){
                        $config[$value] = array('data' => explode(',', $formConfig[$value]));
                    }else{
                        $config[$value] = array('data' => array(strtolower($formConfig[$value])));
                    }
                }else{
                    $config[$value] = array();
                }
            }
        }
 
        
        if($category != 'outlet' && $category->getSlug() == 'lentillas'){

            $config['brand'] = array(
                    'class' => 'CatalogueBundle:Brand',
                    'query_builder' => function(EntityRepository $er)use($category) {
                        return $er->createQueryBuilder('b')
                            ->where('b.category = :category')
                            ->setParameter('category', $category)
                            ->orderBy('b.name', 'ASC');
                    },
                    'expanded'  => false,
                    'multiple'  => true,
                    'required' => false
            );
            if(isset($formConfig['brand'])){
                $config['brand'] = array_merge($config['brand'], array('data' => array($formConfig['brand'] )));
            }
        } 
        
        if(isset($formConfig['features'])){
            foreach ($formConfig['features'] as $feature) {
                $value = $feature->getSlug();
                
                
                if(method_exists(new Search(), $value)){
                    $builder->add($value, \Symfony\Component\Form\Extension\Core\Type\ChoiceType::class, array_merge($config[$value], array(
                        'choices'   => Search::$value(),
                        'required'  => false,
                        'expanded'  => true,
                        'multiple'  => true
                    )));
                }else{
                    $values = $feature->getValues();
                    $retunValues = array();
                    foreach ($values as $val) {
                        $retunValues[strtolower($val->getName())] = $val->getName();
                    }
                     $builder->add($value, \Symfony\Component\Form\Extension\Core\Type\ChoiceType::class, array(
                        'choices'   =>  $retunValues,
                        'required'  => false,
                        'expanded'  => true,
                        'multiple'  => true
                    ));
                    
                }
            }
             
        }
       
        
        $priceFromConfig = array();
        $priceToConfig = array();
        
                
        if(isset($formConfig['price_from'])){               
            $priceFromConfig['data'] = $formConfig['price_from'];
        }
        if(isset($formConfig['price_to'])){
            $priceToConfig['data'] = $formConfig['price_to'];
        }
        if($category != 'outlet' && count($category->getSubCategories())>0)$builder->add('category', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, $config['category']);
        $builder->add('brand', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, $config['brand']);
        $builder->add('model', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, array(
                'class' => 'CatalogueBundle:BrandModel',
                'required' => false
            ));
        
        $builder->add('sort', \Symfony\Component\Form\Extension\Core\Type\HiddenType::class, array(
            'required'  => false,
        ));
        $builder->add('price_from', \Symfony\Component\Form\Extension\Core\Type\HiddenType::class, $priceFromConfig);
        $builder->add('price_to', \Symfony\Component\Form\Extension\Core\Type\HiddenType::class, $priceToConfig);
        $builder->add('latitude', \Symfony\Component\Form\Extension\Core\Type\HiddenType::class);
        $builder->add('longitude', \Symfony\Component\Form\Extension\Core\Type\HiddenType::class);
//        $builder->add('city', null, array('attr' => array('autocomplete' => 'off')));
//        $builder->add('cp', null, array('attr' => array('autocomplete' => 'off')));
            
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CatalogueBundle\Form\Model\Search',
            'formConfig' => null
        ));
    }
    
     
    
    public function getName()
    {
        return 'search';
    }

}
