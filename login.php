<?php
session_start();

if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: index.php");
    exit;
}

require_once "config.php";

$username = $password = "";
$username_err = $password_err = $login_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter username.";
    } else{
        $username = trim($_POST["username"]);
    }
    
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    if(empty($username_err) && empty($password_err)){
        $sql = "SELECT id, username, password_hash FROM users WHERE username = ?";
        
        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            $param_username = $username;
            
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){                    
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);
                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($password, $hashed_password)){
                            session_start();
                            
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;                            
                            
                            header("location: index.php");
                        } else{
                            $login_err = "Invalid username or password.";
                        }
                    }
                } else{
                    $login_err = "Invalid username or password.";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            mysqli_stmt_close($stmt);
        }
    }
    
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CryptoLotto</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
        }

        /* Animated background */
        body::before {
            content: '';
            position: fixed;
            width: 200%;
            height: 200%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.05)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            animation: float 20s infinite linear;
            z-index: -1;
        }

        @keyframes float {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }

        /* Header */
        header {
            padding: 20px 0;
            position: fixed;
            width: 100%;
            top: 0;
            background: rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            z-index: 1000;
            transition: all 0.3s ease;
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .logo {
            font-size: 28px;
            font-weight: bold;
            background: linear-gradient(45deg, #ff6b6b, #ffa726);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-decoration: none;
        }

        .back-btn {
            background: transparent;
            border: 2px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .back-btn:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.5);
            transform: translateY(-2px);
        }

        /* Main container */
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 100px 20px 50px;
        }

        .login-wrapper {
            display: grid;
            grid-template-columns: 1fr 1fr;
            max-width: 1000px;
            width: 100%;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            border-radius: 30px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            animation: slideInUp 0.8s ease;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .welcome-section {
            padding: 60px 40px;
            background: linear-gradient(45deg, rgba(255, 107, 107, 0.1), rgba(255, 167, 38, 0.1));
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
        }

        .welcome-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="20" r="2" fill="rgba(255,255,255,0.1)"/><circle cx="80" cy="80" r="2" fill="rgba(255,255,255,0.1)"/><circle cx="60" cy="30" r="1" fill="rgba(255,255,255,0.05)"/></svg>');
            opacity: 0.3;
        }

        .welcome-content {
            position: relative;
            z-index: 2;
        }

        .welcome-section h2 {
            font-size: 2.8rem;
            margin-bottom: 20px;
            background: linear-gradient(45deg, #ff6b6b, #ffa726);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .welcome-section p {
            font-size: 1.1rem;
            line-height: 1.6;
            opacity: 0.9;
            margin-bottom: 30px;
        }

        .features {
            list-style: none;
            padding: 0;
        }

        .features li {
            padding: 12px 0;
            display: flex;
            align-items: center;
            gap: 15px;
            font-size: 1rem;
            opacity: 0.9;
            transform: translateX(-20px);
            animation: slideInLeft 0.6s ease forwards;
        }

        .features li:nth-child(1) { animation-delay: 0.2s; }
        .features li:nth-child(2) { animation-delay: 0.4s; }
        .features li:nth-child(3) { animation-delay: 0.6s; }
        .features li:nth-child(4) { animation-delay: 0.8s; }

        @keyframes slideInLeft {
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .features li::before {
            content: '🎰';
            font-size: 1.2rem;
            animation: bounce 2s infinite;
        }

        .features li:nth-child(2)::before { content: '💎'; }
        .features li:nth-child(3)::before { content: '🚀'; }
        .features li:nth-child(4)::before { content: '🏆'; }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-5px);
            }
            60% {
                transform: translateY(-3px);
            }
        }

        .form-section {
            padding: 60px 40px;
        }

        .form-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .form-header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            background: linear-gradient(45deg, #ff6b6b, #ffa726);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .form-header p {
            opacity: 0.8;
            font-size: 1rem;
        }

        /* Login error message */
        .login-error {
            background: rgba(255, 107, 107, 0.1);
            border: 1px solid rgba(255, 107, 107, 0.3);
            color: #ff6b6b;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
            backdrop-filter: blur(10px);
            animation: shake 0.5s ease-in-out;
        }

        .login-error::before {
            content: '🚫';
            font-size: 1.2rem;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: white;
            font-weight: 500;
            font-size: 0.95rem;
        }

        .input-wrapper {
            position: relative;
        }

        .form-group input[type="text"],
        .form-group input[type="password"] {
            width: 100%;
            padding: 18px 20px;
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            background: rgba(0, 0, 0, 0.2);
            color: white;
            font-size: 1rem;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .form-group input[type="text"]:focus,
        .form-group input[type="password"]:focus {
            outline: none;
            border-color: #ffa726;
            box-shadow: 0 0 20px rgba(255, 167, 38, 0.3);
            transform: translateY(-2px);
        }

        .form-group input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .form-group .error {
            color: #ff6b6b;
            font-size: 0.85rem;
            margin-top: 8px;
            display: flex;
            align-items: center;
            gap: 5px;
            background: rgba(255, 107, 107, 0.1);
            padding: 8px 12px;
            border-radius: 8px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 107, 107, 0.2);
        }

        .form-group .error::before {
            content: '⚠️';
            font-size: 0.9rem;
        }

        .submit-btn {
            width: 100%;
            padding: 18px;
            border: none;
            border-radius: 15px;
            background: linear-gradient(45deg, #ff6b6b, #ffa726);
            color: white;
            font-weight: bold;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 6px 20px rgba(255, 107, 107, 0.3);
            position: relative;
            overflow: hidden;
        }

        .submit-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .submit-btn:hover::before {
            left: 100%;
        }

        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(255, 107, 107, 0.4);
        }

        .submit-btn:active {
            transform: translateY(-1px);
        }

        .register-link {
            text-align: center;
            margin-top: 30px;
            padding-top: 25px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .register-link p {
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 15px;
        }

        .register-link a {
            color: #ffa726;
            text-decoration: none;
            font-weight: 600;
            padding: 12px 25px;
            border: 2px solid rgba(255, 167, 38, 0.3);
            border-radius: 25px;
            transition: all 0.3s ease;
            display: inline-block;
            backdrop-filter: blur(10px);
        }

        .register-link a:hover {
            background: rgba(255, 167, 38, 0.1);
            border-color: #ffa726;
            transform: translateY(-2px);
        }

        .forgot-password {
            text-align: center;
            margin-top: 15px;
        }

        .forgot-password a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .forgot-password a:hover {
            color: #ffa726;
        }

        /* Social login buttons (optional) */
        .social-login {
            margin: 25px 0;
            text-align: center;
        }

        .social-divider {
            position: relative;
            margin: 25px 0;
            text-align: center;
        }

        .social-divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: rgba(255, 255, 255, 0.2);
        }

        .social-divider span {
            background: rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            padding: 0 20px;
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9rem;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .login-wrapper {
                grid-template-columns: 1fr;
                margin: 20px;
            }

            .welcome-section {
                padding: 40px 30px;
                text-align: center;
            }

            .welcome-section h2 {
                font-size: 2.2rem;
            }

            .form-section {
                padding: 40px 30px;
            }

            .form-header h1 {
                font-size: 2rem;
            }

            .container {
                padding: 80px 10px 30px;
            }
        }

        /* Loading animation */
        .loading {
            position: relative;
            pointer-events: none;
        }

        .loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            margin: auto;
            border: 2px solid transparent;
            border-top-color: white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        @keyframes spin {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }

        /* Welcome animation */
        .welcome-back {
            background: rgba(76, 175, 80, 0.1);
            border: 1px solid rgba(76, 175, 80, 0.3);
            color: #4CAF50;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            backdrop-filter: blur(10px);
            animation: fadeInDown 0.5s ease;
        }

        .welcome-back::before {
            content: '👋';
            font-size: 1.2rem;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <nav>
            <a href="index.php" class="logo">🎰 CryptoLotto</a>
            <a href="index.php" class="back-btn">← Back to Home</a>
        </nav>
    </header>

    <!-- Main Content -->
    <div class="container">
        <div class="login-wrapper">
            <!-- Welcome Section -->
            <div class="welcome-section">
                <div class="welcome-content">
                    <h2>Welcome Back!</h2>
                    <p>Ready to continue your winning journey? Login to access your account and participate in exciting lottery draws!</p>
                    
                    <ul class="features">
                        <li>Check your lottery tickets</li>
                        <li>View winning history</li>
                        <li>Claim your rewards</li>
                        <li>Join new draws</li>
                    </ul>
                </div>
            </div>

            <!-- Form Section -->
            <div class="form-section">
                <div class="form-header">
                    <h1>Sign In</h1>
                    <p>Enter your credentials to access your account</p>
                </div>

                <?php if(!empty($login_err)): ?>
                    <div class="login-error"><?php echo $login_err; ?></div>
                <?php endif; ?>

                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id="loginForm">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <div class="input-wrapper">
                            <input type="text" name="username" id="username" placeholder="Enter your username" value="<?php echo $username; ?>" required>
                        </div>
                        <?php if(!empty($username_err)): ?>
                            <span class="error"><?php echo $username_err; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-wrapper">
                            <input type="password" name="password" id="password" placeholder="Enter your password" required>
                        </div>
                        <?php if(!empty($password_err)): ?>
                            <span class="error"><?php echo $password_err; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="submit-btn" id="submitBtn">
                            🔐 Sign In
                        </button>
                    </div>
                </form>

                <div class="forgot-password">
                    <a href="#" onclick="alert('Forgot password feature coming soon!')">Forgot your password?</a>
                </div>

                <div class="register-link">
                    <p>Don't have an account yet?</p>
                    <a href="register.php">Create Account</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Form submission with loading state
        const form = document.getElementById('loginForm');
        const submitBtn = document.getElementById('submitBtn');

        form.addEventListener('submit', function() {
            submitBtn.classList.add('loading');
            submitBtn.textContent = 'Signing In...';
            submitBtn.disabled = true;
        });

        // Input animations
        const inputs = document.querySelectorAll('input');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.parentElement.style.transform = 'scale(1.02)';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.parentElement.style.transform = 'scale(1)';
            });
        });

        // Header scroll effect
        window.addEventListener('scroll', () => {
            const header = document.querySelector('header');
            if (window.scrollY > 50) {
                header.style.background = 'rgba(0, 0, 0, 0.3)';
            } else {
                header.style.background = 'rgba(0, 0, 0, 0.1)';
            }
        });

        // Auto-focus username field
        document.getElementById('username').focus();

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'Enter') {
                form.submit();
            }
        });

        // Enhanced error handling animation
        const errorElements = document.querySelectorAll('.error, .login-error');
        errorElements.forEach(error => {
            if (error.textContent.trim() !== '') {
                error.style.animation = 'shake 0.5s ease-in-out';
            }
        });

        // Welcome animation for returning users
        if (window.location.search.includes('welcome=back')) {
            const welcomeMsg = document.createElement('div');
            welcomeMsg.className = 'welcome-back';
            welcomeMsg.textContent = 'Welcome back! Please sign in to continue.';
            document.querySelector('.form-header').after(welcomeMsg);
            
            setTimeout(() => {
                welcomeMsg.remove();
            }, 5000);
        }
    </script>
</body>
</html>