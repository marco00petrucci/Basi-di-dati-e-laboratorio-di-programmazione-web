<?php
session_start();
require('../db_connect.php');

// Verifica se l'utente è un admin ed è registrato al blog
$username = $_SESSION['user_session'];
$check_admin = mysqli_query($conn, "SELECT admin FROM users WHERE username = '$username' AND admin = 1") or die("Connessione fallita: " . mysqli_error($conn));
if (!isset($_SESSION['user_session']) || (mysqli_num_rows($check_admin) == 0)) header("Location: ../index.php?no_access");
?>

<!DOCTYPE html>
<html lang="it">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Bloggy | Gestisci messaggi</title>
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

			<div id="utente">
				<span id='dropdown_btn'>▾</span>
				<ul id="subnav">
					<li id="go_to_dashboard">Dashboard</li>
					<li id="add_blog">Aggiungi blog</li>
					<li id="manage_comments">Gestisci commenti</li>
					<li id="manage_users">Gestisci utenti</li>
					<li id="manage_messaggi">Gestisci messaggi</li>
					<li id="logout">
						<img src="../image/login.png" class="button_icon icona statica reg-login" style="margin-top:2.5px" alt="Logout">
						<img src="../image/login.gif" class="button_icon icona attiva reg-login" alt="Logout">&nbsp;Logout
					</li>
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
		</nav>
	</header>

	<nav id="nav_1"></nav>
	<nav id="nav_2"></nav>

	<?php

	// Se si sceglie di eliminare un messaggio
	if (isset($_GET['elimina_messaggio'])) {
		$del_mess = mysqli_query($conn, "DELETE FROM messaggi WHERE id = '$_GET[elimina_messaggio]'") or die("Connessione fallita: " . mysqli_error($conn));
		if ($del_mess) {
			echo "<div class='avviso'>
			  		<h1><img src='../image/warning.svg' alt='Alt!' width='25px' height='25px' >&nbsp;ATTENZIONE!</h1>
			  		<p>Messaggio eliminato! 🗑</p>
					<script>setTimeout(\"window.location.href = 'messaggi.php'\", 2500);</script>
		    	</div>";
		} else {
			echo "<div class='avviso'>
					<h1><img src='../image/warning.svg' alt='Alt!' width='25px' height='25px' >&nbsp;ATTENZIONE!</h1>
			  		<p>Qualcosa è andato storto! 😣</p>
					<script>setTimeout(\"window.location.href = 'messaggi.php'\", 2500);</script>
		    	</div>";
		}
	} ?>

	<main>
		<h1>GESTISCI MESSAGGI</h1>
		<h3 id="descrizione">Visualizza i messaggi inviati dagli utenti e contattali!</h3>

		<?php
		$view_mess = mysqli_query($conn, "SELECT * FROM messaggi ORDER BY data_m") or die("Connessione fallita: " . mysqli_error($conn));
		$postNo = 1;
		if (mysqli_num_rows($view_mess) == 0) echo "<p id='risultati'>Nessun messaggio!</p>";
		else { ?>
			<table>
				<thead>
					<tr>
						<th>Numero</th>
						<th>Inviato il:</th>
						<th>Nome completo</th>
						<th>Email</th>
						<th>Telefono</th>
						<th>Messaggio</th>
						<th>Azioni</th>
					</tr>
				</thead>
				<tbody>
					<?php
					while ($result = mysqli_fetch_assoc($view_mess)) {
						$commento_id = $result['id'];
						$data_m = $result['data_m'];
						$nome = $result['nome'];
						$email = $result['email'];
						$telefono = $result['telefono'];
						$messaggio = $result['messaggio'];
						echo "<tr>
							<td style='width:6%'>$postNo</td>
							<td>$data_m</td>
							<td>$nome</td>
							<td>
								<a href='mailto: $email'>$email
							</td>
							<td>
								<a href='tel: $telefono'>$telefono</a>
							</td>
							<td style='width:30%'>$messaggio</td>
							<td style='width:10%; text-align:center'>
								<a href='tel: $telefono'>
									<img src='../image/phone_icon.png' class='icona statica other_icon' alt='Icona telefono' title='Telefona al mittente'>
									<img src='../image/phone_icon.gif' class='icona attiva other_icon' alt='Icona telefono' title='Telefona al mittente'>
								</a> | 
								<a href='mailto: $email'>
									<img src='../image/email_icon.png' class='icona statica other_icon' alt='Icona email' title='Invia email'>
									<img src='../image/email_icon.gif' class='icona attiva other_icon' alt='Icona email' title='Invia email'>
								</a> | 
								<a href='messaggi.php?elimina_messaggio=$commento_id' id='elimina_messaggio'>
									<img src='../image/trash_icon.png' class='icona statica delete' alt='Elimina messaggio' title='Elimina messaggio'>
									<img src='../image/trash_icon.gif' class='icona attiva delete' alt='Elimina messaggio' title='Elimina messaggio'>
								</a>
							</td>
						</tr>";
						$postNo++;
					}
					?>
				</tbody>
			</table>
		<?php
		}
		mysqli_close($conn); ?>
	</main>

	<img src="../image/button_top.svg" id="button_top" alt="Vai all'inizio della pagina">

	<img src="../image/footer.svg" alt="Footer">
	<footer>
		<a href="../about.php">All rights reserved | © 2021 | Created by Marco Petrucci</a>
	</footer>
</body>

</html>