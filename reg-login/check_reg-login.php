<?php
session_start();
require('../db_connect.php');

date_default_timezone_set('Europe/Rome');
$data = strftime('%Y-%m-%d %H:%M:%S ', time());
$vietate = array("SELECT", "INSERT", "UPDATE", "DELETE", "DROP", "ALTER", "<script>");

//Invia il messaggio
if (isset($_POST['cont_invia'])) {
     $nome = mysqli_real_escape_string($conn, $_POST['Anome']);
     $email = mysqli_real_escape_string($conn, $_POST['Aemail']);
     $telefono = mysqli_real_escape_string($conn, $_POST['Atelefono']);
     $messaggio = mysqli_real_escape_string($conn, $_POST['messaggio']);

     $nome_ok = true;
     for ($k = 0; $k <= 6 && $nome_ok; $k++) {
          if (stripos($nome, $vietate[$k]) !== false) $nome_ok = false;
     }

     $email_ok = true;
     for ($k = 0; $k <= 6 && $email_ok; $k++) {
          if (stripos($email, $vietate[$k]) !== false) $email_ok = false;
     }

     if (!empty($telefono)) {
          $telefono_ok = true;
          for ($k = 0; $k <= 6 && $telefono_ok; $k++) {
               if (stripos($telefono, $vietate[$k]) !== false) $telefono_ok = false;
          }
     } else $telefono_ok = true;

     $messaggio_ok = true;
     for ($k = 0; $k <= 6 && $messaggio_ok; $k++) {
          if (stripos($messaggio, $vietate[$k]) !== false) $messaggio_ok = false;
     }

     // Se non sono stati inseriti caratteri tipici della SQL Injection
     if ($nome_ok && $email_ok && $telefono_ok && $messaggio_ok) {
          $query = "INSERT INTO messaggi (data_m, nome, email, telefono, messaggio) VALUES ('$data', '$nome', '$email', '$telefono', '$messaggio')";
          $mex_execution = mysqli_query($conn, $query) or die("Connessione fallita: " . mysqli_error($conn));
          if ($mex_execution) echo "Messaggio inviato";
     } else echo "Dati corrotti";
}

// Verifica il funzionamento della registrazione
if (isset($_POST['reg_btn'])) {
     $nome = mysqli_real_escape_string($conn, $_POST['Rnome']);
     $cognome = mysqli_real_escape_string($conn, $_POST['Rcognome']);
     $username = mysqli_real_escape_string($conn, $_POST['Rusername']);
     $email = mysqli_real_escape_string($conn, $_POST['Remail']);
     $password = mysqli_real_escape_string($conn, $_POST['Rpassword']);

     $username_query = "SELECT * FROM users WHERE username = '$username'";
     $username_exec = mysqli_query($conn, $username_query) or die("Connessione fallita: " . mysqli_error($conn));
     $username_result = mysqli_fetch_assoc($username_exec);

     $email_query = "SELECT * FROM users WHERE email = '$email'";
     $email_exec = mysqli_query($conn, $email_query) or die("Connessione fallita: " . mysqli_error($conn));
     $email_result = mysqli_fetch_assoc($email_exec);

     // Verifica se l'username ?? gi?? stato preso
     if (mysqli_num_rows($username_exec) > 0 && $username_result['username'] == $username) echo "Username gi?? esistente";

     // Verifica se l'email ?? gi?? stata presa
     else if (mysqli_num_rows($email_exec) > 0 && $email_result['email'] == $email) echo "Email gi?? esistente";

     // Se l'username o l'email non sono state gi?? prese
     else {
          $nome_ok = true;
          for ($k = 0; $k <= 6 && $nome_ok; $k++) {
               if (stripos($nome, $vietate[$k]) !== false) $nome_ok = false;
          }

          $cognome_ok = true;
          for ($k = 0; $k <= 6 && $cognome_ok; $k++) {
               if (stripos($cognome, $vietate[$k]) !== false) $cognome_ok = false;
          }

          $username_ok = true;
          for ($k = 0; $k <= 6 && $username_ok; $k++) {
               if (stripos($username, $vietate[$k]) !== false) $username_ok = false;
          }

          $email_ok = true;
          for ($k = 0; $k <= 6 && $email_ok; $k++) {
               if (stripos($email, $vietate[$k]) !== false) $email_ok = false;
          }

          $password_ok = true;
          for ($k = 0; $k <= 6 && $password_ok; $k++) {
               if (stripos($password, $vietate[$k]) !== false) $password_ok = false;
          }

          // Se non sono stati inseriti caratteri tipici della SQL Injection
          if ($nome_ok && $cognome_ok && $username_ok && $email_ok && $password_ok) {
               $query = "INSERT INTO users (add_data, username, nome, cognome, email, password) VALUES ('$data', '$username', '$nome', '$cognome', '$email', '$password')";
               $reg_exec = mysqli_query($conn, $query) or die("Connessione fallita: " . mysqli_error($conn));
               if ($reg_exec) {
                    $_SESSION['user_session'] = $username;
                    echo "Registrato";
               }
          } else echo "Dati corrotti";
     }
}

// Verifica il funzionamento del login
if (isset($_POST['login_btn'])) {
     $username = trim($_POST['Lusername']);
     $password = trim($_POST['Lpassword']);
     $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
     $execution = mysqli_query($conn, $query) or die("Connessione fallita: " . mysqli_error($conn));
     $result = mysqli_fetch_assoc($execution);
     if ($result['password'] == $password) {
          echo "Login effettuato";
          $_SESSION['user_session'] = $username;
     } else echo "Login non effettuato";
}

// Modifica campi user al click del bottone cambia
if (isset($_POST['modifica_btn'])) {
     $nome = mysqli_real_escape_string($conn, $_POST["Mnome"]);
     $cognome = mysqli_real_escape_string($conn, $_POST["Mcognome"]);
     $telefono = mysqli_real_escape_string($conn, $_POST['Mtelefono']);
     $email = mysqli_real_escape_string($conn, $_POST['Memail']);
     $epassword = mysqli_real_escape_string($conn, $_POST['Mpassword']);
     $ecpassword = mysqli_real_escape_string($conn, $_POST['Mcpassword']);

     // Verifica se l'email ?? gi?? stata presa
     $email_query = "SELECT * FROM users WHERE email = '$email'";
     $email_exec = mysqli_query($conn, $email_query) or die("Connessione fallita: " . mysqli_error($conn));
     $email_result = mysqli_fetch_assoc($email_exec);

     if (mysqli_num_rows($email_exec) > 0 && $email_result['email'] == $email && $_POST["email_vecchia"] != $email) echo "Email gi?? esistente";

     // Se la email non ?? gi?? stata presa
     else {

          $nome_ok = true;
          for ($k = 0; $k <= 6 && $nome_ok; $k++) {
               if (stripos($nome, $vietate[$k]) !== false) $nome_ok = false;
          }

          $cognome_ok = true;
          for ($k = 0; $k <= 6 && $cognome_ok; $k++) {
               if (stripos($cognome, $vietate[$k]) !== false) $cognome_ok = false;
          }

          if (!empty($telefono)) {
               $telefono_ok = true;
               for ($k = 0; $k <= 6 && $telefono_ok; $k++) {
                    if (stripos($telefono, $vietate[$k]) !== false) $telefono_ok = false;
               }
          } else $telefono_ok = true;

          $email_ok = true;
          for ($k = 0; $k <= 6 && $email_ok; $k++) {
               if (stripos($email, $vietate[$k]) !== false) $email_ok = false;
          }

          $epassword_ok = true;
          for ($k = 0; $k <= 6 && $epassword_ok; $k++) {
               if (stripos($epassword, $vietate[$k]) !== false) $epassword_ok = false;
          }

          if (!empty($ecpassword)) {
               $ecpassword_ok = true;
               for ($k = 0; $k <= 6 && $ecpassword_ok; $k++) {
                    if (stripos($ecpassword, $vietate[$k]) !== false) $ecpassword_ok = false;
               }
          } else $ecpassword_ok = true;

          // Se non sono stati inseriti caratteri tipici della SQL Injection
          if ($nome_ok && $cognome_ok && $telefono_ok && $email_ok && $epassword_ok && $ecpassword_ok) {
               if(empty($telefono)) $Uquery = "UPDATE users SET nome = '$nome', cognome = '$cognome', telefono = NULL, email = '$email', password = '$epassword' WHERE username = '$_SESSION[user_session]'";
               else $Uquery = "UPDATE users SET nome = '$nome', cognome = '$cognome', telefono = '$telefono', email = '$email', password = '$epassword' WHERE username = '$_SESSION[user_session]'";
               $edit_exec = mysqli_query($conn, $Uquery) or die("Connessione fallita: " . mysqli_error($conn));
               if ($edit_exec) echo "Modificato";
          } else echo "Dati corrotti";
     }
}

if (isset($_GET['blog'])) {
     // Verifica se il nome del blog ?? gi?? stato preso
     $nome_b_query = "SELECT nome_blog FROM blog WHERE nome_blog = '$_GET[blog]'";
     $nome_b_exec = mysqli_query($conn, $nome_b_query) or die("Connessione fallita: " . mysqli_error($conn));

     if (mysqli_num_rows($nome_b_exec) > 0) echo "Esiste gi??";
}

if (isset($_GET['aggiungi_categoria'])) {
     // Cerca tutti i blog o categorie che contengono i caratteri del testo digitato
     $query = "SELECT DISTINCT categoria FROM post WHERE categoria LIKE '%$_GET[aggiungi_categoria]%' LIMIT 6";
     $result = mysqli_query($conn, $query) or die("Connessione fallita: " . mysqli_error($conn));
     if (!empty($_GET['aggiungi_categoria'] && mysqli_num_rows($result) > 0)) {
          while ($riga = mysqli_fetch_array($result)) {
               echo "<p class='result'>$riga[categoria]</p>";
          }
     }
}

// Se si sceglie di commentare il post inserisci i dati nel database
if (isset($_POST['invia_commento'])) {
     $username = $_SESSION['user_session'];
     $commento = mysqli_real_escape_string($conn, $_POST['commento']);
     $id_post = mysqli_real_escape_string($conn, $_POST['id_post']);

     $commento_ok = true;
     for ($k = 0; $k <= 6 && $commento_ok; $k++) {
          if (stripos($commento, $vietate[$k]) !== false) $commento_ok = false;
     }

     // Se non sono stati inseriti caratteri tipici della SQL Injection nel commento
     if ($commento_ok) {
          $sql = "INSERT INTO commenti (data_c, username, commento, id_post, stato) VALUES ('$data', '$username', '$commento', '$id_post', 1)";
          $execution = mysqli_query($conn, $sql) or die("Connessione fallita: " . mysqli_error($conn));
          if ($execution) echo "Aggiunto";
     }
     else echo "Dati corrotti";
}
?>