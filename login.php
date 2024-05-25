<?php
require_once("config/index.php");
// redirectToLoginIfNotLoggedIn(); // Restrict access to logged-in users

$db = new Database();
if (!empty($_GET['error'])) {
    // Display the error message
    echo '<div style="color: red; font-weight: bold;">' . htmlspecialchars($_GET['error']) . '</div>';
}
if (isset($_POST['login']) && $_POST['login'] == 'Submit') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (!empty($username) && !empty($password)) {
        // Use the select function without parameters
        $columns = '*';
        $table = 'users';
        $where = "username = '$username' AND password = '" . md5($password) . "'";
        // print_r($where)."<br>";
        
        $user = $db->sql($columns, $table, $where);
        // print_r($user)."<br>";


        if ($user) {
            
            // Start a session if not already started
            session_start();

            // Set user information in session variables
            $_SESSION['user_id'] = $user[0]['id']; // Assuming 'id' is a column in your users table
            $_SESSION['username'] = $user[0]['username']; // Assuming 'username' is a column in your users table

            // Set success message
            setSuccessMessage('Login Successfully');

            // Redirect to the dashboard or home page
            header("Location: dashboard.php");
            exit();
        } else {
            setErrorMessage('Invalid username or password');
            // Redirect to the login page (same page)
            header("Location: login.php");
            exit();
        }
    } else {
        setErrorMessage('Password & username required');
        // Redirect to the login page (same page)
        header("Location: login.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- <link rel="stylesheet" href="css/style.css"> -->

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <!-- <link rel="stylesheet" href="css/style.css">  -->
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">
            <img src="image/logo.jpeg" height="40" width="140">
        </a>
    </nav>

    <div class="container p-5">
        <?php
        // Check for error message and display it
        $errorMessage = getErrorMessage();
        if (!empty($errorMessage)) {
            echo '<div style="color: red; font-weight: bold;">' . $errorMessage . '</div>';
        }
        ?>        <form action="login.php" method="post"  autocomplete="off">
            <h2>Login Form</h2><br>
            <div class="form-group">
                <label for="exampleInputEmail1">Username</label>
                <input type="text" name="username"  required class="form-control" id="exampleInputEmail1" placeholder="Enter username">
            </div>
            <div class="form-group">
                <label for="exampleInputPassword1">Password</label>
                <input type="password" name="password"  class="form-control" id="exampleInputPassword1" placeholder="Password">
            </div>

            <button type="submit" name="login" value="Submit" class="btn btn-primary">Submit</button>
        </form>
       
        <?php if (isset($error)) : ?>
            <div style="color: red; font-weight: bold;"><?php echo $error; ?></div>
        <?php endif; ?>
    </div>
</body>
</html>


