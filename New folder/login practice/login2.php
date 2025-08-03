<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tap. Pay. Now! Login</title>

    <!-- Bootstrap 4 CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" 
          integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" 
          crossorigin="anonymous">

    <!-- Custom Styles -->
    <link rel="stylesheet" href="css/styles.css?v=1.1">
</head>
<body>
    <div class="container h-100">
        <div class="d-flex justify-content-center h-100">
            <div class="user_card">
                <!-- Brand Logo on Top -->
                <div class="d-flex justify-content-center">
                    <div class="brand_logo_container">
                        <img src="img/logo.png" class="brand_logo" alt="Tap. Pay. Now Logo">
                    </div>
                </div>

                <!-- Login Form -->
                <div class="d-flex justify-content-center form_container">
                    <form action="login_process.php" method="POST">
                        <div class="input-group mb-3">
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                            </div>
                            <input 
                                type="text" 
                                name="username" 
                                id="username" 
                                class="form-control input_user" 
                                placeholder="Username" 
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
                                class="form-control input_user" 
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

                        <div class="d-flex justify-content-center mt-3">
                            <button type="submit" name="login" id="login" class="btn login_btn">Login</button>
                        </div>
                    </form>
                </div>

                <!-- Links: Sign Up & Forgot Password -->
                <div class="mt-4">
                    <div class="d-flex justify-content-center links">
                        Don't have an account? <a href="registration.php" class="ml-2">Sign up</a>
                    </div>
                    <div class="d-flex justify-content-center mt-2">
                        <a href="#" class="text-white">Forgot your password?</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"
            integrity="sha256-FgpCb/KJQ1LNfOu91ta32o/NMZx1twRo8QtmkMRdAu8="
            crossorigin="anonymous">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

    <!-- Optional: Add your custom JS here -->
    <!-- <script src="js/scripts.js"></script> -->
</body>
</html>