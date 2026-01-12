<?php

namespace App\Models;

use PDO;
class Wallet {
    private $db;
    public function __construct($db) {
        $this->db = $db;
    }

    public function getWalletByUserAndMonth($user_id, $month_year) {
        $stmt = $this->db->prepare("SELECT * FROM wallets WHERE user_id=? AND month_year=?");
        $stmt->execute([$user_id, $month_year]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createWallet($user_id, $month_year, $budget) {
        $stmt = $this->db->prepare("INSERT INTO wallets (user_id, month_year, budget, balance) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $month_year, $budget, $budget]);
    }

    public function updateBudget($wallet_id, $budget) {
        $stmt = $this->db->prepare("UPDATE wallets SET budget=?, balance=? WHERE id=?");
        $stmt->execute([$budget, $budget, $wallet_id]);
    }
}
