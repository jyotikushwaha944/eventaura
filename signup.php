<?php
session_start();
require 'includes/database.php';


$errors = []; // Array to hold validation error messages

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $userName = $_POST['userName'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $userType = $_POST['usertype']; // Capture the user type

    // Validate first name, last name, username, email, and phone number
    $namePattern = '/^[A-Z][a-zA-Z]{4,}$/'; // First letter capital, at least 5 characters, only letters

    if (empty($firstName) || empty($lastName) || empty($userName) || empty($email) || empty($password)) {
        $errors[] = 'All fields are required.';
    } elseif (!preg_match($namePattern, $firstName)) {
        $errors[] = 'First name must start with a capital letter and be at least 5 characters long, containing only letters.';
    } elseif (!preg_match($namePattern, $lastName)) {
        $errors[] = 'Last name must start with a capital letter and be at least 5 characters long, containing only letters.';
    } elseif (!preg_match($namePattern, $userName)) {
        $errors[] = 'Username must start with a capital letter and be at least 5 characters long, containing only letters.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format.';
        } else {
        // All validations passed, proceed with user registration
        $passwordHashed = password_hash($password, PASSWORD_DEFAULT);
        $createdDate = date('Y-m-d H:i:s');

        // Get the MySQLi connection
        $conn = getDB();

        // Prepare the SQL statement
        $stmt = $conn->prepare("INSERT INTO user (firstname, lastname, username, usertype_id, password, email, created_datetime) VALUES (?, ?, ?, ?, ?, ?, ?)");

        if ($stmt === false) {
            $errors[] = 'Prepare failed: ' . htmlspecialchars($conn->error);
        } else {
            $stmt->bind_param("sssssss", $firstName, $lastName, $userName, $userType, $passwordHashed, $email, $createdDate);

            if ($stmt->execute()) {
                $_SESSION['message'] = "Registration successful. Please log in.";
                header("Location: login.php");
                exit();
            } else {
                $errors[] = 'Execute failed: ' . htmlspecialchars($stmt->error);
            }

            $stmt->close();
        }

        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
        }

        .register-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-image: url('img/concert2.avif');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .register-box {
            display: flex;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            width: 100%;
            background-color: #ffffff; /* Ensure register box has a white background */
        }

        .register-sidebar {
            background-color: #f7f7f7;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 10px;
            width: 50%;
        }

        .register-sidebar img {
            max-width: 100%;
            height: 100%;
        }

        .register-form-container {
            padding: 40px 30px;
            width: 50%;
        }
    </style>
</head>
<body>
<?php include 'header.php'; ?>

    <div class="register-container">
        <div class="register-box">
            <div class="register-form-container">
                <h4 class="text-center mb-4">Register</h4>
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-warning" role="alert">
                        <?php foreach ($errors as $error): ?>
                            <p><?php echo htmlspecialchars($error); ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <form action="" method="POST">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="firstName">First Name:</label>
                            <input type="text" id="firstName" name="firstName" class="form-control form-control-sm" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="lastName">Last Name:</label>
                            <input type="text" id="lastName" name="lastName" class="form-control form-control-sm" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="userName">User Name:</label>
                        <input type="text" id="userName" name="userName" class="form-control form-control-sm" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="email">Email:</label>
                            <input type="email" id="email" name="email" class="form-control form-control-sm" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="usertype">User Type:</label>
                            <select id="usertype" name="usertype" class="form-control form-control-sm" required>
                                <option value="1">Individual</option>
                                <option value="2">Organization</option>
                                <option value="3">Admin</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" class="form-control form-control-sm" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Register</button>
                </form>
                <p class="text-center mt-3"><a href="login.php">Already have an account? Log in</a></p>
            </div>
            <div class="register-sidebar">
                <img src="img/sidebar.png" alt="Sidebar Image">
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" crossorigin="anonymous"></script>
</body>
</html>