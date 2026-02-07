<?php
// views/admin/users.php
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../login');
    exit;
}

include __DIR__ . '/../partials/header.php';

$pdo = get_db_connection();
// Fetch all users
// Soo qaado dhammaan isticmaalayaasha
$stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();
?>

<div class="bg-slate-50 min-h-screen py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-center mb-12">
            <div>
                <div class="flex items-center gap-4 mb-2">
                    <a href="../admin" class="text-slate-400 hover:text-orange-600 transition"><i
                            class="fas fa-chevron-left"></i></a>
                    <h1 class="text-4xl font-bold text-slate-900">User Management</h1>
                </div>
                <p class="text-slate-500">Manage user accounts and roles</p>
            </div>

            <a href="./users/create"
                class="mt-6 md:mt-0 bg-orange-600 text-white px-8 py-4 rounded-2xl font-bold shadow-lg shadow-orange-200 hover:bg-orange-700 transition flex items-center gap-2">
                <i class="fas fa-user-plus"></i>
                <span>Add New User</span>
            </a>
        </div>

        <div class="bg-white rounded-[2rem] shadow-xl border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                        <tr>
                            <th class="px-8 py-6">User</th>
                            <th class="px-8 py-6">Email</th>
                            <th class="px-8 py-6">Role</th>
                            <th class="px-8 py-6">Joined Date</th>
                            <th class="px-8 py-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($users as $user): ?>
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-8 py-6">
                                    <span
                                        class="font-bold text-slate-900"><?php echo htmlspecialchars($user['name']); ?></span>
                                </td>
                                <td class="px-8 py-6 text-slate-600"><?php echo htmlspecialchars($user['email']); ?></td>
                                <td class="px-8 py-6">
                                    <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase <?php
                                    echo $user['role'] === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700';
                                    ?>">
                                        <?php echo $user['role']; ?>
                                    </span>
                                </td>
                                <td class="px-8 py-6 text-slate-500 text-sm">
                                    <?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                <td class="px-8 py-6 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="./users/edit?id=<?php echo $user['id']; ?>"
                                            class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if ($user['id'] != $_SESSION['user']['id']): ?>
                                            <a href="./users/delete?id=<?php echo $user['id']; ?>"
                                                onclick="return confirm('Are you sure you want to delete this user?')"
                                                class="w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center hover:bg-red-600 hover:text-white transition">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>