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


### Url pour lister les utilisateurs avec token
    http://localhost:8000/apip/users       avec authorization Bearer token

### url pour poster un utilisateur
    url: http://localhost:8000/apip/users POST insertion en tant que pratitient
    params: {
        "first_name":"dev_paticien",
        "last_name":"devpaticien",
        "username":"devpraticien",
        "roles":"ROLE_PRATICIENT",
        "email":"devpaticienenjana@gmail.com",
        "password":"devenjana",
        "telephone":"0341417474",
        "date_on_born":"1995-07-01",
        "type_patient": 1,
        "sexe":"femme",
        "fonction":"generaliste",
        "id_address": 7,
        "num_rue": "4567",
        "quartier": "Mahazoarivo"
    }

    url: http://localhost:8000/apip/users POST insertion en tant que patient
        params : {
        "first_name":"dev",
        "last_name":"dev",
        "date_on_born":"1995-07-01",
        "id_address": 2,
        "telephone":"0341417474",
        "username":"dev_enjana",
        "roles":"ROLE_PATIENT",
        "email":"devenjana@gmail.com",
        "password":"devenjana",
        "type_patient": "1",
        "sexe":"femme"
    }
## Ordo Vaccination
    url get all ordo vaccination http://localhost:9000/apip/ordo_vaccinations
    url get by id of ordo vaccination http://localhost:9000/apip/ordo_vaccinations/1