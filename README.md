# Progetto Tecnologie Web 2025/2026
Progetto del corso Tecnologie Web (a.a. 2025/2026) - Corso di Laurea in Informatica, UniPD.

# 🚀 Guida all'Installazione Locale con Docker

## Prerequisiti
* Docker installato sul tuo sistema
* Docker Compose (spesso incluso con Docker Desktop)
* Git (opzionale, solo per il clone)

---

## Passi per l'Installazione
### 1. Ottenere il Codice Sorgente
**Opzione A - Clonare con Git:**
```bash
git clone [URL-del-repository]
```

**Opzione B - Scaricare Manualmente:**
1. Vai alla pagina del repository
2. Clicca sul pulsante "Code"
3. Seleziona "Download ZIP"
4. Estrai l'archivio nella cartella desiderata

### 2. Accedere alla Cartella del Progetto
Apri il terminale e naviga nella cartella:
```bash
cd [nome-cartella]
```

### 3. Avviare l'Applicazione con Docker
Esegui uno dei seguenti comandi in base alla tua versione di Docker:
```bash
docker-compose up --build
docker compose up --build
```

* `up`: Avvia i container
* `--build`: Ricostruisce le immagini Docker per assicurarsi di avere le ultime modifiche

### 4. Accedere all'Applicazione
* Mantieni il terminale aperto - **I container devono rimanere in esecuzione**
* Apri il browser
* Vai all'indirizzo: http://localhost:8080/index.php
