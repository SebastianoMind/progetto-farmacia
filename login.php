<?php
// inizializziamo la sessione
session_start();

// controllo se l'utente è già loggato: se si, allora vado nella pagina index.php
if (isset($_SESSION['login']) && $_SESSION['login'] === true) {
    header('location:index.php');
    exit();
}


require_once('config/db_connection.php');
include('config/get_page.php');


$username_err = $password_err = $login_err = "";
$username = $password = "";



if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty(trim($_POST['username']))) {
        $username_err = "Inserisci il tuo nome utente.";
    } else {
        $username = trim($_POST['username']);
    }


    if (empty(trim($_POST['password']))) {
        $password_err = "Inserisci la password.";
    } else {
        $password = trim($_POST['password']);
    }


    if (empty($username_err) && empty($password_err)) {

        $check_user_info = "SELECT `username`, `password` FROM login WHERE username = ?";

        if ($check_user = $conn->prepare($check_user_info)) {

            $check_user->bindParam(1, $param_user, PDO::PARAM_STR);

            $param_user = $username;

            if ($check_user->execute()) {

                $user = $check_user->fetch(PDO::FETCH_ASSOC);



                if ($check_user->rowCount() == 1) {


                    /*   $check_user->bindColumn($id, $username, $password, PDO::PARAM_STR);  */
                    if ($user) {
                        if (password_verify($password, $user['password'])) {



                            $_SESSION['login'] = true;
                            $_SESSION['id'] = $id;
                            $_SESSION['username'] = $username;



                            header('location:index.php');
                            exit();
                        } else {

                            $login_err = "Password non corretta: riprova...";
                        }
                    }
                } else {
                    $login_err = "Username non presente nel database...";
                }
            } else {
                echo "Qualcosa è andato storto...";
            }


            unset($check_user);
        }
    }
    // chiudo connession db
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
            <h2>Login</h2>
            <p>Please fill in your credentials to login.</p>

            <?php
            if (!empty($login_err)) {
                echo "<div class=\"alert alert-danger\">$login_err</div>";
            }

            ?>

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
                    <button type="submit" class="btn btn-primary">Login</button>
                </div>
                <p>Non hai un'account?<a href="register.php"> Registati qui</a></p>
            </form>
        </main>


        <?php include('template/footer.php'); ?>

    </section>


</body>

</html>