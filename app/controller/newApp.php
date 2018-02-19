<?php

namespace controller;

class newApp {

    use \traits\sendResponse;
    
    protected $container;

    function __construct(\Slim\Container $container) {
        $this->container = $container;
    }

    function __invoke($request, $response) {
        if ($request->isGet()) {

            $this->sendResponse($request, $response, "newApp.phtml", ["id" => $_SESSION['APP_ID']]);

        } else if ($request->isPost()) {

            $data = $request->getParsedBody();

            $name = filter_var(@$data['name'], FILTER_SANITIZE_STRING);
            $class = filter_var(@$data['class'], FILTER_SANITIZE_STRING);
            
            if (!(is_string($name) && strlen($name) > 0)) {
                $this->redirectWithMessage($response, 'newApp', "error", ["Chyba!", "Vyplňte jméno!"]);
            } else if (strpos($name, "_") !== false) {
                $this->redirectWithMessage($response, 'newApp', "error", ["Chyba!", "Nepoužívejte speciální znaky!"]);
            } else if (!(is_string($class) && strlen($class) == 1)) {
                $this->redirectWithMessage($response, 'newApp', "error", ["Chyba!", "Zvolte třídu!"]);
            } else if (
                is_string($_SESSION['APP_ID']) && strlen($_SESSION['APP_ID']) == 5 &&
                is_string($_SESSION['APP_PASS']) && strlen($_SESSION['APP_PASS']) > 7
            ) {
                if ($this->container->auth->register($_SESSION['APP_ID'], $name, $_SESSION['APP_PASS'], $class)) {
                    $this->container->auth->login($_SESSION['APP_ID'], $_SESSION['APP_PASS']);
                    $response = $response->withRedirect($this->container->router->pathFor("dashboard"), 301);
                }
            } else {
                $this->redirectWithMessage($response, 'index', "error", ["Chyba!", "Nastala chyba, zkuste to prosím znovu."]);
            }
        }

        return $response;
    }
}