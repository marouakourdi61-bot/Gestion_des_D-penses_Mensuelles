<?php
namespace Core;

class Security {

    // Génère un token CSRF et le stocke en session
    public static function generateCSRFToken(): string {
        if (!isset($_SESSION)) session_start();
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    // Vérifie le token CSRF
    public static function checkCSRFToken(string $token): bool {
        if (!isset($_SESSION)) session_start();
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    // Nettoie une chaîne pour éviter les attaques XSS
    public static function clean(string $data): string {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        return $data;
    }

    // Valide un email
    public static function validateEmail(string $email): bool {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    // Valide un mot de passe (min 8 caractères)
    public static function validatePassword(string $password): bool {
        return strlen($password) >= 8;
    }

    // Hash sécurisé d'un mot de passe
    public static function hashPassword(string $password): string {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    // Vérifie un mot de passe avec son hash
    public static function verifyPassword(string $password, string $hash): bool {
        return password_verify($password, $hash);
    }

    // Vérifie si l'utilisateur est connecté
    public static function isLoggedIn(): bool {
        if (!isset($_SESSION)) session_start();
        return isset($_SESSION['user_id']) && isset($_SESSION['user_role']);
    }

    // Vérifie si l'utilisateur est enseignant
    public static function isTeacher(): bool {
        return self::isLoggedIn() && $_SESSION['user_role'] === 'enseignant';
    }

    // Vérifie si l'utilisateur est étudiant
    public static function isStudent(): bool {
        return self::isLoggedIn() && $_SESSION['user_role'] === 'etudiant';
    }

    // Redirige si non connecté
    public static function requireLogin(): void {
        if (!self::isLoggedIn()) {
            header('Location: ../auth/login.php');
            exit();
        }
    }

    // Redirige si non enseignant
    public static function requireTeacher(): void {
        self::requireLogin();
        if (!self::isTeacher()) {
            header('Location: ../teacher/dashboard.php');
            exit();
        }
    }

    // Redirige si non étudiant
    public static function requireStudent(): void {
        self::requireLogin();
        if (!self::isStudent()) {
            header('Location: ../student/dashboard.php');
            exit();
        }
    }

    // Déconnexion sécurisée
    public static function logout(): void {
        if (!isset($_SESSION)) session_start();
        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }
}
