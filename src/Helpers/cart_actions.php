<?php
// src/Helpers/cart_actions.php

// Check if request is POST
// Hubi in codsigu yahay POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Prevent admins from ordering
    // Ka jooji admin-ka inuu dalab sameeyo
    if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin') {
        header('Location: /products');
        exit;
    }

    // Get product ID and action type
    // Hel ID-ga alaabta iyo nooca ficilka (action)
    $product_id = $_POST['product_id'] ?? null;
    $action = $_POST['action'] ?? 'add';

    if ($product_id) {
        // Initialize cart if not exists
        // Bilow gaariga (cart) haddii uusan jirin
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // Add item to cart
        // Ku dar alaab gaariga
        if ($action === 'add') {
            $pdo = get_db_connection();
            // Fetch product details
            // Soo qaado faahfaahinta alaabta
            $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
            $stmt->execute([$product_id]);
            $product = $stmt->fetch();

            if ($product) {
                // If product already in cart, increase quantity
                // Haddii alaabtu horey ugu jirtay gaariga, kordhi tirada
                if (isset($_SESSION['cart'][$product_id])) {
                    $_SESSION['cart'][$product_id]['quantity']++;
                } else {
                    // Add new product to cart
                    // Ku dar alaab cusub gaariga
                    $_SESSION['cart'][$product_id] = [
                        'id' => $product['id'],
                        'title' => $product['title'],
                        'price' => $product['price'],
                        'image' => $product['image'],
                        'quantity' => 1
                    ];
                }
                $_SESSION['flash_message'] = "Added to cart!";
            }
        }
        // Remove item from cart
        // Ka saar alaabta gaariga
        elseif ($action === 'remove') {
            unset($_SESSION['cart'][$product_id]);
        }
        // Update quantity
        // Wax ka bedel tirada
        elseif ($action === 'update') {
            $quantity = (int) ($_POST['quantity'] ?? 1);
            if ($quantity > 0) {
                $_SESSION['cart'][$product_id]['quantity'] = $quantity;
            } else {
                unset($_SESSION['cart'][$product_id]);
            }
        }
    }
}

// Redirect back to previous page or cart
// Dib ugu celi bogga hore ama gaariga
header('Location: ' . ($_SERVER['HTTP_REFERER'] ?: './cart'));
exit;
