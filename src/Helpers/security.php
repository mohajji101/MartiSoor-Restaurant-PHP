<?php
// src/Helpers/security.php

/**
 * Generate CSRF Token
 * Abuur CSRF Token si loo sugo amniga foomamka
 */
function generate_csrf_token()
{
    // Check if token exists, if not create one
    // Hubi haddii token jiro, haddii kale samee mid cusub
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF Token
 * Hubi in CSRF Token-ka uu sax yahay
 */
function verify_csrf_token($token)
{
    // Compare session token with submitted token
    // Isbarbar dhig token-ka session-ka iyo kan la soo diray
    return !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Output CSRF Input Field
 * Soo saar input-ka CSRF ee foomka
 */
function csrf_input()
{
    echo '<input type="hidden" name="csrf_token" value="' . generate_csrf_token() . '">';
}
