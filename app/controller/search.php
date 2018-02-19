<?php

namespace controller;

class search {
    
    use \traits\sendResponse;
    
    protected $container;

    function __construct(\Slim\Container $container) {
        $this->container = $container;
    }

    function __invoke($request, $response, $args) {

        if ($request->isPost()) {
            $data = $request->getParsedBody();
            $bookName = filter_var(@$data['bookName'], FILTER_SANITIZE_STRING);
        } else if ($request->isGet()) {
            $bookName = filter_var(@$args['bookName'], FILTER_SANITIZE_STRING);
        }
        
        $books = $this->container->db->select("books", "*", ["OR" => ["name" => $bookName, "author" => $bookName]]);

        $response = $this->sendResponse($request, $response, "search.phtml", [
            "books" => $books
        ]);

        return $response;
    }
}