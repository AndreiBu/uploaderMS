<?php

use Phinx\Migration\AbstractMigration;

class CronTask extends AbstractMigration
{

    public function up()
    {
        $table = $this->table('cron_task');
        $table->addColumn('date_cr', 'datetime')
            ->addColumn('task_id', 'string', ['null' => true, 'limit' => 255])
            ->addColumn('path', 'string', ['null' => true, 'limit' => 255])
            ->addColumn('size', 'integer', ['default' => 0])
            ->addColumn('status', 'integer', ['default' => 0])
            ->addColumn('type', 'string', ['null' => true, 'limit' => 20])
            ->addColumn('task', 'text', ['null' => true])
            ->addColumn('duration', 'integer', ['default' => 0])
            ->addIndex('date_cr')
            ->addIndex('size')
            ->create();

        $table = $this->table('task');
        $table->addColumn('date_cr', 'datetime')
            ->addColumn('status', 'integer', ['default' => 0])
            ->addColumn('user_id', 'integer', ['null' => true])
            ->addColumn('item_id', 'integer', ['null' => true])
            ->addColumn('session_id', 'string', ['null' => true, 'limit' => 255])
            ->addColumn('file_name', 'string', ['null' => true, 'limit' => 255])
            ->addColumn('size', 'integer', ['default' => 0])
            ->addColumn('extension', 'string', ['null' => true, 'limit' => 10])
            ->addColumn('webhook', 'string', ['null' => true, 'limit' => 500])
            ->addIndex('status')
            ->create();
    }
    public function down()
    {
        $this->table('cron_task')->drop()->save();
        $this->table('task')->drop()->save();
    }
}
