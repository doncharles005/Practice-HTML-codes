<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tap. Pay. Now! Login</title>
    <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="css/styles.css">
</head>
<body>
    <div class="container h-100">
        <div class="d-flex justify-content-center h-100">
            <div class="user_card">
                <div class="d-flex justify-content-center">
                    <div class="brand_logo_container">
                        <img src="img/logo.png" class="brand_logo" alt="Tap. Pay. Now Logo">                    
                    </div>        
                </div> 
                <div class="d-flex justify-content-center form_container">
                    <form>
                        <div class="input-group mb-3">
                            <div class="input-group -append">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                            </div>
                            <input type="text" name="username" id="username" class="form-control input_user" required>
                        </div>
                        <div class="input-group mb-2">
                            <div class="input-group -append">
                                <span class="input-group-text"><i class="fas fa-key"></i></span>
                            </div>
                            <input type="password" name="password" id="username" class="form-control input_user" required>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" name="rememberme" class="custom-control-input" id="customControlinline">
                                <label class="custome-control-label" for="customControlInline">Remember me</label>
                            </div>
                        </div>
                    </form>    
                </div>   
                <div class="d-flex justify-content-center mt-3 login-container">
                    <button type="button" name="button" id="login" class="btn login_btn">Login</button>
                </div>  
                <div class="mt-4">
                    <div class="d-flex jusitify-content-center links">
                        Don't have an account? <a href="registration.php" class="m1-2">Sign up</a>
                    </div>
                    <div class="d-flex jusitify-content-center">
                        <a href="#"> Forget your password?</a>
                    </div>

                </div>   
            </div> 
        </div>    
    </div>    
<script src="http://code.jquery.com/jquery-3.3.1.min.js"
            integrity="sha256-FgpCb/KJQ1LNfOu91ta32o/NMZx1twRo8QtmkMRdAu8="
            crossorigin="anonymous">
</script>
<script type="text/javascript" src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js">
</script>  
</body>
</html>