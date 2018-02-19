<?php

namespace controller\info;

class group {
    
    use \traits\sendResponse;
    
    protected $container;

    function __construct(\Slim\Container $container) {
        $this->container = $container;
    }

    function __invoke($request, $response, $args) {

        if ($request->isGet()) {

            $remote = $this->remoteFiles();

            if (!$remote) {
                $remote = [["name" => ""]];
                $this->container->flash->addMessage("error", ["Connection with GitHub failed", "Please try again later"]);
            }

            if ($args['id'] == "new") {
                
                $info = [
                    "id" => "new",
                    "config" => "",
                    "name" => "Create Group"
                ];

                $response = $this->sendResponse($request, $response, "info/group.phtml", ["info" => $info, "configs" => $remote]);
            } else {
                $info = $this->container->db->get("categories", "*", ["id" => $args['id']]);
                
                if (!$info) {
                    $this->redirectWithMessage($response, 'dashboard', "error", ["Group not found!", ""]);
                } else {
                    $response = $this->sendResponse($request, $response, "info/group.phtml", [
                        "info"    => $info,
                        "configs" => $remote
                    ]);
                }
            }

        } else if ($request->isPut()) {

            $data = $request->getParsedBody();

            $config = filter_var(@$data['config'], FILTER_SANITIZE_STRING);
            
            if ($args['id'] == "new") {

                $name = filter_var(@$data['name'], FILTER_SANITIZE_STRING);

                $this->container->db->insert("categories", [
                    "name" => $name,
                    "config" => $config
                ]);

                $this->redirectWithMessage($response, "dashboard", "status", ["Group created!", ""]);
            } else {

                $this->container->db->update("categories", [
                    "config" => $config
                ], ["id" => $args['id']]);

                $this->redirectWithMessage($response, "dashboard", "status", ["Group updated!", ""]);
            }
        } else if ($request->isDelete()) {
            
            $this->container->db->delete("categories", ["id" => $args['id']]);
            $this->container->db->update("systems", ["category" => "0"], ["category" => $args['id']]);

            $this->redirectWithMessage($response, "dashboard", "status", ["Group removed!", ""]);
        }
        return $response;
    }

    private function remoteFiles() {

        $url = "https://api.github.com/repos/gpjp-hades/Instructions/contents/";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["User-Agent: gpjp-hades"]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch); 
        curl_close($ch);

        $json = json_decode($output, true);

        if (isset($json['message'])) {
            return false;
        } else {
            $ret = [];
            foreach ($json as $file) {
                if ($file['type'] == "file" && $file['name'] == $file['path']) {
                    array_push($ret, $file);
                }
            }
            return $ret;
        }
    }
}