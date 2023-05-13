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
	<title>Bloggy | Gestisci commenti</title>
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
				$(this).next($(".post.sx")).slideToggle(400);
				$(this).find("img").toggleClass("down up");
			});
		});
	</script>
</head>

<body>
	<?php
	// Se si sceglie di approvare un commento
	if (isset($_GET['Approve_ID'])) {
		$approva_comm = mysqli_query($conn, "UPDATE commenti SET stato = 1, approved_by = '$_SESSION[user_session]' WHERE id_c = '$_GET[Approve_ID]'") or die("Connessione fallita: " . mysqli_error($conn));
		if ($approva_comm) {
			echo "<div class='avviso'>
					<h1><img src='../image/warning.svg' alt='Alt!' width='25px' height='25px' >&nbsp;ATTENZIONE!</h1>
					<p>Commento approvato!</p>
                         <script>setTimeout(\"window.location.href = 'commenti.php'\", 2500);</script>
		  		</div>";
		} else {
			echo "<div class='avviso'>
					<h1><img src='../image/danger.svg' alt='Alt!' width='25px' height='25px' >&nbsp;ATTENZIONE!</h1>
					<p>Qualcosa è andato storto... Perpiacere riprova!</p>
                         <script>setTimeout(\"window.location.href = 'commenti.php'\", 2500);</script>
		  		</div>";
		}
	}

	// Se si sceglie di disapprovare un commento
	if (isset($_GET['Unapprove_ID'])) {
		$disappr_comm = mysqli_query($conn, "UPDATE commenti SET stato = 0, approved_by = '$_SESSION[user_session]' WHERE id_c = '$_GET[Unapprove_ID]'") or die("Connessione fallita: " . mysqli_error($conn));
		if ($disappr_comm) {
			echo "<div class='avviso'>
					<h1><img src='../image/warning.svg' alt='Alt!' width='25px' height='25px' >&nbsp;ATTENZIONE!</h1>
					<p>Commento non approvato!</p>
                         <script>setTimeout(\"window.location.href = 'commenti.php'\", 2500);</script>
		  		</div>";
		} else {
			echo "<div class='avviso'>
					<h1><img src='../image/danger.svg' alt='Alt!' width='25px' height='25px' >&nbsp;ATTENZIONE!</h1>
					<p>Qualcosa è andato storto... Perpiacere riprova!</p>
                         <script>setTimeout(\"window.location.href = 'commenti.php'\", 2500);</script>
		  		</div>";
		}
	}
	?>

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

	<main>
		<h1>GESTISCI COMMENTI</h1>
		<h3 id="descrizione">Approva, disapprova e gestisci i commenti del sito!</h3>

		<!-- Commenti APPROVATI dagli amministratori -->
		<h2 class="pointer">Commenti approvati&ensp;<img src="../image/button_top.svg" class="down" alt="Mostra e nascondi elementi" /></h2>
		<?php
		$comm_appr = mysqli_query($conn, "SELECT * FROM commenti AS c, post AS p WHERE c.stato = 1 AND p.id = c.id_post AND c.approved_by IS NOT NULL AND c.commento IS NOT NULL ORDER BY c.data_c") or die("Connessione fallita: " . mysqli_error($conn));
		if (mysqli_num_rows($comm_appr) > 0) {
			$postNo = 1; ?>
			<table>
				<thead>
					<tr>
						<th>Numero:</th>
						<th>Aggiunto il:</th>
						<th>Aggiunto da:</th>
						<th>Commento</th>
						<th>sul post:</th>
						<th>Approvato da:</th>
						<th>Azione</th>
					</tr>
				</thead>

				<tbody>
					<?php
					while ($result = mysqli_fetch_assoc($comm_appr)) {
						$id_c = $result['id_c'];
						$data_c = $result['data_c'];
						$username = $result['username'];
						$commento = $result['commento'];
						$approved_by = $result['approved_by'];
						$id_post = $result['id'];
						$titolo_post = $result['titolo'];
						echo "<tr>
								<td style='width:8%'>$postNo</td>
								<td>$data_c</td>";

						// Se l'autore del commento è l'utente loggato al sito
						if ($username == $_SESSION['user_session']) {
							echo "<td>
									<a href='account.php'>$username</a>
                         		 </td>";
						}
						// Se l'autore del commento NON è l'utente loggato al sito
						else {
							echo "<td>
                              		<a href='account.php?username=$username'>$username</a>
                         		</td>";
						}

						echo "<td>$commento</td>
							  <td>
							  	  <a href='../single.php?id=$id_post'>$titolo_post</a>
							  </td>";

						// Se l'utente che ha approvato il commento è l'utente loggato al sito
						if ($approved_by == $_SESSION['user_session']) {
							echo "<td>
                              		<a href='account.php'>$approved_by</a>
                         		 </td>";
						}
						// Se l'utente che ha approvato il commento NON è l'utente loggato al sito
						else {
							echo "<td>
                              		 <a href='account.php?username=$approved_by'>$approved_by</a>
                         		 </td>";
						}

						echo "<td style='width:5%; text-align:center'>
								<a href='commenti.php?Unapprove_ID=$id_c'>
									<img class='icona statica unapprove' src='../image/meno.png' alt='Non approvare commento' title='Non approvare commento'>
                                   	<img class='icona attiva unapprove' src='../image/meno.gif' alt='Non approvare commento' title='Non approvare commento'>
								</a>
							</td>
						</tr>";
						$postNo++;
					} ?>
				</tbody>
			</table>
		<?php
		}
		// Se non ci sono commenti approvati
		else echo "<h3 style='background:rgb(255,255,255,.7); width:18%; margin-left:2%; padding-left: 1%; border-radius:20px'>Nessun commento è stato approvato.</h3>"; ?>

		<!-- Commenti NON APPROVATI dagli amministratori -->
		<h2 class="pointer">Commenti non approvati&ensp;<img src="../image/button_top.svg" class="down" alt="Mostra e nascondi elementi" /></h2>
		<?php
		$comm_disappr = mysqli_query($conn, "SELECT * FROM commenti as c, post as p WHERE c.stato = 0 AND p.id = c.id_post AND c.approved_by IS NOT NULL AND c.commento IS NOT NULL ORDER BY data_c") or die("Connessione fallita: " . mysqli_error($conn));
		if (mysqli_num_rows($comm_disappr) > 0) {
			$postNo = 1; ?>
			<table>
				<thead>
					<tr>
						<th>Numero:</th>
						<th>Aggiunto il:</th>
						<th>Aggiunto da:</th>
						<th>Commento</th>
						<th>sul post:</th>
						<th>Disapprovato da:</th>
						<th>Azione</th>
					</tr>
				</thead>
				<tbody>
					<?php
					while ($result = mysqli_fetch_assoc($comm_disappr)) {
						$id_c = $result['id_c'];
						$data_c = $result['data_c'];
						$username = $result['username'];
						$commento = $result['commento'];
						$approved_by = $result['approved_by'];
						$id_post = $result['id'];
						$titolo_post = $result['titolo'];
						echo "<tr>
								<td style='width:8%'>$postNo</td>
								<td>$data_c</td>";

						// Se l'autore del commento è l'utente loggato al sito
						if ($username == $_SESSION['user_session']) {
							echo "<td>
									<a href='account.php'>$username</a>
                         		 </td>";
						}
						// Se l'autore del commento NON è l'utente loggato al sito
						else {
							echo "<td>
                              		<a href='account.php?username=$username'>$username</a>
                         		 </td>";
						}

						echo "<td>$commento</td>
							<td><a href='../single.php?id=$id_post'>$titolo_post</a></td>";

						// Se l'utente che non ha approvato il commento è l'utente loggato al sito
						if ($approved_by == $_SESSION['user_session']) {
							echo "<td>
									<a href='account.php'>$approved_by</a>
								</td>";
						}
						// Se l'utente che non ha approvato il commento NON è l'utente loggato al sito
						else {
							echo "<td>
									<a href='account.php?username=$approved_by'>$approved_by</a>
								</td>";
						}

						echo "<td style='width:5%; text-align:center'>
								<a href='commenti.php?Approve_ID=$id_c'>
									<img class='icona statica approva' src='../image/plus.png' alt='Approvare commento' title='Approvare commento'>
                                   	<img class='icona attiva approva' src='../image/plus.gif' alt='Approvare commento' title='Approvare commento'>
								</a>
							</td>
						</tr>";
						$postNo++;
					} ?>
				</tbody>
			</table>
		<?php
		}
		// Se non ci sono commenti approvati
		else echo "<h3 style='background:rgb(255,255,255,.7); width:20%; margin-left:2%; padding-left: 1%; border-radius:20px'>Nessun commento non è stato approvato.</h3>"; ?>

		<!-- Commenti da visionare -->
		<h2 class="pointer">Commenti da visionare&ensp;<img src="../image/button_top.svg" class="down" alt="Mostra e nascondi elementi" /></h2>
		<?php
		$visiona_comm = mysqli_query($conn, "SELECT * FROM commenti AS c, post AS p WHERE c.stato = 1 AND p.id = c.id_post AND c.approved_by IS NULL AND c.commento IS NOT NULL ORDER BY c.data_c") or die("Connessione fallita: " . mysqli_error($conn));
		if (mysqli_num_rows($visiona_comm) > 0) {
			$postNo = 1; ?>
			<table>
				<thead>
					<tr>
						<th>Numero:</th>
						<th>Aggiunto il:</th>
						<th>Aggiunto da:</th>
						<th>Commento</th>
						<th>sul post:</th>
						<th>Azione</th>
					</tr>
				</thead>
				<tbody>
					<?php
					while ($result = mysqli_fetch_assoc($visiona_comm)) {
						$id_c = $result['id_c'];
						$data_c = $result['data_c'];
						$username = $result['username'];
						$commento = $result['commento'];
						$id_post = $result['id'];
						$titolo_post = $result['titolo'];
						echo "<tr>
							<td style='width:8%'>$postNo</td>
							<td>$data_c</td>";

						// Se l'autore del commento è l'utente loggato al sito
						if ($username == $_SESSION['user_session']) {
							echo "<td>
                              		<a href='account.php'>$username</a>
                         		</td>";
						}
						// Se l'autore del commento NON è l'utente loggato al sito
						else {
							echo "<td>
                              		<a href='account.php?username=$username'>$username</a>
                         		</td>";
						}
						echo "<td>$commento</td>
						<td><a href='../single.php?id=$id_post'>$titolo_post</a></td>
						<td style='width:6%; text-align:center'>
							<a href='commenti.php?Unapprove_ID=$id_c'>
								<img class='icona statica unapprove' src='../image/meno.png' alt='Non approvare commento' title='Non approvare commento'>
                              	<img class='icona attiva unapprove' src='../image/meno.gif' alt='Non approvare commento' title='Non approvare commento'>
							</a>
							<a href='commenti.php?Approve_ID=$id_c'>
								<img class='icona statica approva' src='../image/plus.png' alt='Approvare commento' title='Approvare commento'>
                              	<img class='icona attiva approva' src='../image/plus.gif' alt='Approvare commento' title='Approvare commento'>
							</a>
						</td>
					</tr>";
						$postNo++;
					} ?>
				</tbody>
			</table>
		<?php
		}
		// Se non ci sono commenti da visionare
		else echo "<h3 style='background:rgb(255,255,255,.7); width:18%; margin-left:2%; padding-left: 1%; border-radius:20px'>Non ci sono commenti da visionare.</h3>";
		mysqli_close($conn); ?>
	</main>

	<img src="../image/button_top.svg" id="button_top" alt="Vai all'inizio della pagina">

	<img src="../image/footer.svg" alt="Footer">
	<footer>
		<a href="../about.php">All rights reserved | © 2021 | Created by Marco Petrucci</a>
	</footer>

</body>

</html>