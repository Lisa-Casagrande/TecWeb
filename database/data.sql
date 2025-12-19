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
('admin', 'admin', 'admin@infuseme.com', 'admin');

-- ================================================================
-- 2. UTENTE
-- ================================================================
INSERT INTO utente (email, password_hash, nome, cognome, data_nascita, indirizzo, citta) VALUES
('utente@utente.com', 'password', 'utente', 'utente', '1975-01-02', 'Via Luzzati', 'Padova');

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
('Arancia', 'Apporta una nota agrumata, acidula e leggermente amara. L\'olio essenziale contenuto nella scorza rilascia un aroma intenso.', 'frutto', 100, 'images/ingredienti/arancia.webp'),
('Bacche di Goji', 'Di sapore debolmente dolce e acidulo, con un retrogusto terroso.', 'frutto', 100, 'images/ingredienti/goji.webp'),
('Frutto della Passione', 'Il suo sapore è prevalentemente acido e fruttato.', 'frutto', 100, 'images/ingredienti/passionFruit.webp'),
('Ribes', 'Caratterizzati da un\'alta acidità e astringenza. Il loro sapore dolce è molto tenue e necessita spesso di essere bilanciato.', 'frutto', 100, 'images/ingredienti/ribes.webp'),
('Mele', 'Conferiscono un sapore dolce e neutro, aggiungendo principalmente corpo all\'infusione.', 'frutto', 100, 'images/ingredienti/mela.webp'),
('Ananas', 'Dolcezza marcata con note tropicali. Tende a rilasciare zuccheri nell\'infusione, addolcendo naturalmente la bevanda.', 'frutto', 100, 'images/ingredienti/ananas.webp'),
('Fichi', 'Apportano una dolcezza intensa e un sapore di frutta cotta.', 'frutto', 100, 'images/ingredienti/fico.webp'),
('Lampone', 'Dolce e leggermente acidulo, con note fruttate fresche che rendono l\'infusione vivace.', 'frutto', 100, 'images/ingredienti/lampone.webp'),
('Albicocca', 'Morbida e vellutata, dona un sapore dolce e fruttato senza appesantire la bevanda.', 'frutto', 100, 'images/ingredienti/albicocca.webp'),
('Limone', 'Fresco e agrumato, conferisce una punta di acidità brillante che ravviva l\'infusione.', 'frutto', 100, 'images/ingredienti/limone.webp'),
('Lychee', 'Dolce e aromatico, con leggere note floreali ed esotiche che rendono la bevanda raffinata.', 'frutto', 100, 'images/ingredienti/lychee.webp');

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
('Fiori di Lavanda', 'L\'aroma è floreale e balsamico. Se sovradosata, può conferire all\'infuso un sapore saponoso.', 'fiore', 100, 'images/ingredienti/lavanda.webp'),
('Petali di Rosa', 'Il sapore è molto sottile e leggermente dolce. L\'aroma si percepisce più all\'olfatto che al gusto.', 'fiore', 100, 'images/ingredienti/rosa.webp'),
('Fiori di Sambuco', 'Hanno un sapore floreale e fruttato leggero. Spesso utilizzati in miscela con altri fiori.', 'fiore', 100, 'images/ingredienti/fioreSambuco.webp'),
('Menta Piperita', 'Il suo sapore fresco e pungente è dato dal mentolo. Rinfresca il palato e lascia una sensazione di freddo.', 'fiore', 100, 'images/ingredienti/mentaPiperita.webp'),
('Melissa', 'Il sapore è prevalentemente limonato. L\'effetto è rinfrescante e meno invasivo del limone vero.', 'fiore', 100, 'images/ingredienti/melissa.webp'),
('Camomilla', 'Il sapore è dolce, leggermente erbaceo e maltato. Ha un effetto calmante e rilassante.', 'fiore', 100, 'images/ingredienti/camomilla.webp'),
('Fiori di Tiglio', 'Il gusto è dolce e floreale, con una nota leggermente mielata. L\'aroma è rilassante e armonioso.', 'fiore', 100, 'images/ingredienti/fioreTiglio.webp'),
('Ibisco', 'Il sapore è acidulo e fruttato, simile al frutto della passione. Dona un colore rosso intenso all\'infuso.', 'fiore', 100, 'images/ingredienti/ibisco.webp'),
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
('Eucalipto', 'Dà una struttura balsamica di fondo. L\'aroma principale ha un effetto rinfrescante sulle vie respiratorie.', 'note', 100, 'images/ingredienti/eucalipto.webp'),
('Ashwagandha', 'Il sapore è fortemente terroso, amaro e astringente. Radice adattogena il cui utilizzo è primariamente funzionale.', 'note', 100, 'images/ingredienti/ashwagandha.webp');

-- ================================================================
-- 5. PRODOTTI DEL CATALOGO
-- ================================================================
-- TÈ VERDI
INSERT INTO prodotto (nome, descrizione, prezzo, grammi, categoria, img_path, disponibilita, id_base) VALUES
('Tè Verde al Matcha', 'La potenza e la tradizione del Matcha in una forma pratica, con un profondo Umami e note erbacee.', 6.50, 50, 'tè_verde', 'images/prodotti/TeVerdeMatcha.jpg', 200, 3),
('Tè Verde Speziato', 'Un tè verde avvolgente e rigenerante, arricchito da un corposo bouquet di spezie calde.', 6.50, 50, 'tè_verde', 'images/prodotti/TeVerdeSpeziato.jpg', 200, 3),
('Tè Verde Deteinato', 'Tutta la freschezza e il carattere erbaceo del tè verde, senza l\'eccitazione della teina.', 6.50, 50, 'tè_verde', 'images/prodotti/TeVerdeDeteinato.jpg', 200, 3);

-- TÈ NERI
INSERT INTO prodotto (nome, descrizione, prezzo, grammi, categoria, img_path, disponibilita, id_base) VALUES
('Tè Nero ai Frutti Rossi', 'Un tè nero robusto e avvolgente, arricchito da un\'esplosione di frutti di bosco selvatici.', 6.50, 50, 'tè_nero', 'images/prodotti/TeNeroFruttiRossi.jpg', 200, 5),
('Tè Nero all\'Arancia e Cannella', 'Un tè nero corposo e invitante, riscaldato dalle note dolci e speziate della cannella e dell\'arancia.', 6.50, 50, 'tè_nero', 'images/prodotti/TeNeroAranciaCannella.jpg', 200, 5),
('Tè Nero agli Agrumi', 'Un\'esplosione di sole in tazza con la robustezza del tè nero e la vivacità degli agrumi.', 6.50, 50, 'tè_nero', 'images/prodotti/TeNeroAgrumi.jpg', 200, 5);

-- TÈ BIANCHI
INSERT INTO prodotto (nome, descrizione, prezzo, grammi, categoria, img_path, disponibilita, id_base) VALUES
('Tè Bianco al Limone e Lime', 'La pura delicatezza del tè bianco incontra la vibrante freschezza degli agrumi.', 7.00, 50, 'tè_bianco', 'images/prodotti/TeBiancoLimoneLime.jpg', 200, 1),
('Tè Bianco all\'Albicocca e Fiori di Pesco', 'La delicatezza del tè bianco incontra la dolcezza vellutata dell\'albicocca e il profumo dei fiori di pesco.', 7.00, 50, 'tè_bianco', 'images/prodotti/TeBiancoAlbicoccaPesco.jpg', 200, 1),
('Tè Bianco alla Rosa', 'L\'incontro tra la pura delicatezza del tè bianco e il profumo sofisticato dei petali di rosa.', 7.00, 50, 'tè_bianco', 'images/prodotti/TeBiancoRosa.jpg', 200, 1);

-- TÈ GIALLI
INSERT INTO prodotto (nome, descrizione, prezzo, grammi, categoria, img_path, disponibilita, id_base) VALUES
('Tè Giallo alla Vaniglia e Pera', 'Tè giallo arricchito con aroma di vaniglia e pera, per la tua dolce merenda.', 5.00, 50, 'tè_giallo', 'images/prodotti/TeGialloVanigliaPera.jpg', 200, 2);

-- TISANE
INSERT INTO prodotto (nome, descrizione, prezzo, grammi, categoria, img_path, disponibilita, id_base) VALUES
('Tisana Lampone e Vaniglia', 'Una pausa dolce e fruttata che profuma di bosco e di pasticceria, con lampone e vaniglia.', 6.00, 50, 'tisana', 'images/prodotti/TisanaLamponeVaniglia.jpg', 200, 6),
('Tisana Drenante', 'Una miscela fresca e purificante, studiata per sostenere il benessere naturale dell\'organismo.', 6.50, 50, 'tisana', 'images/prodotti/TisanaDrenante.jpg', 200, 6),
('Tisana Relax', 'Un abbraccio calmo e rassicurante in una tazza, con melissa, valeriana e camomilla.', 6.50, 50, 'tisana', 'images/prodotti/TisanaRelax.jpg', 200, 6),
('Tisana Purificante', 'Una miscela erbacea e terrosa, pensata per un momento di benessere con ortica e tarassaco.', 6.50, 50, 'tisana', 'images/prodotti/TisanaPurificante.jpg', 200, 6),
('Tisana al Tiglio e Fiori d\'Arancio', 'Un infuso dolce e floreale che è un vero balsamo per i sensi, con tiglio e fiori d\'arancio.', 6.00, 50, 'tisana', 'images/prodotti/TisanaTiglioArancio.jpg', 200, 6),
('Camomilla', 'Il classico senza tempo, pura e rassicurante con fiori di camomilla selezionati.', 5.00, 50, 'tisana', 'images/prodotti/Camomilla.jpg', 200, 6);

-- INFUSI SPECIALI
INSERT INTO prodotto (nome, descrizione, prezzo, grammi, categoria, img_path, disponibilita, id_base) VALUES
('Infuso Alpino - Edizione 50° Anniversario', 'Assapora il gusto della tradizione con l\'infuso originario della nostra famiglia, il primo prodotto nel 1975.', 7.00, 50, 'infuso', 'images/prodotti/InfusoAlpino50.jpg', 200, 6);

-- KIT SPECIALE
INSERT INTO prodotto (nome, descrizione, prezzo, grammi, categoria, img_path, disponibilita, id_base) VALUES
('Kit Edizione Speciale 50° Anniversario', 'Un esclusivo cofanetto che celebra il nostro mezzo secolo di passione con una selezione dei nostri prodotti più amati.', 25.00, 100, 'altro', 'images/prodotti/Kit50.jpg', 100, NULL);

-- ================================================================
-- 6. RELAZIONI PRODOTTO-INGREDIENTE
-- ================================================================
INSERT INTO prodotto_ingrediente (id_prodotto, id_ingrediente) VALUES
(2, 12), -- Tè Verde Speziato: Zenzero
(2, 13), -- Tè Verde Speziato: Cannella
(2, 15), -- Tè Verde Speziato: Cardamomo
(4, 4),  -- Tè Nero ai Frutti Rossi: Ribes
(4, 8),  -- Tè Nero ai Frutti Rossi: Lampone
(5, 1),  -- Tè Nero all'Arancia e Cannella: Arancia
(5, 13), -- Tè Nero all'Arancia e Cannella: Cannella
(9, 19), -- Tè Bianco alla Rosa: Petali di Rosa
(12, 22), -- Tisana Relax: Melissa
(12, 28), -- Tisana Relax: Valeriana
(12, 23); -- Tisana Relax: Camomilla

-- ================================================================
-- 7. ORDINI DI ESEMPIO
-- ================================================================
INSERT INTO ordine (id_utente, indirizzo_spedizione, stato_ord, conferma_pagamento, sottototale, spese_spedizione, totale, omaggio, descrizione_omaggio) VALUES
(1, 'Via Roma 123, Milano, 20100', 'consegnato', TRUE, 19.50, 4.99, 24.49, FALSE, NULL);

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