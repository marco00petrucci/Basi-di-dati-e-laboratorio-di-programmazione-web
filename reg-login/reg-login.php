<?php
session_start();

if (isset($_SESSION['user_session'])) header("Location: ../index.php?no_access");

if (isset($_GET['registrazione'])) $nome_pagina = "Registrazione";
if (isset($_GET['login'])) $nome_pagina = "Login";
?>

<!DOCTYPE html>
<html lang="it">

<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1">
     <title>Bloggy | <?php echo $nome_pagina; ?></title>
     <style>
          @import url("../style.css");
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

               /* REGISTRAZIONE */

               // Regex per il telefono
               $.validator.addMethod("tel_regex", function(value, element) {
                    return this.optional(element) || /^[0-9]{9,10}$/i.test(value);
               });

               // Gestire la convalida del form della registrazione
               $("#register-form").validate({
                    rules: {
                         Rnome: "required",
                         Rcognome: "required",
                         Rusername: {
                              required: true,
                              minlength: 5
                         },
                         Rtelefono: {
                              tel_regex: true
                         },
                         Remail: {
                              required: true,
                              email: true
                         },
                         Rpassword: {
                              required: true,
                              minlength: 8,
                              maxlength: 15
                         },
                         Rcpassword: {
                              required: true,
                              equalTo: '#Rpassword'
                         }
                    },
                    messages: {
                         Rnome: "<p class='messaggi'>Per favore inserisci il tuo nome.</p>",
                         Rcognome: "<p class='messaggi'>Per favore inserisci il tuo cognome.</p>",
                         Rusername: {
                              required: "<p class='messaggi'>Per favore inserisci l'username.</p>",
                              minlength: "<p class='messaggi'>L'username deve avere minimo 5 caratteri.</p>"
                         },
                         Rtelefono: "<p class='messaggi'>Inserisci un numero di telefono valido.</p>",
                         Remail: "<p class='messaggi'>Per favore inserisci un indirizzo email valido.</p>",
                         Rpassword: {
                              required: "<p class='messaggi'>Per favore fornisci la password.</p>",
                              minlength: "<p class='messaggi'>La password deve avere minimo 8 caratteri.</p>",
                              maxlength: "<p class='messaggi'>La password non deve avere più di 15 caratteri.</p>"
                         },
                         Rcpassword: {
                              required: "<p class='messaggi'>Per favore reinserisci la password.</p>",
                              equalTo: "<p class='messaggi'>Le password non corrispondono!</p>"
                         }
                    },
                    submitHandler: submitForm_register
               });

               // Gestire il modulo di invio della registrazione
               function submitForm_register() {
                    $.ajax({
                         type: 'POST',
                         url: 'check_reg-login.php',
                         data: $("#register-form").serialize(),
                         success: function(risposta) {

                              // Se si crea l'account, reinderizza dashboard
                              if (risposta == "Registrato") {
                                   $("#error").fadeIn(400);
                                   $("#error").html("Benvenuto! Registrazione effettuata 😄");
                                   $("#reg_btn").html('<img src="../image/ajax-loader.gif" alt="Loader" />&nbsp;Signing In...');
                                   $('#register-form').delay(1300).fadeOut();
                                   setTimeout('window.location.href = "dashboard.php"', 1600);
                              }

                              // Se non sono stati inseriti caratteri tipici della SQL Injection
                              else if (risposta == "Dati corrotti") {
                                   $("#error").fadeIn(400);
                                   $("#error").html('<img src="../image/danger.svg" alt="Errore" width="20px" height="20px" >&nbsp;No dati corrotti su questo sito... 👨🏼‍💻');
                              }

                              // Se l'username è già stato utilizzato
                              else if (risposta == "Username già esistente") {
                                   $("#error").fadeIn(400);
                                   $("#error").html('<img src="../image/warning.svg" alt="Warning" width="20px" height="20px" >&nbsp;Spiacenti, username già utilizzato! 😖<a href="login.php">Login!</a>');
                              }

                              // Se la email è già stata utilizzata
                              else if (risposta == "Email già esistente") {
                                   $("#error").fadeIn(400);
                                   $("#error").html('<img src="../image/warning.svg" alt="Warning" width="20px" height="20px" >&nbsp;Spiacenti, email già utilizzata! 😯');
                              }

                              // Se qualcosa è andato storto
                              else {
                                   $("#error").fadeIn(400);
                                   $("#error").html('<img src="../image/danger.svg" alt="Errore" width="20px" height="20px" >&nbsp;Qualcosa è andato storto! 😓');
                              }
                         }
                    });
                    return false;
               }

               // Quando si clicca su Password apre il box "Conferma password"
               $("#Rpassword").keyup(function() {
                    $("#conferma_pass").slideDown(400);
               });


               /* LOGIN */


               // Gestire la convalida del form del login
               $("#login-form").validate({
                    rules: {
                         Lusername: "required",
                         Lpassword: "required",
                    },
                    messages: {
                         Lusername: "<p class='messaggi'>Per favore inserisci l'username.</p>",
                         Lpassword: "<p class='messaggi'>Per favore fornisci la password.</p>"
                    },
                    submitHandler: submitForm_login
               });

               // Gestire il modulo di invio del login
               function submitForm_login() {
                    $.ajax({
                         type: 'POST',
                         url: 'check_reg-login.php',
                         data: $("#login-form").serialize(),
                         success: function(risposta) {

                              // Se il login viene effettuato reinderizza alla dashboard
                              if (risposta == "Login effettuato") {
                                   $("#error").fadeIn(400);
                                   $("#error").html("Bentornato! Login effettuato! 😄");
                                   $("#login_btn").html('<img src="../image/ajax-loader.gif" alt="Loader" />&nbsp;Signing In...');
                                   $('#login-form').delay(1300).fadeOut();
                                   setTimeout('window.location.href = "dashboard.php"', 1600);
                              }

                              // Se non esiste un username come quello digitato
                              else if (risposta == "Username non trovato") {
                                   $("#error").fadeIn(400);
                                   $("#error").html('<img src="../image/warning.svg" alt="Cerca" width="20px" height="20px" >&nbsp;Username non trovato. <a href="reg-login.php?registrazione">Registrati!</a>');
                              }

                              // Se la password è errata
                              else if (risposta == "Password errata") {
                                   $("#error").fadeIn(400);
                                   $("#error").html('<img src="../image/warning.svg" alt="Cerca" width="20px" height="20px" >&nbsp;Password errata.');
                              }

                              // Se qualcosa è andato storto
                              else {
                                   $("#error").fadeIn(400);
                                   $("#error").html('<img src="../image/warning.svg" alt="Cerca" width="20px" height="20px" >&nbsp;Username e/o password errati. <a href="reg-login.php?registrazione">Registrati!</a>');
                              }
                         }
                    });
                    return false;
               }

               $('.reg-login_box:not(#Rpassword, #Rcpassword, #Lpassword)').wrap('<span class="deleteicon" />').after($('<span>').click(function() {
                    $(this).prev('.reg-login_box').val('').trigger('change').focus();
               }));

               $(window).scroll(function() {
                    // Quando la pagina viene scrollata in *SU* a meno di 50px setta di bianco il colore del logo
                    if ($(document).scrollTop() < 50) {
                         $('#logo').attr("src", "../image/logo.png");
                         $("#logo").hover(function() {
                              $("#logo").attr("src", "../image/logo_nero.png");
                         }, function() {
                              $("#logo").attr("src", "../image/logo.png");
                         });
                    }
                    // Quando la pagina viene scrollata in *GIÙ* di 50px setta di nero il colore del logo
                    else {
                         $('#logo').attr("src", "../image/logo_nero.png");
                         $("#logo").hover(function() {
                              $("#logo").attr("src", "../image/logo_nero.png");
                         }, function() {
                              $("#logo").attr("src", "../image/logo_nero.png");
                         });
                    }
               });
          });
     </script>
</head>

<body>
     <!-- Sfondo sito -->
     <div id="animation_container">
          <canvas id="canvas"></canvas>
          <div id="dom_overlay_container"></div>
     </div>

     <header>
          <input type="checkbox" id="menu-btn" />
          <label id="menu-icon" for="menu-btn">
               <span id="nav-icon"></span>
          </label>

          <!-- Logo sito -->
          <a href="../index.php">
               <img src="../image/logo.png" id="logo" alt="Logo" />
          </a>

          <nav id="menu">
               <!-- Cerca nel sito -->
               <form action="../blog.php" method="get" id="cerca" name="cerca" title="Cerca nel sito...">
                    <input type="search" id="testoCerca" name="testoCerca" placeholder="Cerca..." />
                    <button type="submit" id="cercaBtn">
                         <img src="../image/search_icon.svg" alt="Cerca">
                    </button>
                    <div id="autocompletamento"></div>
               </form>

               <?php
               echo "<style>@media screen and (max-width:950px) {#menu>a {margin: 0 0 4px}}</style>"; ?>
               <a href="reg-login.php?login" class="dx" title="Effettua il login">Login</a>
               <span id="divisore" class="dx">|</span>
               <a href="reg-login.php?registrazione" class="dx" title="Registrati">Registrati</a>

               <!-- Sezione about -->
               <a href="../about.php" class="dx" title="About">About</a>
          </nav>
     </header>

     <nav id="nav_1"></nav>
     <nav id="nav_2"></nav>

     <main>
          <?php
          if (isset($_GET['registrazione'])) : ?>
               <h1>Registrati</h1>
               <h3 id="descrizione">Registrati al sito!</h3>
               <form class="form_center" id="register-form" method="post" action="check_reg-login.php">
                    <label for="nome">Nome:</label>
                    <input type="text" class="reg-login_box" placeholder="Nome" name="Rnome" id="Rnome" /><br>

                    <label for="cognome">Cognome:</label>
                    <input type="text" class="reg-login_box" placeholder="Cognome" name="Rcognome" id="Rcognome" /><br>

                    <label for="username">Username:</label>
                    <input type="text" class="reg-login_box" placeholder="Username" name="Rusername" id="Rusername" /><br>

                    <label for="email">Email:</label>
                    <input type="email" class="reg-login_box" placeholder="Indirizzo email" name="Remail" id="Remail" /><br>

                    <label for="password">Password:</label>
                    <input type="password" class="reg-login_box" placeholder="Password" name="Rpassword" id="Rpassword" /><br>

                    <div id="conferma_pass" style="display: none;">
                         <label for="cpassword">Reinserisci password:</label>
                         <input type="password" class="reg-login_box" placeholder="Reinserisci password" name="Rcpassword" id="Rcpassword" />
                    </div>

                    <div id="error"></div>

                    <button type="submit" class="button_form" id="reg_btn" name="reg_btn">
                         <img src="../image/login.png" class="button_icon icona statica reg-login" alt="Registrazione">
                         <img src="../image/login.gif" class="button_icon icona attiva reg-login" alt="Registrazione" style="background:none">&nbsp;Registrati
                    </button>
                    <p style="padding-bottom: 2%; margin:0">Hai già un account? Effettua il <a href="reg-login.php?login">login</a>!</p>
               </form>
          <?php
          endif;

          if (isset($_GET['login'])) : ?>
               <h1>Login</h1>
               <h3 id="descrizione">Entra nel tuo profilo!</h3>

               <form class="form_center" id="login-form" method="post" action="check_reg-login.php">

                    <input type="text" class="reg-login_box" id="Lusername" name="Lusername" placeholder="Username" /><br>

                    <input type="password" class="reg-login_box" id="Lpassword" name="Lpassword" placeholder="Password" /><br>

                    <div id="error"></div>

                    <button type="submit" class="button_form" id="login_btn" name="login_btn">
                         <img src="../image/login.png" class="button_icon icona statica reg-login" alt="Login">
                         <img src="../image/login.gif" class="button_icon icona attiva reg-login" alt="Login" style="background:none">&nbsp;Login
                    </button>
                    <p style="padding-bottom: 2%; margin:0">Non hai un account? <a href="reg-login.php?registrazione">Registrati!</a></p>
               </form>
          <?php
          endif; ?>
     </main>

     <img src="../image/footer.svg" alt="Footer">
     <footer>
          <a href="../about.php">All rights reserved | © 2021 | Created by Marco Petrucci</a>
     </footer>
</body>

</html>