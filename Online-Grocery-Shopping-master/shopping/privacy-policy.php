<?php
session_start();
error_reporting(0);
include('includes/config.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Privacy Policy | Minute Mart</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/green.css">
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    <style>
        /* Reusing the same custom styles from Terms page */
        .policy-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-bottom: 1px solid #dee2e6;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
        
        .policy-title {
            font-weight: 700;
            color: #2c3e50;
            position: relative;
            padding-bottom: 15px;
        }
        
        
        
        .update-date {
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .policy-card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }
        
        .policy-card:hover {
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        
        .policy-card .card-body {
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
        
        .policy-list {
            padding-left: 1.5rem;
        }
        
        .policy-list li {
            margin-bottom: 0.5rem;
            position: relative;
            list-style-type: none;
        }
        
        .policy-list li:before {
            content: '•';
            color: #28a745;
            font-weight: bold;
            display: inline-block; 
            width: 1em;
            margin-left: -1em;
        }
        
        .note-box {
            background-color: #f8f9fa;
            padding: 1rem;
            border-radius: 5px;
            border-left: 3px solid #28a745;
            margin: 1rem 0;
        }
        
        @media (max-width: 768px) {
            .policy-card .card-body {
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

<div class="policy-header">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
                <h1 class="policy-title">Privacy Policy</h1>
                <p class="update-date">Last Updated: <?php echo date("F j, Y"); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="container py-4">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="policy-card">
                <div class="card-body">
                    <h2 class="section-title">1. Introduction</h2>
                    <p>Welcome to Minute Mart ("we", "our", or "us"). We are committed to protecting your privacy. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you visit our website <a href="index.php">minute-mart.onrender.com</a>.</p>
                </div>
            </div>
            
            <div class="policy-card">
                <div class="card-body">
                    <h2 class="section-title">2. Information We Collect</h2>
                    <p>We may collect the following types of information:</p>
                    <ul class="policy-list">
                        <li><strong>Personal Information:</strong> Name, email, phone number, shippin address</li>
                        <li><strong>Payment Information:</strong> Credit card details (processed securely by our payment processor)</li>
                        <li><strong>Account Information:</strong> Username, password, purchase history</li>
                        <li><strong>Technical Data:</strong> IP address, browser type, device information</li>
                        <li><strong>Usage Data:</strong> Pages visited, time spent, clickstream data</li>
                    </ul>
                </div>
            </div>
            
            <div class="policy-card">
                <div class="card-body">
                    <h2 class="section-title">3. How We Use Your Information</h2>
                    <p>We use the information we collect for various purposes:</p>
                    <ul class="policy-list">
                        <li>To process and fulfill your orders</li>
                        <li>To provide customer support</li>
                        <li>To improve our website and services</li>
                        <li>To send promotional emails (you can opt-out anytime)</li>
                        <li>To prevent fraud and enhance security</li>
                        <li>To comply with legal obligations</li>
                    </ul>
                </div>
            </div>
            
            <div class="policy-card">
                <div class="card-body">
                    <h2 class="section-title">4. Information Sharing</h2>
                    <p>We may share your information in the following situations:</p>
                    <ul class="policy-list">
                        <li><strong>Service Providers:</strong> With third parties who perform services for us (payment processing, shipping, etc.)</li>
                        <li><strong>Legal Requirements:</strong> When required by law or to protect our rights</li>
                        <li><strong>Business Transfers:</strong> In connection with any merger or sale of company assets</li>
                    </ul>
                    <div class="note-box">
                        <strong>Note:</strong> We never sell your personal information to third parties for marketing purposes.
                    </div>
                </div>
            </div>
            
            <div class="policy-card">
                <div class="card-body">
                    <h2 class="section-title">5. Data Security</h2>
                    <ul class="policy-list">
                        <li>We implement appropriate security measures to protect your data</li>
                        <li>All transactions are encrypted using SSL technology</li>
                        <li>Payment information is processed through PCI-compliant services</li>
                        <li>Regular security audits of our systems</li>
                    </ul>
                    <p>However, no internet transmission is 100% secure, so we cannot guarantee absolute security.</p>
                </div>
            </div>
            
            <div class="policy-card">
                <div class="card-body">
                    <h2 class="section-title">6. Cookies and Tracking</h2>
                    <p>We use cookies and similar technologies to:</p>
                    <ul class="policy-list">
                        <li>Remember your preferences</li>
                        <li>Analyze site traffic</li>
                        <li>Personalize content</li>
                        <li>Serve targeted advertisements</li>
                    </ul>
                    <p>You can control cookies through your browser settings.</p>
                </div>
            </div>
            
            <div class="policy-card">
                <div class="card-body">
                    <h2 class="section-title">7. Your Rights</h2>
                    <p>Depending on your location, you may have the following rights:</p>
                    <ul class="policy-list">
                        <li>Access the personal data we hold about you</li>
                        <li>Request correction of inaccurate data</li>
                        <li>Request deletion of your data</li>
                        <li>Object to certain processing activities</li>
                        <li>Withdraw consent where applicable</li>
                    </ul>
                    <p>To exercise these rights, please contact us using the information below.</p>
                </div>
            </div>
            
            <div class="policy-card">
                <div class="card-body">
                    <h2 class="section-title">8. Children's Privacy</h2>
                    <p>Our website is not intended for children under 13. We do not knowingly collect personal information from children under 13.</p>
                </div>
            </div>
            
            <div class="policy-card">
                <div class="card-body">
                    <h2 class="section-title">9. Third-Party Links</h2>
                    <p>Our website may contain links to third-party websites. We are not responsible for their privacy practices.</p>
                </div>
            </div>
            
            <div class="policy-card">
                <div class="card-body">
                    <h2 class="section-title">10. Changes to This Policy</h2>
                    <p>We may update this Privacy Policy periodically. We will notify you of significant changes by posting the new policy on our website.</p>
                </div>
            </div>
            
            <div class="policy-card">
                <div class="card-body">
                    <h2 class="section-title">11. Contact Us</h2>
                    <p>If you have questions about this Privacy Policy, please contact us:</p>
                    <div class="note-box">
                        <strong>Minute Mart</strong><br>
                        Email: <?php echo isset($contactEmail) ? $contactEmail : 'privacy@minute-mart.com'; ?><br>
                        Phone: <?php echo isset($contactPhone) ? $contactPhone : '+1 (123) 456-7890'; ?><br>
                        Address: <?php echo isset($companyAddress) ? $companyAddress : '123 Privacy Lane, Pune, India'; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>

</body>
</html>