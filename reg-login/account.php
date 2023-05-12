<?php
session_start();
require('../db_connect.php');

if ((!isset($_SESSION['user_session'])) && (!isset($_GET['username']))) header("Location: ../index.php?no_access");

if (isset($_GET['username'])) {
     $get_user = mysqli_query($conn, "SELECT username FROM users WHERE username = '$_GET[username]'") or die("Connessione fallita: " . mysqli_error($conn));
     if (mysqli_num_rows($get_user) == 0) header("Location: ../index.php?404");
}
?>

<!DOCTYPE html>
<html lang="it">

<head>
     <meta charset="utf-8">
     <meta name="viewport" content="width=device-width, initial-scale=1">
     <title>Bloggy | <?php
                         if (isset($_SESSION['user_session']) && !isset($_GET["username"])) echo $_SESSION['user_session'];
                         else echo $_GET["username"]; ?></title>
     <style>
          @import url("../index.css");
          @import url('https://fonts.googleapis.com/css2?family=Bungee&display=swap');
     </style>
     <link rel="icon" href="../image/logo_icona.png" />
     <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
     <script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.js"></script>
     <script src="../funzioni.js"></script>
     <script src="reg-login.js"></script>
     <script src="https://code.createjs.com/1.0.0/createjs.min.js"></script>
     <script src="../sfondo.js"></script>
     <script>
          $(function() {

               // Quando si clicca sull'avatar, viene ingrandito
               $('#avatar').click(function() {
                    $('#fullscreen').fadeIn();
                    var src = $(this).attr('src'); // Prendi gli attributi dell'avatar sul quale è stato passato il mouse
                    $('#fullscreen img').attr('src', src); // Assegnalo al tag dell'immagine ingrandita
               });
               $('#fullscreen img').click(function() {
                    $("#fullscreen").fadeOut();
               });

               // Rendi cliccabili i bottoni 'modifica utente' quando viene digitato del testo nei campi
               $(".edit_box").keyup(function() {
                    $("#modifica_btn, #reset").css({
                         "opacity": "1",
                         "pointer-events": "auto"
                    });
               });

               // Aggiungi la possibilità di cancellare il testo digitato
               $('.reg-login_box:not(#Rpassword, #Rcpassword, #Lpassword)').wrap('<span class="deleteicon" />').after($('<span>').click(function() {
                    $(this).prev('.reg-login_box').val('').trigger('change').focus();
               }));

               // Regex per il telefono
               $.validator.addMethod("tel_regex", function(value, element) {
                    return this.optional(element) || /^[0-9]{9,10}$/i.test(value);
               });

               // Gestire la convalida del form per modificare l'avatar
               $("#avatar_form").validate({
                    rules: {
                         U_avatar: "required"
                    },
                    messages: {
                         U_avatar: "<p class='messaggi'>Per favore inserisci un'avatar.</p>"
                    }
               });

               // Gestire la convalida del form della modifica dati user
               $("#edit_form").validate({
                    rules: {
                         Mnome: "required",
                         Mcognome: "required",
                         Mtelefono: {
                              tel_regex: true
                         },
                         Memail: "required",
                         Mpassword: {
                              required: true,
                              minlength: 8,
                              maxlength: 15
                         },
                         Mcpassword: {
                              required: true,
                              equalTo: '#Mpassword'
                         }
                    },
                    messages: {
                         Mnome: "<p class='messaggi'>Per favore inserisci il tuo nome.</p>",
                         Mcognome: "<p class='messaggi'>Per favore inserisci il tuo cognome.</p>",
                         Mtelefono: "<p class='messaggi'>Inserisci un numero di telefono valido.</p>",
                         Memail: "<p class='messaggi'>Per favore inserisci un indirizzo email valido.</p>",
                         Mpassword: {
                              required: "<p class='messaggi'>Per favore fornisci la password.</p>",
                              minlength: "<p class='messaggi'>La password deve avere minimo 8 caratteri.</p>",
                              maxlength: "<p class='messaggi'>La password non deve avere più di 15 caratteri.</p>"
                         },
                         Mcpassword: {
                              required: "<p class='messaggi'>Per favore reinserisci la password.</p>",
                              equalTo: "<p class='messaggi'>Le password non corrispondono!</p>"
                         }
                    },
                    submitHandler: submitForm_edit
               });

               // Gestire il modulo di invio della modifica dati user
               function submitForm_edit() {
                    $.ajax({
                         type: 'POST',
                         url: 'check_reg-login.php',
                         data: $("#edit_form").serialize(),
                         success: function(risposta) {

                              // Se si modifica l'account, reinderizza alla dashboard
                              if (risposta == "Modificato") {
                                   $("#error").fadeIn(400).html("Modifica effettuata! 😄");
                                   $("#modifica_btn").html('<img src="../image/ajax-loader.gif" alt="Loader" />&nbsp;Invio...');
                                   $('body').delay(1300).fadeOut();
                                   setTimeout('window.location.href = "dashboard.php"', 1600);
                              }

                              // Se la email è già stata utilizzata
                              else if (risposta == "Email già esistente") $("#error").fadeIn(400).html('<img src="../image/warning.svg" alt="Warning" width="20px" height="20px" >&nbsp;Spiacenti, email già utilizzata! 😯');

                              // Se sono stati inseriti caratteri tipici della SQL Injection
                              else if (risposta == "Dati corrotti") $("#error").fadeIn(400).html('<img src="../image/danger.svg" alt="Errore" width="20px" height="20px" >&nbsp;No dati corrotti su questo sito...');

                              // Se qualcosa è andato storto
                              else $("#error").fadeIn(400).html('<img src="../image/danger.svg" alt="Errore" width="20px" height="20px" >&nbsp;Qualcosa è andato storto! 😓');
                         }
                    });
                    return false;
               }

               $('.edit_box:not(#Mpassword, #Mcpassword)').wrap('<span class="deleteicon" />').after($('<span>').click(function() {
                    $(this).prev('.edit_box').val('').trigger('change').focus();
               }));

               // Quando si clicca su Password apre il box "Conferma password"
               $("#Mpassword").keyup(function() {
                    $("#conferma_pass").slideDown(400);
               });
          });
     </script>
</head>

<body id="account">
     <?php
     if (isset($_GET['disiscrizione']) && $_GET['disiscrizione'] == "error") {
          echo "<div class='avviso'>
                    <h1><img src='../image/warning.svg' alt='Cerca' width='25px' height='25px' >&nbsp;ATTENZIONE!</h1>
                    <p>Prima di poterti disiscrivere devi eliminare i tuoi <a href='dashboard.php'>blog</a> dal sito!</p>
                    <script>setTimeout(\"window.location.href = 'account.php'\", 2500);</script>
               </div>";
     } ?>

     <!-- Sfondo sito -->
     <div id="animation_container">
          <canvas id="canvas"></canvas>
          <div id="dom_overlay_container"></div>
     </div>

     <header>
          <!-- Logo sito -->
          <a href="../index.php">
               <img src="../image/logo.png" id="logo" alt="Logo" />
          </a>

          <!-- Cerca nel sito -->
          <form action="../blog.php" method="get" id="cerca" name="cerca" title="Cerca nel sito...">
               <input type="search" id="testoCerca" name="testoCerca" placeholder="Cerca..." />
               <button type="submit" id="cercaBtn">
                    <img src="../image/search_icon.svg" alt="Cerca">
               </button>
               <div id="autocompletamento"></div>
          </form>

          <?php
          // Se non si è loggati al sito mostra l'opzione login/registrazione
          if (!isset($_SESSION['user_session'])) { ?>
               <a href="reg-login.php?login" class="dx" title="Effettua il login">Login</a>
               <span id="divisore" class="dx">|</span>
               <a href="reg-login.php?registrazione" class="dx" title="Registrati">Registrati</a>

          <?php
               // Se si è loggati, mostra l'avatar, l'username e il dropdown con le varie opzioni
          } else { ?>
               <div id="utente">
                    <span id='dropdown_btn'>▾</span>
                    <ul id="subnav">
                         <?php

                         // Verifica se l'utente è un admin
                         $username = $_SESSION['user_session'];
                         $query = "SELECT admin FROM users WHERE username = '$username' AND admin = 1";
                         $execution = mysqli_query($conn, $query) or die("Connessione fallita: " . mysqli_error($conn));
                         if (mysqli_num_rows($execution) > 0) {
                              while (mysqli_fetch_array($execution)) { ?>

                                   <!-- Se l'utente È un admin mostra tutti gli elementi: -->
                                   <li id="go_to_dashboard">Dashboard</li>
                                   <li id="add_blog">Aggiungi blog</li>
                                   <li id="manage_comments">Gestisci commenti</li>
                                   <li id="manage_users">Gestisci utenti</li>
                                   <li id="manage_messaggi">Gestisci messaggi</li>
                                   <li id="logout">
                                        <img src="../image/login.png" class="button_icon icona statica reg-login" style="margin-top:2.5px" alt="Logout">
                                        <img src="../image/login.gif" class="button_icon icona attiva reg-login" alt="Logout">&nbsp;Logout
                                   </li>
                              <?php
                              }
                         } else { ?>

                              <!-- Se l'utente NON è un admin mostra questi elementi: -->
                              <li id="go_to_dashboard">Dashboard</li>
                              <li id="add_blog">Aggiungi blog</li>
                              <li id="logout">
                                   <img src="../image/login.png" class="button_icon icona statica reg-login" style="margin-top:2.5px" alt="Logout">
                                   <img src="../image/login.gif" class="button_icon icona attiva reg-login" alt="Logout">&nbsp;Logout
                              </li>
                         <?php
                         }
                         ?>
                    </ul>

                    <a href="account.php">
                         <?php
                         // Avatar
                         $username = $_SESSION['user_session'];
                         $avatar_query = "SELECT avatar FROM users WHERE username = '$username'";
                         $avatar = mysqli_query($conn, $avatar_query) or die("Connessione fallita: " . mysqli_error($conn));
                         if (mysqli_num_rows($avatar) > 0) {
                              $row = mysqli_fetch_array($avatar);
                              $imageURL = '../image/' . $row["avatar"];
                              echo "<img src = '$imageURL' id='user_icon' alt='Avatar'/>";
                         } ?>
                         <p class="dx" id="user_name"><?php echo ucfirst($_SESSION['user_session']) ?></p>
                    </a>
               </div>
          <?php
          } ?>
          <!-- Sezione about -->
          <a href="../about.php" class="dx" title="About">About</a>
     </header>

     <nav id="nav_1"></nav>
     <nav id="nav_2"></nav>

     <main>
          <h1>I TUOI DATI:</h1>
          <h3 id="descrizione">Visualizza e modifica i tuoi dati!</h3>

          <div class="visualizza_dati_utente">
               <?php

               // Se non viene scelto di modificare l'account
               if (!isset($_GET['modifica'])) {

                    // Se si cerca un altro account
                    if (isset($_GET['username'])) $username = $_GET['username'];

                    // Se non si cerca un altro account, visualizza i dati dell'account attivo nella sessione
                    else $username = $_SESSION['user_session'];

                    $user_query = mysqli_query($conn, "SELECT *, DATE_FORMAT(add_data, '- membro dal %d %M %Y') AS niceDate FROM users WHERE username = '$username'") or die("Connessione fallita: " . mysqli_error($conn));
                    $row = mysqli_fetch_array($user_query);
                    $imageURL_selected = '../image/' . $row['avatar'];

                    // Mostra l'avatar
                    echo "<img src = '$imageURL_selected' id='avatar' alt='Avatar'/>
                              <div id='fullscreen'>
                                   <img src='' alt='Avatar'/>
                              </div>";

                    // Nome e cognome
                    echo '<h3>' . $row['nome'] . ' ' . $row['cognome'] . '</h3><br>';

                    // Admin e iscritto dal
                    if ($row['admin'] == 1) echo '<p>Utente amministratore ';
                    else echo '<p>Utente ';
                    echo $row['niceDate'] . ' </p><br>';

                    // Email
                    echo '<p>Email: <a href="mailto:' . $row['email'] . '" style="cursor:pointer">' . $row['email'] . '</a></p>';

                    // Telefono
                    if (empty($row['telefono'])) echo '<p>Telefono: non fornito</p>';
                    else echo '<p>Telefono: <a href="tel:' . $row['telefono'] . '">' . $row['telefono'] . '</a></p>';

                    // Se non viene cercato nessun account che non sia quello attivo nella sessione
                    if (isset($_SESSION['user_session']) && $row['username'] == $_SESSION['user_session']) { ?>
                         <a href="account.php?modifica">
                              <button type="submit" class="button_form" id="edit_profile" name="edit_profile">
                                   <img src="../image/user.png" class="button_icon" alt="Icona modifica account">&ensp;Modifica i dati
                              </button>
                         </a>
                         <?php

                         // Verifica che, se si vuole eliminare l'account, non si abbiano blog o post
                         $autore_query = mysqli_query($conn, "SELECT * FROM blog AS b, post AS p WHERE b.autore = '$_SESSION[user_session]' OR b.co_autore = '$_SESSION[user_session]' OR p.autore = '$_SESSION[user_session]'") or die("Connessione fallita: " . mysqli_error($conn));
                         if (mysqli_num_rows($autore_query) > 0) {
                              $autore_row = mysqli_fetch_array($autore_query);
                         ?>
                              <a href="account.php?disiscrizione=error">
                                   <button type="submit" class="button_form" id="disiscrizione">
                                        <img src="../image/delete_user.png" class="button_icon" alt="Disiscriviti">&nbsp;Disiscriviti
                                   </button>
                              </a>
                         <?php
                         } else { ?>
                              <a href="../index.php?disiscrizione=eliminato">
                                   <button type="submit" class="button_form" id="disiscrizione">
                                        <img src="../image/delete_user.png" class="button_icon" alt="Disiscriviti">&nbsp;Disiscriviti
                                   </button>
                              </a>
                    <?php
                         }
                    }
               } else { ?>
                    <!-- Se si sceglie di modificare l'account -->

                    <form id="avatar_form" method="post" enctype="multipart/form-data">
                         <!-- Se si sceglie di modificare l'avatar -->
                         <?php
                         // Mostra l'avatar
                         echo "<img src = '$imageURL' id='avatar' alt='Avatar'/>
                                   <div id='fullscreen'>
                                        <img src='' alt='Avatar'/>
                                   </div>";
                         ?>

                         <input type='file' name='U_avatar' id='U_avatar' title="Scegli nuovo avatar" onchange="document.getElementById('avatar').src = window.URL.createObjectURL(this.files[0])"></input>
                         <p class='messaggi'>
                              <?php
                              if (isset($_POST['modifica_avatar'])) {
                                   $username = $_SESSION['user_session'];
                                   $targetDir = "../image/";
                                   $fileName = basename($_FILES["U_avatar"]["name"]);
                                   $file_target =  $targetDir . $fileName;
                                   $fileType = strtolower(pathinfo($file_target, PATHINFO_EXTENSION));
                                   $allowTypes = array('jpg', 'png', 'jpeg', 'gif');

                                   // Verifica se l'immagine rispetta i criteri di estensione scelti
                                   if (in_array($fileType, $allowTypes)) {

                                        // Verifica che l'immagine non sia più grossa di 1 MB
                                        if ($_FILES["U_avatar"]["size"] < 100000) {

                                             // Se è tutto apposto, inserisci il nome del file nel database
                                             if (move_uploaded_file($_FILES["U_avatar"]["tmp_name"], $file_target)) {
                                                  $update = "UPDATE users SET avatar = '$fileName' WHERE username = '$username'";
                                                  mysqli_query($conn, $update) or die("Connessione fallita: " . mysqli_error($conn));
                                                  echo "L'avatar " . $fileName . " è stato caricato con successo! 📁😁.";
                                                  echo "<script>
                                                            $('#avatar_form').delay(1775).fadeOut();
                                                            setTimeout(function(){location.href='account.php'} , 2000);
                                                       </script>";
                                             } else echo "Caricamento dell'avatar fallito, Per favore riprova 🤞";
                                        } else echo "Spiacenti, il tuo avatar supera 1MB.";
                                   } else echo 'Spiacenti, sono supportate solo le immagini di tipo JPG, JPEG, PNG, GIF. 📸';
                              }
                              ?>
                         </p>

                         <button type="submit" class="button_form" id="modifica_avatar" name="modifica_avatar">
                              <img src="../image/user.png" class="button_icon" alt="Icona modifica account">&ensp;Cambia avatar
                         </button>
                    </form>

                    <!-- Se si sceglie di modificare i dati dell'account -->
                    <form id='edit_form'>
                         <?php
                         // Visualizza i dati dell'account attivo nella sessione da modificare
                         $username = $_SESSION['user_session'];
                         $user_query = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'") or die("Connessione fallita: " . mysqli_error($conn));
                         $row = mysqli_fetch_array($user_query);
                         // Nome
                         echo '<label for="Mnome">Nome:</label>
                                   <input type="text" class="edit_box" name="Mnome" value="' . $row['nome'] . '"></input><br>';

                         // Cognome
                         echo '<label for="Mcognome">Cognome:</label>
                                   <input type="text" class="edit_box" name="Mcognome" value="' . $row['cognome'] . '"></input><br>';

                         // Telefono
                         echo '<label for="Mtelefono">Telefono:</label>
                                   <input type="tel" class="edit_box" name="Mtelefono" value="' . $row['telefono'] . '"></input><br>';

                         // Email
                         echo '<label for="Memail">Email:</label>
                                   <input type="email" class="edit_box" name="Memail" value="' . $row['email'] . '"></input><br>
                                   <input type="hidden" value="' . $row["email"] . '" name="email_vecchia"/>';

                         // Password
                         echo '<label for="Mpassword">Password:</label>
                                   <input type="password" class="edit_box" name="Mpassword" id="Mpassword" value=""></input><br>';

                         // Conferma password
                         echo '<div id="conferma_pass" style="display:none">
                                   <label for="Mcpassword" id="conferma">Conferma password:</label>
                                   <input type="password" class="edit_box" name="Mcpassword" id="Mcpassword" placeholder="Reinserisci password"></input>
                              </div>';
                         ?>

                         <div id="error"></div><br>

                         <button type="submit" class="button_form" id="modifica_btn" name="modifica_btn" style="opacity:.7; pointer-events: none;">
                              <img src="../image/user.png" class="button_icon" alt="Icona modifica account">&nbsp;Cambia
                         </button>

                         <a href="account.php" class="button_form" style="padding: 8px 12px">
                              <img src="../image/meno.png" class="button_icon" alt="Annulla" style="vertical-align: middle">Annulla
                         </a>
                    </form>
               <?php
               } ?>
          </div>

          <h2>BLOG DELL'UTENTE</h2>
          <?php
          if (!isset($_GET['username'])) $user_username = $username;
          else $user_username = $_GET['username'];
          $load_post = mysqli_query($conn, "SELECT *, DATE_FORMAT(creato_il, '%W %d %M %Y, %H:%i') AS niceDate FROM blog WHERE autore = '$user_username' OR co_autore = '$user_username' ORDER BY nome_blog ASC") or die("Connessione fallita: " . mysqli_error($conn));
          if (mysqli_num_rows($load_post) > 0) {
               while ($result = mysqli_fetch_assoc($load_post)) {
                    $blog_immagine = $result['immagine'];
                    $blog_nome = $result['nome_blog'];
                    $blog_autore = $result['autore'];
                    $blog_co_autore = $result['co_autore'];
                    $creato_il = $result['niceDate'];

                    // Stampa i blog
                    echo "<div class='blog' id='blog_$blog_nome'>
                              <a href='../blog.php?testoCerca=$blog_nome'>
                                   <img src='../image/$blog_immagine' alt='Immagine blog'>
                              </a>
                              <h1>
                                   <a href='../blog.php?testoCerca=$blog_nome' style='color:rgb(99, 10, 13)'>$blog_nome</a>
                              </h1>";

                    // Se esiste un co-autore
                    if ($blog_co_autore != null) {
                         echo "<p>
                                   <a href='account.php?username=$blog_autore' style='color:rgb(99, 10, 13)'>$blog_autore</a> e 
                                   <a href='account.php?username=$blog_co_autore' style='color:rgb(99, 10, 13)'>$blog_co_autore</a>  - " . ucfirst($creato_il) . "
                              </p>";
                         if (!isset($_GET['username'])) {
                              echo "<a href='new_blog.php?aggiungi_post=$blog_nome'>
                                        <button type='submit' class='button_form add_post' style='padding:6px 8px'>
                                             <img src='../image/add.png' class='icona add_post' alt='Icona modifica account'>&nbsp;Aggiungi post
                                        </button>
                                        </a>";
                         }
                    } else {
                         echo "<p>
                                   <a href='account.php?username=$blog_autore' style='color:rgb(99, 10, 13)'>$blog_autore</a> - " . ucfirst($creato_il) . "
                              </p>";
                         if (!isset($_GET['username'])) {
                              echo "<a href='new_blog.php?aggiungi_post=$blog_nome'>
                                        <button type='submit' class='button_form add_post' style='padding:6px 8px'>
                                             <img src='../image/add.png' class='icona add_post' alt='Icona modifica account'>&nbsp;Aggiungi post
                                        </button>
                                   </a>";
                         }
                    }
                    echo "</div>";
               }
          } else {
               // Se si accede al proprio profilo
               if (!isset($_GET['username'])) {
                    echo "<h3 style='background:rgb(255,255,255,.7); width:18%; margin-left:2%; padding-left: 1%; border-radius:20px'>Nessun blog. <a href='new_blog.php'>Aggiungine uno!</a></h3>";
               }
               // Se viene cercato un utente
               else echo "<h3 style='background:rgb(255,255,255,.7); width:18%; margin-left:2%; padding-left: 1%; border-radius:20px'>Questo utente non ha nessun blog.</h3>";
          } ?>

          <aside>
               <!-- Blog -->
               <div id="blog">
                    <h4><a href="blog.php">Blog</a></h4>
                    <ul>
                         <?php
                         $find_blog = mysqli_query($conn, "SELECT nome_blog FROM blog ORDER BY creato_il DESC LIMIT 10") or die("Connessione fallita: " . mysqli_error($conn));
                         while ($nome_blog = mysqli_fetch_assoc($find_blog)) {
                              echo "<li>
                                        <a href='../blog.php?testoCerca=$nome_blog[nome_blog]'>$nome_blog[nome_blog]</a>
                                   </li>";
                         } ?>
                    </ul>
               </div>

               <!-- Categorie -->
               <div id="categorie">
                    <h4>Categorie</h4>
                    <ul>
                         <?php
                         $find_cat = mysqli_query($conn, "SELECT DISTINCT categoria FROM post LIMIT 10") or die("Connessione fallita: " . mysqli_error($conn));
                         while ($categoria = mysqli_fetch_assoc($find_cat)) {
                              echo "<li>
                                        <a href='../blog.php?testoCerca=$categoria[categoria]'>$categoria[categoria]</a>
                                   </li>";
                         }
                         ?>
                    </ul>
               </div>

               <!-- Post recenti -->
               <div id="post_recenti">
                    <h4>Post recenti</h4>
                    <ul>
                         <?php
                         $find_post = mysqli_query($conn, "SELECT id, titolo FROM post ORDER BY creato_il DESC LIMIT 10") or die("Connessione fallita: " . mysqli_error($conn));
                         while ($post = mysqli_fetch_assoc($find_post)) {
                              echo "<li>
                                        <a href='../single.php?id=$post[id]'>$post[titolo]</a>
                                   </li>";
                         } ?>
                    </ul>
               </div>

               <!--  Utenti registrati al sito -->
               <div id="users_in_site">
                    <h4>Utenti registrati</h4>
                    <ul>
                         <?php
                         // Cerca gli utenti registrati nel database
                         $execution = mysqli_query($conn, "SELECT username, avatar FROM users ORDER BY add_data ASC LIMIT 5") or die("Connessione fallita: " . mysqli_error($conn));
                         while ($utenti_registrati = mysqli_fetch_assoc($execution)) {
                              $username = $utenti_registrati['username'];
                              $imageURL = '../image/' . $utenti_registrati["avatar"];

                              // Conta quanti blog hanno gli utenti registrati
                              $count_blog = mysqli_query($conn, "SELECT COUNT(*) AS count_blog FROM blog WHERE autore = '$username' OR co_autore = '$username'") or die("Connessione fallita: " . mysqli_error($conn));
                              $count = mysqli_fetch_array($count_blog)['count_blog'] . " blog";
                              if ($count == "0 blog") $count = "Nessun blog";

                              if (isset($_SESSION['user_session']) && $username == $_SESSION['user_session']) {
                                   echo "<li id='per_utenti'>
                                             <a href='reg-login/account.php'>
                                                  <img src = '$imageURL' id='users_in_site_icon' alt='Avatar'/>$username
                                             </a>- $count
                                         </li>";
                              } else {
                                   // Stampa risultato
                                   echo "<li id='per_utenti'>
                                             <a href='reg-login/account.php?username=$username'>
                                                  <img src = '$imageURL' id='users_in_site_icon' alt='Avatar'/>$username
                                             </a>- $count
                                        </li>";
                              }
                         }
                         mysqli_close($conn);
                         ?>
                    </ul>
               </div>
          </aside>
     </main>

     <img src="../image/button_top.svg" id="button_top" alt="Vai all'inizio della pagina">

     <img src="../image/footer.svg" alt="Footer">
     <footer>
          <a href="../about.php">All rights reserved | © 2021 | Created by Marco Petrucci</a>
     </footer>
</body>

</html>