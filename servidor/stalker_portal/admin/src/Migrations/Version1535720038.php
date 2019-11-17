<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720038 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if ($schema->getTable('radio')->hasColumn('enable_monitoring`, DROP COLUMN `monitoring_status`, DROP COLUMN `monitoring_status_updated')) {
            $this->addSql('ALTER TABLE `radio` DROP COLUMN `enable_monitoring`, DROP COLUMN `monitoring_status`, DROP COLUMN `monitoring_status_updated`');
        }
        $this->addSql(<<<EOL
--
ALTER TABLE `radio`
ADD COLUMN `enable_monitoring` TINYINT NOT NULL DEFAULT 0,
ADD COLUMN `monitoring_status` TINYINT NOT NULL DEFAULT 1,
ADD COLUMN `monitoring_status_updated` DATETIME NULL;
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `radio` DROP COLUMN `enable_monitoring`, DROP COLUMN `monitoring_status`, DROP COLUMN `monitoring_status_updated`;
--
EOL
);
    }
}
