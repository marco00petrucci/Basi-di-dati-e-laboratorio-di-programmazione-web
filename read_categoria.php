<?php
include "db_connect.php";

// Cerca tutti i blog o categorie che contengono i caratteri del testo digitato
$query = "SELECT DISTINCT P1.nome_b AS ricerca FROM post AS P1 WHERE P1.nome_b LIKE '%$_GET[testo]%' UNION SELECT DISTINCT P2.categoria AS ricerca FROM post AS P2 WHERE P2.categoria LIKE '%$_GET[testo]%' GROUP BY ricerca LIMIT 0,6";
$result = mysqli_query($conn, $query) or die("Connessione fallita: " . mysqli_error($conn));
if (!empty($_GET['testo'])) {
     $i = 0;
     while ($riga = mysqli_fetch_array($result)) {
          echo "<p class='result'>$riga[ricerca]</p>";
          $i++;
     }
     if ($i == 0) echo "<p class='no_result'>Nessun risultato</p>";
}
?>