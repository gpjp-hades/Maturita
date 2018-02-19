<?php

namespace controller;

class dashboard {
    
    use \traits\sendResponse;
    
    protected $container;

    function __construct(\Slim\Container $container) {
        $this->container = $container;
    }

    function __invoke($request, $response, $args) {

        $level = $this->container->auth->user['level'];
        if ($level == 0) {

            $id = $this->container->auth->user['id'];

            $books = $this->container->db->query(
                "SELECT books.name, books.author, books.region, books.genere, books.id
                 FROM books, lists
                 WHERE $id = lists.user AND lists.book = books.id;")->fetchAll();
            
            /*$this->container->db->insert("books", [
                "name" => "kniha",
                "author" => "já",
                "region" => 2,
                "genere" => "proza"
            ]);*/

            $response = $this->sendResponse($request, $response, "dash/pupil.phtml", [
                "uname" => $this->container->auth->user['name'],
                "class" => $this->container->auth->user['class'],
                "books" => $books,
                "state" => $this->container->auth->user['state']
            ]);
        } else if ($level == 1) {
            $books = $this->container->db->query(
                "SELECT books.name, books.author, COUNT(lists.book)
                 FROM books, lists
                 WHERE lists.book = books.id;")->fetchAll();
            $lists = $this->container->db->select("users", "*", ["state" => 2]);
            $response = $this->sendResponse($request, $response, "dash/teacher.phtml", [
                "books" => $books,
                "lists" => $lists
            ]);
        } else if ($level == 2) {
            $books = $this->container->db->select("books", "*");
            $users = $this->container->db->select("users", "*");
            $response = $this->sendResponse($request, $response, "dash/admin.phtml", [
                "books" => $books,
                "users" => $users
            ]);
        }

        return $response;
    }
}