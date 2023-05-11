<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bloggy | Home</title>
    <style>
        @import url("index.css");
        @import url('https://fonts.googleapis.com/css2?family=Bungee&display=swap');

        #load_data_message {
            margin: 0 32%;
        }

        #load_data_message p {
            background: #fff;
            border-radius: 10px;
            padding: 1% 0%;
            text-align: center;
        }
    </style>
    <link rel="icon" href="image/logo_icona.png" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="funzioni.js"></script>
    <script src="https://code.createjs.com/1.0.0/createjs.min.js"></script>
    <script src="sfondo.js"></script>
    <script>
        $(function() {
            var start = 0,
                action = 'inattivo';

            if (action == 'inattivo') {
                action = 'attivo';
                load_posts(start);
            }

            function load_posts(start) {
                $.ajax({
                    url: "load_post.php",
                    method: "POST",
                    data: {
                        start: start
                    },
                    cache: false,
                    success: function(data) {
                        $('#attach_posts').append(data);
                        if (data == '') {
                            $('#load_data_message').html("<p>Non ci sono altri post</p>");
                            action = 'attivo';
                        } else {
                            $('#load_data_message').html("<p>Caricamento....&nbsp;<img src='../image/ajax-loader.gif' alt='Loader' /></p>");
                            action = "inattivo";
                        }
                    }
                });
            }

            // Visualizza 3 post in più se la pagina viene scrollata fino alla fine
            $(window).scroll(function() {
                if ($(window).scrollTop() + $(window).height() > $("#attach_posts").height() && action == 'inattivo') {
                    action = 'attivo';
                    start += 3;
                    setTimeout(function() {
                        load_posts(start)
                    }, 1000);
                }
            });
        });
    </script>
</head>

<body id="home">
    <?php
    session_start();
    include "db_connect.php";

    // Se si sceglie di fare il logout
    if (isset($_GET['logout']) && isset($_SESSION['user_session'])) {
        unset($_SESSION['user_session']);
        if (session_destroy()) {
            echo "<div class='avviso'>
                    <h1><img src='image/warning.svg' alt='Alt!' width='25px' height='25px' >&nbsp;ATTENZIONE!</h1>
                    <p>Logout effettuato!</p>
                    <script>setTimeout('window.location.href = \"index.php\"', 2500);</script>
                </div>";
        }
    }

    // Se si sceglie di cancellare l'account
    if (isset($_GET['disiscrizione']) && isset($_SESSION['user_session']) && $_GET['disiscrizione'] == "eliminato") {
        $Dquery = "DELETE FROM users WHERE username = '$_SESSION[user_session]'";
        mysqli_query($conn, $Dquery) or die("Connessione fallita: " . mysqli_error($conn));
        unset($_SESSION['user_session']);
        if (session_destroy()) {
            echo "<div class='avviso'>
                    <h1><img src='image/warning.svg' alt='Alt!' width='25px' height='25px' >&nbsp;ATTENZIONE!</h1>
                    <p>Disiscrizione dal sito effettuata!</p>
                    <script>setTimeout('window.location.href = \"index.php\"', 2500);</script>
                 </div>";
        }
    }

    // Se si entra in una pagina alla quale non si può accedere
    if (isset($_GET['no_access'])) {
        echo "<div class='avviso'>
                  <h1><img src='image/warning.svg' alt='Alt!' width='25px' height='25px' >&nbsp;ATTENZIONE!</h1>
                  <p>Non puoi accedere a questa pagina!🐱‍💻</p>
                  <script>setTimeout('window.location.href = \"index.php\"', 2500);</script>
             </div>";
    }

    // Se si entra in una pagina inesistente
    if (isset($_GET['404'])) {
        echo "<div class='avviso'>
                  <h1><img src='image/warning.svg' alt='Alt!' width='25px' height='25px' >&nbsp;ATTENZIONE!</h1>
                  <p>Questa pagina non esiste!🐱‍💻</p>
                  <script>setTimeout('window.location.href = \"index.php\"', 2500);</script>
             </div>";
    }

    // Se si è appena creato un blog
    if (isset($_GET['aggiungi_blog_post'])) {
        echo "<div class='avviso'>
                <h1>🤩AVVENUTO</h1>
                <p>Blog creato!</p>
                <script>setTimeout('window.location.href = \"index.php\"', 2500);</script>
            </div>";
    }

    // Se si è appena creato un post
    if (isset($_GET['aggiungi_post'])) {
        echo "<div class='avviso'>
                <h1>🤩AVVENUTO</h1>
                <p>Post creato!</p>
                <script>setTimeout('window.location.href = \"index.php\"', 2500);</script>
            </div>";
    } ?>

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
        <a href="index.php">
            <img src="image/logo.png" id="logo" alt="Logo" />
        </a>

        <nav id="menu">
            <!-- Cerca nel sito -->
            <form action="blog.php" method="get" id="cerca" name="cerca" title="Cerca nel sito...">
                <input type="search" id="testoCerca" name="testoCerca" placeholder="Cerca..." />
                <button type="submit" id="cercaBtn">
                    <img src="image/search_icon.svg" alt="Cerca">
                </button>
                <div id="autocompletamento"></div>
            </form>

            <?php
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
        </nav>
    </header>

    <nav id="nav_1"></nav>
    <nav id="nav_2"></nav>

    <main>
        <article>
            <h1>BENVENUTO SU <img src="image/logo.png" alt="Logo Bloggy">!</h1>
            <h3 id="descrizione" style="margin: -1.5% 0% 0 34%; text-align:left">...Il sito per condividere le passioni con tutti!</h3>

            <div id="attach_posts"></div>
            <div id="load_data_message"></div>
        </article>

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

    <img src="image/footer.svg" alt="Footer">
    <footer>
        <a href="about.php">All rights reserved | © 2021 | Created by Marco Petrucci</a>
    </footer>
</body>

</html>