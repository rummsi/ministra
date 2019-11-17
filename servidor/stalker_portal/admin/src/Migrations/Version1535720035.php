<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720035 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if ($schema->getTable('users')->hasColumn('expire_billing_date')) {
            $this->addSql('ALTER TABLE `users` DROP COLUMN `expire_billing_date`');
        }
        $this->addSql(<<<EOL
--
ALTER TABLE `users`
ADD COLUMN `expire_billing_date` TIMESTAMP NULL DEFAULT NULL;
INSERT INTO `adm_grp_action_access`
        (`controller_name`,             `action_name`,    `is_ajax`,  `description`, `hidden`)
VALUES  ('users',           'set-expire-billing-date',            1, 'Set/unset expire billing date for user',  0);
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `users` DROP COLUMN `expire_billing_date`;
--
EOL
);
    }
}
