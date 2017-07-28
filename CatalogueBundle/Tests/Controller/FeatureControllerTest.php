<?php

namespace CatalogueBundle\Tests\Controller;

use CoreBundle\Tests\CoreTest;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @class  FeatureControllerTest
 * @brief Test the  Feature entity
 *
 * To run the testcase:
 * @code
 * vendor/bin/phpunit -v src/CatalogueBundle/Tests/Controller/FeatureControllerTest.php
 * @endcode
 */
class FeatureControllerTest  extends CoreTest
{
    /**
     * @code
     * vendor/bin/phpunit -v --filter testFeature src/CatalogueBundle/Tests/Controller/FeatureControllerTest.php
     * @endcode
     * 
     */
    public function testFeatureAdmin()
    {
        
        //////////////////////////////////////////////////////////////////////////////
        // Category ///////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////
        $uid = rand(999,9999);
        $crawler = $this->createCategory($uid);
        
        /////////////////////////////////////////////////////////////////////////////////
        // Feature //////////////////////////////////////////////////////////////////////
        /////////////////////////////////////////////////////////////////////////////////
       
        //index
        $crawler = $this->client->request('GET', '/admin/features/', array(), array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'admin',
        ));
        //Asserts
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Features")')->count());
      
        ///////////////////////////////////////////////////////////////////////////////////////////
        //Click new ///////////////////////////////////////////////////////////////////////////////
        ///////////////////////////////////////////////////////////////////////////////////////////
        $link = $crawler
            ->filter('a:contains("Add new")') // find all links with the text "Greet"
            ->eq(0) // select the second link in the list
            ->link()
        ;
        $crawler = $this->client->click($link);// and click it
        //Asserts
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("New feature")')->count());
   
        //get entity 
        $container = $this->client->getContainer();
        $manager = $container->get('doctrine')->getManager();
        $entity = $manager->getRepository('CatalogueBundle:Category')->findOneByName('category '.$uid);
        
        //fill form
        $form = $crawler->selectButton('Save')->form();
        $uid = rand(999,9999);
        $form['feature[name]'] = 'feature '.$uid;
        $form['feature[category]']->select($entity->getId());
        $crawler = $this->client->submit($form);// submit the form
 
        //Asserts
        $this->assertTrue($this->client->getResponse() instanceof RedirectResponse);
        $crawler = $this->client->followRedirect();
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("feature '.$uid.'")')->count());

        ///////////////////////////////////////////////////////////////////////////////////////////
        //Show/////////////////////////////////////////////////////////////////////////////////////
        ///////////////////////////////////////////////////////////////////////////////////////////
        
        ///////////////////////////////////////////////////////////////////////////////////////////
        //Click edit///////////////////////////////////////////////////////////////////////////////
        ///////////////////////////////////////////////////////////////////////////////////////////
        $link = $crawler
            ->filter('a:contains("Edit")') // find all links with the text "Greet"
            ->eq(0) // select the second link in the list
            ->link()
        ;
        $crawler = $this->client->click($link);// and click it
        //Asserts
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Edit feature '.$uid.'")')->count());
        
        //fill form
        $form = $crawler->selectButton('Save')->form();
        $uid = rand(999,9999);
        $form['feature[name]'] = 'feature '.$uid;
        //$form['feature[category]']->select($entity->getId());
        $crawler = $this->client->submit($form);// submit the form
        
        //Asserts
        $this->assertTrue($this->client->getResponse() instanceof RedirectResponse);
        $crawler = $this->client->followRedirect();
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("feature '.$uid.'")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Feature has been successfully edited")')->count());

        ///////////////////////////////////////////////////////////////////////////////////////////
        //Click delete/////////////////////////////////////////////////////////////////////////////
        ///////////////////////////////////////////////////////////////////////////////////////////
        $form = $crawler->filter('form[id="delete-entity"]')->form();
        $crawler = $this->client->submit($form);// submit the form
        $this->assertTrue($this->client->getResponse() instanceof RedirectResponse);
        $crawler = $this->client->followRedirect();
        //Asserts
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Feature has been successfully deleted")')->count());
    }
  
}
