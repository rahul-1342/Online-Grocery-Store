<?php
session_start();
error_reporting(0);
include('includes/config.php');

// Initialize variables
$success = false;
$error = false;
$name = $email = $contactno = $subject = $message = '';

// Check if user is logged in
$userLoggedIn = isset($_SESSION['login']) && $_SESSION['login'] != '';

// Get user details if logged in
if($userLoggedIn) {
    $userId = $_SESSION['id'];
    $sql = "SELECT * FROM users WHERE id=?";
    $query = $con->prepare($sql);
    $query->bind_param("i", $userId);
    $query->execute();
    $result = $query->get_result();
    $user = $result->fetch_object();
    
    
    if($user) {
        $name = $user->name;
        $email = $user->email;
        $contactno = $user->contactno;
    }
}

// Handle form submission
if(isset($_POST['submit'])) {
    // Verify user is logged in
    if(!$userLoggedIn) {
        $error = "Please login to submit the contact form";
    } else {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $subject = $_POST['subject'];
        $message = $_POST['message'];
        $userId = $_SESSION['id'];
        
        // Validate inputs
        if(empty($name) || empty($email) || empty($subject) || empty($message)) {
            $error = "Please fill in all required fields";
        } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Please enter a valid email address";
        } else {
            // Prepare and bind
            $stmt = $con->prepare("INSERT INTO contact_queries (user_id, name, email, phone, subject, message, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param("isssss", $userId, $name, $email, $phone, $subject, $message);
            
            
            // Execute query
            if($stmt->execute()) {
                $success = true;
                // Clear form fields except user details
                $subject = $message = '';
            } else {
                $error = "There was an error submitting your message. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Contact Us | Minute Mart</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/green.css">
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    
    <!-- SweetAlert CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <style>
        /* Previous styles remain the same */
        .login-required {
            background-color: #fff8e1;
            border-left: 4px solid #ffc107;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border-radius: 4px;
        }
        .contact-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-bottom: 1px solid #dee2e6;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
        
        .contact-title {
            font-weight: 700;
            color: #2c3e50;
            position: relative;
            padding-bottom: 15px;
        }
        
        .contact-title:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 3px;
            background-color: #28a745;
        }
        
        .contact-card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }
        
        .contact-card:hover {
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        
        .contact-card .card-body {
            padding: 1.75rem;
        }
        
        .section-title {
            color: #28a745;
            font-weight: 600;
            margin-bottom: 1rem;
            position: relative;
            padding-left: 15px;
        }
        
        .section-title:before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            height: 60%;
            width: 4px;
            background-color: #28a745;
            border-radius: 2px;
        }
        
        .contact-info {
            padding-left: 1.5rem;
        }
        
        .contact-info li {
            margin-bottom: 1rem;
            position: relative;
            list-style-type: none;
            padding-left: 2rem;
        }
        
        .contact-info li i {
            position: absolute;
            left: 0;
            top: 0;
            color: #28a745;
            font-size: 1.25rem;
        }
        
        .contact-form .form-control {
            border-radius: 4px;
            border: 1px solid #ced4da;
            padding: 12px 15px;
            margin-bottom: 1rem;
        }
        
        .contact-form .form-control:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }
        
        .btn-submit {
            background-color: #28a745;
            color: white;
            padding: 10px 25px;
            border: none;
            border-radius: 4px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-submit:hover {
            background-color: #218838;
            transform: translateY(-2px);
        }
        
        .map-container {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }
        
        .alert-success {
            color: #3c763d;
            background-color: #dff0d8;
            border-color: #d6e9c6;
        }
        
        .alert-danger {
            color: #a94442;
            background-color: #f2dede;
            border-color: #ebccd1;
        }
        select.form-control {
    line-height: 1.5;
    height: auto !important;
}





        @media (max-width: 768px) {
            .contact-card .card-body {
                padding: 1.25rem;
            }
            
            .section-title {
                font-size: 1.25rem;
            }
        }
    </style>
</head>
<body>

<header class="header-style-1">
    <?php include('includes/top-header.php'); ?>
    <?php include('includes/main-header.php'); ?>
    <?php include('includes/menu-bar.php'); ?>
</header>

<div class="contact-header">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
                <h1 class="contact-title">Contact Us</h1>
                <p class="text-muted">We'd love to hear from you</p>
            </div>
        </div>
    </div>
</div>

<div class="container py-4">
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="contact-card">
                <div class="card-body">
                    <h2 class="section-title">Get In Touch</h2>
                    <p>Have questions or feedback? Fill out the form below and we'll get back to you as soon as possible.</p>
                    
                    <?php if(!$userLoggedIn): ?>
                        <div class="login-required">
                            <h4><i class="fa fa-exclamation-circle"></i> Login Required</h4>
                            <p>You need to be logged in to submit the contact form. Please <a href="login.php">login</a> or <a href="register.php">register</a> if you don't have an account.</p>
                        </div>
                    <?php endif; ?>
                    
                    <form id="contactForm" class="contact-form" method="post">
                        <div class="form-group">
                            <input type="text" class="form-control" name="name" placeholder="Your Name" 
                                   value="<?php echo htmlspecialchars($name); ?>" 
                                   <?php echo !$userLoggedIn ? 'disabled' : ''; ?> required>
                        </div>
                        <div class="form-group">
                            <input type="email" class="form-control" name="email" placeholder="Your Email" 
                                   value="<?php echo htmlspecialchars($email); ?>" 
                                   <?php echo !$userLoggedIn ? 'disabled' : ''; ?> required>
                        </div>
                        <div class="form-group">
                            <input type="tel" class="form-control" name="phone" placeholder="Your Phone (Optional)" 
                                   value="<?php echo htmlspecialchars($contactno); ?>" 
                                   <?php echo !$userLoggedIn ? 'disabled' : ''; ?>>
                        </div>
                        <div class="form-group">
                            <select class="form-control" name="subject" <?php echo !$userLoggedIn ? 'disabled' : ''; ?> required>
                                <option value="">Select Subject</option>
                                <option value="General Inquiry" <?php echo (isset($subject) && $subject == 'General Inquiry') ? 'selected' : ''; ?>>General Inquiry</option>
                                <option value="Order Support" <?php echo (isset($subject) && $subject == 'Order Support') ? 'selected' : ''; ?>>Order Support</option>
                                <option value="Product Question" <?php echo (isset($subject) && $subject == 'Product Question') ? 'selected' : ''; ?>>Product Question</option>
                                <option value="Returns" <?php echo (isset($subject) && $subject == 'Returns') ? 'selected' : ''; ?>>Returns</option>
                                <option value="Other" <?php echo (isset($subject) && $subject == 'Other') ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <textarea class="form-control" name="message" rows="5" placeholder="Your Message" 
                                      <?php echo !$userLoggedIn ? 'disabled' : ''; ?> required><?php echo htmlspecialchars($message); ?></textarea>
                        </div>
                        <button type="submit" name="submit" class="btn-submit" <?php echo !$userLoggedIn ? 'disabled' : ''; ?>>
                            <?php echo $userLoggedIn ? 'Send Message' : 'Login to Submit'; ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6 mb-4">
            <div class="contact-card">
                <div class="card-body">
                    <h2 class="section-title">Our Information</h2>
                    <ul class="contact-info">
                        <li>
                            <i class="fa fa-map-marker"></i>
                            <strong>Address:</strong><br>
                            <?php echo isset($companyAddress) ? $companyAddress : '123 Main Street, Cityville, State 12345'; ?>
                        </li>
                        <li>
                            <i class="fa fa-phone"></i>
                            <strong>Phone:</strong><br>
                            <?php echo isset($contactPhone) ? $contactPhone : '+1 (123) 456-7890'; ?>
                        </li>
                        <li>
                            <i class="fa fa-envelope"></i>
                            <strong>Email:</strong><br>
                            <?php echo isset($contactEmail) ? $contactEmail : 'support@minute-mart.com'; ?>
                        </li>
                        <li>
                            <i class="fa fa-clock-o"></i>
                            <strong>Hours:</strong><br>
                            <?php echo isset($businessHours) ? $businessHours : 'Monday-Friday: 9am-6pm<br>Saturday: 10am-4pm<br>Sunday: Closed'; ?>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="map-container">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3022.215256627003!2d-73.98784468459382!3d40.74844047932789!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNDDCsDQ0JzU0LjQiTiA3M8KwNTknMTkuNyJX!5e0!3m2!1sen!2sus!4v1620000000000!5m2!1sen!2sus" 
                        width="100%" 
                        height="300" 
                        style="border:0;" 
                        allowfullscreen="" 
                        loading="lazy"></iframe>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>

<!-- SweetAlert JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Show SweetAlert notifications based on PHP variables
<?php if($success): ?>
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: 'Thank you for contacting us! We\'ll get back to you soon.',
        confirmButtonColor: '#28a745',
        timer: 3000
    });
<?php endif; ?>

<?php if($error): ?>
    Swal.fire({
        icon: 'error',
        title: 'Error!',
        text: '<?php echo $error; ?>',
        confirmButtonColor: '#dc3545'
    });
<?php endif; ?>

// Form submission handling
document.getElementById('contactForm').addEventListener('submit', function(e) {
    // You can add additional client-side validation here if needed
    // For example:
    const subject = document.querySelector('select[name="subject"]').value;
    if(subject === "") {
        e.preventDefault();
        Swal.fire({
            icon: 'warning',
            title: 'Oops...',
            text: 'Please select a subject',
            confirmButtonColor: '#ffc107'
        });
    }
});
</script>

</body>
</html>