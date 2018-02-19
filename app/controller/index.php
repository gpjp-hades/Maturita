<?php

namespace controller;

class index {

    use \traits\sendResponse;
    
    protected $container;

    function __construct(\Slim\Container $container) {
        $this->container = $container;
    }

    function __invoke($request, $response) {
        if ($request->isGet()) {

            $this->sendResponse($request, $response, "index.phtml");

        } elseif ($request->isPost()) {

            $data = $request->getParsedBody();

            $name = filter_var(@$data['name'], FILTER_SANITIZE_STRING);
            $pass = filter_var(@$data['pass'], FILTER_SANITIZE_STRING);
            
            if ($this->container->auth->login($name, $pass)) {
                $response =  $response->withRedirect($this->container->router->pathFor('dashboard'), 301);
            } else {
                sleep(2);

                $this->redirectWithMessage($response, 'index', "error", ["Přihlášení selhalo", "Zkuste to znovu"]);
            }
        }

        return $response;
    }
}