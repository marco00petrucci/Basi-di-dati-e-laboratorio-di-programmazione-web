<?php
// Connetti al database
$conn = mysqli_connect('localhost', 'root', '', 'my_bddbloggy') or die("Connessione fallita: " . mysqli_connect_error());
$conn-> query("SET lc_time_names = 'it_IT'");
?>