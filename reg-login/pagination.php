<?php
include "../db_connect.php";
session_start();

$username = $_SESSION['user_session'];

$check_admin = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username' AND admin = 1") or die("Connessione fallita: " . mysqli_error($conn));

// Se l'utente non è un admin mostra tutti i post senza LIMIT
if (mysqli_num_rows($check_admin) == 0) $query = "SELECT * FROM post WHERE autore = '$username' ORDER BY creato_il DESC";

// Se l'utente è un admin mostra tutti i post con LIMIT
else {
     if (isset($_GET["page"])) $page = $_GET["page"];
     else $page = 1;

     $limit = 5;
     $start_from = ($page - 1) * $limit;
     $query = "SELECT * FROM post ORDER BY creato_il DESC LIMIT $start_from, $limit";
}
$exec = mysqli_query($conn, $query) or die("Connessione fallita: " . mysqli_error($conn));


if (mysqli_num_rows($exec) == 0) {
     echo "<p id='risultati' style='width:30%; margin-left: 1%; background:rgb(255, 255, 255, .7)'>Non hai post al momento...😪
               <a href='new_blog.php' style='color:#000'>Aggiungi post</a>
           </p>";
} else { ?>
     <table>
          <thead>
               <tr>
                    <th>Post numero:</th>
                    <th>Data pubblicazione</th>
                    <th>Categoria</th>
                    <th>Titolo</th>
                    <th>Autore</th>
                    <th>Contenuto</th>
                    <th>Immagine</th>
                    <th>Azione</th>
               </tr>
          </thead>

          <tbody>
          <?php
          while ($result = mysqli_fetch_assoc($exec)) {
               $id = $result['id'];
               $data = $result['creato_il'];
               $categoria = $result['categoria'];
               $titolo = $result['titolo'];
               $autore = $result['autore'];
               $contenuto = $result['contenuto'];
               $immagine = $result['immagine'];

               echo "<tr>
                         <td>$id</td>
                         <td>$data</td>
                         <td>$categoria</td>
                         <td>
                              <a href='../single.php?id=$id'>$titolo</a>
                         </td>";

               // Se l'autore del post è l'utente loggato al sito
               if ($autore == $_SESSION['user_session']) {
                    echo "<td>
                              <a href='account.php'>$autore</a>
                         </td>";
               }
               // Se l'autore del post non è l'utente loggato al sito
               else {
                    echo "<td>
                              <a href='account.php?username=$autore'>$autore</a>
                         </td>";
               }

               // Se i caratteri del contenuto sono superiori a 20 allora mostra solo quelli
               if (strlen($contenuto) > 200) {
                    echo "<td style='width:20%'>" . substr($contenuto, 0, 200) . '...</td>';
               } else {
                    echo "<td>$contenuto</td>";
               }

               echo "<td style='text-align:center'>
                         <img src= '../image/$immagine' style='width: 230px'>
                     </td>";

               // Icone azione
               echo "<td style='width:6%; text-align:center'>
                         <a href='edit_post.php?modifica=$id'>
                              <img class='icona statica edit' src='../image/edit_pencil.png' alt='Modifica post' title='Modifica post'>
                              <img class='icona attiva edit' src='../image/edit_pencil.gif' alt='Modifica post' title='Modifica post'>
				     </a>|
                         <a href='edit_post.php?elimina=$id' class='delete_post_icon' >
				     	<img class='icona statica delete' src='../image/trash_icon.png' alt='Elimina post' title='Elimina post'>
                              <img class='icona attiva delete' src='../image/trash_icon.gif' alt='Elimina post' title='Elimina post'>
				     </a>
                     </td>
               </tr>";
          }
     }
     mysqli_close($conn); ?>
          </tbody>
     </table>