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

class EventTypesController extends Controller
{
    protected $eventModel;
    protected $eventTypeModel;
    protected $entityFactory;

    public function __construct(View $view, FlashMessages $flash,Model $eventModel, Model $eventTypeModel, EntityFactory $entityFactory) {

        parent::__construct($view, $flash);
        $this->eventModel = $eventModel;
        $this->eventTypeModel = $eventTypeModel;
        $this->entityFactory = $entityFactory;
    }

    public function index(Request $request, Response $response): Response
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
        $trash = 0;
        $event_types = $this->eventTypeModel->getAll($offset, $limit, $trash);

        // pagination controll;
        $amountPosts = $this->eventTypeModel->getAmount();
        $amountPages = ceil($amountPosts->amount / $limit);


        return $this->view->render($response, 'admin/event_types/index.twig', [
            'event_types' => $event_types,
            'page' => $page,
            'amountPages' => $amountPages
            ]);
    }

    public function add(Request $request, Response $response, array $args)
    {

        // if has nothing on body, it is a plain empty page.
        if (empty($request->getParsedBody())) {
            return $this->view->render($response, 'admin/event_types/add.twig');
        }

        // --------
        // if has something in request body.
        // --------

        // getting data from request;
        $data = $request->getParsedBody();

        // if has any treatment on data, do it in here..
        // $data = ?

        if (!isset($data['status'])) {
            $data['status'] = 0;
        } else {
            $data['status'] = (int) $data['status'];
        }

        $data['trash'] = 0;

        // create eventType from data;
        $eventType = $this->entityFactory->createEventType($data);

        // add eventType on db
        $eventType->id = (int) $this->eventTypeModel->add($eventType);


        // if has all ok in add on db
        if($eventType->id !== null) {
            // if get this point, something unforeseen happened
            $this->flash->addMessage('success', "Tipo de Evento adicionado com Sucesso.");
            return $this->httpRedirect($request, $response, '/admin/event_types');

        }

        // if get this point, something unforeseen happened
        $this->flash->addMessage('danger', "Erro indefinido ao adicionar Eventos. Por favor entre em contato com a Farol 360.");
        return $this->httpRedirect($request, $response, '/admin/event_types');
    }

    public function edit(Request $request, Response $response, array $args): Response
    {

        // retrive argument id in url, if has it
        $eventTypeId = intval($args['id']);

        // select in db the event by id
        $eventType = $this->eventTypeModel->get($eventTypeId);

        // if event dnt exist, return error
        if (!$eventType) {
             $this->flash->addMessage('danger', "Tipo de Evento não encontrado.");
            return $this->httpRedirect($request, $response, '/admin/event_types');
        }


        return $this->view->render($response, 'admin/event_types/edit.twig', [
                'eventType' => $eventType,
            ]
        );

    }

    public function disable(Request $request, Response $response, array $args): Response
    {
        $eventId = intval($args['id']);
        $events = $this->eventModel->disableByEventType($eventId);
        $this->eventTypeModel->disable($eventId);
        $this->flash->addMessage('success', "Tipo de Evento desabilitado com sucesso.");
        return $this->httpRedirect($request, $response, '/admin/event_types');
    }

    public function enable(Request $request, Response $response, array $args): Response
    {
        $eventId = intval($args['id']);
        $this->eventTypeModel->enable($eventId);
        $this->flash->addMessage('success', "Tipo de Evento habilitado com sucesso.");
        return $this->httpRedirect($request, $response, '/admin/event_types');
    }

    public function getTerms(Request $request, Response $response, array $args): Response
    {
        $eventTypeId = intval($args['id']);
        $eventType =  $this->eventTypeModel->get($eventTypeId);
        return $response->withJson($eventType->agree_terms,200);
    }

    public function remove(Request $request, Response $response, array $args): Response
    {
        $eventTypeId = (int) $args['id'];

        $this->eventTypeModel->delete($eventTypeId);

        $this->flash->addMessage('success', "Tipo de Evento removido com sucesso.");
        return $this->httpRedirect($request, $response, '/admin/event_types');

    }

    /**
        To remove an event from trash

    public function trashRemove(Request $request, Response $response, array $args): Response
    {

        // get id
        $eventTypeId = intval($args['id']);
        $eventType = $this->eventTypeModel->get($eventTypeId);

        // if event exist
        if ($eventType != null) {

            // set to send trash or recover
            $this->eventTypeModel->trashRemove($eventTypeId);
            $this->flash->addMessage('success', "Evento removido da lixeira.");
            return $this->httpRedirect($request, $response, '/admin/trash');

        } else {
            $this->flash->addMessage('danger', "Evento não encontrado.");
            return $this->httpRedirect($request, $response, '/admin/trash');
        }


    }
    */
    /**
        To send an event to trash

    public function trashSend(Request $request, Response $response, array $args): Response
    {

        // get id
        $eventTypeId = intval($args['id']);
        $eventType = $this->eventTypeModel->get($eventTypeId);

        // if event exist
        if ($eventType != null) {

            // set to send trash or recover
            $this->eventTypeModel->trashSend($eventTypeId);
            $this->flash->addMessage('success', "Evento enviado para a lixeira.");
            return $this->httpRedirect($request, $response, '/admin/event_types');

        } else {
            $this->flash->addMessage('danger', "Evento não encontrado.");
            return $this->httpRedirect($request, $response, '/admin/event_types');
        }


    }
    */

    public function update(Request $request, Response $response): Response
    {
        // get data from body request
        $data = $request->getParsedBody();

        // if has any treatment on data, do it in here..
        // $data = ?

        $oldevent = $this->eventTypeModel->get((int)$data['id']);
        $data['trash'] = (int)$oldevent->trash;

        // if status not set..
        if (!isset($data['status']) ) {
            $data['status'] = 0;

        // just typecast to int
        } else {
            $data['status'] = (int) $data['status'];
        }

        // create object event
        $eventType = $this->entityFactory->createEventType($data);

        $this->eventTypeModel->update($eventType);

        $this->flash->addMessage('success', "Tipo de Evento atualizado com sucesso.");
        return $this->httpRedirect($request, $response, '/admin/event_types');



    }

    public function verifytoremove(Request $request, Response $response, array $args)
    {

        $eventTypeId = (int) $args['id'];

        $event_types = $this->eventTypeModel->get($eventTypeId);

        if (isset($event_types)) {
            $eventAmount = $this->eventModel->getAmountByEventType((int) $event_types->id);

            echo $eventAmount->amount ;

        }

    }

    public function verifytounpublish(Request $request, Response $response, array $args)
    {

        $eventTypeId = (int) $args['id'];
        $event_types = $this->eventTypeModel->get($eventTypeId);

        if (isset($event_types)) {
            $eventAmount = $this->eventModel->getAmountPublishedByEventType((int) $event_types->id);

            return $response->withJson($eventAmount->amount, 200);


        }

    }
}
