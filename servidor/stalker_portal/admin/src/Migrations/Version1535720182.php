<?php

namespace Ministra\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
class Version1535720182 extends \Doctrine\DBAL\Migrations\AbstractMigration
{
    public function up(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
--
ALTER TABLE `video` MODIFY `rating_kinopoisk` FLOAT(7,5) DEFAULT 0;
ALTER TABLE `video` MODIFY `rating_count_kinopoisk` INTEGER UNSIGNED DEFAULT 0;
ALTER TABLE `video` MODIFY `rating_imdb` FLOAT(7,5) DEFAULT 0;
ALTER TABLE `video` MODIFY `rating_count_imdb` INTEGER UNSIGNED DEFAULT 0;
EOL
);
    }
    public function down(\Doctrine\DBAL\Schema\Schema $schema)
    {
        $this->addSql(<<<EOL
ALTER TABLE `video` MODIFY `rating_kinopoisk` VARCHAR(64);
ALTER TABLE `video` MODIFY `rating_count_kinopoisk` VARCHAR(64);
ALTER TABLE `video` MODIFY `rating_imdb` VARCHAR(64);
ALTER TABLE `video` MODIFY `rating_count_imdb` VARCHAR(64);
--
EOL
);
    }
}
