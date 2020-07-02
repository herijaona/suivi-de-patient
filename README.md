# suivi-de-patient
Suivi de patient

###Installation 
    composer install
    php bin/console doctrine:database:create 
    php bin/console d:s:u -f
    php bin/console d:f:l
    php bin/console server:run



### Url d'authentification
http://localhost:8000/apip/login_check

### Url list des utilisateurs avec token
http://localhost:8000/apip/users       avec authorization Bearer token
