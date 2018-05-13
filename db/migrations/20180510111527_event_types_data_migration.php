<?php

use Phinx\Migration\AbstractMigration;

class EventTypesDataMigration extends AbstractMigration
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
    public function up()
    {
        $event_types = [
            [
                'id' => 1,
                'name' => 'Categoria Padrão',
                'description' => 'Favor alterar esta categoria nas configurações da plataforma.',
                'agree_terms' => 'Termo padrão.',
                'status' => 1,
                'trash' => 0
            ]
        ];
        $this->insert('event_types', $event_types);

    }
}
