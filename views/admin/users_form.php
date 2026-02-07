<?php
// views/admin/users_form.php
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../../login');
    exit;
}

$pdo = get_db_connection();
$id = $_GET['id'] ?? null;
$user = null;
$errors = [];

// Fetch user data if editing
// Soo qaado xogta isticmaalaha haddii la bedelayo
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch();
    if (!$user) {
        header('Location: ./');
        exit;
    }
}

// Handle form submission
// Xakamee soo gudbinta foomka
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role = $_POST['role'] ?? 'customer';
    $password = $_POST['password'] ?? '';

    // Validate inputs
    // Hubi xogta la geliyay
    if (empty($name))
        $errors[] = "Name is required.";
    if (empty($email))
        $errors[] = "Email is required.";

    // Check email uniqueness if it changed or new user
    // Hubi in email-ka uusan jirin haddii la bedelay ama qof cusub yahay
    if (empty($errors)) {
        if ($id) {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->execute([$email, $id]);
        } else {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
        }
        if ($stmt->fetch()) {
            $errors[] = "Email already in use.";
        }
    }

    // Password strength check
    // Hubinta awoodda furaha
    if (empty($errors)) {
        if (!empty($password)) {
            if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z0-9]+$/', $password)) {
                $errors[] = "Password must contain uppercase, lowercase, and numbers. No special characters allowed.";
            } elseif (strlen($password) < 8) {
                $errors[] = "Password must be at least 8 characters long.";
            }
        }
    }

    if (empty($errors)) {
        if ($id) {
            // Update existing user
            // Cusbooneysii isticmaalaha jira
            if (!empty($password)) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, role = ?, password = ? WHERE id = ?");
                $stmt->execute([$name, $email, $role, $hashed_password, $id]);
            } else {
                $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, role = ? WHERE id = ?");
                $stmt->execute([$name, $email, $role, $id]);
            }
        } else {
            // Create new user
            // Samee isticmaale cusub
            if (empty($password)) {
                $errors[] = "Password is required for new users.";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (name, email, role, password) VALUES (?, ?, ?, ?)");
                $stmt->execute([$name, $email, $role, $hashed_password]);
            }
        }

        if (empty($errors)) {
            header('Location: /PHP/Projects/Restaurant-Ordering-System/public/admin/users');
            exit;
        }
    }
}

include __DIR__ . '/../partials/header.php';
?>

<div class="bg-slate-50 min-h-screen py-12">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-12">
            <div class="flex items-center gap-4 mb-2">
                <a href="/admin/users" class="text-slate-400 hover:text-orange-600 transition"><i
                        class="fas fa-arrow-left"></i></a>
                <h1 class="text-4xl font-bold text-slate-900"><?php echo $id ? 'Edit User' : 'Add New User'; ?></h1>
            </div>
            <p class="text-slate-500">Enter user details and assign roles</p>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-8">
                <p class="text-sm text-red-700">
                    <?php foreach ($errors as $error)
                        echo htmlspecialchars($error) . '<br>'; ?>
                </p>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-[2rem] shadow-xl border border-slate-100 p-10">
            <form action="" method="POST" class="space-y-8">
                <div class="space-y-6">
                    <div>
                        <label for="name" class="block text-sm font-bold text-slate-700 mb-2">Full Name</label>
                        <input type="text" id="name" name="name"
                            value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" required
                            class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-orange-500 focus:outline-none transition">
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-bold text-slate-700 mb-2">Email Address</label>
                        <input type="email" id="email" name="email"
                            value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required
                            class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-orange-500 focus:outline-none transition">
                    </div>

                    <div>
                        <label for="role" class="block text-sm font-bold text-slate-700 mb-2">Role</label>
                        <select id="role" name="role"
                            class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-orange-500 focus:outline-none transition appearance-none">
                            <option value="customer" <?php echo (isset($user['role']) && $user['role'] === 'customer') ? 'selected' : ''; ?>>Customer</option>
                            <option value="admin" <?php echo (isset($user['role']) && $user['role'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
                        </select>
                    </div>

                    <div>
                        <label for="password"
                            class="block text-sm font-bold text-slate-700 mb-2"><?php echo $id ? 'New Password (leave blank to keep current)' : 'Password'; ?></label>
                        <input type="password" id="password" name="password"
                            class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-orange-500 focus:outline-none transition">
                    </div>
                </div>

                <div class="pt-8 border-t border-slate-100 flex gap-4">
                    <button type="submit"
                        class="flex-1 bg-orange-600 text-white px-8 py-4 rounded-2xl font-bold shadow-lg shadow-orange-200 hover:bg-orange-700 transition">
                        <?php echo $id ? 'Update User' : 'Create User'; ?>
                    </button>
                    <a href="/admin/users"
                        class="px-8 py-4 rounded-2xl font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>