<?php

namespace CatalogueBundle\Twig;

use Twig_SimpleFunction;
use Symfony\Component\Security\Core\User\UserInterface;
use CatalogueBundle\Entity\Product;
use DateTime;

/**
 * Class CatalogueExtension
 */
class CatalogueExtension extends \Twig_Extension
{
    private $parameters;

    private $container;

    public function setContainer($container)
    {
        $this->container = $container;
    }
    
    /**
     * @param array $parameters
     */
    public function __construct(array $parameters)
    {
        if(isset($parameters['parameters'])) $this->parameters = $parameters['parameters'];
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new Twig_SimpleFunction('get_product_stats', array($this, 'getProductStats')),
            new Twig_SimpleFunction('get_product_image', array($this, 'getProductImage')),
            
        );
    }
   
    /**
    * Returns statistics from product
    *
    */
    public function getProductStats(Product $product, $start, $end)
    {
        $stats = $this->container->get('doctrine')->getManager()->getRepository('CatalogueBundle:Product')
                ->getProductStats($product, $start, $end);
        
        return $stats;
    }
    
    public function getProductImage($product, $size='400') {
        
        if(method_exists($product, 'getProductImage') && count($product->getImages())>0){
            $imageEntity = $product->getImages()->first();
            $instance = new \CoreBundle\Twig\CoreExtension();
            $instance->setContainer($this->container);
            $image = $instance->getThumbImage($imageEntity->getPath(), $size);
            if($image == ''){
                return $this->getDefaultProductImage($size);
            }
            return $image;
        }else{
            return $this->getDefaultProductImage($size);
        }
        
    }
    
    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'catalogue_extension';
    }
}