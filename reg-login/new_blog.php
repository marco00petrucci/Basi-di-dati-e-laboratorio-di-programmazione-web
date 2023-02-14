<?php
session_start();
require('../db_connect.php');

// Verifica se l'utente è un admin
if (!isset($_SESSION['user_session'])) {
     header("Location: ../index.php?no_access");
}
if (isset($_GET['aggiungi_post'])) {
     $username = $_SESSION['user_session'];
     $query = "SELECT * FROM users AS u, blog AS b WHERE (u.username = '$username' AND u.admin = 1) OR ('$_GET[aggiungi_post]' = b.nome_blog AND b.autore = '$username' OR b.co_autore = '$username')";
     $execution = mysqli_query($conn, $query) or die("Connessione fallita: " . mysqli_error($conn));
     if (mysqli_num_rows($execution) == 0) {
          header("Location: ../index.php?no_access");
     }
}
?>

<!DOCTYPE html>
<html lang="it">

<head>
     <meta charset="UTF-8">
     <meta http-equiv="X-UA-Compatible" content="IE=edge">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>Bloggy | Aggiungi
          <?php
          if (!isset($_GET['aggiungi_post'])) echo "blog";
          else echo "post"; ?></title>
     <style>
          @import url("../index.css");
          @import url('https://fonts.googleapis.com/css2?family=Bungee&display=swap');

          main h3:not(#descrizione) {
               background-color: #fff;
               padding: 2px;
               border-radius: 10px;
          }

          label {
               font-variant: small-caps;
               font-size: 1.2em;
          }
     </style>
     <link rel="icon" href="../image/logo_icona.png" />
     <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
     <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/jquery.validate.min.js"></script>
     <script src="//cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.13.1/additional-methods.js"></script>
     <script src="../funzioni.js"></script>
     <script src="reg-login.js"></script>
     <script src="https://code.createjs.com/1.0.0/createjs.min.js"></script>
     <script src="../sfondo.js"></script>
     <script>
          $(function() {

               $.validator.addMethod('filesize', function(value, element, param) {
                    return this.optional(element) || (element.files[0].size <= param)
               });

               // Verifica se esiste già un blog col nome che ha i caratteri del testo digitato
               $("#nome_blog").keyup(function() {
                    $.ajax({
                         type: "get",
                         url: "check_reg-login.php",
                         data: 'blog=' + $(this).val(),
                         success: function(risposta) {

                              if (risposta == "Esiste già") {
                                   $("#blog_already_exists").fadeIn(200).html("<p class=messaggi style='margin-bottom:-2%'>Esiste già un blog con questo nome! 😣</p>");
                                   $('#aggiungi_blog_post').css({ // Rendi "invisibile" il tasto submit se esiste già un blog col nome del testo digitato
                                        'pointer-events': 'none',
                                        'opacity': '.7'
                                   });
                              } else {
                                   $("#blog_already_exists").fadeOut(); // Nascondilo se non esistono blog col nome del testo digitato
                                   $('#aggiungi_blog_post').css({
                                        'pointer-events': 'auto',
                                        'opacity': '1'
                                   });
                              }
                         }
                    });
               });

               // Verifica se esiste già una categoria col nome che ha i caratteri del testo digitato
               $("#add_categoria").keyup(function() {
                    $.ajax({
                         type: "get",
                         url: "check_reg-login.php",
                         data: 'aggiungi_categoria=' + $(this).val(),
                         success: function(data) {
                              $("#cat_already_exists").fadeIn();
                              $("#cat_already_exists").html(data);
                         }
                    });
               });

               // Inserisci testo filtrato nel box cerca al click
               $(document).on("click", ".result", function() {
                    $("#add_categoria").val($(this).text());
                    $("#cat_already_exists").hide();
               });

               // Gestire la convalida del form per creare un blog
               $("#new_blog").validate({
                    rules: {
                         nome_blog: "required",
                         immagine_blog: {
                              required: true,
                              extension: "jpg,jpeg,png,gif",
                              filesize: 2 * 1024 * 1024 // <- 2 MB
                         },
                         titolo: "required",
                         add_categoria: "required",
                         immagine_post: {
                              required: true,
                              extension: "jpg,jpeg,png,gif",
                              filesize: 2 * 1024 * 1024 // <- 2 MB
                         },
                         contenuto: "required"
                    },
                    messages: {
                         nome_blog: "<p class='messaggi'>Per favore inserisci il nome del blog.</p>",
                         immagine_blog: {
                              required: "<p class='messaggi'>Per favore inserisci un immagine al blog.</p>",
                              extension: "<p class='messaggi'>Spiacenti, sono supportate solo le immagini di tipo JPG, JPEG, PNG, GIF. 📸</p>",
                              filesize: "<p class='messaggi'>Spiacenti, l'immagine del blog supera 2MB.</p>"
                         },
                         titolo: "<p class='messaggi'>Per favore inserisci il nome del post.</p>",
                         add_categoria: "<p class='messaggi'>Per favore inserisci la categoria del post.</p>",
                         immagine_post: {
                              required: "<p class='messaggi'>Per favore inserisci un immagine al post.</p>",
                              extension: "<p class='messaggi'>Spiacenti, sono supportate solo le immagini di tipo JPG, JPEG, PNG, GIF. 📸</p>",
                              filesize: "<p class='messaggi'>Spiacenti, l'immagine del post supera 2MB.</p>"
                         },
                         contenuto: "<p class='messaggi'>Per favore inserisci il contenuto del post.</p>"
                    }
               });

               // Gestire la convalida del form per creare un post
               $("#new_post").validate({
                    rules: {
                         add_categoria: "required",
                         titolo: "required",
                         immagine_nuovo_post: {
                              required: true,
                              extension: "jpg,jpeg,png,gif",
                              filesize: 2 * 1024 * 1024 // <- 2 MB
                         },
                         contenuto: "required"
                    },
                    messages: {
                         add_categoria: "<p class='messaggi'>Per favore inserisci una categoria al post.</p>",
                         titolo: "<p class='messaggi'>Per favore inserisci il nome del post.</p>",
                         immagine_nuovo_post: {
                              required: "<p class='messaggi'>Per favore inserisci un immagine al post.</p>",
                              extension: "<p class='messaggi'>Spiacenti, sono supportate solo le immagini di tipo JPG, JPEG, PNG, GIF. 📸</p>",
                              filesize: "<p class='messaggi'>Spiacenti, l'immagine del post supera 2MB.</p>"
                         },
                         contenuto: "<p class='messaggi'>Per favore inserisci il contenuto del post.</p>"
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

          <!-- Sezione about -->
          <a href="../about.php" class="dx" title="About">About</a>
     </header>

     <nav id="nav_1"></nav>
     <nav id="nav_2"></nav>

     <main>
          <?php
          // Se si sceglie di aggiungere un blog
          if (!isset($_GET['aggiungi_post'])) { ?>
               <h1>AGGIUNGI BLOG</h1>
               <h3 id="descrizione">Aggiungi un blog e una categoria prima di creare un post!</h3>

               <form class='post' id="new_blog" action="new_blog.php" method="POST" enctype="multipart/form-data">

                    <h3>NUOVO BLOG</h3>
                    <label for="nome_blog">Nome blog</label><br>
                    <input type="text" class="input_box" name="nome_blog" id="nome_blog" placeholder="Aggiungi un nome al blog">
                    <div id='blog_already_exists' name='blog_already_exists' style="display: none;"></div><br><br>

                    <label for="immagine_blog">Inserisci immagine</label><br>
                    <img id='immagine_blog' alt='Immagine blog' /><br class="spazio_input" style="display:none">
                    <input type="file" name="immagine_blog" id="blog_file" onchange="$('#immagine_blog, .spazio_input').css('display','initial'); $('#immagine_blog').attr('src', window.URL.createObjectURL(this.files[0]))"><br><br>

                    <label for="co_autore">Scegli co-autore:</label><br>
                    <select name="co_autore" class="form-control" id="co_autore">
                         <option selected='selected'>Nessun co-autore</option>
                         <?php
                         // Verifica che l'utente sia l'autore del post, e non il co-autore
                         $sql = "SELECT username FROM users WHERE username != '$username'";
                         $execution = mysqli_query($conn, $sql) or die("Connessione fallita: " . mysqli_error($conn));
                         while ($row = mysqli_fetch_array($execution)) {
                              echo "<option>$row[username]</option>";
                         }
                         ?>
                    </select><br><br>

                    <h3>NUOVO POST</h3>
                    <label for="titolo">Titolo</label><br>
                    <input type="text" class="input_box" name="titolo" id="postname" placeholder="Aggiungi un titolo"><br><br>

                    <label for="add_categoria">Aggiungi categoria</label><br>
                    <input type="text" class="input_box" name="add_categoria" id="add_categoria" placeholder="Nome categoria">
                    <div id='cat_already_exists' style="display: none;"></div><br><br>

                    <label for="immagine_post">Inserisci immagine</label><br>
                    <img id='immagine_post' alt='Immagine post' /><br class="spazio_input" style="display:none">
                    <input type="file" name="immagine_post" id="postfile" onchange="$('#immagine_post, .spazio_input').css('display','initial'); $('#immagine_post').attr('src', window.URL.createObjectURL(this.files[0]))"><br><br>

                    <label for="contenuto">Contenuto</label><br>
                    <textarea name="contenuto" class="newpost_box" id="contenuto" placeholder="Scrivi qualcosa..." cols="30" rows="8"></textarea><br>

                    <input type="submit" class="button_form" id="aggiungi_blog_post" name="aggiungi_blog_post" value="Crea">
               </form>
          <?php
               // Se si sceglie di aggiungere un post
          } else { ?>
               <h1>AGGIUNGI POST</h1>
               <h3 id="descrizione">Crea un nuovo post in "<?php echo $_GET['aggiungi_post'] ?>"!</h3>

               <form class='post' id="new_post" action="new_blog.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="blog_da_modificare" value="<?php echo $_GET['aggiungi_post'] ?>">

                    <label for="add_categoria">Categoria</label><br>
                    <input type="text" class="input_box" name="add_categoria" id="add_categoria" placeholder="Nome categoria">
                    <div id='cat_already_exists' style="display: none;"></div><br><br>

                    <label for="titolo">Titolo</label><br>
                    <input type="text" class="input_box" name="titolo" id="postname" placeholder="Aggiungi un titolo"><br><br>

                    <label for="immagine_nuovo_post">Inserisci immagine</label><br>
                    <img id='immagine_nuovo_post' alt='Immagine post' name="immagine_post" /><br class="spazio_input" style="display: none;">
                    <input type="file" name="immagine_nuovo_post" id="postfile" onchange="$('#immagine_nuovo_post, .spazio_input').css('display','initial'); $('#immagine_nuovo_post').attr('src', window.URL.createObjectURL(this.files[0]))"><br><br>

                    <label for="contenuto">Contenuto</label><br>
                    <textarea name="contenuto" class="newpost_box" id="contenuto" placeholder="Scrivi qualcosa..." cols="30" rows="8"></textarea><br>

                    <input type="submit" class="button_form" name="new_post_submit" value="Aggiungi">
               </form>
          <?php
          } ?>

          <aside>
               <!-- Blog -->
               <div id="blog">
                    <h4><a href="../blog.php">Blog</a></h4>
                    <ul>
                         <?php
                         $sql = "SELECT * FROM blog ORDER BY creato_il DESC LIMIT 10";
                         $execution = mysqli_query($conn, $sql) or die("Connessione fallita: " . mysqli_error($conn));
                         while ($recent = mysqli_fetch_assoc($execution)) {
                              $blog = $recent['nome_blog'];
                              echo "<li>
                                        <a href='../blog.php?testoCerca=$blog'>$blog</a>
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
                         $sql = "SELECT * FROM post ORDER BY creato_il DESC LIMIT 10";
                         $execution = mysqli_query($conn, $sql) or die("Connessione fallita: " . mysqli_error($conn));
                         while ($recent = mysqli_fetch_assoc($execution)) {
                              $id = $recent['id'];
                              echo "<li>
                                        <a href='../single.php?id=$id'>$recent[titolo]</a>
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
                              $imageURL = '../image/' . $utenti_registrati["avatar"];


                              // Conta quanti blog hanno gli utenti registrati
                              $numero_blog = "SELECT COUNT(*) AS count_blog FROM blog WHERE autore = '$username' OR co_autore = '$username'";
                              $count_execution = mysqli_query($conn, $numero_blog) or die("Connessione fallita: " . mysqli_error($conn));
                              $row = mysqli_fetch_array($count_execution);
                              $count = $row['count_blog'] . " blog";
                              if ($count == "0 blog") $count = "Nessun blog";

                              if (isset($_SESSION['user_session']) && $username == $_SESSION['user_session']) {
                                   echo "<li id='per_utenti'>
                                             <a href='account.php'>
                                             <img src = '$imageURL' id='users_in_site_icon' alt='Avatar'/>$username
                                             </a>- $count
                                        </li>";
                              } else {
                                   // Stampa risultato
                                   echo "<li id='per_utenti'>
                                             <a href='account.php?username=$username'>
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

     <?php
     date_default_timezone_set('Europe/Rome');
     $time = time();
     $vietate = array("SELECT", "INSERT", "UPDATE", "DELETE", "DROP", "ALTER", "<script>");

     if (isset($_POST['aggiungi_blog_post'])) {
          $dateTime = strftime('%Y-%m-%d %H:%M:%S', $time);
          $nome_blog = mysqli_real_escape_string($conn, $_POST['nome_blog']);
          $autore = $_SESSION['user_session'];
          $co_autore = mysqli_real_escape_string($conn, $_POST['co_autore']);
          $immagine_blog = $_FILES['immagine_blog']['name'];
          $image_blog_directory = "../image/" . basename($_FILES['immagine_blog']['name']);

          $titolo = mysqli_real_escape_string($conn, $_POST['titolo']);
          $nome_categoria = mysqli_real_escape_string($conn, $_POST['add_categoria']);
          $contenuto = mysqli_real_escape_string($conn, $_POST['contenuto']);
          $immagine_post = $_FILES['immagine_post']['name'];
          $image_post_directory = "../image/" . basename($_FILES['immagine_post']['name']);

          $nome_blog_ok = true;
          for ($k = 0; $k <= 6 && $nome_blog_ok; $k++) {
               if (stripos($nome_blog, $vietate[$k]) !== false) $nome_blog_ok = false;
          }

          $titolo_ok = true;
          for ($k = 0; $k <= 6 && $titolo_ok; $k++) {
               if (stripos($titolo, $vietate[$k]) !== false) $titolo_ok = false;
          }

          $nome_categoria_ok = true;
          for ($k = 0; $k <= 6 && $nome_categoria_ok; $k++) {
               if (stripos($nome_categoria, $vietate[$k]) !== false) $nome_categoria_ok = false;
          }

          $contenuto_ok = true;
          for ($k = 0; $k <= 6 && $contenuto_ok; $k++) {
               if (stripos($contenuto, $vietate[$k]) !== false) $contenuto_ok = false;
          }

          // Se non sono stati inseriti caratteri tipici della SQL Injection
          if ($nome_blog_ok && $titolo_ok && $nome_categoria_ok && $contenuto_ok) {

               // Se non si sceglie nessun co-autore per il blog
               if ($co_autore == "Nessun co-autore") $query = "INSERT INTO blog (creato_il, nome_blog, autore, co_autore, immagine) VALUES ('$dateTime', '$nome_blog', '$autore', NULL, '$immagine_blog');";

               // Se si sceglie un co-autore per il blog
               else {
                    $query = "INSERT INTO blog (creato_il, nome_blog, autore, co_autore, immagine) VALUES ('$dateTime', '$nome_blog', '$autore', '$co_autore', '$immagine_blog');";
               }
               $query .= "INSERT INTO post (creato_il, nome_b, categoria, titolo, autore, contenuto, immagine) VALUES ('$dateTime', '$nome_blog', '$nome_categoria', '$titolo', '$autore', '$contenuto', '$immagine_post');";

               $execution = mysqli_multi_query($conn, $query) or die("Connessione fallita: " . mysqli_error($conn));

               if ($execution) {
                    move_uploaded_file($_FILES['immagine_blog']['name'], $image_blog_directory);
                    move_uploaded_file($_FILES['immagine_post']['name'], $image_post_directory);
                    echo "<script>window.location.href = '../index.php?aggiungi_blog_post';</script>";
               }
          } else {
               echo "<div class='avviso'>
                         <h1><img src='../image/warning.svg' alt='Alt!' width='25px' height='25px' >&nbsp;ATTENZIONE!</h1>
                         <p>Sono stati inseriti dati corrotti! 😣</p>
                         <script>setTimeout(\"window.location.href = 'new_blog.php'\", 2500);</script>
                    </div>";
          }
     }

     if (isset($_POST['new_post_submit'])) {
          $dateTime = strftime('%Y-%m-%d %H:%M:%S', $time);
          $blog_da_modificare = mysqli_real_escape_string($conn, $_POST['blog_da_modificare']);
          $add_categoria = mysqli_real_escape_string($conn, $_POST['add_categoria']);
          $titolo = mysqli_real_escape_string($conn, $_POST['titolo']);
          $autore = $_SESSION['user_session'];
          $contenuto = mysqli_real_escape_string($conn, $_POST['contenuto']);
          $immagine = $_FILES['immagine_nuovo_post']['name'];
          $imageDirectory = "../image/" . basename($_FILES['immagine_nuovo_post']['name']);

          $titolo_ok = true;
          for ($k = 0; $k <= 6 && $titolo_ok; $k++) {
               if (stripos($titolo, $vietate[$k]) !== false) $titolo_ok = false;
          }

          $add_categoria_ok = true;
          for ($k = 0; $k <= 6 && $add_categoria_ok; $k++) {
               if (stripos($add_categoria, $vietate[$k]) !== false) $add_categoria_ok = false;
          }

          $contenuto_ok = true;
          for ($k = 0; $k <= 6 && $contenuto_ok; $k++) {
               if (stripos($contenuto, $vietate[$k]) !== false) $contenuto_ok = false;
          }

          // Se non sono stati inseriti caratteri tipici della SQL Injection
          if ($titolo_ok && $add_categoria_ok && $contenuto_ok) {
               $query_post = "INSERT INTO post (creato_il, nome_b, categoria, titolo, autore, contenuto, immagine) VALUES ('$dateTime', '$blog_da_modificare', '$add_categoria', '$titolo', '$autore', '$contenuto', '$immagine')";
               $execution = mysqli_query($conn, $query_post) or die("Connessione fallita: " . mysqli_error($conn));
               if ($execution) {
                    move_uploaded_file($_FILES['immagine_nuovo_post']['name'], $imageDirectory);
                    echo "<script>window.location.href = '../index.php?aggiungi_post';</script>";
               }
          } else {
               echo "<div class='avviso'>
                         <h1><img src='../image/warning.svg' alt='Alt!' width='25px' height='25px' >&nbsp;ATTENZIONE!</h1>
                         <p>Sono stati inseriti dati corrotti! 😣</p>
                         <script>setTimeout(\"window.location.href = 'new_blog.php?aggiungi_post=$_POST[blog_da_modificare]'\", 2500);</script>
                    </div>";
          }
     } ?>

     <img src="../image/button_top.svg" id="button_top" alt="Vai all'inizio della pagina">

     <img src="../image/footer.svg" alt="Footer">
     <footer>
          <div id="links">
               <a href="https://www.instagram.com/_marco00petrucci_/">
                    <img src="../image/Instagram_icon.svg" alt="Instagram icon" title="Seguici su Instagram!">
               </a>
               <a href="https://www.facebook.com/marco00petrucci/">
                    <img src="../image/Facebook_icon.svg" alt="Facebook icon" title="Seguici su Facebook!">
               </a>
          </div>

          <a href="../about.php">All rights reserved | © 2021 | Created by Marco Petrucci</a>
     </footer>

</body>

</html>