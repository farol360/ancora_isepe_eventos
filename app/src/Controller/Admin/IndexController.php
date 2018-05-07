<?php
declare(strict_types=1);

namespace Farol360\Ancora\Controller\Admin;

use Farol360\Ancora\Controller;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Flash\Messages as FlashMessages;
use Slim\Views\Twig as View;

class IndexController extends Controller
{

    protected $version;

    public function __construct(
        View $view,
        FlashMessages $flash,
        $version
    ) {
        parent::__construct($view, $flash);
        $this->version = $version;
    }

    public function index(Request $request, Response $response): Response
    {
        return $this->view->render($response, 'admin/dashboard/index.twig');
    }

    public function about(Request $request, Response $response): Response
    {
        return $this->view->render($response, 'admin/dashboard/about.twig', ['version' => $this->version]);
    }
}
