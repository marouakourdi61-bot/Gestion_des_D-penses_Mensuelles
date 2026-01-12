<?php

namespace App\Controllers;

use Core\Database;

class Controller {
    protected $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    protected function getCurrentWalletId() {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }

        $user_id = $_SESSION['user_id'];
        $month_year = date('Y-m');

        $stmt = $this->db->prepare("SELECT id FROM wallets WHERE user_id = ? AND month_year = ?");
        $stmt->execute([$user_id, $month_year]);
        $wallet = $stmt->fetch();

        return $wallet['id'] ?? null;
    }
}

