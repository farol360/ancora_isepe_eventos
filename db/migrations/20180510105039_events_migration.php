<?php

use Phinx\Migration\AbstractMigration;

class EventsMigration extends AbstractMigration
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
        $events = $this->table('events');
        $events->addColumn('name', 'string');
        $events->addColumn('img_featured', 'string');
        $events->addColumn('id_event_type', 'integer');
        $events->addColumn('date_event', 'date');
        $events->addColumn('description', 'string');
        $events->addColumn('price', 'string');
        $events->addColumn('status', 'integer');
        $events->addColumn('trash', 'integer');
        $events->addColumn('agree_terms', 'text');
        $events->addColumn('subscription_limit', 'integer');
        $events->addColumn('workload', 'integer');
        $events->addTimestamps();
        $events->create();
    }
}
