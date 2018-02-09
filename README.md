# recipeApp
Project using Symfony 4

create your own databse and configure connections in .env 

migrations handled by doctrine:
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate

Setup commands from project root:
php bin/console server:run
yarn run encore dev --watch
