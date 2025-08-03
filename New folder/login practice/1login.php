<?php
session_start();

// If already logged in, redirect to index
if (isset($_SESSION['userlogin']) && $_SESSION['userlogin'] === true) {
    header("Location: index.php");
    exit; // Always exit after redirect
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tap. Pay. Now! Login</title>

    <!-- Bootstrap 4 CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" 
          integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" 
          crossorigin="anonymous">

    <!-- Custom Styles -->
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<div class="container h-100">
    <div class="d-flex justify-content-center h-100">
        <div class="user_card">
            <!-- Logo -->
            <div class="d-flex justify-content-center">
                <div class="brand_logo_container">
                    <img src="img/logo.png" class="brand_logo" alt="Tap. Pay. Now Logo">
                </div>
            </div>

            <!-- Login Form -->
            <div class="d-flex justify-content-center form_container">
                <form id="loginForm">
                    <div class="input-group mb-3">
                        <div class="input-group-append">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                        </div>
                        <input 
                            type="text" 
                            name="username" 
                            id="username" 
                            class="form-control input_user" 
                            placeholder="Email Address" 
                            required>
                    </div>

                    <div class="input-group mb-2">
                        <div class="input-group-append">
                            <span class="input-group-text"><i class="fas fa-key"></i></span>
                        </div>
                        <input 
                            type="password" 
                            name="password" 
                            id="password" 
                            class="form-control input_pass" 
                            placeholder="Password" 
                            required>
                    </div>

                    <div class="form-group mb-3">
                        <div class="custom-control custom-checkbox">
                            <input 
                                type="checkbox" 
                                name="rememberme" 
                                class="custom-control-input" 
                                id="customControlInline">
                            <label class="custom-control-label" for="customControlInline">Remember me</label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-center mt-3 login_container">
                        <button type="submit" class="btn login_btn">Login</button>
                    </div>
                </form>
            </div>

            <!-- Links -->
            <div class="mt-4">
                <div class="d-flex justify-content-center links">
                    Don't have an account? <a href="registration.php" class="ml-2">Sign Up</a>
                </div>
                <div class="d-flex justify-content-center mt-2">
                    <a href="#" class="text-white">Forgot your password?</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.3.1.min.js"
        integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
        crossorigin="anonymous"></script>

<!-- Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Login Script -->
<script>
$(function () {
    $('#loginForm').on('submit', function (e) {
        e.preventDefault(); // Prevent default form submission

        const email = $('#username').val().trim();
        const password = $('#password').val();

        // Client-side validation
        if (!email || !password) {
            Swal.fire('Error', 'Please fill in all fields.', 'warning');
            return;
        }

        // Send login data via AJAX
        $.ajax({
            type: 'POST',
            url: 'jslogin.php',
            data: {
                username: email,
                password: password
            },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Logged In!',
                        text: response.message,
                        timer: 1200,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = 'index.php';
                    });
                } else {
                    Swal.fire('Login Failed', response.message, 'error');
                }
            },
            error: function () {
                Swal.fire('Error', 'Something went wrong. Please try again.', 'error');
            }
        });
    });
});
</script>

</body>
</html>