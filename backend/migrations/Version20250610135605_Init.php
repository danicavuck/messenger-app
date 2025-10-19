<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250610135605_Init extends AbstractMigration {

    /**
     * @param Schema $schema
     *
     * @return void
     */
    public function up(Schema $schema): void {
        $this->addSql("CREATE EXTENSION IF NOT EXISTS \"uuid-ossp\"");
        $this->addSql("CREATE EXTENSION IF NOT EXISTS \"pg_trgm\"");
        $this->addSql("SET datestyle to ISO, YMD;");
    }

    /**
     * @param Schema $schema
     *
     * @return void
     */
    public function down(Schema $schema): void {
        $this->addSql("DROP EXTENSION IF EXISTS \"uuid-ossp\"");
        $this->addSql("DROP EXTENSION IF EXISTS \"pg_trgm\"");
    }
}
