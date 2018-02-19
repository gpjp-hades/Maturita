<?php

namespace controller;

class adminUsers {
    
    use \traits\sendResponse;
    
    protected $container;

    function __construct(\Slim\Container $container) {
        $this->container = $container;
    }

    function __invoke($request, $response, $args) {
        $data = $request->getParsedBody();

        if ($request->isPut()) {
            $name  = filter_var(@$data['name'],  FILTER_SANITIZE_STRING);
            $pass  = filter_var(@$data['pass'],  FILTER_SANITIZE_STRING);
            
            if ($this->container->db->has('users', ["name" => "_" . $name])) {
                $this->redirectWithMessage($response, 'dashboard', "error", ["Chyba!", "Uživatel již založen!"]);
            } else {
                $this->container->auth->register(null, "_" . $name, $pass, null, 1);
                $this->redirectWithMessage($response, 'dashboard', "status", ["Účet učitele založen", ""]);
            }
        } else if ($request->isDelete()) {
            $id  = filter_var(@$data['id'],  FILTER_SANITIZE_STRING);

            if ($id == 1) {
                $this->redirectWithMessage($response, 'dashboard', "error", ["Chyba!", "Nelze odebrat admina"]);
            } else if ($this->container->db->has("users", ["id" => $id])) {
                $this->container->db->delete("users", ["id" => $id]);
                $this->redirectWithMessage($response, 'dashboard', "status", ["Uživatel odebrán", ""]);
            } else {
                $this->redirectWithMessage($response, 'dashboard', "error", ["Chyba!", "Uživatel nenalezen!"]);
            }
        }

        return $response;
    }
}