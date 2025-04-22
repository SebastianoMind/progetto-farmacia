<?php



// controllo se l'utente è già loggato: se si, allora vado nella pagina index.php
if (isset($_SESSION['login']) && $_SESSION['login'] === true) {
    header('location:index.php');
    exit();
}
// Include config file
require_once('config/db_connection.php');
include('config/get_page.php');

// definizione variabili per gestire messaggi errore
$username = $password = $confirm_password = "";
$username_err = $password_err = $confirm_password_err = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {


    if (empty(trim($_POST['username']))) {
        $username_err = "Inserisci il tuo nome utente";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST['username']))) {
        $username_err = "il tuo nome utente può contenere solo lettere, numeri. ";
    } else {

        $login_user_info = "SELECT `id`, `username`, `password` FROM login WHERE username = ?";

        $username = trim($_POST['username']);

        if ($login_user = $conn->prepare($login_user_info)) {

            $login_user->bindParam(1, $username, PDO::PARAM_STR);



            if ($login_user->execute()) {


                $user = $login_user->fetch(PDO::FETCH_ASSOC);

                if ($login_user->rowCount()  == 1) {
                    $username_err = "Questo username è gia stato usato.";
                } else {
                    $username;
                }
            } else {
                echo "Qualcosa è andato storto...";
            }

            unset($login_user);
        }
    }


    if (empty(trim($_POST['password']))) {
        $password_err = "Inserisci una password.";
    } elseif (strlen(trim($_POST['password'])) < 8) {
        $password_err = "La password deve contenere almeno 8 caratteri";
    } else {
        $password = trim($_POST['password']);
    }


    if (empty(trim($_POST['confirm_password']))) {
        $confirm_password_err = "Per favore conferma la password";
    } else {
        $confirm_password = trim($_POST['confirm_password']);

        if ($password !== $confirm_password && empty($password_err)) {
            $confirm_password_err = "Le password non coincidono.";
        }
    }





    if (empty($username_err) && empty($password_err) && empty($confirm_password_err)) {


        $insert_user_info = 'INSERT INTO login (`username`, `password`) VALUES (?, ?)';

        if ($insert_user = $conn->prepare($insert_user_info)) {


            $insert_user->bindParam(1, $param_user, PDO::PARAM_STR);
            $insert_user->bindParam(2, $param_password, PDO::PARAM_STR);


            $param_user = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT);

            if ($insert_user->execute()) {
                header('location:login.php');
            } else {
                echo "Qualcosa è andato storto...";
            }

            // chiudo connessione
            unset($insert_user);
        }
    }
    // chiudo connessione al db
    unset($conn);
}



?>

<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css">

</head>

<body class="d-flex h-100 text-center text-white bg-dark">
    <section class="container d-flex w-100 h-100 p-3 mx-auto flex-column">
        <?php include('template/header.php'); ?>


        <main class="px-3 login">
            <h2>Sign Up</h2>
            <p>Please fill this form to create an account.</p>
            <form method="post">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control <?= (!empty($username_err)) ? 'is-invalid' : '' ?>">
                    <span class="invalid-feedback"><?= $username_err; ?></span>
                </div>

                <div class="form-group">
                    <label>password</label>
                    <input type="password" name="password" class="form-control <?= (!empty($password_err)) ? 'is-invalid' : '' ?>">
                    <span class="invalid-feedback"><?= $password_err; ?></span>
                </div>
                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" class="form-control <?= (!empty($confirm_password_err)) ? 'is-invalid' : '' ?>">
                    <span class="invalid-feedback"><?= $confirm_password_err; ?></span>

                </div>
                <div class="form-group">
                    <input type="submit" class="btn btn-primary" value="Registrati">
                    <input type="reset" class="btn btn-secondary ml-2" value="reset">
                </div>
                <p>Sei già registrato? <a href="login.php">Vai alla pagina di login</a> </p>

            </form>

        </main>


        <?php include('template/footer.php'); ?>

    </section>


</body>

</html>