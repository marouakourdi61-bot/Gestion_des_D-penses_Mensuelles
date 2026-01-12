<?php


namespace App\Controllers;

require_once __DIR__ . '/../models/Expense.php';
require_once __DIR__ . '/Controller.php';

use App\Models\Expense;

class ExpenseController extends Controller {
    public function store() {
        $title = $_POST['title'];
        $amount = $_POST['amount'];
        $date = $_POST['date'];
        $category_id = $_POST['category_id'];
        $wallet_id = $this->getCurrentWalletId();

        $expenseModel = new Expense($this->db);
        $expenseModel->create($wallet_id, $title, $amount, $date, $category_id);

        header("Location: index.php?controller=dashboard&action=index");
        exit;
    }
}
