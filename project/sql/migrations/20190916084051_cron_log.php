<?php

use Phinx\Migration\AbstractMigration;

class CronLog extends AbstractMigration
{

    public function up()
    {
        $table = $this->table('cron_log');
        $table->addColumn('created', 'datetime')
            ->addColumn('text', 'text', ['null' => true])
            ->create();
    }
    public function down()
    {
        $this->table('cron_log')->drop()->save();
    }
}
