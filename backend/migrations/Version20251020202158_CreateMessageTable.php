<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251020202158_CreateMessageTable extends AbstractMigration {
  public function up(Schema $schema): void {
    $this->addSql('CREATE TABLE messages (
                    id UUID NOT NULL,
                    user_id UUID NOT NULL,
                    content TEXT NOT NULL,
                    created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    status VARCHAR(50) NOT NULL DEFAULT \'SENT\',
                    PRIMARY KEY(id))');

    $this->addSql('ALTER TABLE messages
                   ADD CONSTRAINT FK_MESSAGES_USER FOREIGN KEY (user_id)
                   REFERENCES "user" (id)
                   ON DELETE CASCADE');

    $this->addSql('CREATE INDEX IDX_MESSAGES_user_id ON messages (user_id)');
    $this->addSql('CREATE INDEX IDX_MESSAGES_CREATED_AT ON messages (created_at)');

    $this->addSql('COMMENT ON COLUMN messages.id IS \'(DC2Type:uuid)\'');
    $this->addSql('COMMENT ON COLUMN messages.user_id IS \'(DC2Type:uuid)\'');
//    $this->addSql('COMMENT ON COLUMN messages.status IS \'(DC2Type:message_status)\'');
  }

  public function down(Schema $schema): void {
    $this->addSql('DROP TABLE IF EXISTS messages');
  }
}
