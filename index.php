<!DOCTYPE html>
<html>
    <head>
        <title>Login Page</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f2f2f2;
            }
            #container {
                margin: 0 auto;
                max-width: 500px;
                background-color: #fff;
                padding: 20px;
                border-radius: 5px;
                box-shadow: 0px 0px 10px #ddd;
            }
            h1 {
                text-align: center;
                margin-bottom: 20px;
            }
            label {
                display: block;
                font-size: 18px;
                margin-bottom: 10px;
            }
            input[type="text"], input[type="password"] {
                display: block;
                width: 95%;
                padding: 10px;
                border-radius: 5px;
                margin-bottom: 20px;
                font-size: 18px;
            }
            input[type="submit"] {
                background-color: #4CAF50;
                color: #fff;
                border-radius: 5px;
                padding: 10px 20px;
                font-size: 18px;
                cursor: pointer;
            }
            input[name="signup"] {
                float: right;
            }
            input[type="submit"]:hover {
                background-color: #3e8e41;
            }
            .error {
                color: red;
                margin-bottom: 10px;
            }
        </style>
    </head>
    <body>
        <div id="container">
            <h1>Login Page</h1>
            <?php
                session_start();

                $users = json_decode(file_get_contents('users.json'), true);

                $username = $_POST['username'];
                // Hash the password
                $password = hash('sha256', $_POST['password']);

                // Check if the user is already logged in
                if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
                    // Redirect to the calendar page
                    header("location: calendar.php");
                    exit;
                }

                // Check if the user has submitted the login form
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                    // Check if the user has submitted the login form
                    if (isset($_POST['auth']) && $_POST['username'] != '') {
                        foreach ($users as $user) {
                            if ($user['username'] === $username && $user['password'] === $password) {
                                $_SESSION['loggedin'] = true;
                                $_SESSION['authenticated'] = true;
                                $_SESSION['username'] = $username;

                                // Redirect to the calendar page
                                header("location: calendar.php");
                                exit;
                            }
                        } $error = "Invalid username or password.";
                    } else if (isset($_POST['guest'])) {
                        $_SESSION['loggedin'] = true;
                        $_SESSION['authenticated'] = false;
                        $_SESSION['username'] = 'Guest';

                        // Redirect to the calendar page
                        header("location: calendar.php");
                    } else if (isset($_POST['signup'])) {
                        $newUser = [
                            'username' => $username,
                            'password' => $password
                        ];
                        
                        // Check if the username already exists in the database
                        $userExists = false;
                        foreach ($users as $user) {
                            if ($user['username'] === $newUser['username']) {
                                $userExists = true;
                                break;
                            }
                        }
                        
                        // If the username is unique, add the new user to the JSON file
                        if (!$userExists) {
                            $users[] = $newUser;
                            file_put_contents('users.json', json_encode($users, JSON_PRETTY_PRINT));
                            
                            $_SESSION['loggedin'] = true;
                            $_SESSION['authenticated'] = true;
                            $_SESSION['username'] = $newUser['username'];
                    
                            // Redirect to the calendar page
                            header("location: calendar.php");
                            exit;
                        } else {
                            $error = "Username already exists. Please choose a different username.";
                        }
                    }
                }
            ?>
            <?php if (isset($error)): ?>
                <div class="error"> <?php echo $error; ?> </div>
            <?php endif; ?>
            <form method="post">
                <label>Username:</label>
                <input type="text" name="username">
                <label>Password:</label>
                <input type="password" name="password">
                <input type="submit" name="auth" value="Login">
                <input type="submit" name="guest" value="Login as guest">
                <input type="submit" name="signup" value="Sign Up">
            </form>
        </div>
    </body>
</html>