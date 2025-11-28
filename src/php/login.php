<?php
session_start();

$file = "utenti.json";
$utenti = json_decode(file_get_contents($file), true);

$username = $_POST["username"];
$password = $_POST["password"];

foreach ($utenti as $u) {
    if (
        ($u["email"] === $username || $u["username"] === $username) &&
        password_verify($password, $u["password"])
    ) {
        $_SESSION["nome"] = $u["nome"];
        $_SESSION["cognome"] = $u["cognome"];
        $_SESSION["email"] = $u["email"];
        $_SESSION["username"] = $u["username"];

        header("Location: ../account.php");
        exit;
    }
}

die("Credenziali errate. <a href='../login.html'>Riprova</a>");
