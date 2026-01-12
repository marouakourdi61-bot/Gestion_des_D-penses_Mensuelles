<?php
namespace App\Controllers;

class DashboardController extends Controller {

    public function index() {
        

        if (!isset($_SESSION['user_id'])) {
            header('Location: /index.php?action=login');
            exit();
        }

        require __DIR__ . '/../views/wallet/dashboard.php';
    }
}
