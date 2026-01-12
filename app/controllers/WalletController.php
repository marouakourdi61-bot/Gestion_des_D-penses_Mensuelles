<?php

namespace App\Controllers;

use App\Controllers\Controller; 
use App\models\Wallet;





class WalletController extends Controller {

    

    public function saveBudget() {

        

        if (!isset($_SESSION['user_id'])) {
            header("Location: /wallet/public/index.php?controller=auth&action=login");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = $_SESSION['user_id'];
            $budget = floatval($_POST['budget'] ?? 0);
            $month_year = date('Y-m');

            $walletModel = new Wallet($this->db);

            $wallet = $walletModel->getWalletByUserAndMonth($user_id, $month_year);

            if ($wallet) {

                $walletModel->updateBudget($wallet['id'], $budget);
            } else {
                
                $walletModel->createWallet($user_id, $month_year, $budget);
            }

            $_SESSION['success'] = "Budget mis à jour avec succès !";

            
            header("Location: /wallet/public/index.php?controller=wallet&action=index");
            exit();
        }
    }
}
