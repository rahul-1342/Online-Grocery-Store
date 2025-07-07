<?php
session_start();
error_reporting(0);
include('includes/config.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Cancellation & Refund Policy | Minute Mart</title>
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
        
        .contact-address {
            background-color: #f8f9fa;
            padding: 1rem;
            border-radius: 5px;
            border-left: 3px solid #28a745;
        }
        
        .note-box {
            background-color: #fff8e1;
            border-left: 4px solid #ffc107;
            padding: 1rem;
            margin: 1rem 0;
            border-radius: 0 4px 4px 0;
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
                <h1 class="policy-title">Cancellation & Refund Policy</h1>
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
                    <h2 class="section-title">1. Order Cancellation</h2>
                    <ul class="policy-list">
                        <li>You may cancel your order within <?php echo isset($cancellationWindow) ? $cancellationWindow : '1 hour'; ?> of placing it.</li>
                        <li>To cancel, please contact our customer support or use the cancellation option in your account.</li>
                        <li>Orders that have already been processed or shipped cannot be cancelled.</li>
                    </ul>
                    <div class="note-box">
                        <strong>Note:</strong> Some products marked as "Non-Cancellable" cannot be cancelled once ordered.
                    </div>
                </div>
            </div>
            
            <div class="policy-card">
                <div class="card-body">
                    <h2 class="section-title">2. Refund Eligibility</h2>
                    <ul class="policy-list">
                        <li>Refunds are applicable only if the cancellation request is made within the specified time window.</li>
                        <li>Products must be unused, unopened, and in their original packaging with all tags intact to be eligible for refund.</li>
                        <li>Perishable goods, personalized items, and digital products are generally not eligible for refunds.</li>
                    </ul>
                </div>
            </div>
            
            <div class="policy-card">
                <div class="card-body">
                    <h2 class="section-title">3. Refund Process</h2>
                    <ul class="policy-list">
                        <li>Once your return is received and inspected, we will notify you of the approval or rejection of your refund.</li>
                        <li>If approved, your refund will be processed within <?php echo isset($refundProcessingTime) ? $refundProcessingTime : '5-7 business days'; ?>.</li>
                        <li>Refunds will be credited to the original method of payment.</li>
                    </ul>
                </div>
            </div>
            
            <div class="policy-card">
                <div class="card-body">
                    <h2 class="section-title">4. Late or Missing Refunds</h2>
                    <ul class="policy-list">
                        <li>If you haven't received your refund within the expected time, first check your bank account again.</li>
                        <li>Contact your credit card company as it may take some time before your refund is officially posted.</li>
                        <li>If you've done all of this and still have not received your refund, please contact us at <?php echo isset($contactEmail) ? $contactEmail : 'support@minute-mart.com'; ?>.</li>
                    </ul>
                </div>
            </div>
            
            <div class="policy-card">
                <div class="card-body">
                    <h2 class="section-title">5. Shipping Returns</h2>
                    <ul class="policy-list">
                        <li>You will be responsible for paying your own shipping costs for returning items.</li>
                        <li>Shipping costs are non-refundable.</li>
                        <li>We recommend using a trackable shipping service and purchasing shipping insurance.</li>
                    </ul>
                </div>
            </div>
            
            <div class="policy-card">
                <div class="card-body">
                    <h2 class="section-title">6. Damaged or Defective Items</h2>
                    <ul class="policy-list">
                        <li>If you receive a damaged or defective product, please contact us within <?php echo isset($defectReportWindow) ? $defectReportWindow : '48 hours'; ?> of delivery.</li>
                        <li>We may require photographic evidence of the damage or defect.</li>
                        <li>We will arrange for a replacement or refund at no additional cost to you.</li>
                    </ul>
                </div>
            </div>
            
            <div class="policy-card">
                <div class="card-body">
                    <h2 class="section-title">7. Contact Us</h2>
                    <p>For any questions about our cancellation and refund policy, please contact us:</p>
                    <div class="contact-address mt-3">
                        <strong>Minute Mart Customer Support</strong><br>
                        Email: <?php echo isset($contactEmail) ? $contactEmail : 'support@minute-mart.com'; ?><br>
                        Phone: <?php echo isset($contactPhone) ? $contactPhone : 'Not provided'; ?><br>
                        Hours: <?php echo isset($supportHours) ? $supportHours : 'Monday-Friday, 9am-6pm'; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>

</body>
</html>