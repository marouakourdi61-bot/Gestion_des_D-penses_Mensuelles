<?php


require_once __DIR__ . '/../layout/header.php';
require_once __DIR__ . '/../../../core/Database.php';

use Core\Database;

if (!isset($_SESSION['user_id'])) {
    header("Location: /wallet/public/index.php?controller=wallet&action=index");
    exit();
}

$db = Database::getInstance();
$user_id = $_SESSION['user_id'];
$month_year = date('Y-m');

$stmt = $db->prepare("SELECT * FROM wallets WHERE user_id = ? AND month_year = ?");
$stmt->execute([$user_id, $month_year]);
$wallet = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$wallet) {
    $stmt = $db->prepare("INSERT INTO wallets (user_id, month_year, budget, balance) VALUES (?, ?, 0, 0)");
    $stmt->execute([$user_id, $month_year]);
    $wallet = [
        'id' => $db->lastInsertId(),
        'budget' => 0,
        'balance' => 0
    ];
}

$budget = $wallet['budget'];

$stmt = $db->prepare("SELECT SUM(amount) FROM transactions WHERE wallet_id = ?");
$stmt->execute([$wallet['id']]);
$totalExpenses = $stmt->fetchColumn() ?: 0;

$remaining = $budget - $totalExpenses;

$stmt = $db->prepare("
    SELECT t.*, c.name AS category_name
    FROM transactions t
    JOIN categories c ON t.category_id = c.id
    WHERE t.wallet_id = ?
    ORDER BY t.date DESC
");
$stmt->execute([$wallet['id']]);
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Wallet</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">



<main class="max-w-6xl mx-auto px-4 mt-8">

    <!-- Budget -->
    <section class="bg-white p-6 rounded-lg shadow mb-8">
        <h2 class="text-xl font-bold mb-4 text-gray-800">Définir mon budget mensuel</h2>

        <form action="index.php?controller=wallet&action=saveBudget"
      method="POST" 
              class="flex flex-col sm:flex-row gap-4">

            <input 
                type="number" 
                name="budget" 
                placeholder="Entrez votre budget (ex: 5000)" 
                value="<?= htmlspecialchars($budget) ?>" 
                required 
                class="w-full sm:w-1/3 border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >

            <select name="category_id" class="border border-gray-300 rounded px-3 py-2">
            <option value="1">Nourriture</option>
            <option value="2">Transport</option>
            <option value="3">Loyer</option>
            <option value="4">Loisirs</option>
            <option value="5">Autre</option>
        </select>

            <button type="submit" 
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded">
                Enregistrer
            </button>
        </form>
    </section>

    <!-- Statistiques -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-10">
        <div class="bg-white p-6 rounded-lg shadow text-center">
            <h2 class="text-2xl font-bold text-blue-600"><?= number_format($budget, 2) ?> MAD</h2>
            <p class="text-gray-600">Budget total</p>
        </div>

        <div class="bg-white p-6 rounded-lg shadow text-center">
            <h2 class="text-2xl font-bold text-red-500"><?= number_format($totalExpenses, 2) ?> MAD</h2>
            <p class="text-gray-600">Total des dépenses</p>
        </div>

        <div class="bg-white p-6 rounded-lg shadow text-center">
            <h2 class="text-2xl font-bold text-green-600"><?= number_format($remaining, 2) ?> MAD</h2>
            <p class="text-gray-600">Solde restant</p>
        </div>
    </div>

    <!-- Liste des dépenses -->
    <section class="bg-white p-6 rounded-lg shadow">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-800">Mes Dépenses</h2>
            <a href="/public/index.php?controller=expense&action=create"
               class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                + Ajouter
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-blue-600 text-white">
                        <th class="px-4 py-2 text-left">Titre</th>
                        <th class="px-4 py-2 text-left">Montant</th>
                        <th class="px-4 py-2 text-left">Date</th>
                        <th class="px-4 py-2 text-left">Catégorie</th>
                        <th class="px-4 py-2 text-left">Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php if($transactions): ?>
                    <?php foreach($transactions as $t): ?>
                    <tr class="border-b">
                        <td class="px-4 py-2"><?= htmlspecialchars($t['title']) ?></td>
                        <td class="px-4 py-2"><?= number_format($t['amount'], 2) ?> MAD</td>
                        <td class="px-4 py-2"><?= htmlspecialchars($t['date']) ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($t['category_name']) ?></td>
                        <td class="px-4 py-2">
                            <a href="/public/index.php?controller=expense&action=delete&id=<?= $t['id'] ?>"
                               class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded">
                                Supprimer
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="px-4 py-2 text-center text-gray-500">
                            Aucune dépense enregistrée
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

</main>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
</body>
</html>
