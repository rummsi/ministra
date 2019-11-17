<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720144 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if (!$schema->getTable('users')->hasColumn('tariff_id_instead_expired')) {
            $this->addSql('ALTER TABLE `users` ADD COLUMN `tariff_id_instead_expired` INT NULL;');
        }
        if (!$schema->getTable('users')->hasColumn('tariff_expired_date')) {
            $this->addSql('ALTER TABLE `users` ADD COLUMN `tariff_expired_date` TIMESTAMP NULL DEFAULT NULL;');
        }
        if (!$schema->getTable('tariff_plan')->hasColumn('days_to_expires')) {
            $this->addSql('ALTER TABLE `tariff_plan` ADD COLUMN `days_to_expires` SMALLINT(3) NULL DEFAULT 0;');
        }
        if (!$schema->getTable('messages_templates')->hasColumn('url')) {
            $this->addSql('ALTER TABLE `messages_templates` ADD COLUMN `url` VARCHAR(512) NULL DEFAULT NULL;');
        }
        $this->addSql(<<<EOL
--
CREATE TABLE `tariffs_notifications` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `tariff_id` INT NOT NULL,
  `notification_delay_in_hours`  SMALLINT(4) NULL DEFAULT 0,
  `template_id` INT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET = UTF8;
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
DROP TABLE `tariffs_notifications`;
ALTER TABLE `messages_templates` DROP COLUMN `url`;
ALTER TABLE `tariff_plan` DROP COLUMN `days_to_expires`;
ALTER TABLE `users` DROP COLUMN `tariff_expired_date`;
ALTER TABLE `users` DROP COLUMN `tariff_id_instead_expired`;
--
EOL
);
    }
}
