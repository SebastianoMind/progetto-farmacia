<?php



$page = str_replace('/lezioni/farmacia/', '', $_SERVER['PHP_SELF']); // ritorna nome del file.php che stiamo visualizzando

switch ($page) {
    case 'index.php':
        $title = "Benvenuto utente";
        break;
    case 'farmaci.php':
        $title = "Elenco farmaci";
        break;
    case 'login.php':
        $title = "Login";
        break;
    case 'register.php':
        $title = "Sign up";
        break;
    default:
        $title = "Pagina non esistente";
}
