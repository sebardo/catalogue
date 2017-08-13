<?php
namespace CatalogueBundle\DataFixtures\ORM;

use CoreBundle\DataFixtures\SqlScriptFixture;
use CatalogueBundle\Entity\Category;
use CatalogueBundle\Entity\Brand;
use CatalogueBundle\Entity\BrandModel;
use CatalogueBundle\Entity\Attribute;
use CatalogueBundle\Entity\AttributeValue;
use CatalogueBundle\Entity\Feature;
use CatalogueBundle\Entity\FeatureValue;
use CatalogueBundle\Entity\Product;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use CoreBundle\Entity\Image;

/*
 * php app/console doctrine:fixtures:load --fixtures=src/CatalogueBundle/DataFixtures/ORM/LoadCatalogueData.php
 */
class LoadCatalogueData extends SqlScriptFixture
{
    /**
     * There two kind of fixtures
     * Bundle fixtures: all info bundle needed
     * Dev fixtures: info for testing porpouse
     */
    public function createFixtures()
    {
        
        /**
         * Bundle fixtures
         */
        if($this->container->getParameter('core.fixtures_bundle_catalogue')){
            $this->runSqlScript('Translation.sql');
        }
        
        /**
         * Dev fixtures
         */
        if($this->container->getParameter('core.fixtures_dev_catalogue')){
                
            //load products, categories, brand and model
            $this->loadFixtures();
            
            
        }
        

        
    }

    private function loadFixtures(){
        $master = array(
            'Footwear' => array(
                'Nike' => array(
                    'Running',
                    'Training',
                    'Casual',
                    'Space'
                ),
                'Reebook' => array(
                    'Track',
                    'Running',
                    'Training',
                    'Casual',
                ),
                'Adidas' => array(
                    'Running',
                    'Training'
                ),
                'Asics' => array(
                    'Walking',
                    'Tennis',
                    'Track',
                    'Volleyball',
                    'Wrestling'
                ),
                'Merrell' => array(
                    'Running',
                    'Casual',
                    'Boots',
                    'Wide Width'
                )
            ),
            'Glasses' => array(
                'Rayban' => array(
                    'Aviator',
                    'Original Wayfarer',
                    'Round',
                    'Hegagonal'
                ),
                'Carrera' => array(
                    'Carrera 6000/CB1Z9',
                    'Carrera 6000 W/C CAPUZ',
                    'Turbo BL790'
                ),
                'Gucci' => array(
                    'Wayfarer',
                    'Aviator'
                ),
                'Police' => array(
                    'Aviator',
                    'Original Wayfarer',
                    'Xtreme'
                ),
                'Oakley' => array(
                    'FROGSKINS',
                    'Garage Rock',
                    'HOLBROOK 9102',
                ),
            ),
            'Watches' => array(
                'Dior' => array(
                    'Skyhawlk',
                    'Smart',
                    'Casual',
                ),
                'Bume et Mercier' => array(
                    'Chronomat',
                    'Superocean',
                    'Smart'
                ),
                'Cartier' => array(
                    'Eco driver',
                    'Promaster',
                ),
                'Citizen' => array(
                    'Smart',
                    'Pocket',
                    'Normal'
                ),
            )
        );

        $em = $this->getManager();
        $categoriesArr = array();
        $brandsArr = array();
        $modelsArr = array();
        foreach ($master as $name => $brands) {
            $categoriesArr[$name] = new Category();
            $categoriesArr[$name]->setName($name);
            $categoriesArr[$name]->setDescription('Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.');
            $categoriesArr[$name]->setActive(true); 
            $em->persist($categoriesArr[$name]);
            foreach ($brands as $name2 => $models) {
                $brandsArr[$name2] = new Brand();
                $brandsArr[$name2]->setName($name2);
                $brandsArr[$name2]->setCategory($categoriesArr[$name]);
                $brandsArr[$name2]->setAvailable(true);
                $em->persist($brandsArr[$name2]);
                foreach ($models as $name3) {
                    $modelsArr[$name3] = new BrandModel();
                    $modelsArr[$name3]->setBrand($brandsArr[$name2]);
                    $modelsArr[$name3]->setName($name3);
                    $modelsArr[$name3]->setAvailable(true);
                    $em->persist($modelsArr[$name3]);
                }
            }
            $em->flush();
        }
        
        $colores = array(
            'Amarillo',
            'Azul',
            'Beige',
            'Bronce',
            'Dorado',
            'Granate',
            'Gris',
            'Lila',
            'Marrón',
            'Naranja',
            'Negro',	 
            'Plateado',
            'Rojo',	 
            'Rosa',	 
            'Transparente',	 
            'Verde'
        );
        
        //Create Attributes
        $attr = new Attribute();
        $attr->setCategory($categoriesArr['Footwear']);
        $attr->setName('Attribute 1');

        $attrValue = new AttributeValue();
        $attrValue->setAttribute($attr);
        $attrValue->setName('Yes');

        $attrValue2 = new AttributeValue();
        $attrValue2->setAttribute($attr);
        $attrValue2->setName('No');
        
        
        //Creates Features
        $feature = new Feature();
        $feature->setCategory($categoriesArr['Footwear']);
        $feature->setName('Color');
        $this->getManager()->persist($feature);
        foreach ($colores as $value) {
            $featureValue = new FeatureValue();
            $featureValue->setFeature($feature);
            $featureValue->setName($value);
            $this->getManager()->persist($featureValue);
        }
 
        $feature2 = new Feature();
        $feature2->setCategory($categoriesArr['Footwear']);
        $feature2->setName('Feature value');

        $featureValue = new FeatureValue();
        $featureValue->setFeature($feature2);
        $featureValue->setName('Yes');

        $featureValue2 = new FeatureValue();
        $featureValue2->setFeature($feature2);
        $featureValue2->setName('No');
        
        $this->getManager()->persist($attr);
        $this->getManager()->persist($attrValue);
        $this->getManager()->persist($attrValue2);
        $this->getManager()->persist($feature2);
        $this->getManager()->persist($featureValue);
        $this->getManager()->persist($featureValue2);
        $this->getManager()->flush();
        
        /*
         * Products
         */
        //Create Products
        $brand = $this->getRepository('CatalogueBundle:Brand')->findOneBy(array('category' => $categoriesArr['Footwear'], 'name' => 'Nike'));
        $model = $this->getRepository('CatalogueBundle:BrandModel')->findOneBy(array('brand' => $brand, 'name' => 'Running'));
        $model2 = $this->getRepository('CatalogueBundle:BrandModel')->findOneBy(array('brand' => $brand, 'name' => 'Training'));
        
        $product = new Product();
        $product->setName('Nike running');
        $product->setPrice(67.84);
        $product->setBrand($brand);
        $product->setModel($model);
        $product->setCategory($categoriesArr['Footwear']);
        $product->setActive(true);
        $product->setAvailable(true);
        $product->setHighlighted(true);
        $product->setDescription('Description Nike running for testing.');
        $product->setReference(uniqid());
        $product->setMetaTitle('Nike running');
        $product->setMetaDescription('Nike running');
        $product->setStock(7);
        //$product->addAttributeValue($attrValue2);
        $product->addFeatureValue($featureValue);
        $product->setFreeTransport(true);
        //$product->setFinalShot(true);
        //$product->setTechnicalDetails('Technical details test.');
        @mkdir(__DIR__ . '/../../../../../../web/uploads/images/product/1');
        copy(__DIR__ . '/images/1/nike-running-34d569540b8eb6dc872bb10d95f0efd7.jpeg', __DIR__ . '/../../../../../../web/uploads/images/product/1/nike-running-34d569540b8eb6dc872bb10d95f0efd7.jpeg');
        copy(__DIR__ . '/images/1/thumbnail/nike-running-34d569540b8eb6dc872bb10d95f0efd7_142.jpg', __DIR__ . '/../../../../../../web/uploads/images/product/1/nike-running-34d569540b8eb6dc872bb10d95f0efd7_142.jpg');
        copy(__DIR__ . '/images/1/thumbnail/nike-running-34d569540b8eb6dc872bb10d95f0efd7_260.jpg', __DIR__ . '/../../../../../../web/uploads/images/product/1/nike-running-34d569540b8eb6dc872bb10d95f0efd7_260.jpg');
        copy(__DIR__ . '/images/1/thumbnail/nike-running-34d569540b8eb6dc872bb10d95f0efd7_380.jpg', __DIR__ . '/../../../../../../web/uploads/images/product/1/nike-running-34d569540b8eb6dc872bb10d95f0efd7_380.jpg');
        copy(__DIR__ . '/images/1/thumbnail/nike-running-34d569540b8eb6dc872bb10d95f0efd7_400.jpg', __DIR__ . '/../../../../../../web/uploads/images/product/1/nike-running-34d569540b8eb6dc872bb10d95f0efd7_400.jpg');
        $image = new Image();
        $image->setPath('uploads/images/product/1/nike-running-34d569540b8eb6dc872bb10d95f0efd7.jpeg');
        $product->addImage($image);
        
        
        

        $product2 = new Product();
        $product2->setName('Footwear for running');
        $product2->setPrice(110.14);
        $product2->setBrand($brand);
        $product2->setModel($model2);
        $product2->setCategory($categoriesArr['Footwear']);
        $product2->setActive(true);
        $product2->setAvailable(true);
        $product2->setHighlighted(true);
        $product2->setDescription('Description Footwear for running for testing.');
        $product2->setReference(uniqid());
        $product2->setMetaTitle('Footwear for running');
        $product2->setMetaDescription('Footwear for running');
        $product2->setStock(12);
        //$product2->addAttributeValue($attrValue);
        $product2->addFeatureValue($featureValue2);
        $product2->addRelatedProduct($product);
        $product2->setFreeTransport(true);
        //$product2->setFinalShot(true);
        //$product2->setTechnicalDetails('Technical details test 2.');
        @mkdir(__DIR__ . '/../../../../../../web/uploads/images/product/2');
        copy(__DIR__ . '/images/2/nike-running-34d569540b8eb6dc872bb10d95f0efd2.jpeg', __DIR__ . '/../../../../../../web/uploads/images/product/2/nike-running-34d569540b8eb6dc872bb10d95f0efd2.jpeg');
        copy(__DIR__ . '/images/2/thumbnail/nike-running-34d569540b8eb6dc872bb10d95f0efd2_142.jpg', __DIR__ . '/../../../../../../web/uploads/images/product/2/nike-running-34d569540b8eb6dc872bb10d95f0efd2_142.jpg');
        copy(__DIR__ . '/images/2/thumbnail/nike-running-34d569540b8eb6dc872bb10d95f0efd2_260.jpg', __DIR__ . '/../../../../../../web/uploads/images/product/2/nike-running-34d569540b8eb6dc872bb10d95f0efd2_260.jpg');
        copy(__DIR__ . '/images/2/thumbnail/nike-running-34d569540b8eb6dc872bb10d95f0efd2_380.jpg', __DIR__ . '/../../../../../../web/uploads/images/product/2/nike-running-34d569540b8eb6dc872bb10d95f0efd2_380.jpg');
        copy(__DIR__ . '/images/2/thumbnail/nike-running-34d569540b8eb6dc872bb10d95f0efd2_400.jpg', __DIR__ . '/../../../../../../web/uploads/images/product/2/nike-running-34d569540b8eb6dc872bb10d95f0efd2_400.jpg');
        $image2 = new Image();
        $image2->setPath('uploads/images/product/2/nike-running-34d569540b8eb6dc872bb10d95f0efd2.jpeg');
        $product2->addImage($image2);
        

        $product3 = new Product();
        $product3->setName('High training shoes');
        $product3->setPrice(270.99);
        $product3->setBrand($brand);
        $product3->setModel($model2);
        $product3->setCategory($categoriesArr['Footwear']);
        $product3->setActive(true);
        $product3->setAvailable(true);
        $product3->setHighlighted(true);
        $product3->setDescription('Description High training shoes for testing.');
        $product3->setReference(uniqid());
        $product3->setMetaTitle('High training shoes');
        $product3->setMetaDescription('High training shoes');
        $product3->setStock(12);
        //$product3->addAttributeValue($attrValue);
        $product3->addFeatureValue($featureValue);
        $product3->addRelatedProduct($product);
        $product3->addRelatedProduct($product2);
        $product3->setFreeTransport(true);
//        $product3->setFinalShot(true);
//        $product3->setTechnicalDetails('Technical details test 3.');
        @mkdir(__DIR__ . '/../../../../../../web/uploads/images/product/3');
        copy(__DIR__ . '/images/3/product-test-1-01867bee8967216c2bbfa972987c9417.jpeg', __DIR__ . '/../../../../../../web/uploads/images/product/3/product-test-1-01867bee8967216c2bbfa972987c9417.jpeg');
        copy(__DIR__ . '/images/3/thumbnail/product-test-1-01867bee8967216c2bbfa972987c9417_142.jpg', __DIR__ . '/../../../../../../web/uploads/images/product/3/product-test-1-01867bee8967216c2bbfa972987c9417_142.jpg');
        copy(__DIR__ . '/images/3/thumbnail/product-test-1-01867bee8967216c2bbfa972987c9417_260.jpg', __DIR__ . '/../../../../../../web/uploads/images/product/3/product-test-1-01867bee8967216c2bbfa972987c9417_260.jpg');
        copy(__DIR__ . '/images/3/thumbnail/product-test-1-01867bee8967216c2bbfa972987c9417_380.jpg', __DIR__ . '/../../../../../../web/uploads/images/product/3/product-test-1-01867bee8967216c2bbfa972987c9417_380.jpg');
        copy(__DIR__ . '/images/3/thumbnail/product-test-1-01867bee8967216c2bbfa972987c9417_400.jpg', __DIR__ . '/../../../../../../web/uploads/images/product/3/product-test-1-01867bee8967216c2bbfa972987c9417_400.jpg');
        $image3 = new Image();
        $image3->setPath('uploads/images/product/3/product-test-1-01867bee8967216c2bbfa972987c9417.jpeg');
        $product3->addImage($image3);
        
        
        
        $this->getManager()->persist($product);
        $this->getManager()->persist($product2);
        $this->getManager()->persist($product3);
        $this->getManager()->flush();
        
    }

    public function getOrder()
    {
        return 3; // the order in which fixtures will be loaded
    }
}
