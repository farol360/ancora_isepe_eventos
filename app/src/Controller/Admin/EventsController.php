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

class EventsController extends Controller
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
        $events = $this->eventModel->getAll($offset, $limit, $trash);
        $eventsType = $this->eventTypeModel->getAll();

        // pagination controll;
        $amountPosts = $this->eventModel->getAmount();
        $amountPages = ceil($amountPosts->amount / $limit);


        return $this->view->render($response, 'admin/events/index.twig', [
            'events' => $events,
            'eventsType' => $eventsType,
            'page' => $page,
            'amountPages' => $amountPages
            ]);
    }

    public function add(Request $request, Response $response, array $args)
    {

        // if has nothing on body, it is a plain empty page.
        if (empty($request->getParsedBody())) {
            $eventsTypes = $this->eventTypeModel->getPublished();

            return $this->view->render($response, 'admin/events/add.twig', [
                'eventsTypes' => $eventsTypes
                ]);
        }

        // --------
        // if has something in request body.
        // --------

        // getting data from request;
        $data = $request->getParsedBody();

        // if has any treatment on data, do it in here..
        // $data = ?

        if ($data['workload'] <= 0) {

            $event = $this->entityFactory->createEvent($data);

            $eventsTypes = $this->eventTypeModel->getPublished();

            //inform error msg
            $this->flash->addMessage('danger', "Carga horária deve ser preenchida corretamente.");

            //redirect to this url
            return $this->httpRedirect($request, $response, '/admin/events/add');
        }

        if ($data['img_featured']) {

        }

        $data['img_featured'] = 'images/default-img.jpg';

        if ($data['status'] == null) {
            $data['status'] = 0;
        } else {
            $data['status'] = (int) $data['status'];
        }

        $data['trash'] = 0;

        // create event from data;
        $event = $this->entityFactory->createEvent($data);

        // add event on db
        $event->id = (int) $this->eventModel->add($event);

        // if has all ok in add on db
        if($event->id !== null) {

            // -------
            // working on uploaded images by usr
            // -------

            // get uploaded files
            $files = $request->getUploadedFiles();

            // if has file in img_featured key
            if (!empty($files['img_featured'])) {
                $image = $files['img_featured'];

                //if has no error on upload
                if ($image->getError() === UPLOAD_ERR_OK) {

                    //verify allowed extensions
                    $filename = $image->getClientFilename();

                    $allowedExtensions = [
                        'jpg',
                        'jpeg',
                        'gif',
                        'png'
                    ];

                    // if not allowed extension
                    if (!in_array(pathinfo($filename,PATHINFO_EXTENSION), $allowedExtensions)) {

                        //inform error msg
                        $this->flash->addMessage('danger', "Imagem em formato inválido.");

                        //redirect to this url
                        return $this->httpRedirect($request, $response, '/admin/posts/add');
                    }

                    //verify size
                    if ($image->getSize() > 400000) {

                        //inform error msg
                        $this->flash->addMessage('danger', "Imagem muito grande (max 300kb).");

                        //redirect to this url
                        return $this->httpRedirect($request, $response, '/admin/events/add');
                    }

                    // --------
                    // if pass by all verificators..
                    // --------

                    // cabulous function
                    $filename = sprintf(
                        '%s.%s',
                        uniqid(),
                        pathinfo($image->getClientFilename(), PATHINFO_EXTENSION)
                    );

                    // path to usr img
                    $path = 'upload/img/';

                    // move img to path
                    $image->moveTo($path . $filename);

                    // update path in db event
                    $event->img_featured = $path . $filename;

                    $this->eventModel->update($event);

                    // add sucess msg
                    $this->flash->addMessage('success', "Evento adicionada com sucesso.");

                    // redirect to events list
                    return $this->httpRedirect($request, $response, '/admin/events');

                // if has error on $image
                } else {
                    $size = $image->getSize();
                    if ($size == 0) {
                        $this->flash->addMessage('success', "Evento adicionado com sucesso. Imagem padrão utilizada.");
                        return $this->httpRedirect($request, $response, '/admin/events');
                    }
                    $this->flash->addMessage('danger', "Erro ao adicionar imagem. Favor contactar a Farol 360. Erro número: " . $image->getError());
                    return $this->httpRedirect($request, $response, '/admin/events');
                }
            // if $files['img_featured'] is empty
            } else {

                $this->flash->addMessage('danger', "Erro ao adicionar (imagem vazia). Favor contactar a Farol 360.");
                return $this->httpRedirect($request, $response, '/admin/events');
            }
        }

        // if get this point, something unforeseen happened
        $this->flash->addMessage('danger', "Erro indefinido ao adicionar Eventos. Por favor entre em contato com a Farol 360.");
        return $this->httpRedirect($request, $response, '/admin/events');
    }

    public function certificates(Request $request, Response $response): Response
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
        $events = $this->eventModel->getAll($offset, $limit, $trash);
        $eventsType = $this->eventTypeModel->getAll();

        // pagination controll;
        $amountPosts = $this->eventModel->getAmount();
        $amountPages = ceil($amountPosts->amount / $limit);


        return $this->view->render($response, 'admin/events/certificates.twig', [
            'events' => $events,
            'eventsType' => $eventsType,
            'page' => $page,
            'amountPages' => $amountPages
            ]);
    }

    public function edit(Request $request, Response $response, array $args): Response
    {

        // retrive argument id in url, if has it
        $eventId = intval($args['id']);

        // select in db the event by id
        $event = $this->eventModel->get($eventId);

        // if event dnt exist, return error
        if (!$event) {
             $this->flash->addMessage('danger', "Evento não encontrado.");
            return $this->httpRedirect($request, $response, '/admin/events');
        }

        // get objets to render edit interface
        $eventsTypes = $this->eventTypeModel->getPublished();

        return $this->view->render($response, 'admin/events/edit.twig', [
                'event' => $event,
                'eventsTypes' => $eventsTypes
            ]
        );

    }

    public function disable(Request $request, Response $response, array $args): Response
    {
        $eventId = intval($args['id']);
        $this->eventModel->disable($eventId);
        $this->flash->addMessage('success', "Evento desabilitado com sucesso.");
        return $this->httpRedirect($request, $response, '/admin/events');
    }

    public function enable(Request $request, Response $response, array $args): Response
    {
        $eventId = intval($args['id']);
        $this->eventModel->enable($eventId);
        $this->flash->addMessage('success', "Evento habilitado com sucesso.");
        return $this->httpRedirect($request, $response, '/admin/events');
    }

    /**
        To remove an event from trash
    */
    public function trashRemove(Request $request, Response $response, array $args): Response
    {

        // get id
        $eventId = intval($args['id']);
        $event = $this->eventModel->get($eventId);

        // if event exist
        if ($event != null) {

            // set to send trash or recover
            $this->eventModel->trashRemove($eventId);
            $this->flash->addMessage('success', "Evento removido da lixeira.");
            return $this->httpRedirect($request, $response, '/admin/trash');

        } else {
            $this->flash->addMessage('danger', "Evento não encontrado.");
            return $this->httpRedirect($request, $response, '/admin/trash');
        }


    }

    /**
        To send an event to trash
    */
    public function trashSend(Request $request, Response $response, array $args): Response
    {

        // get id
        $eventId = intval($args['id']);
        $event = $this->eventModel->get($eventId);

        // if event exist
        if ($event != null) {

            // set to send trash or recover
            $this->eventModel->trashSend($eventId);
            $this->flash->addMessage('success', "Evento enviado para a lixeira.");
            return $this->httpRedirect($request, $response, '/admin/events');

        } else {
            $this->flash->addMessage('danger', "Evento não encontrado.");
            return $this->httpRedirect($request, $response, '/admin/events');
        }


    }

    public function trashIndex(Request $request, Response $response, array $args): Response
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


        return $this->view->render($response, 'admin/events/trashIndex.twig', [
            'events' => $events,
            'eventsType' => $eventsType,
            'page' => $page,
            'amountPages' => $amountPages
            ]);



    }

    public function update(Request $request, Response $response): Response
    {
        // get data from body request
        $data = $request->getParsedBody();

        // if has any treatment on data, do it in here..
        // $data = ?

        // if status not set..
        if ($data['status'] == null ) {
            $data['status'] = 0;

        // just typecast to int
        } else {
            $data['status'] = (int) $data['status'];

        }

        // create object event
        $event = $this->entityFactory->createEvent($data);

        $oldEvent = $this->eventModel->get((int) $event->id);

        $event->trash = $oldEvent->trash;

        $files = $request->getUploadedFiles();

        // if files are empty means size == 0
        if ($files['img_featured']->getSize() != 0) {
            $image = $files['img_featured'];

            if ($image->getError() === UPLOAD_ERR_OK) {

               //verify allowed extensions
                $filename = $image->getClientFilename();

                $allowedExtensions = [
                    'jpg',
                    'jpeg',
                    'gif',
                    'png'
                ];

                // if not allowed extension
                if (!in_array(pathinfo($filename,PATHINFO_EXTENSION), $allowedExtensions)) {

                    //inform error msg
                    $this->flash->addMessage('danger', "Imagem em formato inválido.");

                    //redirect to this url
                    return $this->httpRedirect($request, $response, '/admin/posts/add');
                }

                //verify size
                if ($image->getSize() > 400000) {

                    //inform error msg
                    $this->flash->addMessage('danger', "Imagem muito grande (max 300kb).");

                    //redirect to this url
                    return $this->httpRedirect($request, $response, '/admin/posts/add');
                }

                $filename = sprintf(
                    '%s.%s',
                    uniqid(),
                    pathinfo($image->getClientFilename(), PATHINFO_EXTENSION)
                );

                $path = 'upload/img/';
                $image->moveTo($path . $filename);
                $event->img_featured = $path . $filename;

            }

            // remove old img from disk
            if (file_exists($request->getParsedBody()['img_featured_old'])) {
                unlink($request->getParsedBody()['img_featured_old']);
            }

        // if has no image, set old as atual
        } else {
            $oldEvent = $this->eventModel->get((int)$event->id);
            $event->img_featured = $oldEvent->img_featured;

        }


        $this->eventModel->update($event);

        $this->flash->addMessage('success', "Evento atualizado com sucesso.");
        return $this->httpRedirect($request, $response, '/admin/events');



    }
}
