<?php

namespace CatalogueBundle\Tests\Controller;

use CoreBundle\Tests\CoreTest;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @class  CategoryControllerTest
 * @brief Test the  Category entity
 *
 * To run the testcase:
 * @code
 * vendor/bin/phpunit -v src/CatalogueBundle/Tests/Controller/CategoryControllerTest.php
 * @endcode
 */
class CategoryControllerTest  extends CoreTest
{
    /**
     * @code
     * vendor/bin/phpunit -v --filter testCategoryAdmin src/CatalogueBundle/Tests/Controller/CategoryControllerTest.php
     * @endcode
     * 
     */
    public function testCategoryAdmin()
    {
        //////////////////////////////////////////////////////////////////////////////
        // Category///////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////
        $uid = rand(999,9999);
        $crawler = $this->createCategory($uid);

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
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Edit category '.$uid.'")')->count());
        
        //fill form
        $form = $crawler->selectButton('Save')->form();
        $uid = rand(999,9999);
        $form['category[name]'] = 'category '.$uid;
        $form['category[description]'] = 'category description'.$uid;
        $form['category[metaTitle]'] = 'Meta title_'.$uid;
        $form['category[metaDescription]'] = 'Meta description_'.$uid;
        $form['category[active]']->tick();
        $crawler = $this->client->submit($form);// submit the form
        
        //Asserts
        $this->assertTrue($this->client->getResponse() instanceof RedirectResponse);
        $crawler = $this->client->followRedirect();
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("category '.$uid.'")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("The category has been successfully edited")')->count());

        ///////////////////////////////////////////////////////////////////////////////////////////
        //Click delete/////////////////////////////////////////////////////////////////////////////
        ///////////////////////////////////////////////////////////////////////////////////////////
        $form = $crawler->filter('form[id="delete-entity"]')->form();
        $crawler = $this->client->submit($form);// submit the form
        $this->assertTrue($this->client->getResponse() instanceof RedirectResponse);
        $crawler = $this->client->followRedirect();
        //Asserts
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("The category has been successfully deleted")')->count());
    }
    
    
}
