<?php
session_start();
error_reporting(0);
include('includes/config.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Terms and Conditions | Minute Mart</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/green.css">
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    <style>
        /* Custom Styles for Terms Page */
        .terms-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-bottom: 1px solid #dee2e6;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
        
        .terms-title {
            font-weight: 700;
            color: #2c3e50;
            position: relative;
            padding-bottom: 15px;
        }

        .update-date {
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .terms-card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }
        
        .terms-card:hover {
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        
        .terms-card .card-body {
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
        
        .terms-list {
            padding-left: 1.5rem;
        }
        
        .terms-list li {
            margin-bottom: 0.5rem;
            position: relative;
            list-style-type: none;
        }
        
        .terms-list li:before {
            content: '•';
            color: #28a745;
            font-weight: bold;
            display: inline-block; 
            width: 1em;
            margin-left: -1em;
        }
        
        .contact-address {
            background-color: #f8f9fa;
            padding: 1rem;
            border-radius: 5px;
            border-left: 3px solid #28a745;
        }
        
        @media (max-width: 768px) {
            .terms-card .card-body {
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

<div class="terms-header">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
                <h1 class="terms-title">Terms and Conditions</h1>
                <p class="update-date">Last Updated: <?php echo date("F j, Y"); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="container py-4">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="terms-card">
                <div class="card-body">
                    <h2 class="section-title">1. Introduction</h2>
                    <p>Welcome to Minute Mart ("we", "our", or "us"). These Terms and Conditions govern your use of our website located at https://minute-mart.onrender.com and our services. By accessing or using our website, you agree to be bound by these Terms.</p>
                </div>
            </div>
            
            <div class="terms-card">
                <div class="card-body">
                    <h2 class="section-title">2. Account Registration</h2>
                    <ul class="terms-list">
                        <li>You must provide accurate and complete information when creating an account.</li>
                        <li>You are responsible for maintaining the confidentiality of your account credentials.</li>
                        <li>You must be at least 18 years old to create an account or make purchases.</li>
                    </ul>
                </div>
            </div>
            
            <div class="terms-card">
                <div class="card-body">
                    <h2 class="section-title">3. Products and Pricing</h2>
                    <ul class="terms-list">
                        <li>All product descriptions and prices are subject to change without notice.</li>
                        <li>We reserve the right to limit quantities and refuse service to anyone.</li>
                        <li>Prices are in <?php echo isset($currency) ? $currency : 'USD'; ?> and exclude applicable taxes.</li>
                    </ul>
                </div>
            </div>
            
            <div class="terms-card">
                <div class="card-body">
                    <h2 class="section-title">4. Orders and Payments</h2>
                    <ul class="terms-list">
                        <li>By placing an order, you authorize us to charge your payment method.</li>
                        <li>We accept various payment methods as displayed at checkout.</li>
                        <li>Orders are subject to availability and confirmation.</li>
                    </ul>
                </div>
            </div>
            
            <div class="terms-card">
                <div class="card-body">
                    <h2 class="section-title">5. Shipping and Delivery</h2>
                    <ul class="terms-list">
                        <li>Shipping costs and delivery times vary by location.</li>
                        <li>Risk of loss passes to you upon delivery.</li>
                        <li>You are responsible for providing accurate shipping information.</li>
                    </ul>
                </div>
            </div>
            
            <div class="terms-card">
                <div class="card-body">
                    <h2 class="section-title">6. Returns and Refunds</h2>
                    <ul class="terms-list">
                        <li>Returns must be made within <?php echo isset($returnPeriod) ? $returnPeriod : '30'; ?> days of receipt.</li>
                        <li>Products must be unused and in original packaging.</li>
                        <li>Refunds will be issued to the original payment method.</li>
                    </ul>
                </div>
            </div>
            
            <div class="terms-card">
                <div class="card-body">
                    <h2 class="section-title">7. Intellectual Property</h2>
                    <p>All content on this website, including text, graphics, logos, and images, is our property or the property of our licensors and is protected by copyright laws.</p>
                </div>
            </div>
            
            <div class="terms-card">
                <div class="card-body">
                    <h2 class="section-title">8. User Conduct</h2>
                    <p>You agree not to:</p>
                    <ul class="terms-list">
                        <li>Use the website for any illegal purpose</li>
                        <li>Attempt to gain unauthorized access to our systems</li>
                        <li>Interfere with the proper working of the website</li>
                        <li>Use any automated means to access the website</li>
                    </ul>
                </div>
            </div>
            
            <div class="terms-card">
                <div class="card-body">
                    <h2 class="section-title">9. Limitation of Liability</h2>
                    <p>To the fullest extent permitted by law, Minute Mart shall not be liable for any indirect, incidental, or consequential damages arising from your use of the website.</p>
                </div>
            </div>
            
            <div class="terms-card">
                <div class="card-body">
                    <h2 class="section-title">10. Changes to Terms</h2>
                    <p>We may update these Terms at any time. The updated version will be posted on our website with a new effective date.</p>
                </div>
            </div>
            
            <div class="terms-card">
                <div class="card-body">
                    <h2 class="section-title">11. Governing Law</h2>
                    <p>These Terms shall be governed by and construed in accordance with the laws of <?php echo isset($governingLaw) ? $governingLaw : 'the state in which our company is registered'; ?>.</p>
                </div>
            </div>
            
            <div class="terms-card">
                <div class="card-body">
                    <h2 class="section-title">12. Contact Information</h2>
                    <p>For questions about these Terms, please contact us at:</p>
                    <div class="contact-address mt-3">
                        <strong>Minute Mart</strong><br>
                        Email: <?php echo isset($contactEmail) ? $contactEmail : 'support@minute-mart.com'; ?><br>
                        Phone: <?php echo isset($contactPhone) ? $contactPhone : 'Not provided'; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>

</body>
</html>