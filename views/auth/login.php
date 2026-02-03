<?php
// views/auth/login.php
include __DIR__ . '/../partials/header.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email)) $errors[] = "Email is required.";
    if (empty($password)) $errors[] = "Password is required.";
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) $errors[] = "Invalid CSRF token.";

    if (empty($errors)) {
        $pdo = get_db_connection();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role']
            ];
            header('Location: ./');
            exit;
        } else {
            $errors[] = "Invalid email or password.";
        }
    }
}
?>

<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-3xl shadow-2xl border border-slate-100">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-slate-900">Sign in to your account</h2>
            <p class="mt-2 text-center text-sm text-slate-600">
                Welcome back to MartiSoor Restaurant
            </p>
        </div>
        
        <?php if (!empty($errors)): ?>
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4">
                <div class="flex">
                    <div class="flex-shrink-0"><i class="fas fa-exclamation-circle text-red-500"></i></div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700">
                            <?php foreach ($errors as $error) echo htmlspecialchars($error) . '<br>'; ?>
                        </p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <form class="mt-8 space-y-6" action="" method="POST">
            <?php csrf_input(); ?>
            <div class="space-y-4">
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700">Email address</label>
                    <input id="email" name="email" type="email" required class="mt-1 block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition" placeholder="john@example.com">
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
                    <input id="password" name="password" type="password" required class="mt-1 block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition" placeholder="••••••••">
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember-me" name="remember-me" type="checkbox" class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-slate-300 rounded">
                    <label for="remember-me" class="ml-2 block text-sm text-slate-900">
                        Remember me
                    </label>
                </div>

                <div class="text-sm">
                    <a href="/forgot-password" class="font-medium text-orange-600 hover:text-orange-500 transition">
                        Forgot your password?
                    </a>
                </div>
            </div>

            <div>
                <button type="submit" class="group relative w-full flex justify-center py-4 px-4 border border-transparent text-sm font-bold rounded-xl text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition shadow-lg shadow-orange-200">
                    Sign in
                </button>
            </div>
            
            <div class="text-center">
                <p class="text-sm text-slate-600">
                    Don't have an account? <a href="./register" class="font-bold text-orange-600 hover:text-orange-500 transition">Sign up</a>
                </p>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
