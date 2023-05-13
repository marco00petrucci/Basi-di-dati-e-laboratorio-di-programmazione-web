<?php
session_start();
require('../db_connect.php');

date_default_timezone_set('Europe/Rome');
$data = date('Y-m-d H:i:s');

function check_SQL_inj($test, $ok)
{
     $vietate = array("SELECT", "INSERT", "UPDATE", "DELETE", "DROP", "ALTER", "<script>");
     for ($k = 0; $k <= 6 && $ok; $k++) {
          if (stripos($test, $vietate[$k]) !== false) $ok = false;
     }
     return $ok;
}

// Invia il messaggio
if (isset($_POST['cont_invia'])) {
     $nome = mysqli_real_escape_string($conn, $_POST['Anome']);
     $email = mysqli_real_escape_string($conn, $_POST['Aemail']);
     $telefono = mysqli_real_escape_string($conn, $_POST['Atelefono']);
     $messaggio = mysqli_real_escape_string($conn, $_POST['messaggio']);

     $nome_ok = $email_ok = $telefono_ok = $messaggio_ok = true;
     if (empty($telefono)) $telefono_is_ok = true;
     else $telefono_is_ok = check_SQL_inj($telefono, $telefono_ok);

     $nome_is_ok = check_SQL_inj($nome, $nome_ok);
     $email_is_ok = check_SQL_inj($email, $email_ok);
     $messaggio_is_ok = check_SQL_inj($messaggio, $messaggio_ok);

     // Se non sono stati inseriti caratteri tipici della SQL Injection
     if ($nome_is_ok && $email_is_ok && $telefono_is_ok && $messaggio_is_ok) {
          mysqli_query($conn, "INSERT INTO messaggi (data_m, nome, email, telefono, messaggio) VALUES ('$data', '$nome', '$email', '$telefono', '$messaggio')") or die("Connessione fallita: " . mysqli_error($conn));
          echo "Messaggio inviato";
     } else echo "Dati corrotti";
}

// Verifica il funzionamento della registrazione
if (isset($_POST['reg_btn'])) {
     $nome = mysqli_real_escape_string($conn, $_POST['Rnome']);
     $cognome = mysqli_real_escape_string($conn, $_POST['Rcognome']);
     $username = mysqli_real_escape_string($conn, $_POST['Rusername']);
     $email = mysqli_real_escape_string($conn, $_POST['Remail']);
     $password = mysqli_real_escape_string($conn, $_POST['Rpassword']);

     $username_query = mysqli_query($conn, "SELECT username FROM users WHERE username = '$username'") or die("Connessione fallita: " . mysqli_error($conn));

     $email_query = mysqli_query($conn, "SELECT email FROM users WHERE email = '$email'") or die("Connessione fallita: " . mysqli_error($conn));

     // Verifica se l'username è già stato preso
     if (mysqli_num_rows($username_query) > 0) echo "Username già esistente";

     // Verifica se l'email è già stata presa
     else if (mysqli_num_rows($email_query) > 0) echo "Email già esistente";

     // Se l'username o l'email non sono state già prese
     else {
          $nome_ok = $cognome_ok = $username_ok = $email_ok = $password_ok = true;
          $nome_is_ok = check_SQL_inj($nome, $nome_ok);
          $cognome_is_ok = check_SQL_inj($cognome, $cognome_ok);
          $username_is_ok = check_SQL_inj($username, $username_ok);
          $email_is_ok = check_SQL_inj($email, $email_ok);
          $password_is_ok = check_SQL_inj($password, $password_ok);

          // Se non sono stati inseriti caratteri tipici della SQL Injection
          if ($nome_is_ok && $cognome_is_ok && $username_is_ok && $email_is_ok && $password_is_ok) {
               $crypt_password = password_hash($password, PASSWORD_DEFAULT);
               $reg_exec = mysqli_query($conn, "INSERT INTO users (add_data, username, nome, cognome, email, password) VALUES ('$data', '$username', '$nome', '$cognome', '$email', '$crypt_password')") or die("Connessione fallita: " . mysqli_error($conn));
               $_SESSION['user_session'] = $username;
               echo "Registrato";
          } else echo "Dati corrotti";
     }
}

// Verifica il funzionamento del login
if (isset($_POST['login_btn'])) {
     $username = mysqli_real_escape_string($conn, $_POST['Lusername']);
     $find_username = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'") or die("Connessione fallita: " . mysqli_error($conn));
     if (mysqli_num_rows($find_username) > 0) {
          $result_user = mysqli_fetch_assoc($find_username);
          $hash_user = $result_user['password'];
          $password = mysqli_real_escape_string($conn, $_POST['Lpassword']);
          if (password_verify($password, $hash_user)) {
               echo "Login effettuato";
               $_SESSION['user_session'] = $username;
          } else echo "Password errata";
     } else echo "Username non trovato";
}

// Modifica campi user al click del bottone cambia
if (isset($_POST['modifica_btn'])) {
     $nome = mysqli_real_escape_string($conn, $_POST["Mnome"]);
     $cognome = mysqli_real_escape_string($conn, $_POST["Mcognome"]);
     $telefono = mysqli_real_escape_string($conn, $_POST['Mtelefono']);
     $email = mysqli_real_escape_string($conn, $_POST['Memail']);
     $epassword = mysqli_real_escape_string($conn, $_POST['Mpassword']);
     $ecpassword = mysqli_real_escape_string($conn, $_POST['Mcpassword']);

     // Verifica che la mail sia stata cambiata dall'utente
     if ($_POST["email_vecchia"] != $email) {

          // Verifica se l'email è già stata presa
          $check_email = mysqli_query($conn, "SELECT email FROM users WHERE email = '$email'") or die("Connessione fallita: " . mysqli_error($conn));
          if (mysqli_num_rows($check_email) > 0) echo "Email già esistente";

          // Se la email non è già stata presa
          else {
               $nome_ok = $cognome_ok = $email_ok = $epassword_ok = true;
               $nome_is_ok = check_SQL_inj($nome, $nome_ok);
               $cognome_is_ok = check_SQL_inj($cognome, $cognome_ok);
               $email_is_ok = check_SQL_inj($email, $email_ok);
               // $epassword_is_ok = check_SQL_inj($epassword, $epassword_ok);

               if (empty($telefono)) $telefono_is_ok = true;
               else $telefono_is_ok = check_SQL_inj($telefono, $telefono_ok);
               // if (empty($ecpassword)) $ecpassword_is_ok = true;
               // else $ecpassword_is_ok = check_SQL_inj($ecpassword, $ecpassword_ok);

               // Se non sono stati inseriti caratteri tipici della SQL Injection
               if ($nome_is_ok && $cognome_is_ok && $telefono_is_ok && $email_is_ok) {
                    if (empty($telefono)) $Uquery = "UPDATE users SET nome = '$nome', cognome = '$cognome', telefono = NULL, email = '$email', password = '$epassword' WHERE username = '$_SESSION[user_session]'";
                    else $Uquery = "UPDATE users SET nome = '$nome', cognome = '$cognome', telefono = '$telefono', email = '$email', password = '$epassword' WHERE username = '$_SESSION[user_session]'";
                    $edit_exec = mysqli_query($conn, $Uquery) or die("Connessione fallita: " . mysqli_error($conn));
                    if ($edit_exec) echo "Modificato";
               } else echo "Dati corrotti";
          }
     } echo "Email invariata";
}

if (isset($_GET['blog'])) {
     // Verifica se il nome del blog è già stato preso
     $check_nome_b = mysqli_query($conn, "SELECT nome_blog FROM blog WHERE nome_blog = '$_GET[blog]'") or die("Connessione fallita: " . mysqli_error($conn));
     if (mysqli_num_rows($check_nome_b) > 0) echo "Esiste già";
}

if (isset($_GET['aggiungi_categoria'])) {
     // Cerca tutti i blog o categorie che contengono i caratteri del testo digitato
     $search = mysqli_query($conn, "SELECT DISTINCT categoria FROM post WHERE categoria LIKE '%$_GET[aggiungi_categoria]%' LIMIT 6") or die("Connessione fallita: " . mysqli_error($conn));
     if (!empty($_GET['aggiungi_categoria'] && mysqli_num_rows($search) > 0)) {
          while ($riga = mysqli_fetch_array($search)) {
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
     $commento_is_ok = check_SQL_inj($commento, $commento_ok);

     // Se non sono stati inseriti caratteri tipici della SQL Injection nel commento
     if ($commento_is_ok) {
          $execution = mysqli_query($conn, "INSERT INTO commenti (data_c, username, commento, id_post, stato) VALUES ('$data', '$username', '$commento', '$id_post', 1)") or die("Connessione fallita: " . mysqli_error($conn));
          if ($execution) echo "Aggiunto";
     } else echo "Dati corrotti";
}
mysqli_close($conn);
?>