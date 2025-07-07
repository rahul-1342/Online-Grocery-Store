<?php
session_start();
error_reporting(0);
include('includes/config.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Shipping & Delivery Policy | Minute Mart</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/green.css">
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    <style>
        /* Reusing the same custom styles */
        
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
        
        .shipping-table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
        }
        
        .shipping-table th, .shipping-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        
        .shipping-table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        
        .shipping-table tr:hover {
            background-color: #f8f9fa;
        }
        
        @media (max-width: 768px) {
            .policy-card .card-body {
                padding: 1.25rem;
            }
            
            .section-title {
                font-size: 1.25rem;
            }
            
            .shipping-table {
                display: block;
                overflow-x: auto;
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
                <h1 class="policy-title">Shipping & Delivery Policy</h1>
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
                    <h2 class="section-title">1. Shipping Options</h2>
                    <p>We offer the following shipping methods:</p>
                    <table class="shipping-table">
                        <thead>
                            <tr>
                                <th>Method</th>
                                <th>Delivery Time</th>
                                <th>Cost</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Standard Shipping</td>
                                <td><?php echo isset($standardShippingTime) ? $standardShippingTime : '3-5 business days'; ?></td>
                                <td><?php echo isset($standardShippingCost) ? $standardShippingCost : 'Free on orders over $50'; ?></td>
                            </tr>
                            <tr>
                                <td>Express Shipping</td>
                                <td><?php echo isset($expressShippingTime) ? $expressShippingTime : '1-2 business days'; ?></td>
                                <td><?php echo isset($expressShippingCost) ? $expressShippingCost : '$9.99'; ?></td>
                            </tr>
                            <tr>
                                <td>Same-Day Delivery</td>
                                <td>Within 24 hours</td>
                                <td><?php echo isset($sameDayShippingCost) ? $sameDayShippingCost : '$14.99'; ?></td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="note-box">
                        <strong>Note:</strong> Delivery times are estimates and not guaranteed. Some items may have different delivery timeframes.
                    </div>
                </div>
            </div>
            
            <div class="policy-card">
                <div class="card-body">
                    <h2 class="section-title">2. Processing Time</h2>
                    <ul class="policy-list">
                        <li>Orders are typically processed within <?php echo isset($processingTime) ? $processingTime : '1-2 business days'; ?> after payment confirmation.</li>
                        <li>Orders placed on weekends or holidays will be processed the next business day.</li>
                        <li>You will receive a confirmation email with tracking information once your order ships.</li>
                    </ul>
                </div>
            </div>
            
            <div class="policy-card">
                <div class="card-body">
                    <h2 class="section-title">3. Delivery Areas</h2>
                    <ul class="policy-list">
                        <li>We currently ship to all <?php echo isset($country) ? $country : 'US'; ?> addresses.</li>
                        <li>International shipping is <?php echo isset($internationalShipping) ? $internationalShipping : 'not currently available'; ?>.</li>
                        <li>Some remote locations may have extended delivery times or additional fees.</li>
                    </ul>
                </div>
            </div>
            
            <div class="policy-card">
                <div class="card-body">
                    <h2 class="section-title">4. Order Tracking</h2>
                    <ul class="policy-list">
                        <li>Once your order ships, you'll receive a tracking number via email.</li>
                        <li>You can track your order using our <a href="track-order.php">Order Tracking</a> page.</li>
                        <li>Please allow 24 hours for tracking information to update in the carrier's system.</li>
                    </ul>
                </div>
            </div>
            
            <div class="policy-card">
                <div class="card-body">
                    <h2 class="section-title">5. Failed Deliveries</h2>
                    <ul class="policy-list">
                        <li>If delivery is attempted but unsuccessful, the carrier will typically leave a notice.</li>
                        <li>You will have <?php echo isset($redeliveryWindow) ? $redeliveryWindow : '5 business days'; ?> to arrange for redelivery or pickup.</li>
                        <li>Unclaimed packages will be returned to us and a restocking fee may apply.</li>
                    </ul>
                </div>
            </div>
            
            <div class="policy-card">
                <div class="card-body">
                    <h2 class="section-title">6. Shipping Restrictions</h2>
                    <ul class="policy-list">
                        <li>Some items cannot be shipped to certain locations due to legal restrictions.</li>
                        <li>We reserve the right to cancel orders that violate shipping restrictions.</li>
                        <li>Special handling fees may apply for fragile, oversized, or heavy items.</li>
                    </ul>
                </div>
            </div>
            
            <div class="policy-card">
                <div class="card-body">
                    <h2 class="section-title">7. Contact Us</h2>
                    <p>For any questions about shipping or delivery, please contact us:</p>
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