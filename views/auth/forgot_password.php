<?php
// views/auth/forgot_password.php
include __DIR__ . '/../partials/header.php';

$message = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (empty($email)) {
        $error = "Please enter your email address.";
    } else {
        $pdo = get_db_connection();
        // Check if user exists
        // Hubi haddii isticmaalaha jiro
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            // Generate token
            // Samee token
            $token = bin2hex(random_bytes(32));
            // Token expiry (1 minute)
            // Waqtiga dhicitaanka (1 daqiiqo)
            $expires = date('Y-m-d H:i:s', strtotime('+1 minute'));

            try {
                // Save token to DB
                // Ku keydi token-ka database-ka
                $update = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expires_at = ? WHERE id = ?");
                $update->execute([$token, $expires, $user['id']]);
            } catch (PDOException $e) {
                // Handle missing columns
                // Xali haddii column-yada maqan yihiin
                if (strpos($e->getMessage(), 'column') !== false && strpos($e->getMessage(), 'does not exist') !== false) {
                    $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS reset_token VARCHAR(255)");
                    $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS reset_expires_at TIMESTAMP");

                    $update = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expires_at = ? WHERE id = ?");
                    $update->execute([$token, $expires, $user['id']]);
                } else {
                    throw $e;
                }
            }

            $resetLink = "http://" . $_SERVER['HTTP_HOST'] . "/reset-password?token=" . $token;

            // Log link for testing
            // Ku qor linkiga file si loo tijaabiyo
            $logFile = __DIR__ . '/../../reset_link_log.txt';
            $logContent = "------------------------------------------------\n";
            $logContent .= "Time: " . date('Y-m-d H:i:s') . "\n";
            $logContent .= "Email: " . $email . "\n";
            $logContent .= "Link: " . $resetLink . "\n";
            $logContent .= "------------------------------------------------\n\n";
            file_put_contents($logFile, $logContent, FILE_APPEND);

            $message = "Password reset link has been generated. <br>Check <b>reset_link_log.txt</b> in your project folder.";
        } else {
            $error = "No account found with this email.";
        }
    }
}
?>

<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-3xl shadow-2xl border border-slate-100">
        <div>
            <div class="mx-auto h-12 w-12 bg-orange-100 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-key text-orange-600 text-xl"></i>
            </div>
            <h2 class="text-center text-3xl font-extrabold text-slate-900">Forgot Password?</h2>
            <p class="mt-2 text-center text-sm text-slate-600">
                Enter your email address to reset your password.
            </p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4">
                <p class="text-sm text-red-700"><?php echo htmlspecialchars($error); ?></p>
            </div>
        <?php endif; ?>

        <?php if ($message): ?>
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-4">
                <p class="text-sm text-green-700"><?php echo $message; // Allowing HTML for the link ?></p>
            </div>
        <?php endif; ?>

        <form class="mt-8 space-y-6" action="" method="POST">
            <?php csrf_input(); ?>
            <div>
                <label for="email" class="block text-sm font-medium text-slate-700">Email address</label>
                <input id="email" name="email" type="email" required
                    class="mt-1 block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition"
                    placeholder="john@example.com">
            </div>

            <div>
                <button type="submit"
                    class="group relative w-full flex justify-center py-4 px-4 border border-transparent text-sm font-bold rounded-xl text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition shadow-lg shadow-orange-200">
                    Send Reset Link
                </button>
            </div>

            <div class="text-center">
                <a href="./login" class="font-medium text-slate-600 hover:text-orange-600 transition">
                    <i class="fas fa-arrow-left mr-1"></i> Back to Login
                </a>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>