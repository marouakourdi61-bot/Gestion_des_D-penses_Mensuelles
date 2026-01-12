<?php

require_once __DIR__ . '/../models/Category.php';

class CategoryController extends Controller {

    public function create() {
        require __DIR__ . '/../views/categories/create.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];

            $categoryModel = new Category($this->db);
            $categoryModel->create($name);

            header("Location: index.php?controller=dashboard&action=index");
            exit;
        }
    }
}
