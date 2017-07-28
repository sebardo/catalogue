<?php
namespace CatalogueBundle\DataFixtures\ORM;

use CoreBundle\DataFixtures\SqlScriptFixture;

/*
 * php app/console doctrine:fixtures:load --fixtures=src/CatalogueBundle/DataFixtures/ORM/LoadCatalogueData.php
 */
class LoadCatalogueData extends SqlScriptFixture
{

    public function createFixtures()
    {
        

        $this->runSqlScript('Translation.sql');

        
    }
    
    public function getOrder()
    {
        return 4; // the order in which fixtures will be loaded
    }
}
