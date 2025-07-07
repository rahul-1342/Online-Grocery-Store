<?php
// Start session at the very beginning
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("includes/config.php"); // DB connection

// Initialize error variable
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    // Sanitize and validate inputs
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? ''; // Password will be verified, no need to sanitize
    
    if (empty($email) || empty($password)) {
        $error = "Please enter both email and password";
    } else {
        try {
            $stmt = $con->prepare("SELECT id, name, password FROM delivery_boys WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $res = $stmt->get_result();
            
            if ($row = $res->fetch_assoc()) {
                if (password_verify($password, $row['password'])) {
                    // Successful login
                    $_SESSION['delivery_boy_id'] = $row['id'];
                    $_SESSION['delivery_boy_name'] = $row['name'];
                    
                    // Regenerate session ID to prevent session fixation
                    session_regenerate_id(true);
                    
                    header("Location: delivery-dashboard.php");
                    exit;
                }
            }
            
            // Generic error message (don't reveal whether email exists)
            $error = "Invalid email or password";
            $stmt->close();
        } catch (Exception $e) {
            // Log the error in production instead of showing to users
            error_log("Login error: " . $e->getMessage());
            $error = "A system error occurred. Please try again later.";
        }
    }
}
?>  
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Delivery Partner Login</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    :root {
      --primary-color: #2c3e50;
      --secondary-color: #3498db;
      --accent-color: #e74c3c;
    }
    
    body {
      background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      margin: 0;
      position: relative;
      overflow: hidden;
    }
    
    body::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiPjxkZWZzPjxwYXR0ZXJuIGlkPSJwYXR0ZXJuIiB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHBhdHRlcm5Vbml0cz0idXNlclNwYWNlT25Vc2UiIHBhdHRlcm5UcmFuc2Zvcm09InJvdGF0ZSg0NSkiPjxyZWN0IHdpZHRoPSIyMCIgaGVpZ2h0PSIyMCIgZmlsbD0icmdiYSgyNTUsMjU1LDI1NSwwLjEpIi8+PC9wYXR0ZXJuPjwvZGVmcz48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSJ1cmwoI3BhdHRlcm4pIi8+PC9zdmc+');
      opacity: 0.6;
      z-index: -1;
    }
    
    .login-container {
      background: white;
      border-radius: 16px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 420px;
      padding: 40px;
      position: relative;
      overflow: hidden;
      transition: transform 0.3s ease;
    }
    
    .login-container:hover {
      transform: translateY(-5px);
    }
    
    .login-header {
      text-align: center;
      margin-bottom: 30px;
    }
    
    .logo {
      width: 120px;
      height: 120px;
      margin: 0 auto 20px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
      border-radius: 50%;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      padding: 15px;
    }
    
    .logo img {
      width: 80px;
      height: 80px;
      object-fit: contain;
    }
    
    .login-header h2 {
      color: var(--primary-color);
      font-weight: 700;
      margin-bottom: 5px;
    }
    
    .login-header p {
      color: #7f8c8d;
      font-size: 0.9rem;
    }
    
    .form-group {
      margin-bottom: 20px;
      position: relative;
    }
    
    .form-label {
      display: block;
      margin-bottom: 8px;
      color: var(--primary-color);
      font-weight: 500;
      font-size: 0.9rem;
    }
    
    .input-group {
      position: relative;
    }
    
    .input-icon {
      position: absolute;
      left: 15px;
      top: 50%;
      transform: translateY(-50%);
      color: #95a5a6;
      font-size: 1rem;
    }
    
    .form-control {
      width: 100%;
      padding: 12px 20px 12px 45px;
      border: 1px solid #e0e0e0;
      border-radius: 8px;
      background-color: #f9f9f9;
      transition: all 0.3s;
      font-size: 0.95rem;
    }
    
    .form-control:focus {
      border-color: var(--secondary-color);
      box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
      background-color: white;
    }
    
    .btn-login {
      width: 100%;
      padding: 12px;
      background: linear-gradient(to right, var(--secondary-color), var(--primary-color));
      border: none;
      border-radius: 8px;
      color: white;
      font-weight: 600;
      font-size: 1rem;
      cursor: pointer;
      transition: all 0.3s;
      margin-top: 10px;
    }
    
    .btn-login:hover {
      background: linear-gradient(to right, #2980b9, #2c3e50);
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    
    .error-message {
      color: var(--accent-color);
      background: #fde8e8;
      padding: 12px;
      border-radius: 8px;
      margin-bottom: 20px;
      text-align: center;
      font-size: 0.9rem;
      animation: shake 0.5s;
    }
    
    @keyframes shake {
      0%, 100% { transform: translateX(0); }
      10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
      20%, 40%, 60%, 80% { transform: translateX(5px); }
    }
    
    .delivery-decoration {
      position: absolute;
      opacity: 0.1;
      z-index: 0;
    }
    
    .delivery-decoration.top-left {
      top: -30px;
      left: -30px;
      font-size: 100px;
      color: var(--secondary-color);
    }
    
    .delivery-decoration.bottom-right {
      bottom: -30px;
      right: -30px;
      font-size: 100px;
      color: var(--primary-color);
    }
    
    .forgot-password {
      text-align: center;
      margin-top: 20px;
    }
    
    .forgot-password a {
      color: #7f8c8d;
      text-decoration: none;
      font-size: 0.85rem;
      transition: color 0.3s;
    }
    
    .forgot-password a:hover {
      color: var(--secondary-color);
      text-decoration: underline;
    }
    
    @media (max-width: 576px) {
      .login-container {
        padding: 30px 20px;
        margin: 0 15px;
      }
      
      .logo {
        width: 100px;
        height: 100px;
      }
      
      .logo img {
        width: 60px;
        height: 60px;
      }
      
      .delivery-decoration {
        display: none;
      }
    }
  </style>
</head>
<body>
  <div class="login-container">
    <!-- Decorative elements -->
    <i class="fas fa-motorcycle delivery-decoration top-left"></i>
    <i class="fas fa-map-marked-alt delivery-decoration bottom-right"></i>
    
    <div class="login-header">
      <div class="logo">
        <!-- Delivery boy logo - replace with your actual logo -->
        <img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCA1MTIgNTEyIj48cGF0aCBmaWxsPSJ3aGl0ZSIgZD0iTTI1NiA0OEMxNDEuMSA0OCA0OCAxNDEuMSA0OCAyNTZzOTMuMSAyMDggMjA4IDIwOHMyMDgtOTMuMSAyMDgtMjA4UzM3MC45IDQ4IDI1NiA0OHptMCAzODRjLTk3LjIgMC0xNzYtNzguOC0xNzYtMTc2czc4LjgtMTc2IDE3Ni0xNzZzMTc2IDc4LjggMTc2IDE3NnMtNzguOCAxNzYtMTc2IDE3NnptODAtMTYwYzAgNDQuMi0zNS44IDgwLTgwIDgwcy04MC0zNS44LTgwLTgwczM1LjgtODAgODAtODBzODAgMzUuOCA4MCA4MHptLTE2MCAwYzAgNDQuMi0zNS44IDgwLTgwIDgwcy04MC0zNS44LTgwLTgwczM1LjgtODAgODAtODBzODAgMzUuOCA4MCA4MHptMTUzLjkgMTQwLjJjLTkuNy0xNi0yNy44LTI1LjgtNDcuMi0yNS44SDIxNS4zYy0xOS40IDAtMzcuNSA5LjgtNDcuMiAyNS44bC0xOS4yIDMxLjdjLTUuMyA4LjctNy40IDE4LjQtNS43IDI3LjljMS43IDkuNSA2LjUgMTguMSAxMy44IDI0LjRjNy4zIDYuMyAxNi42IDkuOSAyNi4zIDEwLjFoMTI2LjZjOS43LS4yIDE5LTQgMjYuMy0xMC4xYzcuMy02LjMgMTIuMS0xNC45IDEzLjgtMjQuNGMxLjctOS41LS40LTE5LjItNS43LTI3LjlsLTE5LjItMzEuN3oiLz48L3N2Zz4=" alt="Delivery Partner">
      </div>
      <h2>Delivery Partner</h2>
      <p>Sign in to access your dashboard</p>
    </div>
    
    <?php if (isset($error)): ?>
      <div class="error-message">
        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
      </div>
    <?php endif; ?>
    
    <form method="post">
      <div class="form-group">
        <label for="email" class="form-label">Email Address</label>
        <div class="input-group">
          <i class="fas fa-envelope input-icon"></i>
          <input type="email" name="email" id="email" class="form-control" placeholder="partner@example.com" required>
        </div>
      </div>
      
      <div class="form-group">
        <label for="password" class="form-label">Password</label>
        <div class="input-group">
          <i class="fas fa-lock input-icon"></i>
          <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
        </div>
      </div>
      
      <button type="submit" name="login" class="btn-login">
        <i class="fas fa-sign-in-alt"></i> Login
      </button>
      
      <div class="forgot-password">
        <a href="forgot-password.php">Forgot your password?</a>
      </div>
    </form>
  </div>

  <!-- Bootstrap JS Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>