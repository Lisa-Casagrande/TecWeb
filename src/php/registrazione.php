<?php
// Percorso file JSON
$file = "utenti.json";

// Legge utenti esistenti
$utenti = [];
if (file_exists($file)) {
    $utenti = json_decode(file_get_contents($file), true);
}

$email = $_POST["email"];
$username = $_POST["username"];

// Controlla se utente esiste
foreach ($utenti as $u) {
    if ($u["email"] === $email || $u["username"] === $username) {
        die("Email o username già in uso. <a href='../registrazione.html'>Torna indietro</a>");
    }
}

// Crea nuovo utente
$nuovo = [
    "nome" => $_POST["nome"],
    "cognome" => $_POST["cognome"],
    "email" => $email,
    "username" => $username,
    "password" => password_hash($_POST["password"], PASSWORD_DEFAULT)
];

$utenti[] = $nuovo;

// Salva nel file
file_put_contents($file, json_encode($utenti, JSON_PRETTY_PRINT));

header("Location: ../login.html?registrato=1");
exit;
