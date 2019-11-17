<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535719972 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('services_package')->hasColumn('service_type')) {
            $this->addSql("ALTER TABLE `services_package` ADD `service_type` VARCHAR(32) default 'periodic';");
        }
        if (!$schema->getTable('video')->hasColumn('rating_mpaa')) {
            $this->addSql("ALTER TABLE `video` ADD `rating_mpaa` VARCHAR(32) default '';");
        }
        if (!$schema->getTable('video')->hasColumn('age')) {
            $this->addSql("ALTER TABLE `video` ADD `age` VARCHAR(32) default '';");
        }
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `video` DROP `age`;
ALTER TABLE `video` DROP `rating_mpaa`;
ALTER TABLE `services_package` DROP `service_type`;
--
EOL
);
    }
}
