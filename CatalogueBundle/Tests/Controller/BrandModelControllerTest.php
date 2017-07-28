<?php

namespace CatalogueBundle\Tests\Controller;

use CoreBundle\Tests\CoreTest;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @class  BrandModelControllerTest
 * @brief Test the  BrandModel entity
 *
 * To run the testcase:
 * @code
 * vendor/bin/phpunit -v  src/CatalogueBundle/Tests/Controller/BrandModelControllerTest.php
 * @endcode
 */
class BrandModelControllerTest  extends CoreTest
{
    /**
     * @code
     * vendor/bin/phpunit -v  --filter testBrandModel src/CatalogueBundle/Tests/Controller/BrandModelControllerTest.php
     * @endcode
     * 
     */
    public function testBrandModel()
    {
        //////////////////////////////////////////////////////////////////////////////
        // Brand ///////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////
        $uid = rand(999,9999);
        $crawler = $this->createBrand($uid);
        $brandName = 'brand '.$uid;
        $container = $this->client->getContainer();
        $manager = $container->get('doctrine')->getManager();
        $brand = $manager->getRepository('CatalogueBundle:Brand')->findOneByName($brandName);
        
        ////////////////////////////////////////////////////////////////////////////////////////
        // Model ///////////////////////////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////////////////////////////////
        $uid = rand(999,9999);
        $crawler = $this->createBrandModel($uid, $brand);

        $link = $crawler
            ->filter('a:contains("Edit")') // find all links with the text "Greet"
            ->eq(0) // select the second link in the list
            ->link()
        ;
        $crawler = $this->client->click($link);// and click it
        //Asserts
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Edit brandmodel '.$uid.'")')->count());
        
        //fill form
        $form = $crawler->selectButton('Save')->form();
        $uid = rand(999,9999);
        $form['brand_model[name]'] = 'brandmodel '.$uid;
        $form['brand_model[available]']->tick();
        $crawler = $this->client->submit($form);// submit the form
        
        //Asserts
        $this->assertTrue($this->client->getResponse() instanceof RedirectResponse);
        $crawler = $this->client->followRedirect();
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("brandmodel '.$uid.'")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Model has been successfully edited")')->count());

        ///////////////////////////////////////////////////////////////////////////////////////////
        //Click delete/////////////////////////////////////////////////////////////////////////////
        ///////////////////////////////////////////////////////////////////////////////////////////
        $form = $crawler->filter('form[id="delete-entity"]')->form();
        $crawler = $this->client->submit($form);// submit the form
        $this->assertTrue($this->client->getResponse() instanceof RedirectResponse);
        $crawler = $this->client->followRedirect();
        //Asserts
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Model has been successfully deleted")')->count());
    }
    
  
}
