<?php
declare(strict_types=1);

namespace Farol360\Ancora\Model;

use Farol360\Ancora\Model;
use Farol360\Ancora\Model\Event;
use GuzzleHttp\Client;

class EventModel extends Model
{
    public function add(Event $event)
    {
        $sql = "INSERT INTO events (name,
            img_featured,
            id_event_type,
            date_event,
            date_event_description,
            description,
            price,
            status,
            trash,
            agree_terms,
            subscription_limit,
            workload
        ) VALUES (
            :name,
            :img_featured,
            :id_event_type,
            :date_event,
            :date_event_description,
            :description,
            :price,
            :status,
            :trash,
            :agree_terms,
            :subscription_limit,
            :workload)";

        $stmt = $this->db->prepare($sql);
        $parameters = [
         ':name'             => $event->name,
         ':img_featured'     => $event->img_featured,
         ':id_event_type'    => $event->id_event_type,
         ':date_event'       => $event->date_event,
         ':date_event_description' => $event->date_event_description,
         ':description'      => $event->description,
         ':price'            => $event->price,
         ':status'           => $event->status,
         ':trash'            => $event->trash,
         ':agree_terms'      => $event->agree_terms,
         ':subscription_limit'=> $event->subscription_limit,
         ':workload'         => $event->workload
        ];

        if ($stmt->execute($parameters)) {
            return $this->db->lastInsertId();
        } else {
            return null;
        }
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM events WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $parameters = [':id' => $id];
        return $stmt->execute($parameters);
    }

    public function disable(int $id): bool
    {
        $sql = "
            UPDATE
                events
            SET
                status = 0
            WHERE
                id = :id
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [
            ':id' => $id,
        ];
        return $stmt->execute($parameters);
    }

    public function disableByEventType(int $id): bool
    {
        $sql = "
            UPDATE
                events
            SET
                status = 0
            WHERE
                id_event_type = :id
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [
            ':id' => $id,
        ];
        return $stmt->execute($parameters);
    }

    public function enable(int $id): bool
    {
        $sql = "
            UPDATE
                events
            SET
                status = 1
            WHERE
                id = :id
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [
            ':id' => $id,
        ];
        return $stmt->execute($parameters);
    }
    public function get(int $id = 0)
    {

        if ($id == 0) {
            $sql = "
                SELECT
                    events.*,
                    event_types.name AS event_type,
                    event_types.id AS event_types_id
                FROM
                    events
                    LEFT JOIN event_types ON event_types.id = events.id_event_type

                LIMIT 1
            ";
            $stmt = $this->db->prepare($sql);

            $stmt->execute();
            $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, Event::class);
            return $stmt->fetch();
        } else {
           $sql = "
                SELECT
                    events.*,
                    event_types.name AS event_type,
                    event_types.id AS event_types_id
                FROM
                    events
                    LEFT JOIN event_types ON event_types.id = events.id_event_type
                WHERE
                    events.id = :id
                LIMIT 1
            ";

            $stmt = $this->db->prepare($sql);
            $parameters = [':id' => $id];
            $stmt->execute($parameters);
            $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, Event::class);
            return $stmt->fetch();
        }



    }

    // TODO TEST
    public function getAll(int $offset = 0, int $limit = PHP_INT_MAX, int $trash = 0 ): array
    {
        $sql = "
            SELECT
                events.*,
                event_types.name as event_type
            FROM
                events
                LEFT JOIN event_types ON events.id_event_type = event_types.id
            WHERE
                events.trash = ?
            ORDER BY
                events.id DESC
            LIMIT ? , ?
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, $trash, \PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, \PDO::PARAM_INT);
        $stmt->bindValue(3, $limit, \PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, Event::class);
        return $stmt->fetchAll();
    }

    // TODO TEST
    public function getAllPublished(int $offset = 0, int $limit = PHP_INT_MAX, int $trash = 0 ): array
    {
        $sql = "
            SELECT
                *
            FROM
                events
            WHERE
                trash = ?
                AND
                status = 1
            ORDER BY
                id DESC
            LIMIT ? , ?
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, $trash, \PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, \PDO::PARAM_INT);
        $stmt->bindValue(3, $limit, \PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, Event::class);
        return $stmt->fetchAll();
    }



    public function getAmount()
    {
        $sql = "
            SELECT
                COUNT(id) AS amount
            FROM
                events

        ";
        $query = $this->db->prepare($sql);
        $query->execute();
        return $query->fetch();
    }

    public function getAmountByEventType(int $id_event_type)
    {
        $sql = "
            SELECT
                COUNT(id) AS amount
            FROM
                events
            WHERE
                id_event_type = :id_event_type

        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [':id_event_type' => $id_event_type];
        $stmt->execute($parameters);
        return $stmt->fetch();
    }

    public function getAmountPublishedByEventType(int $id_event_type)
    {
        $sql = "
            SELECT
                COUNT(id) AS amount
            FROM
                events
            WHERE
                id_event_type = :id_event_type
                AND
                status = 1

        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [':id_event_type' => $id_event_type];
        $stmt->execute($parameters);
        return $stmt->fetch();
    }

    // TODO: TESTS
    public function getByEventType(int $id_event_type):array
    {
        $sql = "
            SELECT
                *
            FROM
                events
            WHERE
                id_event_type = :id_event_type

        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [':id_event_type' => $id_event_type];
        $stmt->execute($parameters);
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, Event::class);
        return $stmt->fetchAll();
    }

    public function trashRemove(int $id): bool
    {
        $sql = "
            UPDATE
                events
            SET
                trash = 0

            WHERE
                id = :id
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [
            ':id' => $id
        ];
        return $stmt->execute($parameters);
    }

    public function trashSend(int $id): bool
    {
        $sql = "
            UPDATE
                events
            SET
                trash = 1,
                status = 0
            WHERE
                id = :id
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [
            ':id' => $id
        ];
        return $stmt->execute($parameters);
    }


    public function update(Event $event): bool
    {
        $sql = "
            UPDATE
                events
            SET
                name            = :name,
                img_featured    = :img_featured,
                id_event_type   = :id_event_type,
                date_event      = :date_event,
                date_event_description = :date_event_description,
                description     = :description,
                price           = :price,
                status          = :status,
                trash           = :trash,
                agree_terms     = :agree_terms,
                subscription_limit = :subscription_limit,
                workload        = :workload
            WHERE
                id = :id
        ";
        $stmt = $this->db->prepare($sql);
        $parameters =
        [
         ':id'           => (int) $event->id,
         ':name'         => $event->name,
         ':img_featured' => $event->img_featured,
         ':id_event_type'=> $event->id_event_type,
         ':date_event'   => $event->date_event,
         ':date_event_description' => $event->date_event_description,
         ':description'  => $event->description,
         ':price'        => $event->price,
         ':status'       => $event->status,
         ':trash'        => $event->trash,
         ':agree_terms'  => $event->agree_terms,
         ':subscription_limit'  => $event->subscription_limit,
         ':workload'    => $event->workload
        ];
        return $stmt->execute($parameters);
    }
}
