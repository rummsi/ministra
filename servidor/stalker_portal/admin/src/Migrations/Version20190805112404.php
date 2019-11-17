<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version20190805112404 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('stb_in_group')->hasIndex('uid_idx')) {
            $schema->getTable('stb_in_group')->addUniqueIndex(['uid'], 'uid_idx');
        }
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if ($schema->getTable('stb_in_group')->hasIndex('uid_idx')) {
            $schema->getTable('stb_in_group')->dropIndex('uid_idx');
        }
    }
    public function getDescription()
    {
        return 'Add uid index for stb_in_group';
    }
}
