---
title: Installazione
description: Come Installare la Base
extends: _layouts.documentation
section: content
---

# Installazione {#installation}

### Clonare la base in locale nella cartella del server, scaricando i submodules, e senza la storia delle modifiche

```bash
git clone https://NOME_BASE.git --recurse-submodules --depth=1
```

### dalla cartella "laravel" creare il file delle variabili d'ambiente .env 
    
```bash
cp .env.latest .env
```

- modificare parametri a seconda delle necessità
- dopo aver messo i nomi dei database, in phpmyadmin creare i database vuoti

### dalla cartella laravel, lanciare in bash il comando

```bash
composer init
```

alternativa: 

```bash
composer update
cd Modules/Xot/Services
composer install
php artisan key:generate
```

### vedere la lista dei moduli con il comando

da url: 

```bash
http://VIRTUAL_HOST.EXT/?_act=artisan&cmd=module-list
```

alternativa da terminale:

```bash
php artisan module:list 
```

### abilitare tutti i moduli con il comando

da url: 

```bash
http://VIRTUAL_HOST.EXT/?_act=artisan&cmd=module-enable&module=NOME_MODULO
```

alternativa da terminale:

```bash
php artisan module:enable NomeModulo
```

### fare la migration

da url: 

```bash
http://VIRTUAL_HOST.EXT/?_act=artisan&cmd=migrate
```

in alternativa da terminale:

```bash
php artisan migrate
```

### per compilare il tema (da dentro la cartella laravel/Themes/NOME_TEMA)

```bash
npm install
npm run dev
```

### a questo punto bisogna crearsi il virtual host con il nome del dominio uguale a quello del file di configurazione. Esempio:

- copio la cartella laravel/config/localhost in laravel/config/local/dominio/* e imposto i parametri nei file

- questo significa che il virtual host deve chiamarsi dominio.local

### IMPORTANTISSIMO per sincronizzare la base si utilizzano i famosi TRE FILES. 

Questi file si trovano nella cartella BASE/bashscripts.

Vanno lanciati dalla cartella della root della BASE.

Si utilizzano così:

```bash
- ./bashscripts/git_pull.sh && ./bashscripts/git_branch.sh per fare il pull
- ./bashscripts/git_pull.sh && ./bashscripts/git_branch.sh per fare il push
```

Dopo aver fatto push, siccome su git ci sono delle azioni che possono modificare i file, bisogna rilanciare subito il pull, come scritto sopra


### FORMATTAZIONE DEL CODICE

Per formattare il codice in modo corretto bisogna usare cs-fixer global. Per installarlo seguire le istruzioni nel seguente file:

```bash
bashscripts/tips/cs-fixer.txt
```