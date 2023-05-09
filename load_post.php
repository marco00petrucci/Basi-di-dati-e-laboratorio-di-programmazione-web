<?php
include 'db_connect.php';

$riga = 0;
if (isset($_POST['riga'])) {
    $riga = mysqli_real_escape_string($conn, $_POST['riga']);
    $riga += 2; // Non stampare post doppiati
}

// Seleziona i post
$query = "SELECT *, DATE_FORMAT(creato_il, '%W %d %M %Y - alle ore %H:%i') AS niceDate FROM post ORDER BY creato_il DESC LIMIT $riga,3";
$result = mysqli_query($conn, $query) or die("Connessione fallita: " . mysqli_error($conn));

while ($riga = mysqli_fetch_array($result)) {
    $id = $riga['id'];
    $data = $riga['niceDate'];
    $categoria = $riga['categoria'];
    $titolo = $riga['titolo'];
    $autore = $riga['autore'];
    $contenuto = $riga['contenuto'];
    $immagine = $riga['immagine'];

    // Stampa i post
    echo "<div class='post' id='post_$id'>
            <a href='single.php?id=$id' style='color:rgb(99, 10, 13)'>
                <img src='image/$immagine' alt='Immagine post'>
                <h1>$titolo</h1>
            </a>
            <p>
                <a href='blog.php?testoCerca=$categoria' style='color:rgb(99, 10, 13)'>
                    <b>$categoria</b>
                </a> - ";

    if (isset($_SESSION['user_session']) && $autore == $_SESSION['user_session']) echo "<a href='reg-login/account.php' style='color:rgb(99, 10, 13)'>$autore</a> | ";
    else echo "<a href='reg-login/account.php?username=$autore' style='color:rgb(99, 10, 13)'>$autore</a> | ";
    echo ucfirst($data) . "</p>";

    // Se i caratteri del contenuto sono superiori a 400 allora mostra solo quelli
    if (strlen($contenuto) > 400) {
        echo "<p>" . substr($contenuto, 0, 400) . "...</p><br>
        </div>";
    } else {
        echo "<p>$contenuto</p><br>
        </div>";
    }
}
