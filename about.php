<!doctype html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bloggy | About</title>
    <style>
        @import url("index.css");
        @import url('https://fonts.googleapis.com/css2?family=Bungee&display=swap');
    </style>
    <link rel="icon" href="image/logo_icona.png" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.js"></script>
    <script src="funzioni.js"></script>
    <script src="https://code.createjs.com/1.0.0/createjs.min.js"></script>
    <script src="sfondo.js"></script>
</head>

<body>
    <!-- Sfondo sito -->
    <div id="animation_container">
        <canvas id="canvas"></canvas>
        <div id="dom_overlay_container"></div>
    </div>

    <header>
        <!-- Logo sito -->
        <a href="index.php">
            <img src="image/logo.png" id="logo" alt="Logo" />
        </a>

        <!-- Cerca nel sito -->
        <form action="blog.php" method="get" id="cerca" name="cerca" title="Cerca nel sito...">
            <input type="search" id="testoCerca" name="testoCerca" placeholder="Cerca..." />
            <button type="submit" id="cercaBtn">
                <img src="image/search_icon.svg" alt="Cerca">
            </button>
            <div id="autocompletamento"></div>
        </form>

        <?php
        session_start();
        include "db_connect.php";

        // Se non si è loggati al sito mostra l'opzione login/registrazione
        if (!isset($_SESSION['user_session'])) { ?>
            <a href="reg-login/reg-login.php?login" class="dx" title="Effettua il login">Login</a>
            <span id="divisore" class="dx">|</span>
            <a href="reg-login/reg-login.php?registrazione" class="dx" title="Registrati">Registrati</a>

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
                                <img src="image/login.png" class="button_icon icona statica reg-login" style="margin-top:2.5px" alt="Logout">
                                <img src="image/login.gif" class="button_icon icona attiva reg-login" alt="Logout">&nbsp;Logout
                            </li>
                        <?php
                        }
                    } else { ?>

                        <!-- Se l'utente NON è un admin mostra questi elementi: -->
                        <li id="go_to_dashboard">Dashboard</li>
                        <li id="add_blog">Aggiungi blog</li>
                        <li id="logout">
                            <img src="image/login.png" class="button_icon icona statica reg-login" style="margin-top:2.5px" alt="Logout">
                            <img src="image/login.gif" class="button_icon icona attiva reg-login" alt="Logout">&nbsp;Logout
                        </li>
                    <?php
                    }
                    ?>
                </ul>

                <a href="reg-login/account.php">
                    <?php
                    // Avatar
                    $username = $_SESSION['user_session'];
                    $avatar_query = "SELECT avatar FROM users WHERE username = '$username'";
                    $avatar = mysqli_query($conn, $avatar_query) or die("Connessione fallita: " . mysqli_error($conn));
                    if (mysqli_num_rows($avatar) > 0) {
                        $row = mysqli_fetch_array($avatar);
                        $imageURL = 'image/' . $row["avatar"];
                        echo "<img src = '$imageURL' id='user_icon' alt='Avatar'/>";
                    } ?>
                    <p class="dx" id="user_name"><?php echo ucfirst($_SESSION['user_session']) ?></p>
                </a>
            </div>
        <?php
        } ?>
        <!-- Sezione about -->
        <a href="about.php" class="dx" title="About">About</a>
    </header>

    <nav id="nav_1"></nav>
    <nav id="nav_2"></nav>

    <main>
        <h1>CONTATTACI!</h1>
        <h3 id="descrizione">Inviaci un messaggio!</h3>

        <form class="form_center" id="contatta-form" name="contatta-form" method="post" action="reg-login/check_reg-login.php">
            <?php
            // Se non c'è nessun utente loggato
            if (!isset($_SESSION['user_session'])) {
                echo "<input type='text' class='input_box' name='Anome' id='Anome' placeholder='Nome completo' style='width:52%'><br>              
                      <input type='email' class='input_box' name='Aemail' id='Aemail' placeholder='Email' style='width:52%'><br>
                      <input type='tel' class='input_box' name='Atelefono' id='Atelefono' placeholder='Numero di telefono' style='width:52%'><br>";
            }

            // Se l'utente è loggato imposta i valori nel form dal database
            else {
                $query = "SELECT nome, cognome, email, telefono FROM users WHERE username = '$_SESSION[user_session]'";
                $execution = mysqli_query($conn, $query) or die("Connessione fallita: " . mysqli_error($conn));
                $riga = mysqli_fetch_assoc($execution);
                $nome = $riga['nome'] . " ". $riga['cognome'];
                $email = $riga['email'];
                $telefono = $riga['telefono'];
                echo "<input type='text' class='input_box' name='Anome' id='Anome' placeholder='Nome completo' value='$nome' style='width:52%'><br>
                      <input type='email' class='input_box' name='Aemail' id='Aemail' placeholder='Email' value='$email' style='width:52%'><br>
                      <input type='tel' class='input_box' name='Atelefono' id='Atelefono' placeholder='Numero di telefono' value='$telefono' style='width:52%'><br>";
            }
            ?>

            <textarea class="messaggio_box" name="messaggio" placeholder="Inserisci messaggio" cols="45" rows="9"></textarea><br>

            <div id="error"></div>

            <button type="submit" class="button_form" id="cont_invia" name="cont_invia">
                <img src="image/login.svg" class="button_icon" alt="Invia messaggio">&nbsp;Invia
            </button>

        </form>

        <aside>
            <!-- Blog -->
            <div id="blog">
                <h4><a href="blog.php">Blog</a></h4>
                <ul>
                    <?php
                    $sql = "SELECT * FROM blog ORDER BY creato_il DESC LIMIT 10";
                    $execution = mysqli_query($conn, $sql) or die("Connessione fallita: " . mysqli_error($conn));
                    while ($recent = mysqli_fetch_assoc($execution)) {
                        $blog = $recent['nome_blog'];
                        echo "<li>
                                <a href='blog.php?testoCerca=$blog'>$blog</a>
                              </li>";
                    }
                    ?>
                </ul>
            </div>

            <!-- Categorie -->
            <div id="categorie">
                <h4>Categorie</h4>
                <ul>
                    <?php
                    $sql = "SELECT DISTINCT categoria FROM post";
					$execution = mysqli_query($conn, $sql) or die("Connessione fallita: " . mysqli_error($conn));
					while ($categoria = mysqli_fetch_assoc($execution)) {
						echo "<li>
                                <a href='blog.php?testoCerca=$categoria[categoria]'>$categoria[categoria]</a>
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
                    $sql = "SELECT * FROM post ORDER BY creato_il DESC LIMIT 10";
                    $execution = mysqli_query($conn, $sql) or die("Connessione fallita: " . mysqli_error($conn));
                    while ($recent = mysqli_fetch_assoc($execution)) {
                        $id = $recent['id'];
                        echo "<li>
                                <a href='single.php?id=$id'>$recent[titolo]</a>
                              </li>";
                    }
                    ?>
                </ul>
            </div>

            <!--  Utenti registrati al sito -->
            <div id="users_in_site">
                <h4>Utenti registrati</h4>
                <ul>
                    <?php
                    // Cerca gli utenti registrati nel database
                    $sql = "SELECT * FROM users ORDER BY add_data ASC LIMIT 10";
                    $execution = mysqli_query($conn, $sql) or die("Connessione fallita: " . mysqli_error($conn));
                    while ($utenti_registrati = mysqli_fetch_assoc($execution)) {
                        $username = $utenti_registrati['username'];
                        $imageURL = 'image/' . $utenti_registrati["avatar"];


                        // Conta quanti blog hanno gli utenti registrati
                        $numero_blog = "SELECT COUNT(*) AS count_blog FROM blog WHERE autore = '$username' OR co_autore = '$username'";
                        $count_execution = mysqli_query($conn, $numero_blog) or die("Connessione fallita: " . mysqli_error($conn));
                        $row = mysqli_fetch_array($count_execution);
                        $count = $row['count_blog'] . " blog";
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
                    ?>
                </ul>
            </div>
        </aside>
    </main>

    <img src="image/button_top.svg" id="button_top" alt="Vai all'inizio della pagina">

    <img src="image/footer.svg" alt="Footer" >
    <footer>
        <div id="links">
            <a href="https://www.instagram.com/_marco00petrucci_/">
                <img src="image/Instagram_icon.svg" alt="Instagram icon" title="Seguici su Instagram!">
            </a>
            <a href="https://www.facebook.com/marco00petrucci/">
                <img src="image/Facebook_icon.svg" alt="Facebook icon" title="Seguici su Facebook!">
            </a>
        </div>

        <a href="about.php">All rights reserved | © 2021 | Created by Marco Petrucci</a>
    </footer>

    <script>
        // Regex per il telefono
        $.validator.addMethod("tel_regex", function(value, element) {
            return this.optional(element) || /^[0-9]{9,10}$/i.test(value);
        });

        // Gestire la convalida del form per contattare
        $("#contatta-form").validate({
            rules: {
                Anome: "required",
                Aemail: {
                    required: true,
                    email: true
                },
                Atelefono: {
                    tel_regex: true
                },
                messaggio: "required"
            },
            messages: {
                Anome: "<p class='messaggi'>Per favore inserisci il tuo nome.</p>",
                Aemail: {
                    required: "<p class='messaggi'>Per favore inserisci la tua mail.</p>",
                    email: "<p class='messaggi'>Per favore inserisci un indirizzo email valido.</p>"
                },
                Atelefono: {
                    tel_regex: "<p class='messaggi'>Per favore inserisci un numero di telefono valido.</p>"
                },
                messaggio: "<p class='messaggi'>Per favore inserisci un messaggio da inviare.</p>"
            },
            submitHandler: submitForm_contatta
        });

        // Gestire il modulo di invio del messaggio
        function submitForm_contatta() {
            $.ajax({
                type: 'POST',
                url: 'reg-login/check_reg-login.php',
                data: $("#contatta-form").serialize(),
                success: function(risposta) {

                    // Se il messaggio viene inviato reinderizza alla home
                    if (risposta == "Messaggio inviato") {
                        $("#error").fadeIn(400);
                        $("#error").html("Messaggio inviato! 😄📨");
                        $("#cont_invia").html('<img src="image/ajax-loader.gif" alt="Loader" />&nbsp;Invio...');
                        $('#contatta-form').delay(1300).fadeOut();
                        setTimeout('window.location.href = "index.php"', 1600);
                    }

                    // Se non sono stati inseriti caratteri tipici della SQL Injection
                    else if (risposta == "Dati corrotti") {
                        $("#error").fadeIn(400);
                        $("#error").html('<img src="image/danger.svg" alt="Errore" width="20px" height="20px" >&nbsp;No dati corrotti su questo sito... 👨🏼‍💻');
                    }

                    // Se qualcosa va storto
                    else {
                        $("#error").fadeIn(400);
                        $("#error").html('<img src="image/danger.svg" alt="Errore" width="20px" height="20px" >&nbsp;Qualcosa è andato storto! 😓');
                    }
                }
            });
            return false;
        }

        $('.input_box').wrap('<span class="deleteicon" />').after($('<span>').click(function() {
            $(this).prev('.input_box').val('').trigger('change').focus();
        }));
    </script>
</body>

</html>