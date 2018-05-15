<?php
declare(strict_types=1);

// includes

use Farol360\Ancora\Controller\Admin\EventsController as EventsAdmin;
use Farol360\Ancora\Controller\Admin\EventTypesController as EventTypesAdmin;
use Farol360\Ancora\Controller\Admin\SubscriptionsController as SubscriptionsAdmin;
use Farol360\Ancora\Controller\Admin\TrashController as TrashAdmin;

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

    $this->group('/attendances', function() {
        $this->get('[/]', SubscriptionsAdmin::class . ':attendances');
        $this->get('/{id:[0-9]+}', SubscriptionsAdmin::class . ':attendances');
        $this->get('/activate/{id:[0-9]+}', SubscriptionsAdmin::class . ':activate');
        $this->get('/deactivate/{id:[0-9]+}', SubscriptionsAdmin::class . ':deactivate');
        $this->get('/open/{id:[0-9]+}', SubscriptionsAdmin::class . ':open');
        $this->get('/export/{id:[0-9]+}', SubscriptionsAdmin::class . ':export');
        $this->map(['GET', 'POST'],'/import/{id:[0-9]+}', SubscriptionsAdmin::class . ':import');
        $this->post('/update/{id:[0-9]+}', SubscriptionsAdmin::class . ':update');
    });

    $this->group('/certificates', function() {
        $this->get('[/]', EventsAdmin::class . ':certificates' );
    });

    $this->group('/events', function() {
        $this->get('[/]', EventsAdmin::class . ':index' );

        $this->map(['GET', 'POST'], '/add', EventsAdmin::class . ':add' );
        $this->get('/disable/{id:[0-9]+}', EventsAdmin::class . ':disable');
        $this->get('/enable/{id:[0-9]+}', EventsAdmin::class . ':enable');
        $this->get('/edit/{id:[0-9]+}', EventsAdmin::class . ':edit' );
        $this->map(['GET', 'POST'], '/update', EventsAdmin::class . ':update' );
        $this->get('/trash/remove/{id:[0-9]+}', EventsAdmin::class . ':trashRemove');
        $this->get('/trash/send/{id:[0-9]+}', EventsAdmin::class . ':trashSend');

    });

    $this->group('/event_types', function () {
        $this->get('[/]', EventTypesAdmin::class . ':index' );
        $this->map(['GET', 'POST'], '/add', EventTypesAdmin::class . ':add' );
        $this->get('/disable/{id:[0-9]+}', EventTypesAdmin::class . ':disable');
        $this->get('/enable/{id:[0-9]+}', EventTypesAdmin::class . ':enable');
        $this->get('/edit/{id:[0-9]+}', EventTypesAdmin::class . ':edit' );
        $this->get('/get_terms/{id:[0-9]+}', EventTypesAdmin::class . ':getTerms' );
        $this->map(['GET', 'POST'], '/update', EventTypesAdmin::class . ':update' );

        $this->get('/remove/{id:[0-9]+}', EventTypesAdmin::class . ':remove' );
        // events type dnt implements trash feature
        //$this->get('/trash/remove/{id:[0-9]+}', EventsTypeAdmin::class . ':trashRemove');
        //$this->get('/trash/send/{id:[0-9]+}', EventsTypeAdmin::class . ':trashSend');

        $this->map(['GET', 'POST'], '/verifytoremove/{id:[0-9]+}', EventTypesAdmin::class . ':verifytoremove' );
        $this->map(['GET', 'POST'], '/verifytounpublish/{id:[0-9]+}', EventTypesAdmin::class . ':verifytounpublish' );
    });

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

    $this->group('/subscriptions', function() {
        $this->get('[/]', SubscriptionsAdmin::class . ':index');
        $this->get('/{id:[0-9]+}', SubscriptionsAdmin::class . ':index');
        $this->get('/activate/{id:[0-9]+}', SubscriptionsAdmin::class . ':activate');
        $this->get('/deactivate/{id:[0-9]+}', SubscriptionsAdmin::class . ':deactivate');
        $this->get('/open/{id:[0-9]+}', SubscriptionsAdmin::class . ':open');
    });

    /**
        Trash index route
    */
    $this->get('/trash', TrashAdmin::class . ':index');

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

$app->group('/eventos', function () {
    $this->get('[/]', Page::class . ':events');
    $this->get('/{id:[0-9]+}', Page::class . ':eventId');

    $this->group('/categorias', function() {
        $this->get('[/]', Page::class . ':eventsType');
        $this->get('/{id:[0-9]+}', Page::class . ':eventsTypeId');
    });
});

$app->group('/inscricao', function () {
    $this->map(['GET', 'POST'], '[/]', Page::class . ':inscricao'); //not found any use for this yet
    $this->get('/{id:[0-9]+}', Page::class . ':inscricao');
    $this->post('/add', Page::class . ':inscricaoAdd');

});

$app->group('/users', function () {
    $this->get('/dashboard', User::class . ':dashboard');
    $this->map(['GET', 'POST'], '/profile', User::class . ':profile');
    $this->map(['GET', 'POST'], '/recover', User::class . ':recover');
    $this->map(['GET', 'POST'], '/recover/token/{token}', User::class . ':recoverPassword');
    $this->map(['GET', 'POST'], '/signin', User::class . ':signIn');
    $this->get('/signout', User::class . ':signOut');
    $this->map(['GET', 'POST'], '/signup', User::class . ':signUp');
    $this->get('/verify/{token}', User::class . ':verify');
    $this->get('/inscricoes', Page::class . ':userInscricoes');
});
