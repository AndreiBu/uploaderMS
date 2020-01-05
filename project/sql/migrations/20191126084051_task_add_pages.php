<?php

use Phinx\Migration\AbstractMigration;

class TaskAddPages extends AbstractMigration
{

    public function up()
    {
        $table = $this->table('task');
        $table->addColumn('pages', 'integer', ['default' => 0])
            ->save();

    }

    public function down()
    {
        $this->table('task')
            ->removeColumn('pages')
            ->save();
    }
}
