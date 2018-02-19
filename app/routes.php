<?php

final class routes {

    function __construct(\Slim\App $app) {
        
        $app->group($app->getContainer()->get('settings')['path'], function() {
            $this->group('', function() {
                
                $this->get('/dashboard', \controller\dashboard::class)
                    ->setName('dashboard');
                
                $this->post('/dashboard/search', \controller\search::class)
                    ->setName('search');
                $this->get('/dashboard/search/{bookName}', \controller\search::class)
                    ->setName('search');

                $this->post('/dashboard/print', \controller\printer::class)
                    ->setName('printer');
                
                $this->map(['PUT', 'DELETE'], '/books', \controller\books::class)
                    ->setName('books');

                $this->map(['PUT', 'DELETE'], '/admin/books', \controller\adminBooks::class)
                    ->add(\middleware\admin::class)
                    ->setName('adminBooks');
                
                $this->map(['PUT', 'DELETE'], '/admin/users', \controller\adminUsers::class)
                    ->add(\middleware\admin::class)
                    ->setName('adminUsers');
                
                $this->post('/user/logout', \controller\auth\logout::class)
                    ->setName('logout');
                
            })->add(\middleware\auth::class);
            
            $this->group('', function() {
                $this->map(['GET', 'POST'], '/', \controller\index::class)
                    ->setName('index');
                
                $this->map(['GET', 'POST'], '/new', \controller\newAuth::class)
                    ->setName('newAuth');
                
                $this->map(['GET', 'POST'], '/register', \controller\newApp::class)
                    ->setName('newApp');
            })->add(\middleware\autologin::class);
        });

    }

}
