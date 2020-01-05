<?php

use Phinx\Migration\AbstractMigration;

class TaskAddDomain extends AbstractMigration
{

    public function up()
    {
        $table = $this->table('task');
        $table->addColumn('domain', 'string', ['null' => true, 'limit' => 255])
            ->save();

    }

    public function down()
    {
        $this->table('task')
            ->removeColumn('domain')
            ->save();
    }
}
