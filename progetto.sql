--
-- Database: progetto
--
CREATE DATABASE IF NOT EXISTS my_bddbloggy DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE my_bddbloggy;

--
-- Struttura della tabella 'users'
--
CREATE TABLE IF NOT EXISTS users (
  id tinyint(3) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  admin bit(1) NOT NULL DEFAULT 0,
  add_data datetime NOT NULL,
  username varchar(100) NOT NULL UNIQUE KEY,
  nome varchar(100) DEFAULT NULL,
  cognome varchar(100) DEFAULT NULL,
  telefono varchar(100) DEFAULT NULL,
  email varchar(100) NOT NULL,
  password varchar(100) NOT NULL,
  avatar varchar(100) NOT NULL DEFAULT 'default.png',
  reso_admin_da varchar(100) DEFAULT NULL,
  FOREIGN KEY (reso_admin_da) REFERENCES users (username) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

--
-- Struttura della tabella 'blog'
--
CREATE TABLE IF NOT EXISTS blog (
  id tinyint(3) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  creato_il datetime NOT NULL,
  nome_blog varchar(100) NOT NULL UNIQUE KEY,
  autore varchar(100) NOT NULL,
  co_autore varchar(100) DEFAULT NULL,
  immagine varchar(100) NOT NULL,
  FOREIGN KEY (autore) REFERENCES users (username) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (co_autore) REFERENCES users (username) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

--
-- Struttura della tabella 'post'
--
CREATE TABLE IF NOT EXISTS post (
  id tinyint(3) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  creato_il datetime NOT NULL,
  nome_b varchar(100) NOT NULL,
  categoria varchar(100) NOT NULL,
  titolo varchar(200) NOT NULL,
  autore varchar(100) NOT NULL,
  contenuto mediumtext NOT NULL,
  immagine varchar(100) NOT NULL,
  FOREIGN KEY (autore) REFERENCES users (username) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (nome_b) REFERENCES blog (nome_blog) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

--
-- Struttura della tabella 'commenti'
--
CREATE TABLE IF NOT EXISTS commenti (
  id_c tinyint(3) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  data_c datetime NOT NULL,
  username varchar(100) NOT NULL,
  commento varchar(255),
  id_post tinyint(3) NOT NULL,
  stato bit(1) NOT NULL DEFAULT 1,
  approved_by varchar(100) DEFAULT NULL,
  FOREIGN KEY (username) REFERENCES users (username) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (id_post) REFERENCES post (id) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (approved_by) REFERENCES users (username) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

--
-- Struttura della tabella 'voti'
--
CREATE TABLE IF NOT EXISTS voti (
  id tinyint(3) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  username varchar(100) NOT NULL,
  voto tinyint(2) DEFAULT NULL,
  id_post tinyint(3) NOT NULL,
  FOREIGN KEY (username) REFERENCES users (username) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (id_post) REFERENCES post (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

--
-- Struttura della tabella 'messaggi'
--
CREATE TABLE IF NOT EXISTS messaggi (
  id tinyint(3) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  data_m datetime NOT NULL,
  nome varchar(100) NOT NULL,
  email varchar(100) NOT NULL,
  telefono varchar(15) DEFAULT NULL,
  messaggio varchar(255) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

--
-- Dump dei dati per la tabella 'users'
--
INSERT INTO users (id, admin, add_data, username, nome, cognome, telefono, email, password, avatar, reso_admin_da) VALUES
(1, b'1', '2021-07-01 15:00:00', 'Marcope', 'Marco', 'Petrucci', '3667493367', 'marco00petrucci@gmail.com', '$2y$10$xUtEB3Obqoxl0PojS/Ah7erTLWAG5J.ggUwR.25XAHfyUnjluu6o.', 'marcope.jpg', 'Marcope'),
(2, b'1', '2021-07-01 19:18:33', 'Ire', 'Irene', 'Franco', '3345673356', 'irenefranco@outlook.it', '$2y$10$jXDsmtkXCvtwCO3ClYxCy.LCkX.tG8aEGrA9uLyhnGOoSrDqE2ZI.', 'gatto.jpg', 'Marcope'),
(3, b'0', '2021-07-05 10:10:56', 'Ale', 'Alessandro', 'Petrucci', NULL, 'alepe@gmail.com', '$2y$10$1AxOouE79yORFqdHCpclfOf3pwKuE/0MkHwVVDHqfQGPdhydoyoC.', 'default.png', NULL),
(4, b'0', '2021-07-07 12:30:40', 'Roby', 'Roberta', 'Rossi', NULL, 'roby@gmail.com', '$2y$10$64NxDdGbUfnkEDw8wCyjqeKQvJBn8vMxstJUBx7Is1lj2a7sL5Lhy', 'default.png', NULL);

--
-- Dump dei dati per la tabella 'blog'
--
INSERT INTO blog (id, creato_il, nome_blog, autore, co_autore, immagine) VALUES
(1, '2021-07-01 15:30:00', 'Hip-hop', 'Marcope', 'Ire', 'rap.jpg'),
(2, '2021-07-01 17:00:00', 'Carnevale di Viareggio', 'Marcope', NULL, 'carnevale.jpg'),
(3, '2021-07-15 17:04:41', 'Tecnologia', 'Ire' , 'Ale', 'coding.jpg'),
(4, '2021-07-09 11:39:54', 'Sport', 'Ale', 'Marcope', 'sport.jpg');

--
-- Dump dei dati per la tabella 'post'
--
INSERT INTO post (id, creato_il, nome_b, categoria, titolo, autore, contenuto, immagine) VALUES
(1, '2021-07-01 15:32:00', 'Hip-Hop', 'Rap', "Emis Killa - L'erba cattiva", 'Marcope', "Anticipato il 6 gennaio dal primo singolo Cashwoman, L'erba cattiva ha esordito al quinto posto della classifica italiana degli album ed è rimasto nelle prime 35 posizioni per tutti i nove mesi successivi, raggiungendo il picco massimo del quarto posto.
Dal disco sono stati estratti anche i singoli Parole di ghiaccio, Dietro front e L'erba cattiva, oltre alla pubblicazione dei videoclip dei brani Sulla luna, Come un pitbull, Cocktailz e Il mondo dei grandi.
Nel giugno 2012 L'erba cattiva è stato certificato disco d'oro per aver raggiunto la soglia delle 30 000 copie vendute, mentre nel 2019 il singolo Parole di ghiaccio è stato certificato doppio disco di platino per aver raggiunto la soglia delle 100 000 copie vendute. Il 4 marzo 2013 l'album è stato certificato disco di platino per le oltre 60 000 copie vendute.", 'erba_cattiva.jpg'),
(2, '2021-07-02 18:00:03', 'Hip-Hop', 'Rap', 'Lazza - Zzala', 'Marcope', "Zzala è stato annunciato il 13 marzo 2017, con pubblicazione prevista per il mese successivo. Nel disco sono stati inseriti i brani DDA e Ouverture, pubblicati tempo addietro come presentazione di Lazza quale new entry di 333 Mob.
Il lavoro musicale del rapper milanese è stato apprezzato soprattutto per la versatilità dell'artista, che si destreggia tra sonorità trap, hip hop e della musica classica.
Il nome dell'album, altro non è che lo pseudonimo del rapper milanese in 'Riocontra' (Verlan).", 'zzala.jpg'),
(3, '2021-07-02 19:20:09', 'Hip-Hop', 'Rap americano', 'Travis Scott - Astroworld', 'Ire', "Astroworld (reso graficamente ASTROWORLD) è il terzo album in studio del rapper statunitense Travis Scott, pubblicato il 3 agosto 2018.
La copertina è stata scattata dal fotografo statunitense David LaChapelle, la quale presenta un gigantesco gonfiabile dorato raffigurante la testa di Scott come l'ingresso di un parco divertimenti, con bambini, genitori e dipendenti del parco di fronte all'entrata. Una seconda copertina presenta lo stesso ingresso del parco divertimenti notturno, che sostituisce le funzionalità per la famiglia con contenuti a tema per adulti. Il 1º agosto, la modella transgender Amanda Lepore, nota collaboratrice di LaChapelle, ha chiesto perché fosse stata esclusa nella versione finale della seconda cover, salvo poi spiegare lei stessa che la modifica era dovuta al fatto che la sua presenza mettesse in ombra le altre personalità presenti sulla copertina.", 'astroworld.jpg'),
(4, '2021-07-03 18:05:41', 'Carnevale di Viareggio', 'Storia', 'Storia del Carnevale di Viareggio', 'Marcope', "La tradizione della sfilata di carri (dapprima calessi) a Viareggio risale al 1873. La tradizione vuole che l'idea di una sfilata il giorno di martedì grasso del 1873 sia nata ai tavoli del Caffè del Casinò, inaugurato quarant'anni prima. Sul finire del secolo, comparvero i carri trionfali, veri e propri monumenti, costruiti in legno, scagliola e juta, modellati da scultori e messi insieme da carpentieri e fabbri che, in Darsena, sugli scali dei cantieri navali, sapevano creare straordinarie imbarcazioni.
La prima guerra mondiale indusse a una pausa bellica che durò sei anni. La manifestazione riprese nel 1921 e i carri sfilarono sui due Viali a mare.
Nel 1921 si cantò la prima canzone ufficiale, nota come la 'Coppa di Champagne', attuale inno del Carnevale, composta dal musicista Icilio Sadun su parole di Lelio Maffei. Quell’anno per la prima volta, anche i carri si animarono a suon di musica, perché la banda trovò posto a bordo della costruzione intitolata “Le nozze di Tonin di Burio” di Guido Baroni, che rappresentava la festa nuziale nell’aia di una casa colonica. Due anni dopo il carro del Pierrot fu la prima maschera a muovere la testa e gli occhi. Nel 1925 il pittore Antonio D’Arliano inventò la tecnica della carta a calco, che da allora ha consentito costruzioni colossali. Nel 1930 Uberto Bonetti, ideò Burlamacco: la maschera simbolo di Viareggio, che, nel manifesto del 1931, apparve in compagnia di Ondina, bagnante simbolo della stagione estiva. Fin dall’inizio (1954) la neonata Rai prima, e l’Eurovisione (1958) poi, hanno consacrato la grande manifestazione, trasportando ovunque, via etere Viareggio e il Carnevale.
Il 20 febbraio 1971 si svolse il primo Carnevale rionale della Darsena.
Oggi il Carnevale di Viareggio è un evento di fama internazionale. Ogni anno si svolgono i Corsi Mascherati, ovvero le sfilate dei carri allegorici nel periodo tra la fine di gennaio e l'inizio di marzo e vi partecipano oltre 600.000 spettatori.", 'bomba.jpg'),
(5, '2021-07-04 20:07:41', 'Carnevale di Viareggio', 'Tradizione', 'La cartapesta', 'Marcope', "La cartapesta è un preparato essenzialmente composto da acqua, colla, gesso e carta; il procedimento di lavorazione parte dalla creazione di un modello in argilla. Con una colata di gesso su questo modello si ottiene il negativo del calco, all'interno del quale vengono applicate le strisce di carta che sono state precedentemente imbevute in un composto di acqua e colla. Grazie a questo materiale i carristi riescono a plasmare masse e volumi molto grandi e, grazie alla leggerezza delle forme vuote, il carro è una struttura semovente spettacolare. Le strisce vengono poi fatte aderire al calco, che ha poi bisogno di molte ore per l'asciugatura.
In seguito si stacca il lavoro di cartapesta e, dopo averlo levigato con carta vetrata, si procede alla decorazione con colori acrilici o a tempera, che vengono ricoperti da un'ulteriore vernice lucida di protezione. Il primo carro di cartapesta fu realizzato a Viareggio, nel 1925: 'I cavalieri del Carnevale' di Antonio D'Arliano. Attualmente uno dei grandi maestri riconosciuti della cartapesta è Arnaldo Galli che insieme al fratello Renato e a Silvano Avanzini ha collaborato per la costruzione di materiali di scena in film di Federico Fellini come Casanova e Boccaccio '70, costruendo un'Anita Ekberg di misure enormi. Maschere in cartapesta dei maestri viareggini hanno fatto da cornice alla cerimonia di apertura dei Mondiali di calcio di Italia '90 ed a quella di chiusura dei XX Giochi olimpici invernali.", 'boiaccone.jpg'),
(6, '2021-07-05 08:03:21', 'Carnevale di Viareggio', 'Storia', 'La musica del Carnevale di Viareggio', 'Marcope', "La storia musicale viareggina affonda le proprie radici nei primi anni '20, quando le poesie dedicate alla città del Carnevale degli illustrissimi maestri quali Egisto Malfatti e Lelio Maffei furono messe in musica.
Quest'ultimo è autore di 'Maschereide', nella quale la maschera viene descritta come il mezzo per abbandonare sé stessi ed essere come la stessa canzone cita: 'il volto sincero dell'umanità'.
Durante questo periodo vengono esaltate le tematiche dei fiori e della primavera, simboli della rinascita viareggina dopo la prima Guerra Mondiale. Sono famose 'Carnevale in primavera' di Umberto Boni, 'Ballata a Viareggio' di Ermete Liberati, Michele Orselli e Mario Mariotti e 'La viareggina' del 1939, di Maria Teresa Marchionni.
A Viareggio il sentimento di rinascita dopo la guerra era molto forte tanto che, si racconta, si lanciavano coriandoli sulle buche lasciate dalle bombe pur di far Carnevale: 'Risorgi ancor più bella', di Gino Guidi è forse una delle musiche più emozionanti della storia della musica viareggina...
Viareggio risentì degli aiuti economici statunitensi tanto che, da questo momento risorse fino a meritarsi l'appellativo de 'La Perla del Tirreno', raccontata in numerose canzoni, soprattutto dal poeta viareggino Egisto Malfatti, l'artista che più di tutti ebbe il merito di rendere la tradizione musicale viareggina nota fuori dai confini cittadini. Egli infatti fu in grado di raccontare la 'Viareggio che fu', tramite numerose poesie e musiche, prime fa tutte: 'Città rosa' e 'Passeggiata Margherita'.
Nel corso degli ultimi anni la canzone viareggina è sempre rimasta sulla cresta dell'onda, spinta dalla voglia di giovani esordienti locali di raccontare il proprio territorio. A tenere particolarmente banco è il tema del divertimento, inevitabilmente interpretato secondo i ritmi odierni: adesso la tradizione musicale del Carnevale viareggino è portata avanti dal Festival di Burlamacco, organizzato dal movimento dei Carnevalari, un concorso canoro dove ogni anno vengono 'sfornate' canzoni nuove, che andranno ad aggiungersi al numero millenario delle musiche precedenti...", 'festival_di_burlamacco.jpg'),
(7, '2021-07-05 12:04:00', 'Tecnologia', 'Software', 'Il processore Core i7', 'Ale', "Core i7 è il nome commerciale di una serie di microprocessori x64 sviluppati da Intel e presentati il 17 novembre 2008.
La nuova architettura, comune anche al Core i7 Extreme, deriva in parte dalla 'Core' dei predecessori, ma Intel ha comunque dichiarato che le innovazioni apportate sono così tante che è assolutamente doveroso considerare il nuovo progetto come salto generazionale e non solo come un affinamento. Con il lancio della nuova architettura Intel ha deciso di utilizzare dei 'differenziatori' per distinguere i modelli destinati alle varie fasce di mercato. Di conseguenza alcuni core sono alla base di diversi processori differenti a seconda delle caratteristiche intrinseche di ciascun modello e quindi della fascia di mercato cui verrà destinato e inoltre, lo stesso nome commerciale viene utilizzato per indicare specifiche versioni di core differenti.", 'core_i7.png'),
(8, '2021-07-05 12:06:00', 'Hip-Hop', 'Rap', 'Nayt - Mood', 'Marcope', 'Mood è il quinto album in studio del rapper italiano Nayt, pubblicato il 4 dicembre 2020 dalla Jive Records, Sony Music e la VNT1 Records.', 'mood.jpg'),
(9, '2021-07-09 11:29:54', 'Tecnologia', 'Hardware', 'La RAM', 'Ire', "In elettronica e informatica, la RAM (acronimo dell'inglese Random Access Memory ovvero memoria ad accesso casuale in contrapposizione con la memoria ad accesso sequenziale) è un tipo di memoria volatile temporanea caratterizzata dal permettere l'accesso diretto a qualunque indirizzo di memoria con lo stesso tempo di accesso.
Nella memoria RAM vengono copiati (caricati) i programmi che la CPU deve eseguire. Una volta chiuso il programma, le modifiche effettuate, se non opportunamente salvate sul disco rigido o su altra memoria non volatile, verranno perse.
Per le sue caratteristiche, la RAM viene utilizzata come memoria primaria nei computer più comuni. Inoltre, si può utilizzare una porzione di RAM come RAM disk, ovvero trattarla come se fosse memoria secondaria, col vantaggio di avere prestazioni in lettura e scrittura enormemente più elevate, con tempi di accesso significativamente migliori.
Il tipo di memoria ad accesso diretto più comune attualmente è a stato solido, a lettura-scrittura e volatile, ma rientrano nel tipo di memoria ad accesso casuale la maggior parte dei tipi di ROM (memoria a sola lettura), la NOR Flash (un tipo di memoria flash), oltre a vari tipi di memorie informatiche utilizzate ai primordi dell'informatica e oggi non più utilizzate come ad esempio la memoria a nucleo magnetico.", 'ram.jpg'),
(10, '2021-07-15 17:04:41', 'Sport', 'Karate', 'La storia del Karate', 'Ire', "Si pensa che il karate sia nato in Cina ma la patria di quello moderno è l'isola di Okinawa (Giappone meridionale) dove larte del to-de, (arte cinese del combattimento), mescolandosi ad altre forme di combattimento locali del luogo, ha dato vita a quella di Okinawa-te. La prima figura leggendaria del karate risale al XVIII secolo: Kanga Sakugawa, uno dei signori di Okinawa che furono mandati in Cina per apprenderne la cultura e la scienza. È proprio a tale figura che si fanno risalire gli evidenti influssi della scuola del nord della Cina sul karate di Okinawa. L’iniziatore del karate moderno è Anko Itosu, mentre colui che lo ha diffuso in tutto il Giappone e nel mondo è Gichin Funakoshi. Nel 1922 Gichin si trasferisce a Tokyo fondando un nuovo stile: lo shotokan. Durante il periodo della 2° Guerra Mondiale alcuni adepti, fra cui Yoshitaka (il figlio di Funakoshi), iniziarono un tipo di karate diverso introducendo tecniche provenienti dalla boxe inglese, calci circolari, protezioni, ecc. Funakoshi non apprezzò i cambiamenti introdotti. Il karate che conosciamo noi oggi, per certi suoi aspetti, è forse più simile a quello praticato da Yoshitaka che non a quello di suo padre.", 'funakoshi.jpg'),
(11, '2021-07-15 18:04:30', 'Sport', 'Karate', 'La filosofia del Karate', 'Marcope', "Il kangi (simbolo in caratteri giapponesi) KARA-TE significa letteralmente 'mano vuota' e indica l'arte marziale nata ad Okinawa (isola dell'arcipelago Ryukyu a metà strada tra Cina e Giappone) che ha appunto la caratteristica, a differenza di molte altre arti orientali, di non comprendere armi nella sua pratica.
Il karate-ka (o praticante del karate) non è solo un atleta, come potrebbe esserlo un calciatore o un tennista, egli si pone come obiettivo lo studio e il perfezionamento personale mediante l'allenamento. Durante la pratica il karateka deve porsi l'obiettivo di ripetere una cosa cento volte, la cosa richiede uno sforzo notevole, ed è attraverso questo sforzo che il karateka cambia se stesso.", 'karategi.jpg'),
(12, '2021-08-01 12:39:54', 'Sport', 'Calcio', 'La Nazionale Italiana', 'Ale', "La nazionale di calcio dell'Italia è la selezione maggiore maschile di calcio della Federazione Italiana Giuoco Calcio, il cui nome ufficiale è nazionale A, e rappresenta l'Italia nelle varie competizioni ufficiali o amichevoli riservate a squadre nazionali.
È una delle nazionali di calcio più titolate del mondo. Gli Azzurri, infatti, annoverano nel loro palmarès quattro campionati mondiali (Italia 1934, Francia 1938, Spagna 1982 e Germania 2006, secondi al mondo dopo il Brasile e a pari merito con la Germania) e due campionati europei (Italia 1968 ed Europa 2020). In bacheca annovera inoltre un titolo olimpico (Berlino 1936) e due Coppe Internazionali (1927-1930 e 1933-1935), mentre nella FIFA Confederations Cup vanta come migliore risultato un terzo posto (2013).
È l'unica selezione, insieme a quella brasiliana, a essersi aggiudicata due titoli mondiali consecutivi (nelle edizioni del 1934 e del 1938), nonché una delle sei (con Uruguay, Inghilterra, Germania Ovest, Argentina e Francia) ad aver vinto il titolo mondiale tra le mura domestiche. Assieme alla Spagna e alla Francia è, inoltre, una delle tre nazionali vincitrici del titolo di campione d'Europa in un'edizione organizzata in casa.
Nella graduatoria FIFA, in vigore dall'agosto 1993, a maggio 2021 occupa il 7º posto; ha occupato il 1º posto più volte, nel novembre del 1993 e nel corso del 2007 (febbraio, aprile-giugno, settembre); il peggior piazzamento è stato invece il 21º posto dell'agosto 2018.", 'figc_italia.png'),
(13, '2021-08-01 13:36:52', 'Sport', 'Karate', 'Le cinture di Karate', 'Ale', "Le classificazioni per i kyū variano da federazione a federazione, ed esistono, presso alcune scuole, ulteriori cinture intermedie (bianca, bianco-gialla, gialla, gialla-arancione, arancione, arancione-verde, verde, verde-blu, blu, blu-marrone, marrone, marrone-nera). Dopo la cintura marrone si passa a cintura nera che rimane tale al raggiungimento di gradi superiori (dan), dal 1° in poi, anche se è possibile trovare federazioni che utilizzano la cintura bianco-rossa per il 6°, 7°, 8° dan e rossa per i 9° e 10° dan. L'ideogramma dan si trova anche nella parola shodan, che significa 'principiante', per dimostrare come l'aver impiegato alcuni anni per diventare cintura nera sia davvero poca cosa in confronto a tutti gli anni di allenamento che aspettano. Generalmente, le cinture si ottengono per esami fino al 5° dan, mentre dal 6° dan in poi, il grado viene assegnato solo per meriti speciali e non più in seguito ad esami, anche se il modo in cui vengono rilasciati i più alti gradi dan può variare da federazione a federazione. Per i gradi più elevati non viene valutata solamente la mera capacità tecnica raggiunta ma soprattutto le doti di esperienza, didattica, organizzazione, sviluppo e dedizione a quest'arte marziale.", 'cinture.jpg'),
(14, '2021-08-02 17:04:41', 'Tecnologia', 'Software', 'Windows 11', 'Ale', "Windows 11 (nome in codice Sun Valley) è un sistema operativo per personal computer, successore di Windows 10, prodotto dalla Microsoft Corporation come parte della famiglia di sistemi operativi Windows NT. La sua presentazione è avvenuta il 24 giugno 2021 con un evento dedicato sul sito ufficiale di Microsoft.
La sua uscita è prevista, per computer appena acquistati, a fine 2021, mentre tramite aggiornamento da Windows 10 (Windows Update) a metà 2022.
In questo momento, però, è disponibile una versione nel canale Dev e Beta del Windows Insider Program.", 'win11.png'),
(15, '2021-08-04 17:02:42', 'Carnevale di Viareggio', 'Tradizione', 'I rioni e i veglioni', 'Marcope', "Tutto il Carnevale è accompagnato da veglioni e feste in maschera che hanno origine antica, ben prima della nascita dei corsi mascherati. Negli anni '20 erano famosi i veglioni 'di colore', feste nelle quali le donne dovevano indossare un abito delle tinte indicate, mentre gli uomini indossavano lo smoking, gli addobbi, i coriandoli e le stelle filanti erano nei colori prescritti. Locali come il Principe di Piemonte, l'albergo Royal e il Cafè chantant Margherita sulla Passeggiata erano la sede ideale per questo tipo di feste e proprio in quest'ultimo locale iniziò nel 1932 la tradizione dei veglioni in costume con un 'ballo incipriato del Settecento'. Negli anni a venire si ricordano i veglioni de La Stampa, della Croce rossa e dei Lions, queste ultime associazioni senza scopo di lucro che spesso e volentieri partecipano attivamente anche oggi alla vita del Carnevale. Oggi i veglioni sono feste rionali durante i fine settimana dei corsi mascherati sul lungomare. Sono feste in strada accompagnate da musiche, maschere e tanto divertimento.", 'rione.jpg'),
(16, '2021-08-05 15:32:00', 'Hip-Hop', 'Trap', 'Vegas Jones - La Bella Musica', 'Marcope', "L'album è composto da 14 brani. Puertosol e Follia del mattino sono i due videoclip e singoli estratti dal disco: il primo il 3 luglio 2019, il secondo il 18 dicembre 2019. All'interno dell'album è presente una sola collaborazione, ovvero quella con Fabri Fibra. L'8 novembre parte la promozione del disco con firmacopie in tutta Italia.", 'bella_musica.jpg');

--
-- Dump dei dati per la tabella 'commenti'
--
INSERT INTO commenti (id_c, data_c, username, commento, id_post, stato, approved_by) VALUES
(1, '2021-07-04 21:20:23', 'Marcope', 'La musica migliore al mondo...', 6, 1, 'Ire'),
(2, '2021-07-17 18:23:30', 'Roby', 'Osu! 🤗🥋', 11, 1, 'Marcope'),
(3, '2021-07-18 16:22:24', 'Roby', 'ALBUM DI M****', 1, 0, 'Marcope'),
(4, '2021-07-20 16:23:53', 'Marcope', 'Grandi aspettative per il nuovo OS di Microsoft', 14, 1, NULL),
(5, '2021-07-23 18:00:03', 'Marcope', 'Il silenzio è oro, ma se lo sprechi è zero 🤐', 2, 1, 'Marcope'),
(6, '2021-08-05 17:32:00', 'Ire', 'Il migliore album di Veggie, Benz!🚗', 16, 1, 'Marcope'),
(7, '2021-08-06 18:34:56', 'Ale', 'Ogni palma che vedi è nata dal cemento 🌴', 16, 1, 'Marcope'),
(8, '2021-08-29 11:54:20', 'Ire', 'Spero di non rimanere delusa dal nuovo aggiornamento...', 14, b'1', NULL);

--
-- Dump dei dati per la tabella 'voti'
--
INSERT INTO voti (id, username, voto, id_post) VALUES
(1, 'Marcope', '3', 6),
(2, 'Roby', '4', 11),
(3, 'Roby', '2', 1),
(4, 'Marcope', '5', 14),
(5, 'Marcope', '4', 2),
(6, 'Ire', '5', 16),
(7, 'Ale', '4', 16),
(8, 'Ire', '5', 3);

--
-- Dump dei dati per la tabella 'messaggi'
--
INSERT INTO messaggi (id, data_m, nome, email, telefono, messaggio) VALUES
(1, '2021-07-21 10:24:55', 'Elena Verdi', 'qualcosa@ealtro.com', '058456372', 'Ciao! come state? Volevo sapere come devo fare per iscrivermi al sito.'),
(2, '2021-07-21 10:27:38', 'Mario Rossi', 'mario@00.com', '0123456789', 'Bel sito! Complimenti agli sviluppatori!'),
(3, '2021-07-12 13:07:01', 'Roberta Bianchi', 'Ire@sl.Ire', '0987654321', 'Per favore voglio diventare amministratore');