<?php
namespace Core;
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/DashboardController.php';
require_once __DIR__ . '/../app/controllers/WalletController.php';



class Router {

    public function run() {
        $action = $_GET['action'] ?? 'login';

        switch ($action) {

            case 'login':
                $controller = new \App\Controllers\AuthController();
                $controller->login();
                break;

            case 'dashboard':
                $controller = new \App\Controllers\DashboardController();
                $controller->index();
                break;

            case 'logout':
                $controller = new \App\Controllers\AuthController();
                $controller->logout();
                break;

            

            
            case 'saveBudget':
                  $controller = new \App\Controllers\WalletController();
                  $controller->saveBudget();
                break;

            
            case 'store':
            $controller = new \App\Controllers\ExpenseController();
            $controller->store();
            break;
          


            case 'category_create':
               $controller = new \App\Controllers\CategoryController();
               $controller->create();
              break;

            case 'category_store':
            $controller = new \App\Controllers\CategoryController();
            $controller->store();
            break;



                default:
                http_response_code(404);
                echo "Page not found";
    
        }
    }
}
