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
            new Twig_SimpleFunction('get_input_color_style', array($this, 'getInputColorStyle')),
            new Twig_SimpleFunction('get_catalogue_categories', array($this, 'getCatalogueCAtegories')),

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
        
        if(method_exists($product, 'getImages') && count($product->getImages())>0){
            $imageEntity = $product->getImages()->first();
            $instance = new \CoreBundle\Twig\CoreExtension();
            $instance->setContainer($this->container);
            $image = $instance->getThumbImage($imageEntity->getPath(), $size);
            if($image == ''){
                return $this->getDefaultProductImage($size);
            }
            return $image;
        }elseif(is_array($product) && isset($product['imagePaths'])){
            $image = $this->getFirstImage($product['imagePaths']);
            $image = $this->getThumbImage($image, '260');
    
            return $image;
        }else{
            return $this->getDefaultProductImage($size);
        }
        
    }
    
    public function getDefaultProductImage($size=null) {
        if(!is_null($size)) return 'http://placehold.it/'.$size;
        return 'http://placehold.it/350x200';
    }
    
    public function getInputColorStyle($key)
    {
        $values = array(
            'amarillo' => 'background-color: yellow;',
            'azul' => 'background-color: blue;' ,
            'beige' => 'background-color: beige;',
            'blanco' => 'background-color: white;border: 1px solid #ccc',
            'bronce' => 'background-color: goldenrod;',
            'dorado' => 'background-color: gold;',
            'granate' => 'background-color: orangered;',
            'gris' => 'background-color: grey;',
            'lila' => 'background-color: purple;',
            'marron' => 'background-color: saddlebrown;',
            'naranja' => 'background-color: orange;',
            'negro' => 'background-color: black;',
            'plateado' => 'background-color: silver;',
            'rojo' => 'background-color: red;',
            'rosa' => 'background-color: pink;',
            'transparente' => 'background: url(/bundles/front/images/no-color.png);',
            'verde' => 'background-color: green;'
        );
        
        if(isset($values[strtolower($key)])) return $values[strtolower($key)];
        
        return null;
    }
    
    public function getFirstImage($imageName) {
        $arr = explode(',', $imageName);
        if(isset($arr[0])) return $arr[0];
        else return null;
    }
    
     public function getThumbImage($imageName, $size) {
        
        if($imageName == '') return null;
        $arr = explode('.', $imageName);
        $arr2 = explode('/', $arr[0]);
        if(is_array($arr2) && count($arr2)>1){
            $name = end($arr2);
            array_pop($arr2);
            $path = implode('/', $arr2);
            $returnPath =  $path.'/thumbnail/'.$name.'_'.$size.'.'.$arr[1];
            
            $coreManager =  $this->container->get('core_manager');
            $twigGlobal = $this->container->get('twig.global');
            
            if(!$coreManager->checkRemoteFile($twigGlobal->getParameter('server_base_url').$returnPath)){
                $returnPath =  $path.'/thumbnail/'.$name.'_'.$size.'.jpg';
            }else{
                return $imageName;
            }
            
            return $returnPath;
        }
        return $arr[0].'_'.$size.'.'.$arr[1];
    }
    
    
    /**
    * Returns statistics from product
    *
    */
    public function getCatalogueCategories()
    {
        $categories = $this->container->get('doctrine')->getManager()->getRepository('CatalogueBundle:Category')
                ->findBy(array());
        
        return $categories;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'catalogue_extension';
    }
}