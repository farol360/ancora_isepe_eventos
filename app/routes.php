<?php
declare(strict_types=1);

// includes
use Farol360\Ancora\Controller\Admin\IndexController as IndexAdmin;
use Farol360\Ancora\Controller\Admin\PermissionController as PermissionAdmin;
use Farol360\Ancora\Controller\Admin\RoleController as RoleAdmin;
use Farol360\Ancora\Controller\Admin\UserController as UserAdmin;

use Farol360\Ancora\Controller\PageController as Page;
use Farol360\Ancora\Controller\UserController as User;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('[/]', Page::class . ':index');

$app->group('/admin', function () {
    $this->get('[/]', IndexAdmin::class . ':index');

    $this->group('/permission', function () {
        $this->get('[/]', PermissionAdmin::class . ':index');
        $this->map(['GET', 'POST'], '/add', PermissionAdmin::class . ':add');
        $this->get('/delete/{id:[0-9]+}', PermissionAdmin::class . ':delete');
        $this->get('/edit/{id:[0-9]+}', PermissionAdmin::class . ':edit');
        $this->post('/update', PermissionAdmin::class . ':update');
    });

    $this->group('/role', function () {
        $this->get('[/]', RoleAdmin::class . ':index');
        $this->map(['GET', 'POST'], '/add', RoleAdmin::class . ':add');
        $this->get('/delete/{id:[0-9]+}', RoleAdmin::class . ':delete');
        $this->get('/edit/{id:[0-9]+}', RoleAdmin::class . ':edit');
        $this->post('/update', RoleAdmin::class . ':update');
    });

    $this->get('/sobre', IndexAdmin::class . ':about');

    $this->group('/user', function () {
        $this->get('[/]', UserAdmin::class . ':index');
        $this->get('/all', UserAdmin::class . ':index');
        $this->get('/export', UserAdmin::class . ':export');
        $this->get('/{id:[0-9]+}', UserAdmin::class . ':view');
        $this->map(['GET', 'POST'], '/add', UserAdmin::class . ':add');
        $this->get('/delete/{id:[0-9]+}', UserAdmin::class . ':delete');
        $this->get('/edit/{id:[0-9]+}', UserAdmin::class . ':edit');
        $this->post('/update', UserAdmin::class . ':update');
    });
});

$app->map(['GET', 'POST'], '/contato', Page::class . ':contato');

$app->get('/obrigado', Page::class . ':contatoObrigado');

$app->group('/users', function () {
    $this->get('/dashboard', User::class . ':dashboard');
    $this->map(['GET', 'POST'], '/profile', User::class . ':profile');
    $this->map(['GET', 'POST'], '/recover', User::class . ':recover');
    $this->map(['GET', 'POST'], '/recover/token/{token}', User::class . ':recoverPassword');
    $this->map(['GET', 'POST'], '/signin', User::class . ':signIn');
    $this->get('/signout', User::class . ':signOut');
    $this->map(['GET', 'POST'], '/signup', User::class . ':signUp');
    $this->get('/verify/{token}', User::class . ':verify');

});
