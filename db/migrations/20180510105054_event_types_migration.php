<?php

use Phinx\Migration\AbstractMigration;

class EventTypesMigration extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $events = $this->table('event_types');
        $events->addColumn('name', 'string');
        $events->addColumn('description', 'string');
        $events->addColumn('status', 'integer');
        $events->addColumn('trash', 'integer');
        $events->addColumn('agree_terms', 'text');
        $events->addTimestamps();
        $events->create();
    }
}
