<?php
declare(strict_types=1);

namespace Farol360\Ancora\Model;

// business objects
use Farol360\Ancora\Model\Event;
use Farol360\Ancora\Model\EventType;
use Farol360\Ancora\Model\Subscription;

// Ancora objects
use Farol360\Ancora\Model\Permission;
use Farol360\Ancora\Model\Role;
use Farol360\Ancora\Model\User;

class EntityFactory
{
    public function createEvent(array $data = []): Event
    {
        return new Event($data);
    }

    public function createEventType(array $data = []): EventType
    {
        return new EventType($data);
    }

    // new objects..


    public function createSubscription(array $data = []): Subscription
    {
        return new Subscription($data);
    }

    // permission, usuários e papéis de usuários
    public function createPermission(array $data = []): Permission
    {
        return new Permission($data);
    }

    public function createRole(array $data = []): Role
    {
        return new Role($data);
    }

    public function createUser(array $data = []): User
    {
        return new User($data);
    }
}
