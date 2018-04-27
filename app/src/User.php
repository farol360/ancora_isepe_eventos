<?php
declare(strict_types=1);

namespace Farol360\Ancora;

use Farol360\Ancora\Mailer;
use Farol360\Ancora\Model\UserModel;
use Psr\Container\ContainerInterface as Container;

class User
{
    protected static $db;
    protected static $userModel;

    public static function getPermissionsValue()
    {
        $return = [];
        $permissionClass = new Permission(self::$db);
        $permissions = $permissionClass->getAll();

        foreach ($permissions as $permission) {
            $return[
                'p_' .
                str_replace('/', '', substr($permission->resource, 0, 1)) .
                str_replace('/', '_', substr(
                    $permission->resource,
                    1,
                    strlen($permission->resource) - 2
                )) .
                str_replace('/', '', substr(
                    $permission->resource,
                    strlen($permission->resource) - 1,
                    1
                ))] = self::hasPermission($permission->resource);
        }

        return $return;
    }

    public static function getEmail()
    {
        if (self::isAuth()) {
            return $_SESSION["user"]["email"];
        }
        return '';
    }

    public static function getName()
    {
        if (self::isAuth()) {
            return $_SESSION["user"]["name"];
        }
        return '';
    }

    public static function getUserRole()
    {
        if (!isset($_SESSION["user"])) {
            $sql = "
                SELECT
                    id
                FROM
                    roles
                WHERE
                    roles.name = 'guest'
            ";
            $query = self::$db->prepare($sql);
            $query->execute();
            return $query->fetch()->id;
        }
        $sql = "
            SELECT
                role_id
            FROM
                users
            WHERE
                users.id = :id
        ";
        $query = self::$db->prepare($sql);
        $query->execute([':id' => $_SESSION["user"]["id"]]);
        return $query->fetch()->role_id;
    }

    public static function hasPermission($permission)
    {
        if (!$permission) {
            return false;
        }
        $role = new Role(self::$db);
        return $role->hasPermission($permission, self::getUserRole());
    }

    public static function isAuth()
    {
        if (isset($_SESSION["user"])) {
            return true;
        }
        return false;
    }

    public static function login($email, $password)
    {
        self::logout();
        $user = self::$userModel->get(null, $email);


        if (!empty($user) && (hash_equals($user->password, crypt($password, $user->password)))) {
            $_SESSION["user"] = (array) $user;

            $sessionId = session_id();
            self::setSessionId($sessionId);
            $_SESSION["user"]["session_id"] = $sessionId;

            return true;
        }
        return false;
    }

    public static function logout()
    {
        unset($_SESSION["user"]);
    }

    public static function setSessionId($sessionId)
    {
        $userId = self::$userModel->get()->id;

        $sql = "
            UPDATE
                users
            SET
                session = :session_id
            WHERE
                id = :id
        ";
        $query = self::$db->prepare($sql);
        $parameters = [
            ':session_id' => $sessionId,
            ':id' => $userId
        ];
        return $query->execute($parameters);
    }

    public static function setupUser(Container $container)
    {
        self::$db = $container->db;
        self::$userModel = new UserModel(self::$db, $container->get('settings')['api']['baseUrl']);
    }

    public static function sendRecover(Mailer $mailer, string $email)
    {
        $sql = "
            UPDATE users SET recover_token = :recover WHERE email = :email
        ";
        $token = bin2hex(random_bytes(16));
        $query = self::$db->prepare($sql);
        $query->execute([
            ':recover' => "!!" . $token,
            ':email' => $email
        ]);
        $user = self::$userModel->get(null, $email);
        if (!$user) {
            return false;
        }
        return $mailer->send(
            $user->name,
            $user->email,
            'Recuperar Senha',
            "<meta charset=\"UTF-8\"><h1>Âncora EAD</h1><a href=\"http://"
                . $_SERVER['SERVER_NAME']
                . "/users/recover/token/!!$token\">Clique aqui</a> para recuperar sua senha no sistema.<br>
            <p>Âncora EAD</p><br><br>"
        );
    }

    public static function sendVerification(Mailer $mailer, string $email)
    {
        $user = self::$userModel->get(null, $email);
        if (!$user) {
            return false;
        }
        $sql = "
            UPDATE users
            SET verification_token = :token
            WHERE email = :email
        ";
        $token = bin2hex(random_bytes(16));
        $query = self::$db->prepare($sql);
        $query->execute([
            ':token' => "!!" . $token,
            ':email' => $email
        ]);
        return $mailer->send(
            $user->name,
            $user->email,
            'Verifique sua conta',
            "<meta charset=\"UTF-8\">
            <h1>Âncora EAD</h1>
            <h3>Muito obrigado por se cadastrar em nosso site!</h3>
            <a href=\"http://"
                . $_SERVER['SERVER_NAME']
                . "/users/verify/!!$token\">Clique aqui</a> para verificar sua conta no sistema.<br>

            <h3>Você terá acesso a:</h3>
            <ul>
                <li>Acesso a plataforma digital de cursos; </li>
                <li>Acesso a conteúdo exclusivo; </li>
                <li>Acesso a conteúdos promocionais e gratuitos. </li>
            </ul>
            <p>Âncora EAD</p><br><br>"
        );
    }

    public static function getUserByToken($token)
    {
        $sql = "
            SELECT
                users.id
            FROM
                users
            WHERE
                recover_token = :token OR
                verification_token = :token
        ";
        $query = self::$db->prepare($sql);
        $query->execute([':token' => $token]);
        $result = $query->fetch();
        if (!$result) {
            return false;
        }
        $userId = $result->id;
        return self::$userModel->get((int)$userId);
    }
}
