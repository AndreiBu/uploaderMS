<?php

use Phinx\Migration\AbstractMigration;

class CrontaskStatusIndex extends AbstractMigration
{

    public function up()
    {
        $table = $this->table('cron_task');
        $table->addIndex('status')
            ->save();

    }

    public function down()
    {
        $this->table('cron_task')
            ->removeIndex('status')
            ->save();
    }
}
