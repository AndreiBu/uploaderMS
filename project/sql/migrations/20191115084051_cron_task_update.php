<?php

use Phinx\Migration\AbstractMigration;

class CronTaskUpdate extends AbstractMigration
{

    public function up()
    {
        $table = $this->table('task');
        $table->addColumn('path', 'string', ['null' => true])
            ->addColumn('request', 'text', ['null' => true])
            ->addColumn('file', 'text', ['null' => true])
            ->addColumn('channel', 'string', ['null' => true, 'limit' => 100])
            ->save();

        $table = $this->table('cron_task');
        $table->addColumn('channel', 'string', ['null' => true, 'limit' => 100])
            ->save();
    }
    public function down()
    {
        $this->table('task')
            ->removeColumn('path')
            ->removeColumn('request')
            ->removeColumn('file')
            ->removeColumn('channel')
            ->save();

        $this->table('cron_task')
            ->removeColumn('channel')
            ->save();
    }
}
