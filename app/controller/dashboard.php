<?php

namespace controller;

class dashboard {
    
    use \traits\sendResponse;
    
    protected $container;

    function __construct(\Slim\Container $container) {
        $this->container = $container;
    }

    function __invoke($request, $response, $args) {

        $level = $this->container->auth->level();
        if ($level == 0) {

            $id = $this->container->auth->user['id'];

            
            $books = $this->container->db->query(
                "SELECT books.name, books.author, books.region, books.genere, books.id
                 FROM books, lists
                 WHERE $id = lists.user AND lists.book = books.id;")->fetchAll();
            
            //var_dump($this->container->db->query("SELECT * FROM books;")->fetchAll());
            /*$this->container->db->insert("books", [
                "name" => "kniha",
                "author" => "já",
                "region" => 2,
                "genere" => "proza"
            ]);*/

            //$this->container->db->insert("lists", ["user"=>64065, "book" => 1]);

            $response = $this->sendResponse($request, $response, "dash/pupil.phtml", [
                "uname" => $this->container->auth->user['name'],
                "class" => $this->container->auth->user['class'],
                "books" => $books
            ]);
        } else if ($level == 1) {
            $response = $this->sendResponse($request, $response, "dash/teacher.phtml");
        } else if ($level == 2) {
            $response = $this->sendResponse($request, $response, "dash/admin.phtml");
        }

        return $response;
    }
}