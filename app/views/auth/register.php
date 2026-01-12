<?php
require_once __DIR__ . '/../../../vendor/autoload.php';

use Core\Security;
use Core\Database;
use App\Models\User;


$error = '';
$success = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
       
        if (!Security::checkCSRFToken($_POST['csrf_token'] ?? '')) {
            throw new Exception("Token CSRF invalide.");
        }

        
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Validation 
        if (empty($name) || empty($email) || empty($password) || empty($confirmPassword)) {
            throw new Exception("Tous les champs sont requis.");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Email invalide.");
        }

        if ($password !== $confirmPassword) {
            throw new Exception("Les mots de passe ne correspondent pas.");
        }

        if (strlen($password) < 8) {
            throw new Exception("Le mot de passe doit contenir au moins 8 caractères.");
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $db = Database::getInstance();
        $userModel = new User($db);

        if ($userModel->exists($email)) {
            throw new Exception("Un compte avec cet email existe déjà.");
        }

        $userModel->create([
            'name' => $name,
            'email' => $email,
            'password' => $passwordHash
        ]);

        $success = "Compte créé avec succès ! Vous pouvez vous connecter.";
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription - Wallet</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-indigo-600 to-purple-700 min-h-screen flex items-center justify-center py-12">

<div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-md">

    <!-- Titre -->
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Créer un compte</h1>
        <p class="text-gray-600 mt-2">Application Wallet</p>
    </div>

    <!-- Message d'erreur -->
    <?php if (!empty($error)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <!-- Message de succès -->
    <?php if (!empty($success)): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <!-- FORMULAIRE -->
    <form action="" method="POST">

        <!-- CSRF -->
         <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken(); ?>">

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Nom complet</label>
            <input type="text" name="name" required
                   class="w-full px-4 py-3 border rounded-lg"
                   placeholder="Ahmed Benjelloun"
                   value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
            <input type="email" name="email" required
                   class="w-full px-4 py-3 border rounded-lg"
                   placeholder="votre@email.com"
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Mot de passe</label>
            <input type="password" name="password" required
                   class="w-full px-4 py-3 border rounded-lg"
                   placeholder="Min. 8 caractères">
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2">Confirmer mot de passe</label>
            <input type="password" name="confirm_password" required
                   class="w-full px-4 py-3 border rounded-lg"
                   placeholder="••••••••">
        </div>

        <button type="submit"
                class="w-full bg-indigo-600 text-white py-3 rounded-lg font-semibold hover:bg-indigo-700 transition">
            S'inscrire
        </button>
    </form>

    <!-- Lien login -->
    <div class="mt-6 text-center">
        <p class="text-gray-600">
            Déjà un compte ?
            <a href="login.php" class="text-indigo-600 font-semibold hover:text-indigo-700">
                Se connecter
            </a>
        </p>
    </div>

</div>
</body>
</html>
