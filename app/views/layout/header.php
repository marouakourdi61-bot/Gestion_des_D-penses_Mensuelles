<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?action=login");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Wallet</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

<header class="bg-blue-600 text-white shadow">
    <div class="max-w-6xl mx-auto px-4 py-4 flex justify-between items-center">

        <div class="flex items-center gap-2">
            <h1 class="text-2xl font-bold">Mon Wallet</h1>
        </div>

        <nav class="flex items-center gap-4 text-sm sm:text-base">
            <a href="index.php?action=dashboard" class="hover:underline">Dashboard</a>
            <a href="index.php?action=expenses" class="hover:underline">Dépenses</a>
            <a href="index.php?action=profile" class="hover:underline">Profil</a>

            <a href="index.php?action=logout"
               class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded transition">
               Déconnexion
            </a>
        </nav>

    </div>
</header>
