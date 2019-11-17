<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Type;
class Version20190710145830 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $table = $schema->getTable('updates');
        $table->addColumn('history_hash', \Doctrine\DBAL\Types\Type::STRING)->setNotnull(false);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $table = $schema->getTable('updates');
        $table->dropColumn('history_hash');
    }
    public function getDescription()
    {
        return 'Add history hash';
    }
}
