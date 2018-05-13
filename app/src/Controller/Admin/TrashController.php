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

class TrashController extends Controller
{

    protected $eventModel;
    protected $eventTypeModel;
    protected $entityFactory;

    public function __construct(View $view, FlashMessages $flash, Model $eventModel, Model $eventTypeModel, EntityFactory $entityFactory) {

        parent::__construct($view, $flash);
        $this->eventModel = $eventModel;
        $this->eventTypeModel = $eventTypeModel;
        $this->entityFactory = $entityFactory;
    }

    public function index(Request $request, Response $response, array $args): Response
    {

        // get params
        $params = $request->getQueryParams();

        // pagination params
        if (!empty($params['page'])) {
            $page = intval($params['page']);
        } else {
            $page = 1;
        }
        $limit = 10;
        $offset = ($page - 1) * $limit;

        // list of events and types
        $trash = 1;
        $events = $this->eventModel->getAll($offset, $limit, $trash);
        $eventsType = $this->eventTypeModel->getAll();

        // pagination controll;
        $amountPosts = $this->eventModel->getAmount();
        $amountPages = ceil($amountPosts->amount / $limit);


        return $this->view->render($response, 'admin/trash.twig', [
            'events' => $events,
            'eventsType' => $eventsType,
            'page' => $page,
            'amountPages' => $amountPages
            ]);



    }

}
