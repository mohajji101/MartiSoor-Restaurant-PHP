<?php
// views/auth/reset_password.php
include __DIR__ . '/../partials/header.php';

$token = $_GET['token'] ?? '';
$error = "";
$success = "";

if (!$token) {
    header('Location: ./login');
    exit;
}

$pdo = get_db_connection();

// Verify token validity
// Hubi jiritaanka iyo waqtiga token-ka
$current_time = date('Y-m-d H:i:s');
$stmt = $pdo->prepare("SELECT * FROM users WHERE reset_token = ? AND reset_expires_at > ?");
$stmt->execute([$token, $current_time]);
$user = $stmt->fetch();

if (!$user) {
    $error = "Invalid or expired reset token.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user) {
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validate password
    // Hubi furaha cusub
    if (empty($password)) {
        $error = "Password is required.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z0-9]+$/', $password)) {
        $error = "Password must contain uppercase, lowercase, and numbers.";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters long.";
    } else {
        // Update password and clear token
        // Cusbooneysii furaha oo tir token-ka
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $update = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires_at = NULL WHERE id = ?");
        $update->execute([$hashed_password, $user['id']]);
        $success = "Password has been reset successfully! <a href='./login' class='underline font-bold'>Login now</a>";
        $user = null; // Hide form
    }
}
?>

<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-3xl shadow-2xl border border-slate-100">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-slate-900">Reset Password</h2>
            <p class="mt-2 text-center text-sm text-slate-600">
                Create a new strong password for your account
            </p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4">
                <p class="text-sm text-red-700"><?php echo htmlspecialchars($error); ?></p>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-4">
                <p class="text-sm text-green-700"><?php echo $success; ?></p>
            </div>
        <?php endif; ?>

        <?php if ($user): ?>
            <form class="mt-8 space-y-6" action="" method="POST">
                <?php csrf_input(); ?>
                <div class="space-y-4">
                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-700">New Password</label>
                        <input id="password" name="password" type="password" required
                            class="mt-1 block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition"
                            placeholder="••••••••">
                        <div id="password-feedback" class="mt-2 text-xs font-bold hidden"></div>
                    </div>
                    <div>
                        <label for="confirm_password" class="block text-sm font-medium text-slate-700">Confirm New
                            Password</label>
                        <input id="confirm_password" name="confirm_password" type="password" required
                            class="mt-1 block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition"
                            placeholder="••••••••">
                        <div id="match-feedback" class="mt-2 text-xs font-bold hidden"></div>
                    </div>
                </div>

                <div>
                    <button type="submit"
                        class="group relative w-full flex justify-center py-4 px-4 border border-transparent text-sm font-bold rounded-xl text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition shadow-lg shadow-orange-200">
                        Reset Password
                    </button>
                </div>
            </form>

            <script>
                const passwordInput = document.getElementById('password');
                const confirmInput = document.getElementById('confirm_password');
                const feedback = document.getElementById('password-feedback');
                const matchFeedback = document.getElementById('match-feedback');
                const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z0-9]+$/;

                function validatePassword() {
                    const val = passwordInput.value;
                    if (!val) {
                        feedback.classList.add('hidden');
                        return;
                    }
                    feedback.classList.remove('hidden');

                    if (val.length < 8) {
                        feedback.textContent = 'Weak: Must be at least 8 characters.';
                        feedback.className = 'mt-2 text-xs font-bold text-red-500';
                    } else if (!regex.test(val)) {
                        feedback.textContent = 'Weak: Must contain uppercase, lowercase, and numbers.';
                        feedback.className = 'mt-2 text-xs font-bold text-orange-500';
                    } else {
                        feedback.textContent = 'Strong Password';
                        feedback.className = 'mt-2 text-xs font-bold text-green-500';
                    }
                    validateMatch();
                }

                function validateMatch() {
                    const val = confirmInput.value;
                    if (!val) {
                        matchFeedback.classList.add('hidden');
                        return;
                    }
                    matchFeedback.classList.remove('hidden');

                    if (val !== passwordInput.value) {
                        matchFeedback.textContent = 'Passwords do not match';
                        matchFeedback.className = 'mt-2 text-xs font-bold text-red-500';
                    } else {
                        matchFeedback.textContent = 'Passwords match';
                        matchFeedback.className = 'mt-2 text-xs font-bold text-green-500';
                    }
                }

                passwordInput.addEventListener('input', validatePassword);
                confirmInput.addEventListener('input', validateMatch);
            </script>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>