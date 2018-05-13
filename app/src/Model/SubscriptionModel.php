<?php
declare(strict_types=1);

namespace Farol360\Ancora\Model;

use Farol360\Ancora\Model;
use Farol360\Ancora\Model\Subscription;
use GuzzleHttp\Client;

class SubscriptionModel extends Model
{
    public function activate(int $subscriptionId)
    {
         $sql = "
            UPDATE
                subscriptions
            SET
                payd = 1
            WHERE
                id = :id
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [
            ':id' => $subscriptionId,
        ];
        return $stmt->execute($parameters);
    }

    public function add(Subscription $subscription)
    {
        $sql = "INSERT INTO subscriptions (
                    id_user,
                    id_event,
                    payd,
                    workload,
                    is_certificate,
                    img_certificate,
                    code_certificate,
                    date_certificate)

                VALUES (
                    :id_user,
                    :id_event,
                    :payd,
                    :workload,
                    :is_certificate,
                    :img_certificate,
                    :code_certificate,
                    :date_certificate)";

        $stmt = $this->db->prepare($sql);
        $parameters = [
            ':id_user' => $subscription->id_user,
            ':id_event' => $subscription->id_event,
            ':payd' => $subscription->payd,
            ':workload' => $subscription->workload,
            ':is_certificate' => $subscription->is_certificate,
            ':img_certificate' => $subscription->img_certificate,
            ':code_certificate' => $subscription->code_certificate,
            ':date_certificate' => $subscription->date_certificate

        ];

        if ($stmt->execute($parameters)) {
            return $this->db->lastInsertId();
        } else {
            return null;
        }
    }


    public function delete(int $id): bool
    {
        $sql = "DELETE FROM subscriptions WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $parameters = [':id' => $id];
        return $stmt->execute($parameters);
    }

    public function deactivate(int $subscriptionId)
    {
         $sql = "
            UPDATE
                subscriptions
            SET
                payd = 2
            WHERE
                id = :id
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [
            ':id' => $subscriptionId,
        ];
        return $stmt->execute($parameters);
    }


    public function get(int $id)
    {
        $sql = "
            SELECT
                *
            FROM
                subscriptions
            WHERE
                id = :id
            LIMIT 1
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [':id' => $id];
        $stmt->execute($parameters);
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, Subscription::class);
        return $stmt->fetch();
    }

    // TODO TEST
    public function getAll(int $offset = 0, int $limit = PHP_INT_MAX): array
    {
        $sql = "
            SELECT
                subscriptions.*,
                events.name as event_name,
                events.price as event_price,
                users.name as user_name
            FROM
                subscriptions
                LEFT JOIN events ON subscriptions.id_event = events.id
                LEFT JOIN users ON subscriptions.id_user = users.id
            ORDER BY
                subscriptions.id DESC
            LIMIT ? , ?
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, $offset, \PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, \PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, Subscription::class);
        return $stmt->fetchAll();
    }

    // TODO TEST
    public function getAllByEvent(int $eventId, int $offset = 0, int $limit = PHP_INT_MAX): array
    {
        $sql = "
            SELECT
                subscriptions.*,
                events.name as event_name,
                events.price as event_price,
                users.name as user_name
            FROM
                subscriptions
                LEFT JOIN events ON subscriptions.id_event = events.id
                LEFT JOIN users ON subscriptions.id_user = users.id
            WHERE
                id_event = ?
            ORDER BY
                subscriptions.id DESC
            LIMIT ? , ?
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, $eventId, \PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, \PDO::PARAM_INT);
        $stmt->bindValue(3, $limit, \PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, Subscription::class);
        return $stmt->fetchAll();
    }

    public function getAmount()
    {
        $sql = "
            SELECT
                COUNT(id) AS amount
            FROM
                subscriptions

        ";
        $query = $this->db->prepare($sql);
        $query->execute();
        return $query->fetch();
    }

    public function getAmountPaydByEvent(int $idEvent) {
        $sql = "
            SELECT
                COUNT(id) AS amount
            FROM
                subscriptions

            WHERE
                id_event = ?
            AND
                payd = 1

        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, $idEvent, \PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, Subscription::class);
        return $stmt->fetch();
    }

    public function getByUser(int $idUser) {
        $sql = "
            SELECT
                subscriptions.*,
                events.name as event_name
            FROM
                subscriptions
                LEFT JOIN events ON subscriptions.id_event = events.id
            WHERE
                id_user = ?
            ORDER BY id DESC

        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, $idUser, \PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, Subscription::class);
        return $stmt->fetchAll();
    }

    public function getPayd(int $offset = 0, int $limit = PHP_INT_MAX): array
    {
        $sql = "
            SELECT
                *
            FROM
                subscriptions
            WHERE
                payd = 1
            ORDER BY
                id DESC
            LIMIT ? , ?
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, $offset, \PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, \PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, Subscription::class);
        return $stmt->fetchAll();
    }

    public function getUserSubscription(int $idUser,int $idEvent) {
         $sql = "
            SELECT
                *
            FROM
                subscriptions
            WHERE
                id_user = ? AND id_event = ?

        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, $idUser, \PDO::PARAM_INT);
        $stmt->bindValue(2, $idEvent, \PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, Subscription::class);
        return $stmt->fetch();
    }

    public function update(Subscription $subscription): bool
    {
        $sql = "
            UPDATE
                subscriptions
            SET
                id_user = :id_user,
                id_event = :id_event,
                payd = :payd,
                workload = :workload,
                is_certificate = :is_certificate,
                img_certificate = :img_certificate,
                code_certificate = :code_certificate,
                date_certificate = :date_certificate
            WHERE
                id = :id
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [
          ':id'              => $subscription->id,
          ':id_user'         => $subscription->id_user,
          ':id_event'        => $subscription->id_event,
          ':payd'         => $subscription->payd,
          ':workload'     => $subscription->workload,
          ':is_certificate' => $subscription->is_certificate,
          ':img_certificate' => $subscription->img_certificate,
          ':code_certificate' => $subscription->code_certificate,
          ':date_certificate' => $subscription->date_certificate
        ];
        return $stmt->execute($parameters);
    }

    public function open(int $subscriptionId)
    {
         $sql = "
            UPDATE
                subscriptions
            SET
                payd = 0
            WHERE
                id = :id
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [
            ':id' => $subscriptionId,
        ];
        return $stmt->execute($parameters);
    }
}
