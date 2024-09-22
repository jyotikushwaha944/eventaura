<?php 
function getUser($conn, $email) {
    $sql = "SELECT * FROM user WHERE email=?";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt === false) {
        echo mysqli_error($conn);
    } else {
        mysqli_stmt_bind_param($stmt, "s", $email);

        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            return mysqli_fetch_array($result, MYSQLI_ASSOC);
        }
    }
}

session_start();
require 'includes/database.php';
$conn = getDB();

if (isset($_POST['email'])) {
    $user = getUser($conn, $_POST['email']);
    if ($user) {
        $username = $user['username'];
        $email = $user['email'];
        $userid = $user['id'];
        $usertype_id = $user['usertype_id'];
        $hashedPassword = $user['password'];

    } else {
        die("User not found");
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $formEmail = $_POST['email'];
    $formPass = $_POST['password'];
    if ($formEmail == '' || $formPass == '') {
        echo '<div class="alert alert-warning" role="alert">One or more fields are empty</div>';
    } else {
        
        if (password_verify($formPass, $hashedPassword)) {
            $_SESSION['loggedIn'] = true;
            $_SESSION['username'] = $username;
            $_SESSION['userid'] = $userid;
            $_SESSION['usertype_id'] = $usertype_id;
            header("Location: index.php");
            exit;
        } else {
            echo '<div class="alert alert-danger" role="alert">Invalid credentials</div>';
        }
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
        }

        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-image: url('img/concert2.avif');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .login-box {
            display: flex;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            width: 100%;
            background-color: #ffffff;  /* Ensure login box has a redbackground */
        }

        .login-sidebar {
            background-color: #ffffff;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 10px;
            width: 50%;

        }

        .login-sidebar img {
            max-width: 100%;
            height: 100%;
        }

        .login-form-container {
            padding: 40px 30px;
            width: 50%;
        }
    </style>
</head>
<body>
<?php include 'header.php'; ?>

    <div class="login-container">
        <div class="login-box">
            <div class="login-form-container">
                <h2 class="text-center mb-4">Log In</h2>
                <form action="" method="POST">
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" class="form-control form-control-sm" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" class="form-control form-control-sm" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Log In</button>
                </form>
                <p class="text-center mt-3"><a href="signup.php">Don't have an account? Register</a></p>
            </div>
            <div class="login-sidebar">
                <img src="img/login.jpg" alt="Sidebar Image">
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" crossorigin="anonymous"></script>
</body>
</html>