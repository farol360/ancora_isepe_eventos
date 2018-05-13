<?php
declare(strict_types=1);

namespace Farol360\Ancora\Controller;

use Farol360\Ancora\Controller;
use Farol360\Ancora\Mailer;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Flash\Messages as FlashMessages;
use Slim\Views\Twig as View;

use Farol360\Ancora\Model;
use Farol360\Ancora\Model\EntityFactory;

class PageController extends Controller
{
    protected $mailer;
    protected $eventModel;
    protected $eventTypeModel;
    protected $subscriptionModel;
    protected $userModel;

    public function __construct(View $view, FlashMessages $flash, Mailer $mailer, Model $eventModel, Model $eventTypeModel, Model $userModel, Model $subscriptionModel, EntityFactory $entityFactory)
    {
        parent::__construct($view, $flash);
        $this->mailer           = $mailer;
        $this->eventModel       = $eventModel;
        $this->eventTypeModel   = $eventTypeModel;
        $this->subscriptionModel     = $subscriptionModel;
        $this->userModel        = $userModel;
        $this->entityFactory    = $entityFactory;
    }

    public function contato(Request $request, Response $response): Response
    {
        if (empty($request)) {
            return $this->view->render($response, 'page/contato.twig');
        } else {
            if (empty($request->getParsedBody())) {
                return $this->view->render($response, 'page/contato.twig');
            } else {
                $texto =
                    "Olá!\n
                    Alguém entrou em contato \n\n
                    -----DADOS----- \n
                    NOME:" . $request->getParsedBody()['nome'] . "\n
                    EMAIL: " . $request->getParsedBody()['email'] . "\n
                    TELEFONE: " . $request->getParsedBody()['telefone'] . "
                    \n\n
                    CORPO DA MENSAGEM: " . $request->getParsedBody()['corpo-email'];


                $this->mailer->send(
                    $request->getParsedBody()['nome'],
                    $request->getParsedBody()['email'],
                    "Contato via Website",
                    $texto
                );

                return $this->httpRedirect($request, $response, '/obrigado');
            }
        }
    }

    public function contatoObrigado(Request $request, Response $response): Response
    {
        return $this->view->render($response, 'page/contato_obrigado.twig');
    }

    public function events(Request $request, Response $response): Response
    {
        $events = $this->eventModel->getAll();
        $eventsType = $this->eventTypeModel->getAll();




        return $this->view->render($response, 'page/events.twig', ['events' => $events,
            'eventsType' => $eventsType,
            ]);
    }

    public function eventId(Request $request, Response $response, array $args): Response
    {
        // retrive argument id in url, if has it
        $eventId = intval($args['id']);

        // select in db the event by id
        $event = $this->eventModel->get($eventId);
        $eventsType = $this->eventTypeModel->getAll();

        $event->subscriptions_atual = $this->subscriptionModel->getAmountPaydByEvent($eventId)->amount;

        return $this->view->render($response, 'page/event.twig', ['event' => $event,
            'eventsType' => $eventsType,
            ]);
    }

    public function eventsType(Request $request, Response $response): Response
    {

        $eventsType = $this->eventTypeModel->getAll();

        return $this->view->render($response, 'page/eventsType.twig', ['eventsType' => $eventsType,
            ]);
    }

    public function eventsTypeId(Request $request, Response $response, array $args): Response
    {
        // retrive argument id in url, if has it
        $eventsTypeId = intval($args['id']);

        $eventsType = $this->eventTypeModel->get($eventsTypeId);

        // select in db the event by id
        $events = $this->eventModel->getByEventType($eventsTypeId);

        return $this->view->render($response, 'page/eventType.twig', ['events' => $events, 'eventsType' => $eventsType,
            ]);
    }

    public function index(Request $request, Response $response): Response
    {
        $events = $this->eventModel->getAll();

        $eventsType = $this->eventTypeModel->getAll();

        foreach ($events as $event) {
            $event->event_type = $this->eventTypeModel->get((int) $event->id_event_type)->name;
        }


        return $this->view->render($response, 'page/index.twig', ['events' => $events,
            'eventsType' => $eventsType,
            ]);
    }

    public function inscricao(Request $request, Response $response, array $args): Response
    {
        $eventId = intval($args['id']);
        $event = $this->eventModel->get($eventId);

        $user = $this->userModel->get();

        if ($user != null) {
            return $this->view->render($response, 'page/inscricao.twig', ['event' => $event, 'user' => $user]);
        }

        return $this->httpRedirect($request, $response, '/signin');
    }

    public function inscricaoAdd(Request $request, Response $response): Response
    {

        if (empty($request->getParsedBody())) {
           return $this->httpRedirect($request, $response, '/eventos');
        }

        $idEvent = intval($request->getParsedBody()['id_events']);

        $event = $this->eventModel->get($idEvent);

        $usr = $this->userModel->get();

        $subscription['id_user']            = (int) $usr->id;
        $subscription['id_event']           = (int) $event->id;
        $subscription['date_subscription']  = date("Y-m-d H:i:s");
        $subscription['payd']               = 0;
        $subscription['is_certificate']     = 0;

        $subscription = $this->entityFactory->createSubscription($subscription);

        // multi subscription treatment
        $subscriptionResult = $this->subscriptionModel->getUserSubscription($subscription->id_user, $subscription->id_event);

        if ($subscriptionResult == false) {
            // must be tested
            $this->subscriptionModel->add($subscription);

            return $this->view->render($response, 'page/inscricao_sucesso.twig');
        } else {
            return $this->view->render($response, 'page/inscricao_duplicada.twig');
        }

    }

    public function userInscricoes(Request $request, Response $response): Response
    {
        $usr = $this->userModel->get();

        $subscriptions  = $this->subscriptionModel->getByUser((int)$usr->id);

        return $this->view->render($response, 'page/inscricoes.twig', [
            'subscriptions'     => $subscriptions]);
    }

}
