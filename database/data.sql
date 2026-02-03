-- ==================================================
-- POPOLAMENTO DATABASE InfuseMe
-- ==================================================
SET NAMES utf8mb4;
SET character_set_client = utf8mb4;
SET character_set_connection = utf8mb4;
SET character_set_database = utf8mb4;
SET character_set_results = utf8mb4;

USE db_InfuseMe;

-- ================================================================
-- 1. ADMIN
-- ================================================================
INSERT INTO admin (nome, cognome, email, password_hash) VALUES
('admin', 'admin', 'admin', '$2y$10$JvlWI2oZtRGXfKndZ9llvutnj65ctbCdZFrP370L7vRucl5Aa3i5W');

-- ================================================================
-- 2. UTENTE
-- ================================================================
INSERT INTO utente (email, password_hash, nome, cognome, data_nascita, indirizzo, citta, CAP) VALUES
('user', '$2a$12$gr2DNHPznCImVG0ad8sTQ.q0unaiK/M2DCSQFyzf8ffLTbsFIDOC.', 'user', 'user', '1975-01-02', 'Via Luzzati', 'Padova', '35121');

-- ================================================================
-- 3. BASI
-- ================================================================
INSERT INTO base (nome, descrizione, img_path, temperatura_infusione, tempo_infusione) VALUES
('Tè Bianco', 'Il più puro e delicato tra i tè. Foglie giovani, minima ossidazione. Infuso giallo pallido, sapore dolce e fresco, ricorda miele e fiori di campo. Ricchissimo di antiossidanti.', 'images/ingredienti/teBianco.jpeg', '65-70 °C', '1-2 minuti'),
('Tè Giallo', 'Tè raro e pregiato, leggermente fermentato. Sapore morbido, vellutato e fruttato. Ideale per chi cerca una tisana sofisticata e meditativa.', 'images/ingredienti/teGiallo.jpeg', '70-75 °C', '1-2 minuti'),
('Tè Verde', 'Non ossidato, colore verde brillante. Sapore erbaceo, vegetale, a volte tostato. Perfetto per una tisana energizzante e purificante.', 'images/ingredienti/teVerde.jpeg', '75-80 °C', '1-2 minuti'),
('Tè Oolong', 'Parzialmente ossidato, sapori che vanno dal floreale al tostato. Retrogusto persistente. Ideale per tisane complesse e intriganti.', 'images/ingredienti/teOolong.jpeg', '80-85 °C', '2-3 minuti'),
('Tè Nero', 'Completamente ossidato, infuso ambrato e sapore intenso, maltato e speziato. Perfetto per tisane energiche e da abbinare a latte o spezie.', 'images/ingredienti/teNero.jpeg', '95-100 °C', '2-3 minuti'),
('Tisane (Erbe)', 'Miscela senza teina: camomilla, rooibos, ibisco, menta, zenzero e altre erbe. Benefici rilassanti, digestivi, immunitari.', 'images/ingredienti/teErbe.jpeg', '95-100 °C', '3-6 minuti'),
('Rooibos', 'Infusione dolce e leggermente terrosa, note di nocciola e vaniglia. Adatta a tutti, anche ai bambini.', 'images/ingredienti/teRooibos.jpeg', '90-95 °C', '5-8 minuti');

-- ================================================================
-- 4. INGREDIENTI
-- ================================================================
-- FRUTTI E BACCHE
INSERT INTO ingrediente (nome, descrizione, tipo, disponibile, img_path) VALUES
('Arancia', 'Apporta una nota agrumata, acidula e leggermente amara. L''olio essenziale contenuto nella scorza rilascia un aroma intenso.', 'frutto', 100, 'images/ingredienti/arancia.webp'),
('Bacche di Goji', 'Di sapore debolmente dolce e acidulo, con un retrogusto terroso.', 'frutto', 100, 'images/ingredienti/goji.webp'),
('Frutto della Passione', 'Il suo sapore è prevalentemente acido e fruttato.', 'frutto', 100, 'images/ingredienti/passionFruit.webp'),
('Ribes', 'Caratterizzati da un''alta acidità e astringenza. Il loro sapore dolce è molto tenue e necessita spesso di essere bilanciato.', 'frutto', 100, 'images/ingredienti/ribes.webp'),
('Mele', 'Conferiscono un sapore dolce e neutro, aggiungendo principalmente corpo all''infusione.', 'frutto', 100, 'images/ingredienti/mela.webp'),
('Ananas', 'Dolcezza marcata con note tropicali. Tende a rilasciare zuccheri nell''infusione, addolcendo naturalmente la bevanda.', 'frutto', 100, 'images/ingredienti/ananas.webp'),
('Fichi', 'Apportano una dolcezza intensa e un sapore di frutta cotta.', 'frutto', 100, 'images/ingredienti/fico.webp'),
('Lampone', 'Dolce e leggermente acidulo, con note fruttate fresche che rendono l''infusione vivace.', 'frutto', 100, 'images/ingredienti/lampone.webp'),
('Albicocca', 'Morbida e vellutata, dona un sapore dolce e fruttato senza appesantire la bevanda.', 'frutto', 100, 'images/ingredienti/albicocca.webp'),
('Limone', 'Fresco e agrumato, conferisce una punta di acidità brillante che ravviva l''infusione.', 'frutto', 100, 'images/ingredienti/limone.webp'),
('Lychee', 'Dolce e aromatico, con leggere note floreali ed esotiche che rendono la bevanda raffinata.', 'frutto', 100, 'images/ingredienti/lychee.webp'),
('Caco', 'Molto dolce e vellutato. Conferisce corpo e una dolcezza naturale intensa all''infusione.', 'frutto', 100, 'images/ingredienti/caco.webp'),
('Mirtillo', 'Dolce e leggermente acidulo, con note fruttate intense e un retrogusto caratteristico.', 'frutto', 100, 'images/ingredienti/mirtillo.webp'),
('Fragola', 'Dolce e zuccherina, conferisce un aroma fruttato intenso e una piacevole freschezza all''infusione.', 'frutto', 100, 'images/ingredienti/fragola.webp'),
('Pera', 'Dolce e delicata, con un profilo aromatico morbido e leggermente floreale.', 'frutto', 100, 'images/ingredienti/pera.webp');

-- SPEZIE E RADICI
INSERT INTO ingrediente (nome, descrizione, tipo, disponibile, img_path) VALUES
('Zenzero', 'Il sapore è piccante e legnoso, più terroso e meno fresco rispetto alla radice grezza. Produce un infuso dal carattere caldo e pungente.', 'spezia', 100, 'images/ingredienti/zenzero.webp'),
('Cannella', 'Dolce e legnosa, domina facilmente le miscele se usata in eccesso.', 'spezia', 100, 'images/ingredienti/cannella.webp'),
('Baccello di Vaniglia', 'Il sapore è complesso, oltre alla dolcezza presenta note cremose e leggermente affumicate.', 'spezia', 100, 'images/ingredienti/vaniglia.webp'),
('Cardamomo', 'Sapore complesso: eucaliptolo (fresco/balsamico), limonene (agrumato) e α-pinene (resinoso).', 'spezia', 100, 'images/ingredienti/cardamomo.webp'),
('Radice di Liquirizia', 'Dolce ma con un distintivo retrogusto amarognolo e terroso. Nota: ha un effetto ipertensivo documentato.', 'spezia', 100, 'images/ingredienti/liquirizia.webp'),
('Pepe Rosa', 'Non è un vero pepe. Il sapore è delicato, leggermente dolce e resinoso, con una piccantezza quasi assente.', 'spezia', 100, 'images/ingredienti/pepeRosa.webp'),
('Anice Stellato', 'Dolce e molto intenso, con un marcato carattere di liquirizia.', 'spezia', 100, 'images/ingredienti/aniceStellato.webp');

-- FIORI E ERBE
INSERT INTO ingrediente (nome, descrizione, tipo, disponibile, img_path) VALUES
('Fiori di Lavanda', 'L''aroma è floreale e balsamico. Se sovradosata, può conferire all''infuso un sapore saponoso.', 'fiore', 100, 'images/ingredienti/lavanda.webp'),
('Petali di Rosa', 'Il sapore è molto sottile e leggermente dolce. L''aroma si percepisce più all''olfatto che al gusto.', 'fiore', 100, 'images/ingredienti/rosa.webp'),
('Fiori di Sambuco', 'Hanno un sapore floreale e fruttato leggero. Spesso utilizzati in miscela con altri fiori.', 'fiore', 100, 'images/ingredienti/fioreSambuco.webp'),
('Menta Piperita', 'Il suo sapore fresco e pungente è dato dal mentolo. Rinfresca il palato e lascia una sensazione di freddo.', 'fiore', 100, 'images/ingredienti/mentaPiperita.webp'),
('Melissa', 'Il sapore è prevalentemente limonato. L''effetto è rinfrescante e meno invasivo del limone vero.', 'fiore', 100, 'images/ingredienti/melissa.webp'),
('Camomilla', 'Il sapore è dolce, leggermente erbaceo e maltato. Ha un effetto calmante e rilassante.', 'fiore', 100, 'images/ingredienti/camomilla.webp'),
('Fiori di Tiglio', 'Il gusto è dolce e floreale, con una nota leggermente mielata. L''aroma è rilassante e armonioso.', 'fiore', 100, 'images/ingredienti/fioreTiglio.webp'),
('Ibisco', 'Il sapore è acidulo e fruttato, simile al frutto della passione. Dona un colore rosso intenso all''infuso.', 'fiore', 100, 'images/ingredienti/ibisco.webp'),
('Fiore di Pesco', 'Ha un aroma delicato e fruttato, leggermente dolce. Aggiunge eleganza e leggerezza alla miscela.', 'fiore', 100, 'images/ingredienti/fiorePesco.webp'),
('Tarassaco', 'Il sapore è erbaceo e leggermente amaro. Spesso utilizzato per le sue proprietà depurative.', 'fiore', 100, 'images/ingredienti/tarassaco.webp'),
('Valeriana', 'Il sapore è terroso e leggermente amaro. Nota per le sue proprietà rilassanti sul sistema nervoso.', 'fiore', 100, 'images/ingredienti/valeriana.webp');

-- DOLCIFICANTI
INSERT INTO ingrediente (nome, descrizione, tipo, disponibile, img_path) VALUES
('Miele', 'Aggiunge una dolcezza naturale, corpo e una nota floreale/terrosa. Ottimo collante per miscele fruttate e speziate.', 'dolcificante', 100, 'images/ingredienti/miele.webp'),
('Foglie di Stevia', 'Le foglie essiccate hanno un potere dolcificante fino a 30 volte lo zucchero. Il retrogusto è liquiriziato e leggermente amaro.', 'dolcificante', 100, 'images/ingredienti/stevia.webp');

-- NOTE PARTICOLARI
INSERT INTO ingrediente (nome, descrizione, tipo, disponibile, img_path) VALUES
('Foglie di Ortica', 'Il sapore è erbaceo e vegetale, simile a quello degli spinaci essiccati. Ricca di sali minerali.', 'note', 100, 'images/ingredienti/ortica.webp'),
('Lemongrass', 'Il sapore è agrumato e fresco, dovuto principalmente alla presenza di citrale. Meno acido del limone.', 'note', 100, 'images/ingredienti/lemongrass.webp'),
('Eucalipto', 'Dà una struttura balsamica di fondo. L''aroma principale ha un effetto rinfrescante sulle vie respiratorie.', 'note', 100, 'images/ingredienti/eucalipto.webp'),
('Ashwagandha', 'Il sapore è fortemente terroso, amaro e astringente. Radice adattogena il cui utilizzo è primariamente funzionale.', 'note', 100, 'images/ingredienti/ashwagandha.webp'),
('Matcha', 'Sapore erbaceo, con leggere note amare e vegetali. Aggiunge profondità, colore verde intenso e carattere alla miscela.', 'note', 100, 'images/ingredienti/matcha.webp');

INSERT INTO ingrediente (nome, descrizione, tipo, disponibile, img_path) VALUES
('Lime', 'Agrumato, fresco e più intensamente aromatico del limone, con una punta di amaro caratteristico nella scorza. Rinfresca e vivacizza l''infusione.', 'frutto', 100, 'images/ingredienti/lime.webp'),
('Fiori di Gelsomino', 'Profumo intensamente floreale, dolce e esotico. Conferisce all''infusione un carattere aromatico, sensuale e leggermente fruttato, molto apprezzato nei blend di tè.', 'fiore', 100, 'images/ingredienti/gelsomino.webp');

-- ================================================================
-- 5. PRODOTTI DEL CATALOGO
-- ================================================================
-- TÈ VERDI
INSERT INTO prodotto (nome, descrizione, prezzo, grammi, categoria, img_path, disponibilita, id_base) VALUES
('Tè Verde al Matcha',  'La potenza e la tradizione del Matcha in una forma pratica. Ogni bustina racchiude un blend studiato per offrire il caratteristico colore verde smeraldo e il sapore unico di questo tè: un profondo Umami, note erbacee e una piacevole cremosità, con una carica di energia e benessere.', 6.50, 50, 'tè_verde', 'images/prodotti/TeVerdeMatcha.jpg', 200, 3),
('Tè Verde Speziato', 'Un tè verde avvolgente e rigenerante, arricchito da un corposo bouquet di spezie calde. Il carattere erbaceo e fresco del tè verde viene coccolato e esaltato dalle note di cannella, zenzero e chiodi di garofano, per un infuso dal sapore antico e confortante.', 6.50, 50, 'tè_verde', 'images/prodotti/TeVerdeSpeziato.jpg', 200, 3),
('Tè Verde Deteinato', 'Tutta la freschezza e il carattere erbaceo del tè verde, senza l''eccitazione della teina. Un processo naturale di deteinazione preserva il suo gusto leggero, con sentori di erba fresca e un finale pulito, rendendolo una piacevole bevanda da gustare in qualsiasi momento della giornata, anche la sera.', 6.50, 50, 'tè_verde', 'images/prodotti/TeVerdeDeteinato.jpg', 200, 3);

-- TÈ NERI
INSERT INTO prodotto (nome, descrizione, prezzo, grammi, categoria, img_path, disponibilita, id_base) VALUES
('Tè Nero ai Frutti Rossi', 'Un tè nero robusto e avvolgente, arricchito da un''esplosione di frutti di bosco selvatici. Il perfetto equilibrio tra le note maltate del tè e la dolcezza acidula di lampone, fragola e mirtillo. Ideale per una carica di energia al mattino o per una pausa di gusto, è un infuso che scalda l''inverno e rinfresca l''estate come un delizioso iced tea.', 6.50, 50, 'tè_nero', 'images/prodotti/TeNeroFruttiRossi.jpg', 200, 5),
('Tè Nero all''Arancia e Cannella', 'Un tè nero corposo e invitante, riscaldato dalle note dolci e speziate della cannella e dalla vivace esplosione agrumata dell''arancia. Un abbraccio avvolgente che unisce la forza maltata del tè alla brillantezza degli agrumi e al calore di una spezia antica. Perfetto per una pausa rigenerante, dona comfort nelle giornate fredde e può essere gustato anche freddo per una pausa estiva rinfrescante.', 6.50, 50, 'tè_nero', 'images/prodotti/TeNeroAranciaCannella.jpg', 200, 5),
('Tè Nero agli Agrumi', 'Un''esplosione di sole in tazza. La robustezza del tè nero fa da tela a un vivace mosaico di agrumi: la dolcezza dell''arancia, la freschezza del limone e la leggera punta amarognola del pompelmo si fondono in un sapore luminoso ed energizzante.', 6.50, 50, 'tè_nero', 'images/prodotti/TeNeroAgrumi.jpg', 200, 5);

-- TÈ BIANCHI
INSERT INTO prodotto (nome, descrizione, prezzo, grammi, categoria, img_path, disponibilita, id_base) VALUES
('Tè Bianco al Limone e Lime', 'La pura delicatezza del tè bianco incontra la vibrante freschezza degli agrumi. Note leggere di limone e lime si fondono con le nuances naturalmente dolci e floreali del tè, creando un infuso raffinato, luminoso e delicatamente rinfrescante.', 7.00, 50, 'tè_bianco', 'images/prodotti/TeBiancoLimoneLime.jpg', 200, 1),
('Tè Bianco all''Albicocca e Fiori di Pesco', 'La delicatezza pura e leggermente floreale del tè bianco incontra la dolcezza vellutata dell''albicocca matura e il profumo dei fiori di pesco. Un infuso raffinato e poetico che culla i sensi con la sua eleganza naturale, offrendo un momento di puro piacere tranquillo.', 7.00, 50, 'tè_bianco', 'images/prodotti/TeBiancoAlbicoccaPesco.jpg', 200, 1),
('Tè Bianco alla Rosa', 'L''incontro tra la pura delicatezza del tè bianco e il profumo sofisticato dei petali di rosa. Un infuso di rara eleganza, dove le note leggere, dolci e leggermente floreali del tè vengono avvolte e esaltate dalla nobiltà del fiore, creando un sapore raffinato e rilassante.', 7.00, 50, 'tè_bianco', 'images/prodotti/TeBiancoRosa.jpg', 200, 1);

-- TÈ GIALLI
INSERT INTO prodotto (nome, descrizione, prezzo, grammi, categoria, img_path, disponibilita, id_base) VALUES
('Tè Giallo alla Vaniglia e Pera', 'Un infuso che unisce la base delicata e floreale del Tè Giallo al gusto dolce e avvolgente della vaniglia e alle note succose della pera matura. Un''esperienza aromatica che coccola il palato.', 5.00, 50, 'tè_giallo', 'images/prodotti/TeGialloVanigliaPera.jpg', 200, 2),
('Tè Giallo al Miele e Caco', 'Un''armonia inaspettata che unisce la delicatezza vellutata del Tè Giallo alla dolcezza avvolgente del miele e alle note calde e fruttate del caco maturo. Un infuso prezioso e confortante, ideale per una pausa di raffinato piacere.', 5.50, 50, 'tè_giallo', 'images/prodotti/TeGialloMieleCaco.jpg', 200, 2);

-- TÈ OOLONG
INSERT INTO prodotto (nome, descrizione, prezzo, grammi, categoria, img_path, disponibilita, id_base) VALUES
('Tè Oolong al Gelsomino e Lychee', 'La base maltata e leggermente tostata del Tè Oolong viene illuminata dal profumo intenso e floreale del gelsomino e dal sapore dolce e succoso del lychee. Freschezza e intensità in un unico infuso.', 7.00, 50, 'tè_oolong', 'images/prodotti/TeOolongGelsominoLychee.jpg', 200, 4);

-- TISANE
INSERT INTO prodotto (nome, descrizione, prezzo, grammi, categoria, img_path, disponibilita, id_base) VALUES
('Tisana Lampone e Vaniglia', 'Una pausa dolce e fruttata che profuma di bosco e di pasticceria. I golosi sentori del lampone maturo si intrecciano alla calda cremosità della vaniglia, creando un infuso senza teina dal carattere confortante e indulgente. Una coccola in tazza per una serata dolce.', 6.00, 50, 'tisana', 'images/prodotti/TisanaLamponeVaniglia.jpg', 200, 6),
('Tisana Drenante', 'Una miscela fresca e purificante, studiata per sostenere il benessere naturale dell''organismo. La rinfrescante menta si unisce alle note erbacee del tarassaco e della betulla in un infuso dal sapore pulito e leggero, ideale per un momento di pausa e depurazione.', 6.50, 50, 'tisana', 'images/prodotti/TisanaDrenante.jpg', 200, 6),
('Tisana Relax', 'Un abbraccio calmo e rassicurante in una tazza. Questa miscela unisce la dolcezza della melissa, le proprietà rilassanti della valeriana e la mitezza della camomilla per creare un infuso dal sapore erbaceo e floreale, ideale per conciliare il relax e prepararsi a una notte di sonno ristoratore.', 6.50, 50, 'tisana', 'images/prodotti/TisanaRelax.jpg', 200, 6),
('Tisana Purificante', 'Una miscela erbacea e terrosa, pensata per un momento di benessere. L''ortica e il tarassaco, piante note per le loro proprietà purificanti, si uniscono in un infuso dal sapore pulito, verde e leggermente amaricante, ideale per sentirsi leggeri e ristabiliti.', 6.50, 50, 'tisana', 'images/prodotti/TisanaPurificante.jpg', 200, 6),
('Tisana al Tiglio e Fiori d''Arancio', 'Un infuso dolce e floreale che è un vero balsamo per i sensi. I fiori di tiglio, noti per le loro proprietà calmanti, danzano in armonia con il profumo solare e leggermente agrumato dei fiori d''arancio, creando una bevanda rilassante che placa la mente e riscalda il cuore.', 6.00, 50, 'tisana', 'images/prodotti/TisanaTiglioArancio.jpg', 200, 6),
('Camomilla', 'Il classico senza tempo, pura e rassicurante. I nostri fiori di camomilla selezionati rilasciano un infuso dal colore giallo sole e dal sapore dolce e mite, con le sue note caratteristiche di mela e miele. La bevanda ideale per donare un senso di pace e favorire un sonno ristoratore.', 5.00, 50, 'tisana', 'images/prodotti/Camomilla.jpg', 200, 6);

-- INFUSI SPECIALI
INSERT INTO prodotto (nome, descrizione, prezzo, grammi, categoria, img_path, disponibilita, id_base) VALUES
('Infuso Mela e Cannella', 'La quintessenza del comfort in una tazza. Questo infuso senza teina unisce il dolce e familiare sapore della mela al calore avvolgente della cannella. Una bevanda naturale che profuma di casa e di ricordi, perfetta in ogni momento della giornata.', 6.00, 50, 'infuso', 'images/prodotti/InfusoMelaCannella.jpg', 200, 6),
('Infuso Rosa, Ibisco e Lampone', 'Un infuso dall''anima romantica e dal carattere vibrante. L''ibisco dona una nota acidula e un colore rosso rubino intenso, che viene ammorbidita dalla dolcezza floreale della rosa e dalla golosità del lampone. Una bevanda senza teina, tanto bella da guardare quanto buona da sorseggiare.', 6.50, 50, 'infuso', 'images/prodotti/InfusoRosaIbiscoLampone.jpg', 200, 6),
('Infuso Alpino - Edizione 50° Anniversario', 'Ogni grande viaggio inizia con un primo passo, e il nostro è iniziato 50 anni fa tra i sentieri e le valli pure dell''Ossola. Questo non è un semplice infuso: è la nostra storia in una tazza. Proprio qui, nel cuore della Val d''Ossola, cinquant''anni fa, venivano raccolte le erbe più generose e profumate: la Menta Alpina, fresca e vivace, la Melissa, calmante e dolce, e il Tiglio, simbolo di longevità e accoglienza. Questa miscela è diventata il nostro segreto, l''infuso che veniva offerto agli ospiti e sorseggiato alla fine di ogni giornata di lavoro, guardando le vette. Fu proprio la forza e la purezza di questo gusto, l''essenza stessa della montagna, a dare vita a InfuseMe. È l''infuso che ci ha dato il coraggio di sognare, il nostro punto di partenza e in occasione del 50° Anniversario lo riproponiamo in edizione speciale: un omaggio alle nostre radici, per condividere con voi non solo un infuso, ma il primo sorso della nostra storia. L''Infuso Alpino: da 50 anni, l''autentico sapore della tradizione.', 7.00, 50, 'infuso', 'images/prodotti/InfusoAlpino50.jpg', 200, 6);

-- KIT SPECIALE
INSERT INTO prodotto (nome, descrizione, prezzo, grammi, categoria, img_path, disponibilita, id_base) VALUES
('Kit Edizione Speciale 50° Anniversario', 'Questo esclusivo cofanetto celebra il nostro mezzo secolo di passione, rendendo omaggio alle radici pure della Val d''Ossola. Contiene una selezione curata dei nostri tè e infusi più amati, incluso l''Infuso Alpino in Edizione Limitata. Un viaggio aromatico attraverso la nostra storia, perfetto da regalare o da concedersi. Scegli il meglio della tradizione del nostro brand. Esperienza Completa: la selezione include i nostri migliori Tè, Tisane e Infusi e l''esclusivo Infuso Alpino per il 50° anniversario. Ideale per un Regalo: riscalda i cuori dei tuoi cari attraverso la nostra selezione di prodotti, un vero viaggio aromatico attraverso la nostra tradizione.', 25.00, 100, 'altro', 'images/prodotti/Kit50.jpg', 100, NULL);

-- ================================================================
-- 6. RELAZIONI PRODOTTO-INGREDIENTE
-- ================================================================
INSERT INTO prodotto_ingrediente (id_prodotto, id_ingrediente) VALUES
(1, 40),
(2, 19),
(2, 22),
(2, 17),
(4, 4),
(4, 8),
(4, 13),
(4, 14),
(5, 1),
(5, 17),
(5, 22),
(6, 1),
(6, 10),
(6, 41),
(7, 10),
(7, 41),
(8, 9),
(8, 31),
(9, 24),
(10, 18),
(10, 15),
(11, 12),
(11, 34),
(12, 11),
(12, 42),
(13, 8),
(13, 18),
(14, 26),
(14, 32),
(15, 27),
(15, 33),
(15, 28),
(16, 36),
(16, 32),
(17, 29),
(17, 1),
(18, 28),
(19, 5),
(19, 17),
(20, 24),
(20, 30),
(20, 8),
(21, 26),
(21, 27),
(21, 29);

-- ================================================================
-- 7. ORDINI DI ESEMPIO
-- ================================================================
INSERT INTO ordine (id_utente, indirizzo_spedizione, stato_ord, conferma_pagamento, sottototale, spese_spedizione, totale, omaggio, descrizione_omaggio) VALUES
(1, 'Via Luzzati, Padova, 35121', 'consegnato', TRUE, 19.50, 4.99, 24.49, FALSE, NULL);

INSERT INTO dettaglio_ordine (id_ordine, id_prodotto, quantita, prezzo_unit) VALUES
(1, 1, 2, 6.50),
(1, 4, 1, 6.50);

-- ================================================================
-- 8. TRACCIAMENTO ADMIN
-- ================================================================
INSERT INTO add_prodotto (id_admin, id_prodotto) 
SELECT 1, id_prodotto FROM prodotto;

INSERT INTO add_base (id_admin, id_base) 
SELECT 1, id_base FROM base;

INSERT INTO add_ingrediente (id_admin, id_ingrediente) 
SELECT 1, id_ingrediente FROM ingrediente;