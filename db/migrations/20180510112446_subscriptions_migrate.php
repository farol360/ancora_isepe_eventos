<?php

use Phinx\Migration\AbstractMigration;

class SubscriptionsMigrate extends AbstractMigration
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
        $subscriptions = $this->table('subscriptions');
        $subscriptions->addColumn('id_user', 'integer');
        $subscriptions->addColumn('id_event', 'integer');
        $subscriptions->addColumn('payd', 'integer');
        $subscriptions->addColumn('workload', 'integer',['null' => true]);
        $subscriptions->addColumn('is_certificate', 'integer');
        $subscriptions->addColumn('img_certificate', 'string', ['null' => true]);
        $subscriptions->addColumn('code_certificate', 'string', ['null' => true]);
        $subscriptions->addColumn('date_certificate', 'date', ['null' => true]);
        $subscriptions->addTimestamps();
        $subscriptions->create();
    }
}
