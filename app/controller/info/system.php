<?php

namespace controller\info;

class system {
    
    use \traits\sendResponse;
    
    protected $container;

    function __construct(\Slim\Container $container) {
        $this->container = $container;
    }

    function __invoke($request, $response, $args) {

        if ($request->isGet()) {
            
            $info = $this->container->db->get("systems", "*", [
                "AND" => [
                    "id" => $args['id'],
                    "approved" => true
                ]
            ]);

            if (!$info) {
                $this->redirectWithMessage($response, 'dashboard', "error", ["System not found!", ""]);
            } else {
                $groups = $this->container->db->select("categories", ["id", "name"]);

                $response = $this->sendResponse($request, $response, "info/system.phtml", [
                    "info"    => $info,
                    "groups"   => $groups
                ]);
            }

        } else if ($request->isPut()) {

            $data = $request->getParsedBody();

            $name = filter_var(@$data['name'], FILTER_SANITIZE_STRING);
            $group = filter_var(@$data['group'], FILTER_SANITIZE_STRING);
            $wiki = filter_var(@$data['wiki'], FILTER_SANITIZE_STRING);

            $this->container->db->update("systems", [
                "name" => $name,
                "category" => $group,
                "wikilink" => $wiki,
                "lastActive" => time()
            ], ["id" => $args['id']]);

            $this->redirectWithMessage($response, "dashboard", "status", ["System updated!", ""]);
            
        } else if ($request->isDelete()) {
            
            $this->container->db->delete("systems", ["id" => $args['id']]);
            $this->redirectWithMessage($response, 'dashboard', "status", ["System removed!", ""]);
        }

        return $response;
    }
}