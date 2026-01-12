<?php

namespace App\Models;
class Expense {
    private $db;
    public function __construct($db) { $this->db = $db; }

    public function create($wallet_id, $title, $amount, $date, $category_id) {
        $stmt = $this->db->prepare("INSERT INTO transactions (wallet_id, title, amount, date, category_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$wallet_id, $title, $amount, $date, $category_id]);
    }
}
