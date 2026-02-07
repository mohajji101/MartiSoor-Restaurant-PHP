<?php
// views/profile.php
if (!isset($_SESSION['user'])) {
    header('Location: /login');
    exit;
}

$pdo = get_db_connection();
$user_id = $_SESSION['user']['id'];
$errors = [];
$success = "";

// Fetch user data
// Soo qaado xogta isticmaalaha
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';

    if (empty($name))
        $errors[] = "Name is required.";
    if (empty($email))
        $errors[] = "Email is required.";

    // Update basic info
    // Cusbooneysii macluumaadka aasaasiga ah
    if (empty($errors)) {
        // Check if email is already used by another user
        // Hubi haddii email-ka uu isticmaalayo qof kale
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$email, $user_id]);
        if ($stmt->fetch()) {
            $errors[] = "Email is already taken.";
        } else {
            $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
            $stmt->execute([$name, $email, $user_id]);
            $_SESSION['user']['name'] = $name;
            $_SESSION['user']['email'] = $email;
            $success = "Profile updated successfully!";
        }
    }

    // Handle password change
    // Xakamee badalida furaha
    if (!empty($new_password)) {
        if (password_verify($current_password, $user['password'])) {
            // Strong password validation
            // Hubinta awoodda furaha
            if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z0-9]+$/', $new_password)) {
                $errors[] = "New password must contain uppercase, lowercase, and numbers. No special characters.";
            } elseif (strlen($new_password) < 8) {
                $errors[] = "New password must be at least 8 characters long.";
            } else {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([$hashed_password, $user_id]);
                $success = "Profile and password updated successfully!";
            }
        } else {
            $errors[] = "Current password is incorrect.";
        }
    }
}

include __DIR__ . '/partials/header.php';
?>

<div class="bg-slate-50 min-h-screen py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-12">
            <h1 class="text-4xl font-bold text-slate-900 mb-2">My Profile</h1>
            <p class="text-slate-500">Manage your account details and security</p>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-8 rounded-r-xl">
                <div class="flex">
                    <div class="flex-shrink-0"><i class="fas fa-exclamation-circle text-red-500"></i></div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700">
                            <?php foreach ($errors as $error)
                                echo htmlspecialchars($error) . '<br>'; ?>
                        </p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-8 rounded-r-xl">
                <div class="flex">
                    <div class="flex-shrink-0"><i class="fas fa-check-circle text-green-500"></i></div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700"><?php echo htmlspecialchars($success); ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Summary Card -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-[2rem] shadow-xl border border-slate-100 p-8 text-center sticky top-24">
                    <div
                        class="w-32 h-32 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center mx-auto mb-6 text-4xl font-bold">
                        <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                    </div>
                    <h2 class="text-2xl font-bold text-slate-900 mb-1"><?php echo htmlspecialchars($user['name']); ?>
                    </h2>
                    <p class="text-slate-500 mb-6"><?php echo htmlspecialchars($user['email']); ?></p>
                    <div
                        class="inline-flex items-center px-4 py-1 rounded-full bg-slate-100 text-slate-600 text-xs font-bold uppercase tracking-wider">
                        <?php echo htmlspecialchars($user['role']); ?>
                    </div>
                </div>
            </div>

            <!-- Edit Form -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-[2rem] shadow-xl border border-slate-100 p-10">
                    <form action="" method="POST" class="space-y-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <h3 class="text-xl font-bold text-slate-900 border-b border-slate-50 pb-4 mb-4">General
                                    Information</h3>
                            </div>
                            <div class="md:col-span-1">
                                <label for="name" class="block text-sm font-bold text-slate-700 mb-2">Display
                                    Name</label>
                                <input type="text" id="name" name="name"
                                    value="<?php echo htmlspecialchars($user['name']); ?>" required
                                    class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-orange-500 focus:outline-none transition">
                            </div>
                            <div class="md:col-span-1">
                                <label for="email" class="block text-sm font-bold text-slate-700 mb-2">Email
                                    Address</label>
                                <input type="email" id="email" name="email"
                                    value="<?php echo htmlspecialchars($user['email']); ?>" required
                                    class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-orange-500 focus:outline-none transition">
                            </div>

                            <div class="md:col-span-2 mt-8">
                                <h3 class="text-xl font-bold text-slate-900 border-b border-slate-50 pb-4 mb-4">Change
                                    Password</h3>
                                <p class="text-slate-500 text-sm mb-6">Leave new password blank if you don't want to
                                    change it.</p>
                            </div>
                            <div class="md:col-span-2">
                                <label for="current_password"
                                    class="block text-sm font-bold text-slate-700 mb-2">Current Password</label>
                                <input type="password" id="current_password" name="current_password"
                                    class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-orange-500 focus:outline-none transition">
                            </div>
                            <div class="md:col-span-2">
                                <label for="new_password" class="block text-sm font-bold text-slate-700 mb-2">New
                                    Password (Uppercase, Lowercase, Number)</label>
                                <input type="password" id="new_password" name="new_password"
                                    class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-orange-500 focus:outline-none transition">
                            </div>
                        </div>

                        <div class="pt-8 border-t border-slate-100">
                            <button type="submit"
                                class="w-full md:w-auto bg-orange-600 text-white px-10 py-4 rounded-2xl font-bold shadow-lg shadow-orange-200 hover:bg-orange-700 transition">
                                Update My Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>