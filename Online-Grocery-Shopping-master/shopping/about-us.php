<?php
session_start();
error_reporting(0);
include('includes/config.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>About Us | Minute Mart</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/green.css">
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    <!-- Same header styles as previous pages -->
    <style>
        /* Reuse the same styling classes */
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
        .team-member {
            text-align: center;
            margin-bottom: 2rem;
        }
        .team-member img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 1rem;
            border: 3px solid #28a745;
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
                    <h1 class="policy-title">About Minute Mart</h1>
                    <p class="text-muted">Our Story, Values, and Team</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container py-4">
        <div class="row">
            <div class="col-lg-10 mx-auto">
                <div class="policy-card">
                    <div class="card-body">
                        <h2 class="section-title">Our Story</h2>
                        <p>Founded in <?php echo isset($foundedYear) ? $foundedYear : '2020'; ?>, Minute Mart began with a simple idea: to make online shopping faster, easier, and more reliable for everyone.</p>
                    </div>
                </div>
                
                <div class="policy-card">
                    <div class="card-body">
                        <h2 class="section-title">Our Mission</h2>
                        <ul class="policy-list">
                            <li>Provide high-quality products at competitive prices</li>
                            <li>Offer exceptional customer service</li>
                            <li>Make online shopping a seamless experience</li>
                        </ul>
                    </div>
                </div>
                
                <div class="policy-card">
                    <div class="card-body">
                        <h2 class="section-title">Meet Our Team</h2>
                        <div class="row">
                            <div class="col-md-4 team-member">
                                <img src="assets/images/team1.jpg" alt="Team Member">
                                <h4>John Doe</h4>
                                <p>Founder & CEO</p>
                            </div>
                            <div class="col-md-4 team-member">
                                <img src="assets/images/team2.jpg" alt="Team Member">
                                <h4>Jane Smith</h4>
                                <p>Operations Manager</p>
                            </div>
                            <div class="col-md-4 team-member">
                                <img src="assets/images/team3.jpg" alt="Team Member">
                                <h4>Mike Johnson</h4>
                                <p>Customer Support</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include('includes/footer.php'); ?>
</body>
</html>