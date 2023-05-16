<?php
session_start();
require('../db_connect.php');

// Verifica se l'utente è un admin ed è registrato al blog
$username = $_SESSION['user_session'];
$execution = mysqli_query($conn, "SELECT admin FROM users WHERE username = '$username' AND admin = 1") or die("Connessione fallita: " . mysqli_error($conn));

if (!isset($_SESSION['user_session']) || (mysqli_num_rows($execution) == 0)) header("Location: ../index.php?no_access");
?>

<!DOCTYPE html>
<html lang="it">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Bloggy | Gestisci utenti</title>
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
	date_default_timezone_set('Europe/Rome');
	$time = time();

	// Se si sceglie di aggiungere un amministratore
	if (isset($_GET['add_admin'])) {
		$add_admin = mysqli_query($conn, "UPDATE users SET admin = 1, reso_admin_da = '$username' WHERE username = '$_GET[add_admin]'") or die("Connessione fallita: " . mysqli_error($conn));
		if ($add_admin) {
			echo "<div class='avviso'>
					<h1><img src='../image/warning.svg' alt='Alt!' width='25px' height='25px' >&nbsp;ATTENZIONE!</h1>
					<p>L'utente \"$_GET[add_admin]\" adesso è un amministratore!</p>
					<script>setTimeout(\"window.location.href = 'users.php'\", 2500);</script>
	  			</div>";
		} else {
			echo "<div class='avviso'>
					<h1><img src='../image/danger.svg' alt='Alt!' width='25px' height='25px' >&nbsp;ATTENZIONE!</h1>
					<p>Qualcosa è andato storto... Perpiacere riprova!</p>
					<script>setTimeout(\"window.location.href = 'users.php'\", 2500);</script>
				</div>";
		}
	}

	// Se si sceglie di togliere un amministratore
	if (isset($_GET['del_admin'])) {
		$del_admin = mysqli_query($conn, "UPDATE users SET admin = 0, reso_admin_da = NULL WHERE username = '$_GET[del_admin]'") or die("Connessione fallita: " . mysqli_error($conn));
		if ($del_admin) {
			echo "<div class='avviso'>
					<h1><img src='../image/warning.svg' alt='Alt!' width='25px' height='25px' >&nbsp;ATTENZIONE!</h1>
					<p>L'utente \"$_GET[del_admin]\" adesso non è più un amministratore!</p>
					<script>setTimeout(\"window.location.href = 'users.php'\", 2500);</script>
	  			</div>";
		} else {
			echo "<div class='avviso'>
					<h1><img src='../image/danger.svg' alt='Alt!' width='25px' height='25px' >&nbsp;ATTENZIONE!</h1>
					<p>Qualcosa è andato storto... Perpiacere riprova!</p>
					<script>setTimeout(\"window.location.href = 'users.php'\", 2500);</script>
				</div>";
		}
	}

	// Se si decide di eliminare un utente
	if (isset($_GET['prepare_delete_admin'])) {
		echo "<div class='avviso no_timeout'>
				<h1><img src='../image/warning.svg' alt='Alt!' width='25px' height='25px' >&nbsp;ATTENZIONE!</h1>
				<p>Sei sicuro di voler eliminare l'utente \"$_GET[prepare_delete_admin]\"? Verranno tolti anche tutti i blog dell'utente!</p>
				<a href='users.php?delete_admin=$_GET[prepare_delete_admin]'>OK</a>&emsp;
				<a href='users.php'>NO</a>
  			</div>";
	}

	if (isset($_GET['delete_admin'])) {
		$del_user = mysqli_query($conn, "DELETE FROM users WHERE username = '$_GET[delete_admin]'") or die("Connessione fallita: " . mysqli_error($conn));
		if ($del_user) {
			echo "<div class='avviso'>
					<h1><img src='../image/warning.svg' alt='Alt!' width='25px' height='25px' >&nbsp;ATTENZIONE!</h1>
					<p>L'utente \"$_GET[delete_admin]\" è stato eliminato dal sito!</p>
					<script>setTimeout(\"window.location.href = 'users.php'\", 2500);</script>
				</div>";
		} else {
			echo "<div class='avviso'>
					<h1><img src='../image/danger.svg' alt='Alt!' width='25px' height='25px' >&nbsp;ATTENZIONE!</h1>
					<p>Qualcosa è andato storto... Perpiacere riprova!</p>
					<script>setTimeout(\"window.location.href = 'users.php'\", 2500);</script>
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
		<h1>GESTISCI UTENTI</h1>
		<h3 id="descrizione">Aggiungi / togli amministratori o elimina utenti dal sito!</h3>

		<h2 class="pointer">Utenti amministratori&ensp;<img src="../image/button_top.svg" class="down" alt="Mostra e nascondi elementi" /></h2>
		<table>
			<thead>
				<tr>
					<th>Numero</th>
					<th>Avatar</th>
					<th>Nome</th>
					<th>Cognome</th>
					<th>Username</th>
					<th>Email</th>
					<th>Aggiunto da</th>
					<th>Aggiunto il</th>
					<th>Azione</th>
				</tr>
			</thead>
			<tbody>
				<?php
				// Effettua la query negli utenti amministratori
				$admin_num = 1;
				$admin_users = mysqli_query($conn, "SELECT * FROM users WHERE admin = 1 ORDER BY add_data ASC") or die("Connessione fallita: " . mysqli_error($conn));
				while ($result = mysqli_fetch_assoc($admin_users)) {
					$admin_id = $result['id'];
					$admin_avatar = $result['avatar'];
					$imageURL = '../image/' . $admin_avatar;
					$admin_nome = $result['nome'];
					$admin_cognome = $result['cognome'];
					$admin_username = $result['username'];
					$admin_add_data = $result['add_data'];
					$admin_email = $result['email'];
					$reso_admin_da = $result['reso_admin_da'];
					echo "<tr>
							<td style='width:5%'>$admin_num</td>
							<td>
								<img src = '$imageURL' id='user_icon' alt='Avatar' style='width:100px; height:100px; margin-left:2%'/>
							</td>
							<td>$admin_nome</td>
							<td>$admin_cognome</td>";

					// Se l'utente è quello loggato al sito
					if ($admin_username == $_SESSION['user_session']) {
						echo "<td>
								<a href='account.php'>$admin_username</a>
							</td>";
					}
					// Se l'utente NON è quello loggato al sito
					else {
						echo "<td>
								<a href='account.php?username=$admin_username'>$admin_username</a>
							</td>";
					}

					echo "<td><a href='mailto:$admin_email'>$admin_email</a></td>";

					// Se l'utente che ha aggiunto l'utente selezionato come admin è l'utente loggato al sito
					if ($reso_admin_da == $_SESSION['user_session']) {
						echo "<td>
								<a href='account.php'>$reso_admin_da</a>
							</td>";
					}
					// Se l'utente che ha aggiunto l'utente selezionato come admin NON è l'utente loggato al sito
					else {
						echo "<td>
								<a href='account.php?username=$reso_admin_da'>$reso_admin_da</a>
							</td>";
					}

					echo "<td>$admin_add_data</td>
						  <td style='width:6%; text-align:center'>";

					// Se l'utente non è quello loggato al sito mostra le azioni
					if ($admin_username != $_SESSION['user_session']) {
						echo "<a href='users.php?del_admin=$admin_username'>
								<img class='icona statica unapprove' src='../image/meno.png' alt='Rimuovi utente dagli admin' title='Rimuovi utente dagli admin'>
								<img class='icona attiva unapprove' src='../image/meno.gif' alt='Rimuovi utente dagli admin' title='Rimuovi utente dagli admin'>
							</a> |
							<a href='users.php?prepare_delete_admin=$admin_username'>
								<img class='icona statica delete' src='../image/trash_icon.png' alt='Elimina utente' title='Elimina utente'>
                                <img class='icona attiva delete' src='../image/trash_icon.gif' alt='Elimina utente' title='Elimina utente'>
							</a>
						</td>
					</tr>";
					}
					// Se l'utente è quello loggato al sito non mostrare azioni
					else echo "-</tr>";
					$admin_num++;
				}
				?>
			</tbody>
		</table>

		<h2 class="pointer">Utenti registrati&ensp;<img src="../image/button_top.svg" class="down" alt="Mostra e nascondi elementi" /></h2>
		<table>
			<thead>
				<tr>
					<th>Numero</th>
					<th>Avatar</th>
					<th>Nome</th>
					<th>Cognome</th>
					<th>Username</th>
					<th>Email</th>
					<th>Aggiunto il</th>
					<th>Azione</th>
				</tr>
			</thead>
			<tbody>
				<?php
				// Effettua la query negli utenti normali
				$user_num = 1;
				$normal_users = mysqli_query($conn, "SELECT * FROM users WHERE admin = 0 ORDER BY add_data ASC") or die("Connessione fallita: " . mysqli_error($conn));
				while ($result = mysqli_fetch_assoc($normal_users)) {
					$user_id = $result['id'];
					$user_avatar = $result['avatar'];
					$imageURL = '../image/' . $user_avatar;
					$user_nome = $result['nome'];
					$user_cognome = $result['cognome'];
					$user_username = $result['username'];
					$user_add_data = $result['add_data'];
					$user_email = $result['email'];
					echo "<tr>
							<td style='width:5%'>$user_num</td>
							<td><img src = '$imageURL' id='user_icon' alt='Avatar' style='width:100px; height:100px; margin-left:2%'/></td>
							<td>$user_nome</td>
							<td>$user_cognome</td>
							<td>
								<a href='account.php?username=$user_username'>$user_username</a>
							</td>
							<td><a href='mailto:$user_email'>$user_email</a></td>
							<td>$user_add_data</td>
							<td style='width:6%; text-align:center'>
								<a href='users.php?add_admin=$user_username'>
									<img class='icona statica approva' src='../image/plus.png' alt='Aggiungi utente come admin' title='Aggiungi utente come admin'>
									<img class='icona attiva approva' src='../image/plus.gif' alt='Aggiungi utente come admin' title='Aggiungi utente come admin'>
								</a>|
								<a href='users.php?prepare_delete_admin=$user_username'>
									<img class='icona statica delete' src='../image/trash_icon.png' alt='Elimina utente' title='Elimina utente'>
                                   	<img class='icona attiva delete' src='../image/trash_icon.gif' alt='Elimina utente' title='Elimina utente'>
								</a>
							</td>
						</tr>";
					$user_num++;
				}
				mysqli_close($conn);
				?>
			</tbody>
		</table>
	</main>

	<img src="../image/button_top.svg" id="button_top" alt="Vai all'inizio della pagina">

	<img src="../image/footer.svg" alt="Footer">
	<footer>
		<a href="../about.php">All rights reserved | © 2021 | Created by Marco Petrucci</a>
	</footer>

</body>

</html>