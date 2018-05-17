<?php
declare(strict_types=1);

namespace Farol360\Ancora\Controller\Admin;

use Farol360\Ancora\Controller;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Slim\Flash\Messages as FlashMessages;
use Slim\Views\Twig as View;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReader;

use Farol360\Ancora\Model;
use Farol360\Ancora\Model\EntityFactory;

class SubscriptionsController extends Controller
{

    protected $userModel;
    protected $eventModel;
    protected $eventTypeModel;
    protected $subscriptionModel;
    protected $entityFactory;

    public function __construct(View $view, FlashMessages $flash, Model $userModel, Model $eventModel, Model $eventTypeModel, Model $subscriptionModel, EntityFactory $entityFactory) {

        parent::__construct($view, $flash);
        $this->userModel = $userModel;
        $this->eventModel = $eventModel;
        $this->eventTypeModel = $eventTypeModel;
        $this->subscriptionModel = $subscriptionModel;
        $this->entityFactory = $entityFactory;
    }

    public function index(Request $request, Response $response, array $args): Response
    {

        $eventId = intval($args['id']);

        if ($eventId > 0) {
            $eventFeatured = $this->eventModel->get($eventId);
        } else {
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
            $eventFeatured = $this->eventModel->get();
            $eventId = (int)$eventFeatured->id;
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

        return $this->view->render($response, 'admin/attendances/index.twig',
            [
            'amountPages'   => $amountPages,
            'page'          => $page,
            'subscriptions' => $subscriptions,
            'events'        => $events,
            'eventFeatured' => $eventFeatured
            ]);

    }

    public function certificates(Request $request, Response $response): Response
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

        // pagination controll;
        $amountPosts = $this->eventModel->getAmount();
        $amountPages = ceil($amountPosts->amount / $limit);

        return $this->view->render($response, 'admin/certificate/index.twig',
            [
            'amountPages'   => $amountPages,
            'page'          => $page,
            'subscriptions' => $subscriptions,
            'events'        => $events,
            'eventFeatured' => $eventFeatured
            ]);
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

    public function export(Request $request, Response $response, array $args)
    {

        $eventId = intval($args['id']);

        $event = $this->eventModel->get($eventId);

        $subscriptions = $this->subscriptionModel->getAllByEvent($eventId);

        $spreadsheet = new Spreadsheet();

        $spreadsheet->getActiveSheet()->setCellValue('C1','LISTA DE PRESENÇA');
        $spreadsheet->getActiveSheet()->mergeCells('C1:D1');
        $spreadsheet->getActiveSheet()->setCellValue('E1','EVENTO: '. $event->name );
        $spreadsheet->getActiveSheet()->mergeCells('E1:G1');
        $spreadsheet->getActiveSheet()->setCellValue('A2','ID Inscrição');
        $spreadsheet->getActiveSheet()->setCellValue('B2','Participante');
        $spreadsheet->getActiveSheet()->setCellValue('C2','Email');
        $spreadsheet->getActiveSheet()->setCellValue('D2','Data do cadastro');
        $spreadsheet->getActiveSheet()->setCellValue('E2','Evento');
        $spreadsheet->getActiveSheet()->setCellValue('F2','E-ID');
        $spreadsheet->getActiveSheet()->setCellValue('G2','Carga Horária');

        $i = 3;
        foreach ($subscriptions as $subscription) {
            $spreadsheet->getActiveSheet()->setCellValue("A$i",
                $subscription->id );
            $spreadsheet->getActiveSheet()->setCellValue("B$i",
                $subscription->user_name );

            $spreadsheet->getActiveSheet()->setCellValue("C$i",
                $subscription->user_email );

            $spreadsheet->getActiveSheet()->setCellValue("D$i",
                $subscription->created_at );

            $spreadsheet->getActiveSheet()->setCellValue("E$i",
                $subscription->event_name );

            $spreadsheet->getActiveSheet()->setCellValue("F$i",
                $eventId );

            $spreadsheet->getActiveSheet()->setCellValue("G$i",
                $subscription->workload );

            $i++;
        }

        //style sheet
        $styleTitle = [
            'font' => [
                'bold' => true,
                'size' => 18,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];

        $spreadsheet->getActiveSheet()->getStyle('C1')->applyFromArray($styleTitle);

        $styleEventName = [
            'font' => [
                'size' => 14,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
            ],
        ];

        $spreadsheet->getActiveSheet()->getStyle('E1')->applyFromArray($styleEventName  );

        $styleHeader = [
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FDFDFD',
                ]
            ],
        ];

        $spreadsheet->getActiveSheet()->getStyle('A1:G1')->applyFromArray($styleHeader);

        $styleArray = [
            'font' => [
                'bold' => true,
                'size' => 13,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],

            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'DDDDDD',
                ]
            ],
        ];
        $spreadsheet->getActiveSheet()->getStyle('A2:G2')->applyFromArray($styleArray);

        $spreadsheet->getActiveSheet()->getProtection()->setSheet(true);

        $lockedCells = [
            'protection' => ['locked' => \PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_PROTECTED],
        ];

        $unlockedCells = [
            'protection' => ['locked' => \PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED],
        ];

        $spreadsheet->getActiveSheet()->getStyle("A3:F$i")->applyFromArray($lockedCells);

        $spreadsheet->getActiveSheet()->getStyle("G3:G$i")->applyFromArray($unlockedCells);

        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);

        $spreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(50);

        //SET IMAGE

        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo');
        $drawing->setPath('images/logo.png');
        $drawing->setHeight(52);
        $drawing->setCoordinates('A1');

        $drawing->setWorksheet($spreadsheet->getActiveSheet());




        $file_path = "upload/export/";
        $file_name = $file_path . 'Lista-Inscricoes-' . time().'.xlsx';

        $writer = new Xlsx($spreadsheet);
        $writer->save($file_name);

         $response = $response->withHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response = $response->withHeader('Content-Disposition', 'attachment; filename="file.xlsx"');

        $stream = fopen($file_name, 'r+');

        return $response->withBody(new \Slim\Http\Stream($stream));
    }

    public function import(Request $request, Response $response, array $args)
    {

        $eventFeaturedId = (int)$args['id'];
        $files = $request->getUploadedFiles();

        if (!empty($files['import'])) {
            $file = $files['import'];

            if ($file->getError() === UPLOAD_ERR_OK) {
                //verify allowed extensions
                $filename = $file->getClientFilename();

                // cabulous function
                $filename = sprintf(
                    '%s.%s',
                    uniqid(),
                    pathinfo($file->getClientFilename(), PATHINFO_EXTENSION)
                );

                // path to usr img
                $path = 'upload/import/';

                // move img to path
                $file->moveTo($path . $filename);


                // spreadsheet logic
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($path . $filename);

               $sheetData = $spreadsheet->getActiveSheet()->toArray(   null, true, true, true);
                foreach ($sheetData as $key => $value) {
                    if ( ($key > 2) and ($value['C'] != null ) ) {
                        $subscriptions[$key]->id = $value['A'];
                        $subscriptions[$key]->user_name = $value['B'];
                        $subscriptions[$key]->user_email = $value['C'];
                        $subscriptions[$key]->id_user = $this->userModel->getByEmail($value['C'])->id;
                        $subscriptions[$key]->created_at = $value['D'];
                        $subscriptions[$key]->event_name = $value['E'];
                        $subscriptions[$key]->id_event = $value['F'];
                        $subscriptions[$key]->workload = $value['G'];


                    }

               }

               // update db
               foreach ($subscriptions as $subscription) {

                   $subscription = $this->entityFactory->createSubscription((array) $subscription);

                   $this->subscriptionModel->update($subscription);
               }


                $this->flash->addMessage('info', "Cargas horárias importadas com sucesso.");

                $url =  '/admin/attendances/' . $eventFeaturedId;
                return $this->httpRedirect($request, $response, $url);
            }
        }


    }

    public function update(Request $request, Response $response, array $args)
    {

        $eventFeaturedId = (int)$args['id'];

        $data = $request->getParsedBody();

        foreach ($data as $key => $value) {
            $this->subscriptionModel->setWorkload((int)$key, (int)$value);
        }


        $url =  '/admin/attendances/' . $eventFeaturedId;

        $this->flash->addMessage('info', "Cargas horárias atualizadas com sucesso.");
        return $this->httpRedirect($request, $response, $url);
    }
}
