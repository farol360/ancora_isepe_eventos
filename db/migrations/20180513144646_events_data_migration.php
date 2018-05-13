<?php

use Phinx\Migration\AbstractMigration;

class EventsDataMigration extends AbstractMigration
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
        $events = [
            [
                'name' => 'Evento Teste',
                'img_featured' => 'images/default-img.jpg',
                'id_event_type' => 1,
                'date_event' => date('Y')+2 . '-' . date('m') . '-' . date('d'),
                'description' => 'Evento para fins de testes da plataforma.',
                'price' => 'R$ 20,00',
                'status' => 1,
                'trash' => 0,
                'agree_terms' => 'Termo padrão.',
                'subscription_limit' => 20,
                'workload' => 40
            ]
        ];
        $this->insert('events', $events);

    }


public function down()
    {
        $this->execute('DELETE FROM events');
    }

}
