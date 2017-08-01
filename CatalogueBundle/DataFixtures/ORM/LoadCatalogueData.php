<?php
namespace CatalogueBundle\DataFixtures\ORM;

use CoreBundle\DataFixtures\SqlScriptFixture;

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
        if($this->container->getParameter('core.fixture_bundle')){
            $this->runSqlScript('Translation.sql');
        }
        
        /**
         * Dev fixtures
         */
        if($this->container->getParameter('core.fixtures_dev')){
            
        }
        

        
    }
    
    public function getOrder()
    {
        return 4; // the order in which fixtures will be loaded
    }
}
