<?php
// views/auth/register.php
include __DIR__ . '/../partials/header.php';

$errors = [];
// Process registration form
// Ka shaqee foomka diiwaangelinta
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validation
    // Hubinta
    if (empty($name))
        $errors[] = "Name is required.";
    if (empty($email))
        $errors[] = "Email is required.";
    if (empty($password))
        $errors[] = "Password is required.";
    if ($password !== $confirm_password)
        $errors[] = "Passwords do not match.";
    if (!verify_csrf_token($_POST['csrf_token'] ?? ''))
        $errors[] = "Invalid CSRF token.";

    // Password strength check
    // Hubinta awoodda furaha
    if (!empty($password)) {
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z0-9]+$/', $password)) {
            $errors[] = "Password must contain uppercase, lowercase, and numbers. No special characters allowed.";
        } elseif (strlen($password) < 8) {
            $errors[] = "Password must be at least 8 characters long.";
        }
    }

    if (empty($errors)) {
        $pdo = get_db_connection();
        // Check if email exists
        // Hubi haddii email-ka horey loo diiwaangeliyay
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = "Email already registered.";
        } else {
            // Hash password and insert user
            // Qari furaha oo geli isticmaalaha database-ka
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $hashed_password]);

            // Auto login after registration
            // Toos u geli (login) kadib diiwaangelinta
            $_SESSION['user'] = [
                'id' => $pdo->lastInsertId(),
                'name' => $name,
                'email' => $email,
                'role' => 'customer'
            ];
            header('Location: ./');
            exit;
        }
    }
}
?>

<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-3xl shadow-2xl border border-slate-100">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-slate-900">Create an account</h2>
            <p class="mt-2 text-center text-sm text-slate-600">
                Join our gourmet community today
            </p>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4">
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

        <form class="mt-8 space-y-6" action="" method="POST">
            <?php csrf_input(); ?>
            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700">Full Name</label>
                    <input id="name" name="name" type="text" value="<?php echo htmlspecialchars($name ?? ''); ?>"
                        required
                        class="mt-1 block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition"
                        placeholder="John Doe">
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700">Email address</label>
                    <input id="email" name="email" type="email" value="<?php echo htmlspecialchars($email ?? ''); ?>"
                        required
                        class="mt-1 block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition"
                        placeholder="john@example.com">
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
                    <input id="password" name="password" type="password" required
                        class="mt-1 block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition"
                        placeholder="••••••••">
                    <div id="password-feedback" class="mt-2 text-xs font-bold hidden"></div>
                </div>
                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-slate-700">Confirm
                        Password</label>
                    <input id="confirm_password" name="confirm_password" type="password" required
                        class="mt-1 block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition"
                        placeholder="••••••••">
                    <div id="match-feedback" class="mt-2 text-xs font-bold hidden"></div>
                </div>
            </div>

            <div>
                <button type="submit" id="submit-btn"
                    class="group relative w-full flex justify-center py-4 px-4 border border-transparent text-sm font-bold rounded-xl text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition shadow-lg shadow-orange-200">
                    Sign up
                </button>
            </div>

            <div class="text-center">
                <p class="text-sm text-slate-600">
                    Already have an account? <a href="./login"
                        class="font-bold text-orange-600 hover:text-orange-500 transition">Log in</a>
                </p>
            </div>
        </form>

        <script>
            const passwordInput = document.getElementById('password');
            const confirmInput = document.getElementById('confirm_password');
            const feedback = document.getElementById('password-feedback');
            const matchFeedback = document.getElementById('match-feedback');
            const submitBtn = document.getElementById('submit-btn');

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
    </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>