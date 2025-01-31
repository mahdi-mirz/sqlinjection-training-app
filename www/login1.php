<?php
ob_start();
session_start();
include("db_config.php");
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$login_msg = '1';
    $username = $_POST['uid'];
    $password = $_POST['password'];

    // SQL Injection vulnerability is still here (concatenation of user input)
    $q = "SELECT * FROM users WHERE username='" . $username . "' AND password='" . md5($password) . "'";

    if (isset($_GET['debug']) && $_GET['debug'] == "true") {
        $msg = "<div style='border:1px solid #4CAF50; padding: 10px'>" . $q . "</div><br />";
    }

    $result = mysqli_query($con, $q);

    if ($result && $row = mysqli_fetch_assoc($result)) {
        $_SESSION["username"] = $row["username"];
        $_SESSION["name"] = $row["name"];

        // Keep session fixation vulnerability
        $redirect_page = isset($_SESSION['next']) ? $_SESSION['next'] : 'searchproducts.php';
        header("Location: " . $redirect_page);
        exit();
    } else {
        $login_msg = "<font style='color:#FF0000'>Invalid username or password!</font>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login Page 1 - SQL Injection Training App</title>
    <link href="./css/htmlstyles.css" rel="stylesheet">
</head>
<body>
    <div class="container-narrow">
        <div class="jumbotron">
            <p class="lead" style="color:white">
                Login Page 1 - Simple Login Bypass
                <?php
                if (!empty($_REQUEST['msg'])) {
                    if ($_REQUEST['msg'] === "1") {
                        $_SESSION['next'] = 'searchproducts.php';
                        echo "<br />Please login to continue to Search Products";
                    } elseif ($_REQUEST['msg'] === "2") {
                        $_SESSION['next'] = 'blindsqli.php';
                        echo "<br />Please login to continue to Blind SQL Injection Page";
                    } elseif ($_REQUEST['msg'] === "3") {
                        $_SESSION['next'] = 'os_sqli.php';
                        echo "<br />Please login to continue to OS Command Injection Page";
                    } else {
                        $_SESSION['next'] = 'searchproducts.php';
                    }
                }
                ?>
            </p>
        </div>

        <div class="response">
            <form method="POST" autocomplete="off">
                <p style="color:white">
                    Username: <input type="text" name="uid"><br /><br />
                    Password: <input type="password" name="password">
                </p>
                <br />
                <p>
                    <input type="submit" value="Submit" />
                    <input type="reset" value="Reset" />
                </p>
            </form>
			
        </div>
		<div>
				<?php
				if (isset($_GET['debug']) && !is_null($msg)) {
					echo $msg;
				};
				if (!is_null($login_msg) && !$login_msg == "1") {
					echo $login_msg;
				};
				?>
				</div>

        <div class="footer">
            <h4><a href="index.php">Home</a></h4>
        </div>
    </div>
</body>
</html>
