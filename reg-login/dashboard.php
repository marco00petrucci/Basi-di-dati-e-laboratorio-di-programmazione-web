<?php
session_start();
require('../db_connect.php');

if (!isset($_SESSION['user_session'])) {
     header("Location: ../index.php?no_access");
}

if (isset($_GET["page"])) {
     $page = $_GET["page"];
} else $page = 1;

$limit = 2;
$start_from = ($page - 1) * $limit;
$query = "SELECT * FROM post ORDER BY id ASC LIMIT $start_from, $limit";
mysqli_query($conn, $query) or die("Connessione fallita: " . mysqli_error($conn));
?>

<!DOCTYPE html>
<html lang="it">

<head>
     <meta charset="utf-8">
     <meta name="viewport" content="width=device-width, initial-scale=1">
     <title>Bloggy | Dashboard</title>
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

               // Al click del titolo mostra e nascondi gli elementi
               $(".pointer").click(function() {
                    $(this).next($(".slide")).slideToggle(400);
                    $(this).find("img").toggleClass("down up");
               });

               // Paginazione dashboard
               $("#target-content").load("pagination.php");
               $("#pagination li").click(function(e) {
                    e.preventDefault();
                    $("#target-content").html('Loading...');
                    $("#pagination li").removeClass('active');
                    $(this).addClass('active');
                    var page = this.id;
                    $("#target-content").load("pagination.php?page=" + page);
               });
          });
     </script>
</head>

<body>
     <?php
     // Se si sceglie di modificare un blog
     if (isset($_GET['modifica_blog']) && isset($_POST['edit_blog'])) {
          $nuovo_nome_blog = mysqli_real_escape_string($conn, $_POST['nuovo_nome_blog']);
          $nuovo_co_autore = mysqli_real_escape_string($conn, $_POST['co_autore']);

          // Se il nuovo nome del blog è uguale a quello vecchio
          if ($nuovo_nome_blog == $_POST['vecchio_nome_blog'] && $nuovo_co_autore == $_POST['vecchio_co_autore']) {
               echo "<div class='avviso'>
                    <h1><img src='../image/warning.svg' alt='Alt!' width='25px' height='25px' >&nbsp;ATTENZIONE!</h1>
                    <p>Non hai modificato niente!</p>
                    <script>setTimeout(\"window.location.href = 'dashboard.php'\", 2500);</script>
               </div>";
          }

          // Se non viene inserito un nome nuovo
          else if (empty($nuovo_nome_blog)) {
               echo "<div class='avviso'>
                    <h1><img src='../image/warning.svg' alt='Alt!' width='25px' height='25px' >&nbsp;ATTENZIONE!</h1>
                    <p>Inserisci un nuovo nome al blog, che non sia vuoto!</p>
                    <script>setTimeout(\"window.location.href = 'dashboard.php'\", 2500);</script>
               </div>";
          }

          // Se è tutto apposto, aggiorna il nome del blog
          else {

               // Se non si sceglie nessun co-autore per il blog
               if ($nuovo_co_autore == "Nessun co-autore") $sql = "UPDATE blog SET nome_blog = '$_POST[nuovo_nome_blog]', co_autore = NULL WHERE nome_blog = '$_POST[vecchio_nome_blog]'";

               else if (empty($nuovo_co_autore) && $nuovo_co_autore != "Nessun co-autore") $sql = "UPDATE blog SET nome_blog = '$_POST[nuovo_nome_blog]' WHERE nome_blog = '$_POST[vecchio_nome_blog]'";

               else if (!empty($nuovo_co_autore) && $nuovo_co_autore != "Nessun co-autore") $sql = "UPDATE blog SET nome_blog = '$_POST[nuovo_nome_blog]', co_autore = '$nuovo_co_autore' WHERE nome_blog = '$_POST[vecchio_nome_blog]'";

               $execution = mysqli_query($conn, $sql) or die("Connessione fallita: " . mysqli_error($conn));
               if ($execution) {
                    echo "<div class='avviso'>
                         <h1>🤩&nbsp;AVVENUTO!</h1>
                         <p>Blog aggiornato!</p>
                         <script>setTimeout(\"window.location.href = 'dashboard.php'\", 2500);</script>
                    </div>";
               } else {
                    echo "<div class='avviso'>
                         <h1><img src='../image/warning.svg' alt='Cerca' width='25px' height='25px' >&nbsp;ATTENZIONE!</h1>
                         <p>Qualcosa è andato storto... Perpiacere riprova!</p>
                         <script>setTimeout(\"window.location.href = 'dashboard.php'\", 2500);</script>
                    </div>";
               }
          }
     }

     // Se si sceglie di eliminare un blog
     if (isset($_GET['delete_blog'])) {
          $sql = "DELETE FROM blog WHERE nome_blog = $_GET[delete_blog]";
          $execution = mysqli_query($conn, $sql) or die("Connessione fallita: " . mysqli_error($conn));
          if ($execution) {
               echo "<div class='avviso'>
                    <h1>🤩&nbsp;AVVENUTO!</h1>
                    <p>Blog eliminato!</p>
                    <script>setTimeout(\"window.location.href = '../index.php'\", 2500);</script>
               </div>";
          } else {
               echo "<div class='avviso'>
                  <h1><img src='../image/warning.svg' alt='Cerca' width='25px' height='25px' >&nbsp;ATTENZIONE!</h1>
                  <p>Qualcosa è andato storto... Perpiacere riprova!</p>
                  <script>setTimeout(\"window.location.href = 'dashboard.php'\", 2500);</script>
               </div>";
          }
     }

     // Se si sceglie di modificare una categoria
     if (isset($_GET['modifica_categoria']) && isset($_POST['edit_categoria'])) {
          $nuovo_nome_categoria = mysqli_real_escape_string($conn, $_POST['nuovo_nome_categoria']);

          // Se il nuovo nome della categoria è uguale a quello vecchio
          if ($nuovo_nome_categoria == $_POST['vecchio_nome_categoria']) {
               echo "<div class='avviso'>
                    <h1><img src='../image/warning.svg' alt='Alt!' width='25px' height='25px' >&nbsp;ATTENZIONE!</h1>
                    <p>Non hai cambiato il nome della categoria!</p>
                    <script>setTimeout(\"window.location.href = 'dashboard.php'\", 2500);</script>
               </div>";
          }

          // Se non viene inserito un nome nuovo
          else if (empty($nuovo_nome_categoria)) {
               echo "<div class='avviso'>
                    <h1><img src='../image/warning.svg' alt='Alt!' width='25px' height='25px' >&nbsp;ATTENZIONE!</h1>
                    <p>Inserisci un nuovo nome alla categoria, che non sia vuoto!</p>
                    <script>setTimeout(\"window.location.href = 'dashboard.php'\", 2500);</script>
               </div>";
          }

          // Se è tutto apposto, aggiorna il nome della categoria
          else {
               $sql = "UPDATE post SET categoria = '$_POST[nuovo_nome_categoria]' WHERE categoria = '$_POST[vecchio_nome_categoria]'";
               $execution = mysqli_query($conn, $sql) or die("Connessione fallita: " . mysqli_error($conn));
               if ($execution) {
                    echo "<div class='avviso'>
                         <h1>🤩&nbsp;AVVENUTO!</h1>
                         <p>Nome della categoria cambiato!</p>
                         <script>setTimeout(\"window.location.href = 'dashboard.php'\", 2500);</script>
                    </div>";
               } else {
                    echo "<div class='avviso'>
                         <h1><img src='../image/warning.svg' alt='Cerca' width='25px' height='25px' >&nbsp;ATTENZIONE!</h1>
                         <p>Qualcosa è andato storto... Perpiacere riprova!</p>
                         <script>setTimeout(\"window.location.href = 'dashboard.php'\", 2500);</script>
                    </div>";
               }
          }
     }

     // Se si sceglie di eliminare una categoria
     if (isset($_GET['delete_categoria'])) {
          $sql = "DELETE FROM post WHERE categoria = $_GET[delete_categoria]";
          $execution = mysqli_query($conn, $sql) or die("Connessione fallita: " . mysqli_error($conn));
          if ($execution) {
               echo "<div class='avviso'>
                    <h1>🤩&nbsp;AVVENUTO!</h1>
                    <p>Categoria eliminata!</p>
                    <script>setTimeout(\"window.location.href = 'dashboard.php'\", 2500);</script>
               </div>";
          } else {
               echo "<div class='avviso'>
                  <h1><img src='../image/warning.svg' alt='Cerca' width='25px' height='25px' >&nbsp;ATTENZIONE!</h1>
                  <p>Qualcosa è andato storto... Perpiacere riprova!</p>
                  <script>setTimeout(\"window.location.href = 'dashboard.php'\", 2500);</script>
               </div>";
          }
     }

     // Se è stato modificato un post
     if (isset($_GET['post_modificato'])) {
          echo "<div class='avviso'>
               <h1>🤩&nbsp;AVVENUTO!</h1>
               <p>Post modificato!</p>
               <script>setTimeout(\"window.location.href = 'dashboard.php'\", 2500);</script>
          </div>";
     }

     // Se è stato eliminato un post
     if (isset($_GET['post_eliminato'])) {
          echo "<div class='avviso'>
               <h1>🤩&nbsp;AVVENUTO!</h1>
               <p>Post eliminato!</p>
               <script>setTimeout(\"window.location.href = 'dashboard.php'\", 2500);</script>
          </div>";
     }
     ?>
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
          <h1>DASHBOARD</h1>
          <h3 id="descrizione">Modifica, elimina e gestisci i tuoi blog e post!</h3>

          <div class="blog_categoria">
               <div>
                    <h2 class="pointer" id="manage_blog" style="width: 30%;">GESTISCI BLOG&ensp;<img src="../image/button_top.svg" class="up" alt="Mostra e nascondi elementi" /></h2>
                    <?php
                    // Verifica se l'utente è un admin
                    $query_check = "SELECT * FROM users WHERE username = '$username' AND admin = 1";
                    $check_admin = mysqli_query($conn, $query_check) or die("Connessione fallita: " . mysqli_error($conn));

                    // Se l'utente non è un admin mostra solo i suoi blog
                    if (mysqli_num_rows($check_admin) == 0) $query = "SELECT * FROM blog WHERE autore = '$username' OR co_autore = '$username' ORDER BY id ASC";

                    // Se l'utente è un admin mostra tutti i blog
                    else $query = "SELECT * FROM blog ORDER BY id ASC";

                    $exec = mysqli_query($conn, $query) or die("Connessione fallita: " . mysqli_error($conn));

                    // Se non si hanno blog
                    if (mysqli_num_rows($exec) == 0) {
                         echo "<p id='risultati' style='width:60%; margin: 0 30% 0 1%; background:rgb(255, 255, 255, .7);'>Non hai blog al momento...😪
                                   <a href='new_blog.php' style='color:#000'>Aggiungi blog</a>
                               </p>";
                    } else { ?>
                         <table class="slide">
                              <thead>
                                   <tr>
                                        <th>Numero:</th>
                                        <th>Nome blog</th>
                                        <?php if (isset($_GET['modifica_blog'])) echo "<th>Co-autore</th>";
                                        else echo "<th>Gestito da:</th>"; ?>
                                        <th>Aggiunto il:</th>
                                        <th>Azione</th>
                                   </tr>
                              </thead>
                              <tbody>
                                   <?php
                                   $num = 1;
                                   while ($result = mysqli_fetch_assoc($exec)) {
                                        $blog_id = $result['id'];
                                        $blog_nome = $result['nome_blog'];
                                        $creato_il = $result['creato_il'];
                                        $blog_autore = $result['autore'];
                                        $blog_co_autore = $result['co_autore'];

                                        echo "<tr>
							               <td style='width:8%'>$num</td>";
                                        $num++;

                                        if (isset($_GET['modifica_blog']) && $_GET['modifica_blog'] == $blog_nome) {
                                             if (empty($blog_co_autore)) $blog_co_autore = "Nessun co-autore";
                                             echo "<td>
                                                       <form action='dashboard.php?modifica_blog=$_GET[modifica_blog]' method='POST'>
								                    <input type='text' class='edit_items' value='$blog_nome' style='text-align:left; width:90%' name='nuovo_nome_blog'>
                                                            <input type='hidden' value='$blog_nome' name='vecchio_nome_blog'>
							               </td>
                                                  <td>
                                                       <input type='hidden' value='$blog_co_autore' name='vecchio_co_autore'>
                                                       <select name='co_autore' class='form-control' id='co_autore' style='width:65%'>
                                                            <option selected='selected'>$blog_co_autore</option>";
                                             if ($blog_co_autore != "Nessun co-autore") echo "<option>Nessun co-autore</option>";

                                             // Verifica che l'utente sia l'autore del post, e non il co-autore
                                             $sql = "SELECT username FROM users WHERE username != '$blog_autore' AND username != '$blog_co_autore'";
                                             $execution = mysqli_query($conn, $sql) or die("Connessione fallita: " . mysqli_error($conn));
                                             while ($row = mysqli_fetch_array($execution)) {
                                                  echo "<option>$row[username]</option>";
                                             }
                                             echo "    </select>
                                                  </td>
                                                  <td>$creato_il</td>
							               <td style='width:80px; text-align:center'>
                                                       <button type='submit' class='button_form conferma_edit_items' name='edit_blog'>Cambia</button>
                                                  </form>
							               </td>
						               </tr>";
                                        } else {
                                             echo "<td>
								               <a href='../blog.php?testoCerca=$blog_nome'>$blog_nome</a>
							               </td>";

                                             // Se l'autore del blog è l'utente loggato al sito
                                             if ($blog_autore == $_SESSION['user_session']) {
                                                  echo "<td>
								               <a href='account.php'>$blog_autore</a>";
                                             } else {
                                                  echo "<td>
								               <a href='account.php?username=$blog_autore'>$blog_autore</a>";
                                             }

                                             // Se esiste un co-autore che è anche l'utente loggato al sito
                                             if ($blog_co_autore != null && $blog_co_autore == $_SESSION['user_session']) {
                                                  echo " e <a href='account.php'>$blog_co_autore</a>
							               </td>";
                                             }

                                             // Se esiste un co-autore
                                             else if ($blog_co_autore != null) {
                                                  echo " e <a href='account.php?username=$blog_co_autore'>$blog_co_autore</a>
							               </td>";
                                             } else echo "</td>";

                                             echo "<td>$creato_il</td>
							          <td style='width:80px; text-align:center'>
                                                  <a href='dashboard.php?modifica_blog=$blog_nome'>
                                                       <img class='icona statica edit' src='../image/edit_pencil.png' alt='Modifica blog' title='Modifica blog'>
                                                       <img class='icona attiva edit' src='../image/edit_pencil.gif' alt='Modifica blog' title='Modifica blog'>
                                                  </a>|
							          	<a href='dashboard.php?prepare_blog_delete=$blog_nome'>
							          		<img class='icona statica delete' src='../image/trash_icon.png' alt='Elimina blog' title='Elimina blog'>
                                             		<img class='icona attiva delete' src='../image/trash_icon.gif' alt='Elimina blog' title='Elimina blog'>
							          	</a>
							          </td>
						          </tr>";
                                        }
                                   }
                                   ?>
                              </tbody>
                         </table>
                    <?php
                    }

                    // Se si sceglie di eliminare un blog, avverti l'utente se vuole dare l'ok
                    if (isset($_GET['prepare_blog_delete'])) {
                         echo "<div class='avviso no_timeout'>
                                   <h1><img src='../image/warning.svg' alt='Alt!' width='25px' height='25px' >&nbsp;ATTENZIONE!</h1>
                                   <p>Sei sicuro di voler eliminare il blog \"$_GET[prepare_blog_delete]\"?<br>Verranno eliminati anche TUTTI i post!</p>
                                   <a href='dashboard.php?delete_blog=\"$_GET[prepare_blog_delete]\"'>OK</a>&emsp;
                                   <a href='dashboard.php'>NO</a>
                              </div>";
                    }
                    ?>
               </div>

               <div>
                    <h2 class="pointer" style="width: 40%;">GESTISCI CATEGORIE&ensp;<img src="../image/button_top.svg" class="up" alt="Mostra e nascondi elementi" /></h2>
                    <?php
                    // Se l'utente non è un admin mostra solo le sue categorie
                    if (mysqli_num_rows($check_admin) == 0) $query = "SELECT DISTINCT * FROM post WHERE autore = '$username' GROUP BY categoria ORDER BY categoria ASC";

                    // Se l'utente è un admin mostra tutte le categorie
                    else $query = "SELECT DISTINCT * FROM post GROUP BY categoria ORDER BY creato_il ASC";

                    $exec = mysqli_query($conn, $query) or die("Connessione fallita: " . mysqli_error($conn));
                    if (mysqli_num_rows($exec) == 0) {
                         echo "<p id='risultati' style='width:70%; margin: 0 30% 0 1%; background:rgb(255, 255, 255, .7);'>Non hai categorie al momento...😪
                                   <a href='new_blog.php' style='color:#000'>Aggiungi categorie</a>
                              </p>";
                    } else { ?>
                         <table class="slide">
                              <thead>
                                   <tr>
                                        <th>Numero:</th>
                                        <th>Nome categoria</th>
                                        <th>Creata da:</th>
                                        <th>Aggiunta il:</th>
                                        <th>Azione</th>
                                   </tr>
                              </thead>
                              <tbody>
                                   <?php
                                   $num = 1;
                                   while ($result = mysqli_fetch_assoc($exec)) {
                                        $cat_nome = $result['categoria'];
                                        $creata_il = $result['creato_il'];
                                        $cat_autore = $result['autore'];
                                        echo "<tr>
							               <td style='width:8%'>$num</td>";

                                        if (isset($_GET['modifica_categoria']) && $_GET['modifica_categoria'] == $cat_nome)
                                             echo "<td>
                                                       <form action='dashboard.php?modifica_categoria=$_GET[modifica_categoria]' method='POST'>
								                    <input type='text' class='edit_items' value='$cat_nome' style='text-align:left' name='nuovo_nome_categoria'>
                                                            <button type='submit' class='button_form conferma_edit_items' name='edit_categoria'>Cambia</button>
                                                            <input type='hidden' class='edit_items' value='$cat_nome' name='vecchio_nome_categoria'>
                                                       </form>
							               </td>";
                                        else {
                                             echo "<td>
                                                       <a href='../blog.php?testoCerca=$cat_nome'>$cat_nome</a>
                                                  </td>";
                                        }

                                        // Se l'autore della categoria è l'utente loggato al sito
                                        if ($cat_autore == $_SESSION['user_session']) {
                                             echo "<td>
								               <a href='account.php'>$cat_autore</a>
							               </td>";
                                        }
                                        // Se l'autore della categoria non è l'utente loggato al sito
                                        else {
                                             echo "<td>
								               <a href='account.php?username=$cat_autore'>$cat_autore</a>
							               </td>";
                                        }

                                        echo "<td>$creata_il</td>
                                             <td style='width:80px; text-align:center'>
                                                  <a href='dashboard.php?modifica_categoria=$cat_nome'>
                                                       <img class='icona statica edit' src='../image/edit_pencil.png' alt='Modifica categoria' title='Modifica categoria'>
                                                       <img class='icona attiva edit' src='../image/edit_pencil.gif' alt='Modifica categoria' title='Modifica categoria'>
                                                  </a>|
							          	<a href='dashboard.php?prepare_categoria_delete=$cat_nome'>
							          		<img class='icona statica delete' src='../image/trash_icon.png' alt='Elimina categoria' title='Elimina categoria'>
                                             		<img class='icona attiva delete' src='../image/trash_icon.gif' alt='Elimina categoria' title='Elimina categoria'>
								          </a>
							          </td>
						          </tr>";
                                        $num++;
                                   }
                                   ?>
                              </tbody>
                         </table>
                    <?php
                    }

                    // Se si sceglie di eliminare una categoria, avverti l'utente se vuole dare l'ok
                    if (isset($_GET['prepare_categoria_delete'])) {
                         echo "<div class='avviso no_timeout'>
                                   <h1><img src='../image/warning.svg' alt='Alt!' width='25px' height='25px' >&nbsp;ATTENZIONE!</h1>
                                   <p>Sei sicuro di voler eliminare la categoria \"$_GET[prepare_categoria_delete]\"?<br>Verranno eliminati anche TUTTI i post annessi alla categoria!</p>
                                   <a href='dashboard.php?delete_categoria=\"$_GET[prepare_categoria_delete]\"'>SI</a>&emsp;
                                   <a href='dashboard.php'>NO</a>
                              </div>";
                    }
                    ?>
               </div>
          </div>

          <h2 class="pointer" id="manage_post" style="margin-top:3%; clear:both">GESTISCI POST&ensp;<img src="../image/button_top.svg" class="up" alt="Mostra e nascondi elementi" /></h2>
          <div class="slide">
               <div id="target-content">
                    <img src="../image/ajax-loader.gif" alt="Loader" />&nbsp;Loading...
               </div>

               <?php
               // Verifica se l'utente è un admin
               $query_check = "SELECT * FROM users WHERE username = '$username' AND admin = 1";
               $check_admin = mysqli_query($conn, $query_check) or die("Connessione fallita: " . mysqli_error($conn));

               // Se l'utente è un admin
               if (mysqli_num_rows($check_admin) > 0) {

                    $limit = 5;
                    $query = "SELECT COUNT(*) FROM post";
                    $rs_result = mysqli_query($conn, $query) or die("Connessione fallita: " . mysqli_error($conn));
                    $row = mysqli_fetch_row($rs_result);
                    $total_records = $row[0];
                    $total_pages = ceil($total_records / $limit); ?>

                    <ul id="pagination">
                         <?php
                         for ($i = 1; $i <= $total_pages; $i++) {
                              if ($i == 1) {
                                   echo "<li class='active' id=$i>
                                        <a href='pagination.php?page=$i'>$i</a><span style='color:#fff; user-select:none'>&thinsp;-&thinsp;</span>
                                    </li>";
                              } else {
                                   echo "<li id=$i>
                                        <a href='pagination.php?page=$i'>$i</a><span style='color:#fff; user-select:none'>&thinsp;-&thinsp;</span>
                                    </li>";
                              }
                         } ?>
                    </ul>
               <?php } ?>
          </div>
     </main>

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