<?php
declare(strict_types=1);

namespace Farol360\Ancora\Model;

use Farol360\Ancora\Model;
use Farol360\Ancora\Model\EventType;
use GuzzleHttp\Client;

class EventTypeModel extends Model
{
    public function add(EventType $eventType)
    {
        $sql = "INSERT INTO event_types (name, description, agree_terms, status, trash) VALUES (:name, :description, :agree_terms, :status, :trash)";

        $stmt = $this->db->prepare($sql);
        $parameters = [
            ':name' => $eventType->name,
            ':description' => $eventType->description,
            ':agree_terms' => $eventType->agree_terms,
            ':status' => $eventType->status,
            ':trash' => $eventType->trash

        ];

        if ($stmt->execute($parameters)) {
            return $this->db->lastInsertId();
        } else {
            return null;
        }
    }


    public function delete(int $id): bool
    {
        $sql = "DELETE FROM event_types WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $parameters = [':id' => $id];
        return $stmt->execute($parameters);
    }

    public function disable(int $id): bool
    {
        $sql = "
            UPDATE
                event_types
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

    public function enable(int $id): bool
    {
        $sql = "
            UPDATE
                event_types
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

    public function get(int $id)
    {
        $sql = "
            SELECT
                *
            FROM
                event_types
            WHERE
                id = :id
            LIMIT 1
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [':id' => $id];
        $stmt->execute($parameters);
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, EventType::class);
        return $stmt->fetch();
    }

    // TODO TEST
    public function getAll(int $offset = 0, int $limit = PHP_INT_MAX): array
    {
         $sql = "
            SELECT
                *
            FROM
                event_types
            ORDER BY
                id DESC
            LIMIT ? , ?
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, $offset, \PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, \PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, EventType::class);
        return $stmt->fetchAll();
    }

    public function getAmount()
    {
        $sql = "
            SELECT
                COUNT(id) AS amount
            FROM
                event_types

        ";
        $query = $this->db->prepare($sql);
        $query->execute();
        return $query->fetch();
    }

    public function getPublished(int $offset = 0, int $limit = PHP_INT_MAX): array
    {
         $sql = "
            SELECT
                *
            FROM
                event_types
            WHERE
                status = 1
            ORDER BY
                id DESC
            LIMIT ? , ?
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, $offset, \PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, \PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, EventType::class);
        return $stmt->fetchAll();
    }


    public function trashRemove(int $id): bool
    {
        $sql = "
            UPDATE
                event_types
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
                event_types
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
    public function update(EventType $eventType): bool
    {
        $sql = "
            UPDATE
                event_types
            SET
                name = :name,
                description = :description,
                agree_terms = :agree_terms,
                status = :status,
                trash = :trash
            WHERE
                id = :id
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [
            ':id'           => $eventType->id,
            ':name'         => $eventType->name,
            ':description'  => $eventType->description,
            ':agree_terms'  => $eventType->agree_terms,
            ':status'       => $eventType->status,
            ':trash'        => $eventType->trash
        ];
        return $stmt->execute($parameters);
    }
}
