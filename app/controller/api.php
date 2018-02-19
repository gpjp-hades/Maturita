<?php

namespace controller;

final class api {
    
    protected $container;
    protected $token;

    function __construct(\Slim\Container $container) {
        $this->container = $container;
    }

    function __invoke($request, $response) {

        $this->token = $request->getAttribute("token");
        $this->name  = $request->getAttribute("name");
        
        if (!$this->container->db->has("systems", ["uid" => $this->token])) {

            if ($this->container->config->getBool("new_reg") == false) {
                return $response->withJson(["result" => "request denied"]);
            }
            
            $this->container->db->insert("systems", [
                "uid" => $this->token,
                "name" => $this->name,
                "lastActive" => time()
            ]);

            return $response->withJson(["result" => "request pending"]);
        } else if ($this->container->db->has("systems", ["uid" => $this->token, "approved" => false])) {

            $this->container->db->update("systems", ["lastActive" => time()], ["uid" => $this->token]);

            return $response->withJson(["result" => "request pending"]);
        } else {

            $configName = $this->container->db->get("systems", 
                ["[>]categories" => ["category" => "id"]], 
                "categories.config",
                ["systems.uid" => $this->token]
            );

            $remote = $this->remoteFiles();
            foreach ($remote as $file) {
                if ($file['name'] == $configName) {
                    $current = $file['name'];
                }
            }
            if (!isset($current)) {
                $current = $this->container->db->get("categories", "config", ["id" => 0]);
            }

            $this->container->db->update("systems", ["lastActive" => time()], ["uid" => $this->token]);
            
            return $response->withJson(["result" => "approved", "config" => $current]);
        }
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
