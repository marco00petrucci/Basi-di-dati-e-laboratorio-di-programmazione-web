<!DOCTYPE html>
<html lang="it">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Bloggy | <?php if (isset($_GET["testoCerca"])) echo $_GET["testoCerca"];
					else echo "Blog" ?></title>
	<style>
		@import url("index.css");
		@import url('https://fonts.googleapis.com/css2?family=Bungee&display=swap');
	</style>
	<link rel="icon" href="image/logo_icona.png" />
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
		<?php
		if (!isset($_GET['testoCerca'])) { ?>
			<h1>I BLOG DEL SITO:</h1>
		<?php
			$sql = "SELECT *, DATE_FORMAT(creato_il, '%W %d %M %Y, %H:%i') AS niceDate FROM blog ORDER BY nome_blog ASC";
			$blog_execution = mysqli_query($conn, $sql) or die("Connessione fallita: " . mysqli_error($conn));
			while ($result = mysqli_fetch_assoc($blog_execution)) {
				$cat_immagine = $result['immagine'];
				$cat_nome = $result['nome_blog'];
				$creato_il = $result['niceDate'];
				$cat_autore = $result['autore'];
				$cat_co_autore = $result['co_autore'];

				// Stampa i blog
				echo "<div class='blog' id='blog_$cat_nome'>
						<a href='blog.php?testoCerca=$cat_nome'>
							<img src='image/$cat_immagine' alt='Immagine blog'>
						</a>
						<h1>
							<a href='blog.php?testoCerca=$cat_nome' style='color:rgb(99, 10, 13)'>$cat_nome</a>
						</h1>";

				// Se esiste un co-autore
				if ($cat_co_autore != null) {
					echo "<p>
							<a href='reg-login/account.php?username=$cat_autore' style='color:rgb(99, 10, 13)'>
								$cat_autore
							</a> e 
							<a href='reg-login/account.php?username=$cat_co_autore' style='color:rgb(99, 10, 13)'>
								$cat_co_autore
							</a>  - " . ucfirst($creato_il) . "
						</p>
					</div>";
				} else
					echo "<p>
							<a href='reg-login/account.php?username=$cat_autore' style='color:rgb(99, 10, 13)'>
								$cat_autore
							</a> - " . ucfirst($creato_il) . "
						</p>
					</div>";
			}
		}

		if (isset($_GET['testoCerca']) && empty($_GET['testoCerca'])) {
			echo "<p id='risultati'>Nessun risultato! 🔎</p>";
		} else {
			if (isset($_GET['testoCerca'])) {

				// Se viene cercato un blog
				$blog_query = "SELECT DISTINCT nome_blog FROM blog WHERE nome_blog = '$_GET[testoCerca]'";
				$blog_execution = mysqli_query($conn, $blog_query) or die("Connessione fallita: " . mysqli_error($conn));
				$blog_row = mysqli_fetch_assoc($blog_execution);
				if (mysqli_num_rows($blog_execution) > 0) {
					$query = "SELECT *, DATE_FORMAT(p.creato_il, '%W %d %M %Y, %H:%i') AS niceDate FROM blog AS b, post AS p WHERE b.nome_blog = '$_GET[testoCerca]' AND b.nome_blog = p.nome_b ORDER BY p.creato_il DESC";
				}

				// Se viene cercato una categoria
				$cat_query = "SELECT DISTINCT categoria FROM post WHERE categoria = '$_GET[testoCerca]'";
				$cat_execution = mysqli_query($conn, $cat_query) or die("Connessione fallita: " . mysqli_error($conn));
				$cat_row = mysqli_fetch_assoc($cat_execution);
				if (mysqli_num_rows($cat_execution) > 0) {
					$query = "SELECT *, DATE_FORMAT(creato_il, '%W %d %M %Y, %H:%i') AS niceDate FROM post WHERE categoria = '$_GET[testoCerca]' ORDER BY creato_il DESC";
				}
				if (mysqli_num_rows($cat_execution) == 0 && mysqli_num_rows($blog_execution) == 0) {
					echo '<p id="risultati">Nessun risultato per: "' . $_GET['testoCerca'] . '"! 🔎</p>';
				} else {
					$execution = mysqli_query($conn, $query) or die("Connessione fallita: " . mysqli_error($conn));

					echo "<h1>$_GET[testoCerca]</h1>";
					if (!empty($execution)) {
						while ($riga = mysqli_fetch_assoc($execution)) {
							$id = $riga['id'];
							$data = $riga['niceDate'];
							$categoria = $riga['categoria'];
							$titolo = $riga['titolo'];
							$autore = $riga['autore'];
							$contenuto = $riga['contenuto'];
							$immagine = $riga['immagine'];

							// Stampa i post
							echo "<div class='post' id='post_$id'>
									<a href='single.php?id=$id' title='Visita il post \"$titolo\"!' style='color:rgb(99, 10, 13)'>
										<img src='image/$immagine' alt='Immagine post'>
										<h1>$titolo</h1>
						 			</a>
                            				<p>
										<a href='blog.php?testoCerca=$categoria' title='Visita il post \"$titolo\"!'>
									   		<b>$categoria</b>
									   	</a> - ";

							if (isset($_SESSION['user_session']) && $autore == $_SESSION['user_session']) echo "<a href='reg-login/account.php'>$autore</a> | ";
							else echo "<a href='reg-login/account.php?username=$autore'>$autore</a> | ";

							echo ucfirst($data) . " </p>";

							// Se i caratteri del contenuto sono superiori a 400 allora mostra solo quelli
							if (strlen($contenuto) > 400) {
								echo "<p>" . substr($contenuto, 0, 400) . '...</p><br>
                        			</div>';
							} else {
								echo "<p>$contenuto</p><br>
                        			</div>";
							}
						}
					} else echo "<p>Nessun post!</p>";
				}
			}
		}
		?>

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