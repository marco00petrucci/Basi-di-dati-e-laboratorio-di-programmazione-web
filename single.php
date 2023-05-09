<?php
session_start();
require('db_connect.php');

if (isset($_SESSION['user_session'])) $username = $_SESSION['user_session'];

if (isset($_GET['id'])) {
	$id_post = $_GET['id'];
	$query = "SELECT * FROM post WHERE id = '$id_post'";
	$execution = mysqli_query($conn, $query) or die("Connessione fallita: " . mysqli_error($conn));
	$check_titolo = mysqli_fetch_assoc($execution);
	$post_titolo = $check_titolo['titolo'];
}
?>

<!DOCTYPE html>
<html lang="it">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Bloggy | <?php echo $post_titolo; ?></title>
	<style>
		@import url("index.css");

		main {
			margin-top: 150px
		}
	</style>
	<link rel="icon" href="image/logo_icona.png" />
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.js"></script>
	<script src="funzioni.js"></script>
	<script src="https://code.createjs.com/1.0.0/createjs.min.js"></script>
	<script src="sfondo.js"></script>
	<script>
		$(function() {

			$("#stella_5").hover(function() {
				$('#stella_1, #stella_2, #stella_3, #stella_4, #stella_5').css("content", "url('image/star.png'");
			}, function() {
				$('#stella_1, #stella_2, #stella_3, #stella_4, #stella_5').css("content", "url('image/star_bianca.png'");
			});

			$("#stella_4").hover(function() {
				$('#stella_1, #stella_2, #stella_3, #stella_4').css("content", "url('image/star.png'");
			}, function() {
				$('#stella_1, #stella_2, #stella_3, #stella_4').css("content", "url('image/star_bianca.png'");
			});

			$("#stella_3").hover(function() {
				$('#stella_1, #stella_2, #stella_3').css("content", "url('image/star.png'");
			}, function() {
				$('#stella_1, #stella_2, #stella_3').css("content", "url('image/star_bianca.png'");
			});

			$("#stella_2").hover(function() {
				$('#stella_1, #stella_2').css("content", "url('image/star.png'");
			}, function() {
				$('#stella_1, #stella_2').css("content", "url('image/star_bianca.png'");
			});

			$("#stella_1").hover(function() {
				$('#stella_1').css("content", "url('image/star.png'");
			}, function() {
				$('#stella_1').css("content", "url('image/star_bianca.png'");
			});

			// Disabilita il bottone 'invia commento' se il commento è vuoto
			$('#commento').keyup(function() {
				if ($(this).val().length != 0) $('#invia_commento').css({
					'pointer-events': "auto",
					"opacity": "1"
				});
				else $('#invia_commento').css({
					'pointer-events': "none",
					"opacity": ".7"
				});
			});

			// Gestire la convalida del form per modificare il commento
			$("#box_commenta").validate({
				rules: {
					commento: "required"
				},
				messages: {
					commento: "<p class='messaggi'>Per favore inserisci un commento.</p>"
				},
				submitHandler: aggiungi_commento
			});

			// Gestire il modulo di invio della modifica del commento
			function aggiungi_commento() {
				$.ajax({
					type: 'POST',
					url: 'reg-login/check_reg-login.php',
					data: $("#box_commenta").serialize(),
					success: function(risposta) {

						// Se si modifica il commento, reinderizza al post in questione
						if (risposta == "Aggiunto") {
							$("#box_commenta #error").fadeIn(400).html("&ensp;Commento inserito! 😄");
							$("#invia_commento").html('<img src="image/ajax-loader.gif" alt="Loader" /><img src="image/invia.png" alt="Invio commento" width="25px"/>');
							$('body').delay(1300).fadeOut();
							setTimeout('window.location.reload()', 1600);
						}

						// Se sono stati inseriti caratteri tipici della SQL Injection
						else if (risposta == "Dati corrotti") $("#box_commenta #error").fadeIn(400).html('&ensp;<img src="image/danger.svg" alt="Errore" width="20px" height="20px" >&nbsp;No dati corrotti su questo sito!');

						// Se qualcosa è andato storto
						else $("#box_commenta #error").fadeIn(400).html('&ensp;<img src="image/danger.svg" alt="Errore" width="20px" height="20px" >&nbsp;Qualcosa è andato storto! 😓');
					}
				});
				return false;
			}

			// Gestire la convalida del form per modificare il commento
			$("form#modifica_commento").validate({
				rules: {
					nuovo_commento: "required"
				},
				messages: {
					nuovo_commento: "<p class='messaggi'>Per favore inserisci un commento.</p>"
				},
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
		<article id="post&commenti">

			<!-- Visualizza il post -->
			<?php
			if (isset($_GET['id'])) {
				$query = "SELECT *, DATE_FORMAT(creato_il, '%W %d %M %Y, %H:%i') AS niceDate FROM post WHERE id = '$_GET[id]'";
				$execution = mysqli_query($conn, $query) or die("Connessione fallita: " . mysqli_error($conn));
				if (mysqli_num_rows($execution) > 0) {
					while ($result = mysqli_fetch_assoc($execution)) {
						$p_id = $result['id'];
						$data = $result['niceDate'];
						$categoria = $result['categoria'];
						$titolo = $result['titolo'];
						$autore = $result['autore'];
						$contenuto = $result['contenuto'];
						$immagine = $result['immagine'];

						// Stampa il post
						echo "<div class='post' id='post_$p_id'>
								<img src='image/$immagine' alt='Immagine post'>
								<h1>$titolo</h1>
								<p>
									<a href='blog.php?testoCerca=$categoria' style='color:rgb(99, 10, 13)'>
										<b>$categoria</b>
									</a> - ";

						if (isset($_SESSION['user_session']) && $autore == $_SESSION['user_session']) echo "<a href='reg-login/account.php' style='color:rgb(99, 10, 13)'>$autore</a> | ";
						else echo "<a href='reg-login/account.php?username=$autore' style='color:rgb(99, 10, 13)'>$autore</a> | ";
						echo ucfirst($data) . "</p>
							<p>$contenuto</p><br>
					 	</div>";
					}
				}
			}
			?>

			<!-- Visualizza commenti e il voto medio -->
			<div id="sezione_commenti">
				<h3 style="margin-top: 0;">COMMENTI E VOTI
					<span style='float:right; margin-right:40px'>
						<?php
						// Valore medio delle votazioni
						$query = mysqli_query($conn, "SELECT AVG(v.voto) AS AVGRATE FROM voti AS v, post AS p WHERE p.id = '$_GET[id]' AND v.id_post = p.id") or die("Connessione fallita: " . mysqli_error($conn));
						$row = mysqli_fetch_array($query);
						echo "Voto medio: " . round($row['AVGRATE'], 1) . "<img src='image/star.png' title='Voto medio: " . round($row['AVGRATE'], 1) . "' width=22px alt='stella' style='vertical-align:top' />";
						?>
					</span>
				</h3>

				<!-- Visualizza commenti ed eventuale voto -->
				<?php
				$execution = mysqli_query($conn, "SELECT *, DATE_FORMAT(data_c, '%W %d %M %Y - alle ore %H:%i') AS niceDate, u.avatar FROM commenti AS c, post AS p, users AS u WHERE c.stato = 1 AND p.id = c.id_post AND p.id = '$_GET[id]' AND c.username = u.username ORDER BY c.data_c") or die("Connessione fallita: " . mysqli_error($conn));
				if (mysqli_num_rows($execution) > 0) {
					while ($commento = mysqli_fetch_assoc($execution)) {
						$id_c = $commento['id_c'];
						$c_avatar = $commento['avatar'];
						$c_nome = $commento['username'];
						$c_data = $commento['niceDate'];
						$c_commento = $commento['commento'];
						$id_post = $commento['id'];

						echo "<div class='show_commento' id='$id_c'>";
						if (isset($_SESSION['user_session']) && $c_nome == $_SESSION['user_session']) {
							echo "<a href='reg-login/account.php'>
									<img src='image/$c_avatar' id='c_avatar' alt='Avatar'>
									<p>$c_nome
								</a>";
						} else {
							echo "<a href='reg-login/account.php?username=$c_nome'>
									<img src='image/$c_avatar' id='c_avatar' alt='Avatar'>
									<p>$c_nome
								</a>";
						}
						echo " | " . ucfirst($c_data) . "</p>";

						// Se si sceglie di modificare il commento, permetti di inserire il nuovo testo
						if (isset($_GET['modifica_commento']) && $_GET['modifica_commento'] == $id_c) {
							echo "<form method='post' id='modifica_commento'>
									<textarea class='commenta_box' name='nuovo_commento' id='nuovo_commento' cols='20' rows='1' style='margin:0 1% 0 0; opacity:.7'>$c_commento</textarea>
									
									<input type='hidden' value='$id_c' name='id_c'>
									<input type='hidden' value='$c_commento' name='vecchio_commento'>

									<button type='submit' class='button_form conferma_edit_items' name='edit_commento' id='edit_commento'>Cambia</button>
									<a href='single.php?id=$_GET[id]' class='button_form' id='annulla_edit' style='padding: 4px 5px; font-size:.8em'>
										<img src='image/meno.png' class='button_icon' alt='Annulla' style='vertical-align: middle'>Annulla
									</a>
									<div id='error'></div>
								</form>";
						} else {
							// Se non si sceglie di modificare il commento, visualizzalo
							echo "<p>$c_commento</p>
									<span style='float:right; margin:-60px 5% 0 0; color:#fff'>";

							// Verifica se l'utente che ha commentato ha anche votato il post
							$voto_sql = "SELECT v.voto FROM post AS p, voti AS v WHERE p.id = '$p_id' AND '$c_nome' = v.username AND '$p_id' = v.id_post";
							$voto_execution = mysqli_query($conn, $voto_sql) or die("Connessione fallita: " . mysqli_error($conn));
							if (mysqli_num_rows($voto_execution) > 0) {
								$voto_fetch = mysqli_fetch_assoc($voto_execution);
								$voto = $voto_fetch['voto'];
								echo "<p style='margin:0; float:left'>Voto: $voto</p>
										<img src='image/star.png' width=20px style='vertical-align:bottom' alt='stella'/>";
							} else echo "<p style='margin:0; float:left'>Nessun voto&ensp;</p>";

							if (isset($_SESSION['user_session']) && $c_nome == $_SESSION['user_session']) {
								echo "<a href='single.php?id=$_GET[id]&modifica_commento=$id_c#sezione_commenti' id='modifica_commento'>
										<img class='icona statica edit commenti' src='image/edit_pencil.png' alt='Modifica commento' title='Modifica commento'>
										<img class='icona attiva edit commenti' src='image/edit_pencil.gif' alt='Modifica commento' title='Modifica commento'>
									</a>
									<a href='single.php?id=$_GET[id]&elimina_commento=$id_c#sezione_commenti' id='elimina_commento'>
										<img class='icona statica delete commenti' src='image/trash_icon.png' alt='Elimina commento' title='Elimina commento'>
										<img class='icona attiva delete commenti' src='image/trash_icon.gif' alt='Elimina commento' title='Elimina commento'>
									</a>";
							}
							echo "</span>
								<div id='error'></div>";
						}
						echo "</div>";
					}
				}

				// Se non ci sono commenti
				else echo "<span style='color: #000;'>Ancora nessun commento.</span>";


				// Se viene scelto di modificare un commento
				if (isset($_POST['edit_commento'])) {
					$vietate = array("SELECT", "INSERT", "UPDATE", "DELETE", "DROP", "ALTER", "<script>");
					$nuovo_commento = mysqli_real_escape_string($conn, $_POST['nuovo_commento']);

					$nuovo_commento_ok = true;
					for ($k = 0; $k <= 6 && $nuovo_commento_ok; $k++) {
						if (stripos($nuovo_commento, $vietate[$k]) !== false) $nuovo_commento_ok = false;
					}

					if ($nuovo_commento_ok) {
						$sql = "UPDATE commenti SET commento = '$nuovo_commento' WHERE id_c = $_POST[id_c]";
						$execution = mysqli_query($conn, $sql) or die("Connessione fallita: " . mysqli_error($conn));
						if ($execution) {
							echo "<script>
									$('#$_POST[id_c] #error').fadeIn(400).html(\"&ensp;<img src='image/ajax-loader.gif' alt='Loader' />&ensp;Commento modificato! 😁\");
									$('form#modifica_commento, #$_POST[id_c] #error').delay(2200).fadeOut();
									setTimeout(\"window.location.href = 'single.php?id=$_GET[id]#sezione_commenti'\", 2500);
								</script>";
						}
					} else {
						echo "<script>
								$('#$_POST[id_c] #error').fadeIn(400).html('&ensp;Sono stati inseriti dati corrotti! 👨🏼‍💻');
								$('#$_POST[id_c] #error').delay(2200).fadeOut();
							</script>";
					}
				}

				// Se viene scelto di eliminare un commento
				if (isset($_GET['elimina_commento'])) {
					$sql = "DELETE FROM commenti WHERE id_c = $_GET[elimina_commento]";
					$execution = mysqli_query($conn, $sql) or die("Connessione fallita: " . mysqli_error($conn));
					if ($execution) {
						echo "<script>
								$('#$_GET[elimina_commento] #error').fadeIn(400).html(\"&ensp;<img src='image/ajax-loader.gif' alt='Loader' />&ensp;Commento eliminato! 🗑\");
								$('form#modifica_commento, #$_GET[elimina_commento] #error').delay(2200).fadeOut();
								setTimeout(\"window.location.href = 'single.php?id=$_GET[id]#sezione_commenti'\", 2500);
							</script>";
					}
				}
				?>

				<?php
				// Se si è loggati si può sia votare che commentare
				if (isset($_SESSION['user_session'])) { ?>
					<fieldset style="background: linear-gradient(to bottom, rgba(194,194,194,1) 0%, rgba(194,194,194,1) 50%, rgba(255,255,255,.1) 100%); border-radius:10px">
						<legend>Commenta e vota il post!</legend>

						<!-- VOTA IL POST -->
						<div id="box_voto">
							<?php
							// Scopri se l'utente ha già votato il post
							$sql = "SELECT v.voto FROM post AS p, voti AS v WHERE p.id = v.id_post AND p.id = '$p_id' AND v.username = '$_SESSION[user_session]'";
							$execution = mysqli_query($conn, $sql) or die("Connessione fallita: " . mysqli_error($conn));
							if (mysqli_num_rows($execution) > 0) {
								$voto_us = mysqli_fetch_assoc($execution);
								$voto_utente = $voto_us['voto'];
							}

							// Se l'utente loggato ha votato il post permetti di modificarlo o eliminarlo
							if (mysqli_num_rows($execution) > 0 && $voto_utente != NULL) { ?>
								<p style='margin-bottom:.5%'>Modifica il tuo voto:
									<!-- Permetti di eliminare il voto -->
									<a href="single.php?id=<?php echo $_GET['id'] ?>&rate=0#box_voto" id="elimina_voto">
										<img src='image/meno.png' title='Cancella voto' width=19px alt='Cancella voto' style="vertical-align:bottom;" />
									</a>
								</p>
								<?php
								switch ($voto_utente) {
										// Se l'utente loggato ha dato il voto "Pessimo"
									case 1: ?>
										<a href="single.php?id=<?php echo $_GET['id'] ?>&rate=1#box_voto">
											<img src='image/star.png' class='stella' title='Pessimo' width=19px alt='stella' />
										</a>
										<a href="single.php?id=<?php echo $_GET['id'] ?>&rate=2#box_voto">
											<img src='image/star_bianca.png' class='stella' id='stella_2' title='Non mi piace' width=19px alt='stella' />
										</a>
										<a href="single.php?id=<?php echo $_GET['id'] ?>&rate=3#box_voto">
											<img src='image/star_bianca.png' class='stella' id='stella_3' title='Buono' width=19px alt='stella' />
										</a>
										<a href="single.php?id=<?php echo $_GET['id'] ?>&rate=4#box_voto">
											<img src='image/star_bianca.png' class='stella' id='stella_4' title='Discreto' width=19px alt='stella' />
										</a>
										<a href="single.php?id=<?php echo $_GET['id'] ?>&rate=5#box_voto">
											<img src='image/star_bianca.png' class='stella' id='stella_5' title='Ottimo' width=19px alt='stella' />
										</a>
									<?php
										break;
										// Se l'utente loggato ha dato il voto "Non mi piace"
									case 2: ?>
										<a href="single.php?id=<?php echo $_GET['id'] ?>&rate=1#box_voto">
											<img src='image/star.png' class='stella' title='Pessimo' width=19px alt='stella' />
										</a>
										<a href="single.php?id=<?php echo $_GET['id'] ?>&rate=2#box_voto">
											<img src='image/star.png' class='stella' title='Non mi piace' width=19px alt='stella' />
										</a>
										<a href="single.php?id=<?php echo $_GET['id'] ?>&rate=3#box_voto">
											<img src='image/star_bianca.png' class='stella' id='stella_3' title='Buono' width=19px alt='stella' />
										</a>
										<a href="single.php?id=<?php echo $_GET['id'] ?>&rate=4#box_voto">
											<img src='image/star_bianca.png' class='stella' id='stella_4' title='Discreto' width=19px alt='stella' />
										</a>
										<a href="single.php?id=<?php echo $_GET['id'] ?>&rate=5#box_voto">
											<img src='image/star_bianca.png' class='stella' id='stella_5' title='Ottimo' width=19px alt='stella' />
										</a>
									<?php
										break;
										// Se l'utente loggato ha dato il voto "Buono"
									case 3: ?>
										<a href="single.php?id=<?php echo $_GET['id'] ?>&rate=1#box_voto">
											<img src='image/star.png' class='stella' title='Pessimo' width=19px alt='stella' />
										</a>
										<a href="single.php?id=<?php echo $_GET['id'] ?>&rate=2#box_voto">
											<img src='image/star.png' class='stella' title='Non mi piace' width=19px alt='stella' />
										</a>
										<a href="single.php?id=<?php echo $_GET['id'] ?>&rate=3#box_voto">
											<img src='image/star.png' class='stella' title='Buono' width=19px alt='stella' />
										</a>
										<a href="single.php?id=<?php echo $_GET['id'] ?>&rate=4#box_voto">
											<img src='image/star_bianca.png' class='stella' id='stella_4' title='Discreto' width=19px alt='stella' />
										</a>
										<a href="single.php?id=<?php echo $_GET['id'] ?>&rate=5#box_voto">
											<img src='image/star_bianca.png' class='stella' id='stella_5' title='Ottimo' width=19px alt='stella' />
										</a>
									<?php
										break;
										// Se l'utente loggato ha dato il voto "Discreto"
									case 4: ?>
										<a href="single.php?id=<?php echo $_GET['id'] ?>&rate=1#box_voto">
											<img src='image/star.png' class='stella' title='Pessimo' width=19px alt='stella' />
										</a>
										<a href="single.php?id=<?php echo $_GET['id'] ?>&rate=2#box_voto">
											<img src='image/star.png' class='stella' title='Non mi piace' width=19px alt='stella' />
										</a>
										<a href="single.php?id=<?php echo $_GET['id'] ?>&rate=3#box_voto">
											<img src='image/star.png' class='stella' title='Buono' width=19px alt='stella' />
										</a>
										<a href="single.php?id=<?php echo $_GET['id'] ?>&rate=4#box_voto">
											<img src='image/star.png' class='stella' title='Discreto' width=19px alt='stella' />
										</a>
										<a href="single.php?id=<?php echo $_GET['id'] ?>&rate=5#box_voto">
											<img src='image/star_bianca.png' class='stella' id='stella_5' title='Ottimo' width=19px alt='stella' />
										</a>
									<?php
										break;
										// Se l'utente loggato ha dato il voto "Ottimo"
									case 5: ?>
										<a href="single.php?id=<?php echo $_GET['id'] ?>&rate=1#box_voto">
											<img src='image/star.png' class='stella' title='Pessimo' width=19px alt='stella' />
										</a>
										<a href="single.php?id=<?php echo $_GET['id'] ?>&rate=2#box_voto">
											<img src='image/star.png' class='stella' title='Non mi piace' width=19px alt='stella' />
										</a>
										<a href="single.php?id=<?php echo $_GET['id'] ?>&rate=3#box_voto">
											<img src='image/star.png' class='stella' title='Buono' width=19px alt='stella' />
										</a>
										<a href="single.php?id=<?php echo $_GET['id'] ?>&rate=4#box_voto">
											<img src='image/star.png' class='stella' title='Discreto' width=19px alt='stella' />
										</a>
										<a href="single.php?id=<?php echo $_GET['id'] ?>&rate=5#box_voto">
											<img src='image/star.png' class='stella' title='Ottimo' width=19px alt='stella' />
										</a>
								<?php
										break;
								}
							}

							// Se l'utente loggato non ha votato il post permetti di aggiungerne uno
							else { ?>
								<p style='margin-bottom:.5%'>Aggiungi un voto:</p>
								<a href="single.php?id=<?php echo $_GET['id'] ?>&rate=1#box_voto">
									<img src='image/star_bianca.png' class='stella' id='stella_1' title='Pessimo' width=19px alt='stella' />
								</a>
								<a href="single.php?id=<?php echo $_GET['id'] ?>&rate=2#box_voto">
									<img src='image/star_bianca.png' class='stella' id='stella_2' title='Non mi piace' width=19px alt='stella' />
								</a>
								<a href="single.php?id=<?php echo $_GET['id'] ?>&rate=3#box_voto">
									<img src='image/star_bianca.png' class='stella' id='stella_3' title='Buono' width=19px alt='stella' />
								</a>
								<a href="single.php?id=<?php echo $_GET['id'] ?>&rate=4#box_voto">
									<img src='image/star_bianca.png' class='stella' id='stella_4' title='Discreto' width=19px alt='stella' />
								</a>
								<a href="single.php?id=<?php echo $_GET['id'] ?>&rate=5#box_voto">
									<img src='image/star_bianca.png' class='stella' id='stella_5' title='Ottimo' width=19px alt='stella' />
								</a>
							<?php
							}
							?>
							<div id='error'></div>
							<?php
							// Se si sceglie di votare il post inserisci i dati nel database
							if (isset($_GET['rate'])) {

								// Se si sceglie di eliminare il voto
								if (isset($voto_utente) && $voto_utente != NULL && $_GET['rate'] == 0) {
									$sql = "DELETE FROM voti WHERE username = '$username' AND id_post = $_GET[id]";
									$messaggio = 'eliminato! 🗑';
								}

								// Se si sceglie di aggiornare il voto
								else if (isset($voto_utente) && $voto_utente != NULL) {
									$sql = "UPDATE voti SET voto = $_GET[rate] WHERE username = '$username' AND id_post = $_GET[id]";
									$messaggio = 'aggiornato! 😄';
								}

								// Se si sceglie di aggiungere un voto
								else {
									$sql = "INSERT INTO voti (username, voto, id_post) VALUES ('$username', $_GET[rate], $_GET[id])";
									$messaggio = 'inserito! 😄';
								}

								$execution = mysqli_query($conn, $sql) or die("Connessione fallita: " . mysqli_error($conn));
								if ($execution) {
									echo '<script>
											$("#box_voto #error").fadeIn(400).html("&ensp;Voto ' . $messaggio . '");
											$("body").delay(1300).fadeOut();
											setTimeout(\'window.location.href = "single.php?id=' . $_GET['id'] . '#box_voto"\', 1600);
										</script>';
								}
							} ?>
						</div>

						<!-- COMMENTA IL POST -->
						<?php
						// Carica l'avatar dell'utente attivo nella sessione
						$sql = "SELECT avatar FROM users WHERE username = '$username'";
						$add_exec = mysqli_query($conn, $sql) or die("Connessione fallita: " . mysqli_error($conn));
						$add_commento = mysqli_fetch_assoc($add_exec);
						echo "<img src='image/$add_commento[avatar]' id='addc_avatar' alt='Avatar'>";
						?>

						<form method="POST" action="reg-login/check_reg-login.php" name="box_commenta" id="box_commenta">
							<div style="display:inline; position:relative">
								<textarea name="commento" id="commento" class="commenta_box" placeholder="Scrivi il commento..." cols="20" rows="1" style="margin-bottom: 0;"></textarea>
								<button id="invia_commento" name="invia_commento">
									<img src="image/invia.png" alt="invia messaggio" width="25px">
								</button>
								<div id='error'></div>

								<input type="hidden" name="id_post" value="<?php echo $_GET['id']; ?>">
							</div>
						</form>
					</fieldset>
				<?php
				}

				// Se non si è loggati non si può nè votare nè commentare
				else echo "<hr style='margin-top:3%; border: solid 1px #000'>
						<p style='margin-left:2%'>Devi essere loggato per poter commentare! <a href='reg-login/reg-login.php?registrazione'>Registrati</a> o effettua il <a href='reg-login/reg-login.php?login'>login</a>!😉</p>";
				?>
			</div>
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
</body>

</html>