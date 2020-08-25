## suivi-de-patient

###Installation 
    composer install
    php bin/console doctrine:database:create 
    php bin/console d:s:u -f
    php bin/console d:f:l
    php bin/console server:run

### Url d'authentification
    http://localhost:8000/api/login_check

### Url pour lister les utilisateurs avec authorization Bearer token
    http://localhost:8000/api/users       

### url pour ajouter utilisateur en tant que praticien
    http://localhost:8000/api/users 
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

 ### url pour ajouter utilisateur en tant que patient:
    http://localhost:8000/api/users 
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
## Centre de santé
    http://localhost:8000/api/centre_healths
    http://localhost:8000/api/centre_healths/{id}
##Vaccin 
#### url pour lister vaccin avec authorization Bearer token
    http://localhost:8000/api/vaccins
    http://localhost:8000/api/vaccins/{id}
## Vaccination
####url pour lister vaccination avec authorization Bearer token
        http://localhost:8000/api/ordo_vaccinations
        http://localhost:8000/api/ordo_vaccinations/{id}
## Consultation
####url pour lister consultation avec authorization Bearer token
        http://localhost:8000/api/ordo_consultations
        http://localhost:8000/api/ordo_vaccinations/{id}
####url pour lister rod vaccinations avec authorization Bearer token
    http://localhost:8000/api/carnet_vaccinations
    http://localhost:8000/api/carnet_vaccinations/{id}
####url pour lister types de centre de santé avec authorization Bearer token
    http://localhost:8000/api/centre_types
    http://localhost:8000/api/centre_types/{id}
####url pour lister ville avec authorization Bearer token
    http://localhost:8000/api/cities
    http://localhost:8000/api/cities/{id}
####url pour lister families avec authorization Bearer token
    http://localhost:8000/api/families
    http://localhost:8000/api/families/{id}
####url pour lister group families avec authorization Bearer token
    http://localhost:8000/api/group_families
    http://localhost:8000/api/group_families/{id}
####url pour lister intervation consultations avec authorization Bearer token
    http://localhost:8000/api/intervation_consultations
    http://localhost:8000/api/intervation_consultations/{id}
####url pour lister intervation medicales avec authorization Bearer token
    http://localhost:8000/api/intervation_medicales
    http://localhost:8000/api/intervation_medicales/{id}
####url pour lister intervention vaccinations avec authorization Bearer token
    http://localhost:8000/api/intervention_vaccinations
    http://localhost:8000/api/intervention_vaccinations/{id}
####url pour lister medicaments avec authorization Bearer token
    http://localhost:8000/api/ordo_medicaments
    http://localhost:8000/api/ordo_medicaments/{id}
####url pour lister ordonnaces(praticien + centre de santé) avec authorization Bearer token
    http://localhost:8000/api/ordonnaces
    http://localhost:8000/api/ordonnaces/{id}
####url pour lister patients avec authorization Bearer token
    http://localhost:8000/api/patients
    http://localhost:8000/api/patients/{id}
####url pour lister carnet vaccinations avec authorization Bearer token
    http://localhost:8000/api/patient_carnet_vaccinations
    http://localhost:8000/api/patient_carnet_vaccinations/{id}
####url pour lister intervation consultations avec authorization Bearer token
    http://localhost:8000/api/patient_intervation_consultations
    http://localhost:8000/api/patient_intervation_consultations/{id}
####url pour lister ordonnace de consultations avec authorization Bearer token
    http://localhost:8000/api/patient_ordo_consultations
    http://localhost:8000/api/patient_ordo_consultations/{id}
####url pour lister ordonnace de medicaments avec authorization Bearer token
    http://localhost:8000/api/patient_ordo_medicaments
    http://localhost:8000/api/patient_ordo_medicaments/{id}
####url pour lister ordonnace de vaccinations avec authorization Bearer token
    http://localhost:8000/api/patient_ordo_vaccinations
    http://localhost:8000/api/patient_ordo_vaccinations/{id}
####url pour lister patients y avait de vaccine avec authorization Bearer token
    http://localhost:8000/api/patient_vaccins
    http://localhost:8000/api/patient_vaccins/{id}
####url pour lister praticiens avec authorization Bearer token
    http://localhost:8000/api/praticiens
    http://localhost:8000/api/praticiens/{id}
####url pour lister praticien spécialiste avec authorization Bearer token
    http://localhost:8000/api/praticien_specialites
    http://localhost:8000/api/praticien_specialites/{id}
####url pour lister régions avec authorization Bearer token
    http://localhost:8000/api/regions
    http://localhost:8000/api/regions/{id}
####url pour lister specialisté de praticien avec authorization Bearer token
    http://localhost:8000/api/specialites
    http://localhost:8000/api/specialites/{id}
####url pour lister pays avec authorization Bearer token
    http://localhost:8000/api/states
    http://localhost:8000/api/states/{id}
####url pour lister type de patients avec authorization Bearer token
    http://localhost:8000/api/type_patients
    http://localhost:8000/api/type_patients/{id}
####url pour lister type de vaccins avec authorization Bearer token
    http://localhost:8000/api/type_vaccins
    http://localhost:8000/api/type_vaccins/{id}
####url pour lister centre de santé ayant de vaccine avec authorization Bearer token
    http://localhost:8000/api/vaccin_centre_healths
    http://localhost:8000/api/vaccin_centre_healths/{id}
####url pour lister vaccin affecté à praticien avec authorization Bearer token
    http://localhost:8000/api/vaccin_praticiens
    http://localhost:8000/api/vaccin_praticiens/{id}
