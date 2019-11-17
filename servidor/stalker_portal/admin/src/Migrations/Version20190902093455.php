<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version20190902093455 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $table = $schema->getTable('smac_certificates');
        $table->addIndex(['uid'], 'smac_certificates_uid_idx');
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $table = $schema->getTable('smac_certificates');
        $table->dropIndex('smac_certificates_uid_idx');
    }
    public function getDescription()
    {
        return 'Add history hash';
    }
}
