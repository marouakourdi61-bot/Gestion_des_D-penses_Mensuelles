<?php
namespace App\Controllers;

use Core\Security;
use App\Models\User;

class AuthController extends Controller {

    public function login() {

        if (!isset($_SESSION)) session_start();

        // If already logged in → dashboard
        if (isset($_SESSION['user_id'])) {
            header('Location: /index.php?action=dashboard');
            exit();
        }

        $error = '';
        $success = $_SESSION['register_success'] ?? '';
        unset($_SESSION['register_success']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                if (!Security::checkCSRFToken($_POST['csrf_token'] ?? '')) {
                    throw new \Exception("Token CSRF invalide.");
                }

                $email = trim($_POST['email'] ?? '');
                $password = $_POST['password'] ?? '';

                if (empty($email) || empty($password)) {
                    throw new \Exception("Tous les champs sont requis.");
                }

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    throw new \Exception("Email invalide.");
                }

                // Using DB from base Controller
                $userModel = new User($this->db);
                $user = $userModel->findByEmail($email);

                if (!$user || !password_verify($password, $user['password'])) {
                    throw new \Exception("Email ou mot de passe incorrect.");
                }

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'] ?? 'user';

                //  REDIRECT TO DASHBOARD
                header('Location: /index.php?action=dashboard');
                exit();

            } catch (\Exception $e) {
                $error = $e->getMessage();
            }
        }

        require __DIR__ . '/../views/auth/login.php';
    }

    public function logout() {
        session_start();
        session_destroy();
        header('Location: /index.php?action=login');
        exit();
    }
}
