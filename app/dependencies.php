<?php
declare(strict_types=1);

$container = $app->getContainer();

$container['cache'] = function ($c) {
    return new Slim\HttpCache\CacheProvider();
};

// Database adapter
$container['db'] = function ($c) {
    $db = $c->get('settings')['db'];

    $dsn = 'mysql:host=' . $db['host'];
    $dsn .= ';dbname=' . $db['name'];
    $dsn .= ';port=' . $db['port'];
    $dsn .= ';charset=' . $db['charset'];

    $pdo = new PDO($dsn, $db['user'], $db['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

    return $pdo;
};

// Flash messages
$container['flash'] = function ($c) {
    return new Slim\Flash\Messages();
};

// Monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    if (!empty($settings['path'])) {
        $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], Monolog\Logger::DEBUG));
    } else {
        $logger->pushHandler(new Monolog\Handler\ErrorLogHandler(0, Monolog\Logger::DEBUG, true, true));
    }
    return $logger;
};

// Mailer
$container['mailer'] = function ($c) {
    $settings = $c->get('settings')['mail'];
    date_default_timezone_set('UTC');

    $mailer = new PHPMailer();
    $mailer->setLanguage('pt_br');
    $mailer->isSMTP();
    $mailer->isHTML(true);
    $mailer->SMTPAuth = true;
    $mailer->SMTPDebug = 0;
    $mailer->Debugoutput = 'html';
    $mailer->Host = $settings['host'];
    $mailer->Port = $settings['port'];
    $mailer->SMTPSecure = $settings['smtpSecureType'];
    $mailer->Username = $settings['username'];
    $mailer->Password = $settings['password'];
    $mailer->setFrom($settings['username'], 'Âncora EAD');
    $mailer->SMTPOptions = [
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true,
        ],
    ];
    if ($c->get('settings')['displayErrorDetails']) {
        $mailer->SMTPDebug = 3;
    }

    return new Farol360\Ancora\Mailer($mailer);
};

// Parsedown: Markdown parser
$container['markdown'] = function ($c) {
    return new Farol360\Ancora\MarkdownParser();
};

$container['notFoundHandler'] = function ($c) {
    return function ($request, $response) use ($c) {
        return $c->get('view')->render(
            $response->withStatus(404),
            '404.twig'
        );
    };
};

// Embera
$container['oembed'] = function ($c) {
    return new Embera\Embera([
        'allow' => ['Youtube', 'Vimeo'],
        'params' => [
            'width' => 560,
            'heigth' => 315,
        ],
    ]);
};

// Twig
$container['view'] = function ($c) {
    $settings = $c->get('settings')['view'];
    $view = new Slim\Views\Twig($settings['template_path'], $settings['twig']);

    // Add extensions
    $view->addExtension(new Slim\Views\TwigExtension($c->get('router'), $c->get('request')->getUri()));
    $view->addExtension(new Twig_Extension_Debug());
    $view->addExtension(new Farol360\Ancora\Twig\AuthExtension());
    $view->addExtension(new Farol360\Ancora\Twig\MarkdownExtension());

    return $view;
};

$container['version'] = $settings['version'];

Farol360\Ancora\User::setupUser($container);

// ------------------
// ------------------
// Controllers
// ------------------
// ------------------

$container['Farol360\Ancora\Controller\Admin\IndexController'] = function ($c) {
    return new Farol360\Ancora\Controller\Admin\IndexController(
        $c['view'],
        $c['flash'],
        $c['version']
    );
};

// Controllers
$container['Farol360\Ancora\Controller\Admin\EventsController'] = function ($c) {
    return new Farol360\Ancora\Controller\Admin\EventsController(
        $c['view'],
        $c['flash'],
        new Farol360\Ancora\Model\EventModel($c['db']),
        new Farol360\Ancora\Model\EventTypeModel($c['db']),
        new Farol360\Ancora\Model\EntityFactory()
    );
};

$container['Farol360\Ancora\Controller\Admin\EventTypesController'] = function ($c) {
    return new Farol360\Ancora\Controller\Admin\EventTypesController(
        $c['view'],
        $c['flash'],
        new Farol360\Ancora\Model\EventModel($c['db']),
        new Farol360\Ancora\Model\EventTypeModel($c['db']),
        new Farol360\Ancora\Model\EntityFactory()
    );
};

$container['Farol360\Ancora\Controller\Admin\PermissionController'] = function ($c) {
    return new Farol360\Ancora\Controller\Admin\PermissionController(
        $c['view'],
        $c['flash'],
        new Farol360\Ancora\Model\PermissionModel($c['db']),
        new Farol360\Ancora\Model\RoleModel($c['db']),
        new Farol360\Ancora\Model\EntityFactory()
    );
};

$container['Farol360\Ancora\Controller\Admin\RoleController'] = function ($c) {
    return new Farol360\Ancora\Controller\Admin\RoleController(
        $c['view'],
        $c['flash'],
        new Farol360\Ancora\Model\RoleModel($c['db']),
        new Farol360\Ancora\Model\EntityFactory()
    );
};

$container['Farol360\Ancora\Controller\Admin\SubscriptionsController'] = function ($c) {
    return new Farol360\Ancora\Controller\Admin\SubscriptionsController(
        $c['view'],
        $c['flash'],
        new Farol360\Ancora\Model\UserModel($c['db']),
        new Farol360\Ancora\Model\EventModel($c['db']),
        new Farol360\Ancora\Model\EventTypeModel($c['db']),
        new Farol360\Ancora\Model\SubscriptionModel($c['db']),
        new Farol360\Ancora\Model\EntityFactory()
    );
};

$container['Farol360\Ancora\Controller\Admin\TrashController'] = function ($c) {
    return new Farol360\Ancora\Controller\Admin\TrashController(
        $c['view'],
        $c['flash'],
        new Farol360\Ancora\Model\EventModel($c['db']),
        new Farol360\Ancora\Model\EventTypeModel($c['db']),
        new Farol360\Ancora\Model\EntityFactory()
    );
};

$container['Farol360\Ancora\Controller\Admin\UserController'] = function ($c) {
    return new Farol360\Ancora\Controller\Admin\UserController(
        $c['view'],
        $c['flash'],
        new Farol360\Ancora\Model\UserModel($c['db']),
        new Farol360\Ancora\Model\RoleModel($c['db']),
        new Farol360\Ancora\Model\EntityFactory()
    );
};

$container['Farol360\Ancora\Controller\PageController'] = function ($c) {
    return new Farol360\Ancora\Controller\PageController(
        $c['view'],
        $c['flash'],
        $c['mailer'],
        new Farol360\Ancora\Model\EventModel($c['db']),
        new Farol360\Ancora\Model\EventTypeModel($c['db']),
        new Farol360\Ancora\Model\UserModel($c['db']),
        new Farol360\Ancora\Model\SubscriptionModel($c['db']),
        new Farol360\Ancora\Model\EntityFactory()
    );
};

$container['Farol360\Ancora\Controller\UserController'] = function ($c) {
    return new Farol360\Ancora\Controller\UserController(
        $c['view'],
        $c['flash'],
        new Farol360\Ancora\Model\UserModel($c['db']),
        $c['mailer'],
        new Farol360\Ancora\Model\EntityFactory()
    );
};
