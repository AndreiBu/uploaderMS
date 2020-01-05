<?php

use Phinx\Migration\AbstractMigration;

class CronUpdate extends AbstractMigration
{

    public function up()
    {
        $table = $this->table('cron_task');
        $table->addColumn('message', 'string', ['null' => true, 'limit' => 255])
            ->addColumn('date_up', 'datetime', ['null' => true])
            ->save();

    }

    public function down()
    {
        $this->table('cron_task')
            ->removeColumn('date_up')
            ->save();
    }
}
