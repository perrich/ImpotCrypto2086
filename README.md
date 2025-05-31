# ImpotCrypto2086
Calculs pour remplir la déclaration des plus ou moins-values réalisées suite à des cessions d’actifs numériques et droits assimilés (cerfa 2086).
Ce script gère les frais de transactions, mais par les soultes.
Seules les transactions en EUR sont gérées. Le format de fichier utilisé est celui de https://finary.com.


## Prérequis

Ce script nécessite PHP 8.4 et Composer.

## Comment s'en servir ?

1. Placer un fichier CSV "transactions.csv" contenant vos transactions au format Finary dans le répertoire data.
Exemple du fichier transactions.csv :

    ```
    type,date,timezone,received_amount,received_currency,sent_amount,sent_currency,fee_amount,fee_currency,description,address,transaction_hash,external_id
    Trade,2017-07-17T15:26:21.720794186Z,GMT,5.0,BTC,9005.0,EUR,9,EUR,Buy,,,
    ```

2. [Optionnel] Placer un fichier CSV "prices.csv" contenant la liste des prix des cryptos, dans le répertoire data. 
Si le fichier n'est pas disponible, il va aller chercher les prix sur CoinGecko mais il y a une limite pour en récuperer quelques uns par minutes. Il se peut donc qu'il faille relancer le script de nombreuses fois avant d'avoir pu obtenir 100% des prix nécessaires aux calculs.
Exemple du fichier prices.csv :

    ```
    currency,date,price
    BTC,2024-09-19,55288.445063292
    ```
3. Vérifier que vos cryptos sont bien gérées dans l'enum Currency ainsi que dans le mapping du PriceProviderFromCoinGeckoApi. Sinon, vous devrez les ajouter (proposer une PR :)).

4. Lancer la commande classique ``composer install`` puis ``php main.php``

5. Vous aller obtenir un résumé contenant les informations à saisir sur la déclaration (les numéros entre parenthèses sont ceux des cases à remplir).
La première partie vous indique, pour information/contrôle, la quantité actuelle pour chaque crypto.
La seconde partie détaille chaque cession, à reporter sur la déclaration.
La dernière partie indique le montant total des plus ou moins values de chaque année ainsi que les impots à payer en supposant que vous êtes à la flat tax.



**Attention, ce script effectue un calcul simple, en prenant les transactions dans l'ordre du fichier et se base sur un prix quotidien pour chaque crypto. Le choix du prix va impacter la valorisation du portefeuille à chaque cession.
De ce fait, il va différer des résultats d'autres outils (ex: https://www.waltio.com) et nécessite votre attention/contrôle avant de reporter les informations sur votre déclaration.**