<?php
session_start();
require('../db_connect.php');

if (!isset($_SESSION['user_session'])) {
	header("Location: ../index.php?no_access");
}

// Se si decide di modificare il post
if (isset($_GET['modifica'])) $sql = "SELECT * FROM post WHERE id = '$_GET[modifica]'";

// Se si decide di eliminare il post
if (isset($_GET['elimina'])) $sql = "SELECT * FROM post WHERE id = '$_GET[elimina]'";

// Scarica i dati della tabella post
$exec = mysqli_query($conn, $sql) or die("Connessione fallita: " . mysqli_error($conn));
$result = mysqli_fetch_array($exec);
$titolo = $result['titolo'];
$categoria = $result['categoria'];
$autore = $result['autore'];
$immagine = $result['immagine'];
$contenuto = $result['contenuto'];

// Verifica se l'utente è un admin o è proprietario del post: se non lo è reinderizza alla home
$username = $_SESSION['user_session'];
$query = "SELECT * FROM users WHERE username = '$username' AND (admin = 1 OR username = '$autore')";
$execution = mysqli_query($conn, $query) or die("Connessione fallita: " . mysqli_error($conn));
if (mysqli_num_rows($execution) == 0) header("Location: ../index.php?no_access");
?>

<!DOCTYPE html>
<html lang="it">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Bloggy | Modifica post <?php echo '"' . $titolo . '"' ?></title>
	<style>
		@import url("../index.css");
		@import url('https://fonts.googleapis.com/css2?family=Bungee&display=swap');
	</style>
	<link rel="icon" href="../image/logo_icona.png" />
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script src="../funzioni.js"></script>
	<script src="reg-login.js"></script>
	<script src="https://code.createjs.com/1.0.0/createjs.min.js"></script>
	<script src="../sfondo.js"></script>
</head>

<body>
	<!-- Sfondo sito -->
	<div id="animation_container">
		<canvas id="canvas"></canvas>
		<div id="dom_overlay_container"></div>
	</div>

	<?php

	// Se si pigia il bottone per aggiornare il post
	if (isset($_POST['post_update'])) {
		$categoria = mysqli_real_escape_string($conn, $_POST['categoria']);
		$titolo = mysqli_real_escape_string($conn, $_POST['titolo']);
		$autore = $_SESSION['user_session'];
		$contenuto = mysqli_real_escape_string($conn, $_POST['contenuto']);
		$nuova_immagine = $_FILES['nuova_immagine']['name'];

		// Se non è stata aggiornata l'immagine prendi la vecchia immagine
		if (empty($nuova_immagine)) {
			$nuova_immagine = $_POST['currentImage'];
		}
		$image_post_directory = "../image/" . basename($nuova_immagine);

		$sql = "UPDATE post SET categoria = '$categoria', titolo = '$titolo', autore = '$autore', contenuto = '$contenuto', immagine = '$nuova_immagine' WHERE id = '$_POST[update_post_by_title]'";

		$execution = mysqli_query($conn, $sql) or die("Connessione fallita: " . mysqli_error($conn));
		if ($execution) {
			move_uploaded_file($nuova_immagine, $image_post_directory);
			header("Location: dashboard.php?post_modificato");
		} else {
			echo "<div class='avviso'>
                    <h1><img src='../image/warning.svg' alt='Alt!' width='25px' height='25px' >&nbsp;ATTENZIONE!</h1>
				<p>Qualcosa è andato storto! 😣</p>
                    <script>setTimeout(\"window.location.href = 'dashboard.php'\", 2500);</script>
               </div>";
		}
	}

	// Se si pigia il bottone per eliminare il post
	if (isset($_POST['post_delete'])) {
		$sql = "DELETE FROM post WHERE id = '$_POST[delete_post_by_title]'";
		$execution = mysqli_query($conn, $sql) or die("Connessione fallita: " . mysqli_error($conn));
		if ($execution) {
			header("Location: dashboard.php?post_eliminato");
		} else {
			echo "<div class='avviso'>
                    <h1><img src='../image/warning.svg' alt='Alt!' width='25px' height='25px' >&nbsp;ATTENZIONE!</h1>
				<p>Qualcosa è andato storto! 😣</p>
                    <script>setTimeout(\"window.location.href = 'dashboard.php'\", 2500);</script>
               </div>";
		}
	}
	?>

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

		// Se si sceglie di modificare il post
		if (isset($_GET['modifica'])) : ?>
			<h1>MODIFICA POST</h1>
			<h3 id="descrizione">Modifica il post!</h3>

			<form action="edit_post.php?modifica=<?php echo $_GET['modifica'] ?>" class="post" method="POST" enctype="multipart/form-data" style="padding-top: 1%;">

				<label for="immagine_post">IMMAGINE:</label><br>
				<img src="../image/<?php echo $immagine; ?>" name="immagine_post" id="immagine_post" alt='Immagine del post' style="width:300px; display:initial"><br>

				<input type="file" id="nuova_immagine" name="nuova_immagine" onchange="$('#immagine_post').attr('src', window.URL.createObjectURL(this.files[0]))" style="margin-bottom:2%"><br>

				<label for="titolo">TITOLO:</label><br>
				<input type="text" class="edit_post_box" name="titolo" id="postname" placeholder="Aggiungi un nuovo titolo" value="<?php echo $titolo; ?>" style="margin-bottom:2%"><br>

				<label for="categoria">CATEGORIA:</label><br>
				<select name="categoria" class="form-control" style="margin-bottom:2%">
					<?php
					$sql = "SELECT DISTINCT categoria FROM post";
					$execution = mysqli_query($conn, $sql) or die("Connessione fallita: " . mysqli_error($conn));
					while ($row = mysqli_fetch_assoc($execution)) {

						// Seleziona la categoria attuale
						if ($categoria === $row['categoria']) {
					?>
							<option selected="selected"><?php echo htmlentities($row['categoria']) ?></option>
						<?php
						} else {
						?>
							<option><?php echo htmlentities($row['categoria']) ?></option>
					<?php
						}
					}
					?>
				</select><br>

				<label for="contenuto">CONTENUTO:</label><br>
				<textarea name="contenuto" id="postContent" cols="60" rows="10"><?php echo $contenuto; ?></textarea><br>

				<button type="submit" class="button_form" id="edit_post_button" name="post_update" title="Modifica post">
					<img class='icona statica edit' src='../image/edit_pencil.png'>
					<img class='icona attiva edit' src='../image/edit_pencil.gif'>Aggiorna
				</button>

				<input type="hidden" name="update_post_by_title" value="<?php echo $_GET['modifica']; ?>">
				<input type="hidden" name="currentImage" value="<?php echo $immagine; ?>">
			</form>
		<?php
		endif;

		// Se si sceglie di eliminare il post
		if (isset($_GET['elimina'])) : ?>

			<h1>ELIMINA POST</h1>
			<form action="edit_post.php?elimina=<?php echo $_GET['elimina'] ?>" class="post" method="POST" enctype="multipart/form-data" style="padding-top:1%">

				<label for="postTitle">TITOLO:</label><br>
				<input disabled type="text" class="edit_post_box" name="titolo" id="postname" value="<?php echo $titolo; ?>" style="margin-bottom:2%"><br>

				<label for="autore">AUTORE:</label><br>
				<input disabled type="text" class="edit_post_box" name="autore" id="autore" value="<?php echo $autore; ?>" style="margin-bottom:2%"><br>

				<label>CATEGORIA:</label><br>
				<input disabled type="text" class="edit_post_box" name="categoria" id="categoria" value="<?php echo $categoria; ?>" style="margin-bottom:2%"><br>

				<label>IMMAGINE:</label><br>
				<img src="../image/<?php echo $immagine; ?>" alt='Immagine del post' style="width:300px;margin-bottom:2%"><br>

				<label for="postContent">CONTENUTO:</label><br>
				<textarea disabled class="postcontent" name="postContent" id="postContent" cols="60" rows="10"><?php echo $contenuto; ?></textarea><br>

				<input type="hidden" name="delete_post_by_title" value="<?php echo $_GET['elimina']; ?>">

				<button type="submit" class="button_form" id="delete_post_button" name="post_delete">
					<img class='icona statica delete' src='../image/trash_icon.png'>
					<img class='icona attiva delete' src='../image/trash_icon.gif'>Elimina
				</button>
			</form>
		<?php
		endif;
		?>

	</main>

	<img src="../image/button_top.svg" id="button_top" alt="Vai all'inizio della pagina">

	<img src="../image/footer.svg" alt="Footer">
	<footer>
		<div id="links">

			<a href="mailto: marco00petrucci@gmail.com">
				<img src='../image/email_icon.png' class='icona statica other_icon_contattaci' alt='Icona email' title='Inviaci una email'>
				<img src='../image/email_icon.gif' class='icona attiva other_icon_contattaci' alt='Icona email' title='Inviaci una email' style="margin:-22px 0 0 2px">
			</a>
			<a href='tel: +39 6218450969'>
				<img src='../image/phone_icon.png' class='icona statica other_icon_contattaci' alt='Icona telefono' title='Telefonaci!'>
				<img src='../image/phone_icon.gif' class='icona attiva other_icon_contattaci' alt='Icona telefono' title='Telefonaci!' style="margin:-22px 0 0 2px">
			</a>
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