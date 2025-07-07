<?php
// 🚫 Do not add any blank lines before this!
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('include/config.php');

// Check if the admin is logged in
if (!isset($_SESSION['alogin']) || strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit();
}

date_default_timezone_set('Asia/Kolkata');
$currentTime = date('d-m-Y h:i:s A', time());

if (isset($_POST['submit'])) {
    $oldPassword = md5($_POST['password']);
    $newPassword = md5($_POST['newpassword']);
    $username = $_SESSION['alogin'];

    // Check if old password is correct
    $sql = mysqli_query($con, "SELECT password FROM admin WHERE password='$oldPassword' AND username='$username'");
    $num = mysqli_fetch_array($sql);

    if ($num > 0) {
        // Update the password
        mysqli_query($con, "UPDATE admin SET password='$newPassword', updationDate='$currentTime' WHERE username='$username'");
        $_SESSION['msg'] = "Password Changed Successfully !!";
    } else {
        $_SESSION['msg'] = "Old Password does not match !!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Admin | Change Password</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
<link href="css/theme.css" rel="stylesheet">
<link href="images/icons/css/font-awesome.css" rel="stylesheet">

    <link href="http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600" rel="stylesheet">

    <script type="text/javascript">
        function valid() {
            if (document.chngpwd.password.value === "") {
                alert("Current Password field is empty!");
                document.chngpwd.password.focus();
                return false;
            } else if (document.chngpwd.newpassword.value === "") {
                alert("New Password field is empty!");
                document.chngpwd.newpassword.focus();
                return false;
            } else if (document.chngpwd.confirmpassword.value === "") {
                alert("Confirm Password field is empty!");
                document.chngpwd.confirmpassword.focus();
                return false;
            } else if (document.chngpwd.newpassword.value !== document.chngpwd.confirmpassword.value) {
                alert("New and Confirm Password fields do not match!");
                document.chngpwd.confirmpassword.focus();
                return false;
            }
            return true;
        }
    </script>
</head>
<body>

<?php include('include/header.php'); ?>

<div class="wrapper">
    <div class="container">
        <div class="row">
            <?php include('include/sidebar.php'); ?>

            <div class="span9">
                <div class="content">

                    <div class="module">
                        <div class="module-head">
                            <h3>Admin Change Password</h3>
                        </div>
                        <div class="module-body">

                            <?php if (isset($_POST['submit'])) { ?>
                                <div class="alert alert-success">
                                    <button type="button" class="close" data-dismiss="alert">×</button>
                                    <?php echo htmlentities($_SESSION['msg']); ?>
                                    <?php echo htmlentities($_SESSION['msg'] = ""); ?>
                                </div>
                            <?php } ?>

                            <form class="form-horizontal row-fluid" name="chngpwd" method="post" onSubmit="return valid();">
                                <div class="control-group">
                                    <label class="control-label" for="basicinput">Current Password</label>
                                    <div class="controls">
                                        <input type="password" placeholder="Enter your current Password" name="password" class="span8 tip" required>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label" for="basicinput">New Password</label>
                                    <div class="controls">
                                        <input type="password" placeholder="Enter your new Password" name="newpassword" class="span8 tip" required>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label" for="basicinput">Confirm Password</label>
                                    <div class="controls">
                                        <input type="password" placeholder="Confirm your new Password" name="confirmpassword" class="span8 tip" required>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <div class="controls">
                                        <button type="submit" name="submit" class="btn">Submit</button>
                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>

                </div><!--/.content-->
            </div><!--/.span9-->
        </div>
    </div><!--/.container-->
</div><!--/.wrapper-->

<?php include('include/footer.php'); ?>

<script src="scripts/jquery-1.9.1.min.js"></script>
<script src="scripts/jquery-ui-1.10.1.custom.min.js"></script>
<script src="bootstrap/js/bootstrap.min.js"></script>
<script src="scripts/flot/jquery.flot.js"></script>

</body>
</html>
