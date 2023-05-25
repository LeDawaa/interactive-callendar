<!DOCTYPE html>
<html>
    <head>
        <title>Login Page</title>

        <link rel="stylesheet" href="css/login_style.css">
    </head>
    <body>
        <div id="container">
            <h1>Login Page</h1>
            <?php
                session_start();

                $users = json_decode(file_get_contents('db/users.json'), true);

                $username = $_POST['username'];
                $password = hash('sha256', $_POST['password']);

                if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
                    header("location: php/calendar.php");
                    exit;
                }

                if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                    if (isset($_POST['auth']) && $_POST['username'] != '') {
                        foreach ($users as $user) {
                            if ($user['username'] === $username && $user['password'] === $password) {
                                $_SESSION['loggedin'] = true;
                                $_SESSION['authenticated'] = true;
                                $_SESSION['username'] = $username;

                                header("location: php/calendar.php");
                                exit;
                            }
                        } $error = "Invalid username or password.";
                    } else if (isset($_POST['guest'])) {
                        $_SESSION['loggedin'] = true;
                        $_SESSION['authenticated'] = false;
                        $_SESSION['username'] = 'Guest';

                        header("location: php/calendar.php");
                    } else if (isset($_POST['signup'])) {
                        $newUser = [
                            'username' => $username,
                            'password' => $password
                        ];
                        
                        $userExists = false;
                        foreach ($users as $user) {
                            if ($user['username'] === $newUser['username']) {
                                $userExists = true;
                                break;
                            }
                        }
                        
                        if (!$userExists) {
                            $users[] = $newUser;
                            file_put_contents('db/users.json', json_encode($users, JSON_PRETTY_PRINT));
                            
                            $_SESSION['loggedin'] = true;
                            $_SESSION['authenticated'] = true;
                            $_SESSION['username'] = $newUser['username'];
                    
                            header("location: php/calendar.php");
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
                <input type="submit" name="guest" value="Login as student">
                <input type="submit" name="signup" value="Sign Up">
            </form>
        </div>
    </body>
</html>