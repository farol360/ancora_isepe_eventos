<?php
declare(strict_types=1);

namespace Farol360\Ancora\Controller\Admin;

use Farol360\Ancora\Controller;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Slim\Flash\Messages as FlashMessages;
use Slim\Views\Twig as View;

use Farol360\Ancora\Model;
use Farol360\Ancora\Model\EntityFactory;

class SubscriptionsController extends Controller
{

    protected $eventModel;
    protected $eventTypeModel;
    protected $subscriptionModel;
    protected $entityFactory;

    public function __construct(View $view, FlashMessages $flash, Model $eventModel, Model $eventTypeModel, Model $subscriptionModel, EntityFactory $entityFactory) {

        parent::__construct($view, $flash);
        $this->eventModel = $eventModel;
        $this->eventTypeModel = $eventTypeModel;
        $this->subscriptionModel = $subscriptionModel;
        $this->entityFactory = $entityFactory;
    }

    public function index(Request $request, Response $response, array $args): Response
    {

        if (isset($args)) {
            $eventId = intval($args['id']);
            $eventFeatured = $this->eventModel->get($eventId);
        } else {
            $eventId = 0;
            $eventFeatured = null;
        }

        // get params
        $params = $request->getQueryParams();

        // pagination params
        if (!empty($params['page'])) {
            $page = intval($params['page']);
        } else {
            $page = 1;
        }
        $limit = 20;
        $offset = ($page - 1) * $limit;

        // list of events and types
        $trash = 0;

        $events = $this->eventModel->getAllPublished();
        $eventsType = $this->eventTypeModel->getAll();

        if ($eventId == 0) {
            $subscriptions = $this->subscriptionModel->getAll($offset, $limit);
        } else {
            $subscriptions = $this->subscriptionModel->getAllByEvent($eventId, $offset, $limit);
        }

        // pagination controll;
        $amountPosts = $this->eventModel->getAmount();
        $amountPages = ceil($amountPosts->amount / $limit);

        return $this->view->render($response, 'admin/inscricoes/index.twig', [
            'eventFeatured' => $eventFeatured,
            'subscriptions' => $subscriptions,
            'page' => $page,
            'amountPages' => $amountPages,
            'events' => $events]);
    }

    public function activate(Request $request, Response $response, array $args): Response
    {
        $subscriptionId = intval($args['id']);

        $params = $request->getQueryParams();

        $this->subscriptionModel->activate($subscriptionId);

        $this->flash->addMessage('success', "Inscrição efetivada com sucesso.");

        $url = '/admin/subscriptions';

        if ($params['event'] != '') {
            $url .= '/' . $params['event'];
        }

        return $this->httpRedirect($request, $response, $url);
    }

    public function attendances(Request $request, Response $response, array $args): Response
    {

        if (isset($args['id'])) {
            $eventId = intval($args['id']);
            $eventFeatured = $this->eventModel->get($eventId);
        } else {
            $eventId = 0;
            $eventFeatured = $this->eventModel->get();
        }

        // get params
        $params = $request->getQueryParams();

        // pagination params
        if (!empty($params['page'])) {
            $page = intval($params['page']);
        } else {
            $page = 1;
        }
        $limit = 20;
        $offset = ($page - 1) * $limit;

        // list of events and types
        $trash = 0;

        $events = $this->eventModel->getAllPublished();
        $eventsType = $this->eventTypeModel->getAll();

        if ($eventId == 0) {
            $subscriptions = $this->subscriptionModel->getAll($offset, $limit);
        } else {
            $subscriptions = $this->subscriptionModel->getAllByEvent($eventId, $offset, $limit);
        }

        var_dump($subscriptions);
        die;
        // pagination controll;
        $amountPosts = $this->eventModel->getAmount();
        $amountPages = ceil($amountPosts->amount / $limit);
        return $this->view->render($response, 'admin/attendances/index.twig', [
            'amountPages'   => $amountPages,
            'page' => $page,
            'subscriptions' => $subscriptions,
            'events'        => $events]);

    }

    public function deactivate(Request $request, Response $response, array $args): Response
    {
        $subscriptionId = intval($args['id']);

        $params = $request->getQueryParams();

        $this->subscriptionModel->deactivate($subscriptionId);

        $this->flash->addMessage('warning', "Inscrição Não efetivada.");

        $url = '/admin/subscriptions';

        if ($params['event'] != '') {
            $url .= '/' . $params['event'];
        }

        return $this->httpRedirect($request, $response, $url);
    }

    public function open(Request $request, Response $response, array $args): Response
    {
        $subscriptionId = intval($args['id']);

        $params = $request->getQueryParams();

        $this->subscriptionModel->open($subscriptionId);

        $this->flash->addMessage('info', "Inscrição em modo aberto.");

        $url = '/admin/subscriptions';

        if ($params['event'] != '') {
            $url .= '/' . $params['event'];
        }

        return $this->httpRedirect($request, $response, $url);
    }
}
