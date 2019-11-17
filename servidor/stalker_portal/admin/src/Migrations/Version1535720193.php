<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720193 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
--
ALTER TABLE `video_season` MODIFY `season_number` SMALLINT NOT NULL;
ALTER TABLE `video_season` MODIFY `season_series` SMALLINT NOT NULL DEFAULT 0;
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `video_season` MODIFY `season_series` TINYINT NOT NULL DEFAULT 0;
ALTER TABLE `video_season` MODIFY `season_number` TINYINT NOT NULL;
--
EOL
);
    }
}
