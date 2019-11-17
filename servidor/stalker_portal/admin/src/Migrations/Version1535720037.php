<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720037 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        if ($schema->getTable('epg_setting')->hasColumn('lang_code')) {
            $this->addSql('ALTER TABLE `epg_setting` DROP COLUMN `lang_code`');
        }
        $this->addSql(<<<EOL
--
ALTER TABLE `epg_setting`
ADD COLUMN `lang_code` VARCHAR(20) NULL DEFAULT NULL AFTER `status`;
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `epg_setting` DROP COLUMN `lang_code`;
--
EOL
);
    }
}
