# php-api-julesgabriel
php-api-julesgabriel created by GitHub Classroom

## Rendu Jules DAYAUX
Etat du projet:
<li>Le projet est sécurisé par JSON WEB TOKEN: <br>
Il faut vous créer un compte sur la route auth/register pour générer automatiquement un token à utiliser dans vos headers pour la sécurisation de l'api
</li>
<li>
 Les Fixtures fonctionnent, installer le projet en local via composer install, changer les variables d'environnement dans le fichier <code>.env</code>, effectuez vos migrations à l'aide de la commande php bin/console <code>doctrine:migration:migrate</code> puis de <code>doctrine:fixtures:load</code>
</li>
<li>
Les principales routes fonctionnent et sont sécurisés par JSON WEB TOKEN
</li>

Néanmoins, je n'ai pas réussis à mettre en place les relations (problématique de setRelation et instanciation de la class, je n'ai pas trop compris).

Merci pour cette semaine de cours.
