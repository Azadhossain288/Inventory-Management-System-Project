<?php
session_start();
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include('../database/connection.php');

    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE email = :email AND password = :password";
    $stmt = $conn->prepare($query);
    $stmt->execute([
        ':email' => $username,
        ':password' => $password
    ]);

    if ($stmt->rowCount() > 0) {
        $stmt->setFetchMode(PDO::FETCH_ASSOC); //  Correct method name
        $user = $stmt->fetchAll()[0];           //  Use $user with $
        $_SESSION['user'] = $user;

        // Optional: debug session
        // var_dump($_SESSION['user']); exit;

        header('Location: ./dashboard.php'); //  Redirect
         
    } else {
        $error_message = 'Please make sure that username and password are correct.';
    }
}
?>






<!DOCTYPE html>
<html>
    <head>
         <title>IMS Login-Inventory Management System</title>
    <link rel="stylesheet" href="../css/style.css">
    </head>
    <body id="loginBody">
        

        <div class="container">
            <div class="loginHeader">
                <h1>IMS</h1>
                <p>Inventory Management System</p>
            </div>

          
         <?php if (!empty($error_message)) { ?>
            <div id="errorMessage" style="color: red; text-align: center;">
                <p><strong>Error:</strong> <?= htmlspecialchars($error_message) ?></p>
            </div>
        <?php } ?>


            <div class="loginBody">
                <form action="log.php" method="POST">
                    <div class="loginInputContainer">
                        <label for="">Username</label>
                        <input placeholder="username" name="username" type="text"/>

                    </div>
                    <div class="loginInputContainer">
                        <label for="">Password</label>
                        <input placeholder="password" name="password" type="password"/>
                    </div>
                    <div class="loginButtonContainer">
                        <button>login</button>
                    </div>
                </form>
            </div>
        </div>
    </body>
    

</html>