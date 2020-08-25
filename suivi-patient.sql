-- --------------------------------------------------------
-- Hôte :                        127.0.0.1
-- Version du serveur:           10.4.10-MariaDB - mariadb.org binary distribution
-- SE du serveur:                Win64
-- HeidiSQL Version:             9.5.0.5196
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Export de la structure de la base pour suivi_patient
DROP DATABASE IF EXISTS `suivi_patient`;
CREATE DATABASE IF NOT EXISTS `suivi_patient` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `suivi_patient`;

-- Export de la structure de la table suivi_patient. address
DROP TABLE IF EXISTS `address`;
CREATE TABLE IF NOT EXISTS `address` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ville_id` int(11) DEFAULT NULL,
  `num_rue` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quartier` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_D4E6F81A73F0036` (`ville_id`),
  CONSTRAINT `FK_D4E6F81A73F0036` FOREIGN KEY (`ville_id`) REFERENCES `city` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Export de données de la table suivi_patient.address : ~0 rows (environ)
/*!40000 ALTER TABLE `address` DISABLE KEYS */;
/*!40000 ALTER TABLE `address` ENABLE KEYS */;

-- Export de la structure de la table suivi_patient. carnet_vaccination
DROP TABLE IF EXISTS `carnet_vaccination`;
CREATE TABLE IF NOT EXISTS `carnet_vaccination` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `intervation_vaccination_id` int(11) DEFAULT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `vaccin_id` int(11) DEFAULT NULL,
  `etat` tinyint(1) DEFAULT NULL,
  `date_prise_initiale` datetime DEFAULT NULL,
  `rappel_vaccin` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_25CC97807D3A0735` (`intervation_vaccination_id`),
  KEY `IDX_25CC97806B899279` (`patient_id`),
  KEY `IDX_25CC97809B14AC76` (`vaccin_id`),
  CONSTRAINT `FK_25CC97806B899279` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`id`),
  CONSTRAINT `FK_25CC97807D3A0735` FOREIGN KEY (`intervation_vaccination_id`) REFERENCES `intervention_vaccination` (`id`),
  CONSTRAINT `FK_25CC97809B14AC76` FOREIGN KEY (`vaccin_id`) REFERENCES `vaccin` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Export de données de la table suivi_patient.carnet_vaccination : ~0 rows (environ)
/*!40000 ALTER TABLE `carnet_vaccination` DISABLE KEYS */;
/*!40000 ALTER TABLE `carnet_vaccination` ENABLE KEYS */;

-- Export de la structure de la table suivi_patient. centre_health
DROP TABLE IF EXISTS `centre_health`;
CREATE TABLE IF NOT EXISTS `centre_health` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `centre_city_id` int(11) DEFAULT NULL,
  `centre_type_id` int(11) DEFAULT NULL,
  `centre_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `centre_phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `centre_referent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_id` int(11) DEFAULT NULL,
  `telephone` int(11) DEFAULT NULL,
  `responsable_centre` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city_id` int(11) DEFAULT NULL,
  `num_rue` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quartier` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_1D672DB4C3CDE9EA` (`centre_city_id`),
  KEY `IDX_1D672DB48D2D07D6` (`centre_type_id`),
  KEY `IDX_1D672DB4F5B7AF75` (`address_id`),
  KEY `IDX_1D672DB48BAC62AF` (`city_id`),
  CONSTRAINT `FK_1D672DB48BAC62AF` FOREIGN KEY (`city_id`) REFERENCES `city` (`id`),
  CONSTRAINT `FK_1D672DB48D2D07D6` FOREIGN KEY (`centre_type_id`) REFERENCES `centre_type` (`id`),
  CONSTRAINT `FK_1D672DB4C3CDE9EA` FOREIGN KEY (`centre_city_id`) REFERENCES `city` (`id`),
  CONSTRAINT `FK_1D672DB4F5B7AF75` FOREIGN KEY (`address_id`) REFERENCES `address` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Export de données de la table suivi_patient.centre_health : ~2 rows (environ)
/*!40000 ALTER TABLE `centre_health` DISABLE KEYS */;
INSERT INTO `centre_health` (`id`, `centre_city_id`, `centre_type_id`, `centre_name`, `centre_phone`, `centre_referent`, `address_id`, `telephone`, `responsable_centre`, `city_id`, `num_rue`, `quartier`) VALUES
	(1, NULL, 2, 'MORAFENO2', '0345656733', NULL, NULL, NULL, 'Randria', 14, '344', 'Mahazoarivo'),
	(2, NULL, 2, 'MORAFENO', '03410044772', NULL, NULL, NULL, 'Hery', 18, '344', 'Mahazoarivo');
/*!40000 ALTER TABLE `centre_health` ENABLE KEYS */;

-- Export de la structure de la table suivi_patient. centre_type
DROP TABLE IF EXISTS `centre_type`;
CREATE TABLE IF NOT EXISTS `centre_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Export de données de la table suivi_patient.centre_type : ~15 rows (environ)
/*!40000 ALTER TABLE `centre_type` DISABLE KEYS */;
INSERT INTO `centre_type` (`id`, `type_name`, `description`) VALUES
	(1, 'HG', 'Hôpital Général'),
	(2, 'CHU', 'Centre Hospitalier Universitaire'),
	(3, 'HC', 'Hôpital Central'),
	(4, 'CL', 'Clinique'),
	(5, 'CP', 'Cabinet Privé'),
	(6, 'FSC', 'Formation sanitaire confessionnelle'),
	(7, 'DI', 'Dispensaire'),
	(8, 'HEPr', 'Hôpital d\'Entreprise privée'),
	(9, 'HEPa', 'Hôpital d\'Entreprise parapublique'),
	(10, 'HD', 'Hôpital de district'),
	(11, 'SSI', 'Service de santé de district'),
	(12, 'CMA', 'Centre Médical d\'arrondissement'),
	(13, 'CSI', 'Centre de Santé Intégré'),
	(14, 'CS', 'Centre de Santé'),
	(15, 'CSA', 'Centre de Santé Ambulatoire');
/*!40000 ALTER TABLE `centre_type` ENABLE KEYS */;

-- Export de la structure de la table suivi_patient. city
DROP TABLE IF EXISTS `city`;
CREATE TABLE IF NOT EXISTS `city` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `region_id` int(11) DEFAULT NULL,
  `name_city` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_2D5B023498260155` (`region_id`),
  CONSTRAINT `FK_2D5B023498260155` FOREIGN KEY (`region_id`) REFERENCES `region` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Export de données de la table suivi_patient.city : ~23 rows (environ)
/*!40000 ALTER TABLE `city` DISABLE KEYS */;
INSERT INTO `city` (`id`, `region_id`, `name_city`) VALUES
	(7, 3, 'NGAOUNDAL'),
	(8, 3, 'TIBATI'),
	(9, 3, 'MAYO-BALEO'),
	(10, 3, 'TIGNERE'),
	(11, 3, 'GALIM-TIGNERE'),
	(12, 4, 'LYON'),
	(13, 3, 'BANYO'),
	(14, 3, 'KONTCHA'),
	(15, 3, 'BANKIM'),
	(16, 3, 'MAYO-DARLE'),
	(17, 3, 'MEIGANGA'),
	(18, 3, 'DJOHONG'),
	(19, 3, 'DIR'),
	(20, 3, 'NGAOUI'),
	(21, 3, 'NGAOUNDERE 1er'),
	(22, 3, 'NGAOUNDERE 2e'),
	(23, 3, 'NGAOUNDERE 3e'),
	(24, 3, 'BELEL'),
	(25, 3, 'MBE'),
	(26, 3, 'NGANHA'),
	(27, 3, 'NYAMBAKA'),
	(28, 3, 'MARTAP'),
	(29, 5, 'Antananarivo2');
/*!40000 ALTER TABLE `city` ENABLE KEYS */;

-- Export de la structure de la table suivi_patient. doctrine_migration_versions
DROP TABLE IF EXISTS `doctrine_migration_versions`;
CREATE TABLE IF NOT EXISTS `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Export de données de la table suivi_patient.doctrine_migration_versions : ~0 rows (environ)
/*!40000 ALTER TABLE `doctrine_migration_versions` DISABLE KEYS */;
/*!40000 ALTER TABLE `doctrine_migration_versions` ENABLE KEYS */;

-- Export de la structure de la table suivi_patient. family
DROP TABLE IF EXISTS `family`;
CREATE TABLE IF NOT EXISTS `family` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_child_id` int(11) DEFAULT NULL,
  `group_family_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_A5E6215BEAE79ABE` (`patient_child_id`),
  KEY `IDX_A5E6215B3ED0A8B` (`group_family_id`),
  CONSTRAINT `FK_A5E6215B3ED0A8B` FOREIGN KEY (`group_family_id`) REFERENCES `group_family` (`id`),
  CONSTRAINT `FK_A5E6215BEAE79ABE` FOREIGN KEY (`patient_child_id`) REFERENCES `patient` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Export de données de la table suivi_patient.family : ~0 rows (environ)
/*!40000 ALTER TABLE `family` DISABLE KEYS */;
INSERT INTO `family` (`id`, `patient_child_id`, `group_family_id`) VALUES
	(1, 3, 1);
/*!40000 ALTER TABLE `family` ENABLE KEYS */;

-- Export de la structure de la table suivi_patient. group_family
DROP TABLE IF EXISTS `group_family`;
CREATE TABLE IF NOT EXISTS `group_family` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `designation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `patient_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_7660F98B6B899279` (`patient_id`),
  CONSTRAINT `FK_7660F98B6B899279` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Export de données de la table suivi_patient.group_family : ~0 rows (environ)
/*!40000 ALTER TABLE `group_family` DISABLE KEYS */;
INSERT INTO `group_family` (`id`, `designation`, `patient_id`) VALUES
	(1, 'Alvin', 3);
/*!40000 ALTER TABLE `group_family` ENABLE KEYS */;

-- Export de la structure de la table suivi_patient. intervation_consultation
DROP TABLE IF EXISTS `intervation_consultation`;
CREATE TABLE IF NOT EXISTS `intervation_consultation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ordo_consulataion_id` int(11) DEFAULT NULL,
  `intervation_medicale_id` int(11) DEFAULT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `praticien_prescripteur_id` int(11) DEFAULT NULL,
  `praticien_consultant_id` int(11) DEFAULT NULL,
  `date_consultation` datetime DEFAULT NULL,
  `etat` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_12AA5A541D458C85` (`ordo_consulataion_id`),
  KEY `IDX_12AA5A54EB70E914` (`intervation_medicale_id`),
  KEY `IDX_12AA5A546B899279` (`patient_id`),
  KEY `IDX_12AA5A54772FC823` (`praticien_prescripteur_id`),
  KEY `IDX_12AA5A54B0CF52FF` (`praticien_consultant_id`),
  CONSTRAINT `FK_12AA5A541D458C85` FOREIGN KEY (`ordo_consulataion_id`) REFERENCES `ordo_consultation` (`id`),
  CONSTRAINT `FK_12AA5A546B899279` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`id`),
  CONSTRAINT `FK_12AA5A54772FC823` FOREIGN KEY (`praticien_prescripteur_id`) REFERENCES `praticien` (`id`),
  CONSTRAINT `FK_12AA5A54B0CF52FF` FOREIGN KEY (`praticien_consultant_id`) REFERENCES `praticien` (`id`),
  CONSTRAINT `FK_12AA5A54EB70E914` FOREIGN KEY (`intervation_medicale_id`) REFERENCES `intervation_medicale` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Export de données de la table suivi_patient.intervation_consultation : ~0 rows (environ)
/*!40000 ALTER TABLE `intervation_consultation` DISABLE KEYS */;
/*!40000 ALTER TABLE `intervation_consultation` ENABLE KEYS */;

-- Export de la structure de la table suivi_patient. intervation_medicale
DROP TABLE IF EXISTS `intervation_medicale`;
CREATE TABLE IF NOT EXISTS `intervation_medicale` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `praticien_id` int(11) DEFAULT NULL,
  `type_intervation` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nature_intervation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_intervation` datetime NOT NULL,
  `lieu_intervation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_sante_patient` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_60CB5AB62391866B` (`praticien_id`),
  CONSTRAINT `FK_60CB5AB62391866B` FOREIGN KEY (`praticien_id`) REFERENCES `praticien` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Export de données de la table suivi_patient.intervation_medicale : ~0 rows (environ)
/*!40000 ALTER TABLE `intervation_medicale` DISABLE KEYS */;
/*!40000 ALTER TABLE `intervation_medicale` ENABLE KEYS */;

-- Export de la structure de la table suivi_patient. intervention_vaccination
DROP TABLE IF EXISTS `intervention_vaccination`;
CREATE TABLE IF NOT EXISTS `intervention_vaccination` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vaccin_id` int(11) DEFAULT NULL,
  `praticien_prescripteur_id` int(11) DEFAULT NULL,
  `praticien_executant_id` int(11) DEFAULT NULL,
  `intervation_medicale_id` int(11) DEFAULT NULL,
  `ordo_vaccination_id` int(11) DEFAULT NULL,
  `status_vaccin` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_prise_vaccin` datetime NOT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `etat` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_65FA185E9B14AC76` (`vaccin_id`),
  KEY `IDX_65FA185E772FC823` (`praticien_prescripteur_id`),
  KEY `IDX_65FA185E551B4574` (`praticien_executant_id`),
  KEY `IDX_65FA185EEB70E914` (`intervation_medicale_id`),
  KEY `IDX_65FA185ECF9731E1` (`ordo_vaccination_id`),
  KEY `IDX_65FA185E6B899279` (`patient_id`),
  CONSTRAINT `FK_65FA185E551B4574` FOREIGN KEY (`praticien_executant_id`) REFERENCES `praticien` (`id`),
  CONSTRAINT `FK_65FA185E6B899279` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`id`),
  CONSTRAINT `FK_65FA185E772FC823` FOREIGN KEY (`praticien_prescripteur_id`) REFERENCES `praticien` (`id`),
  CONSTRAINT `FK_65FA185E9B14AC76` FOREIGN KEY (`vaccin_id`) REFERENCES `vaccin` (`id`),
  CONSTRAINT `FK_65FA185ECF9731E1` FOREIGN KEY (`ordo_vaccination_id`) REFERENCES `ordo_vaccination` (`id`),
  CONSTRAINT `FK_65FA185EEB70E914` FOREIGN KEY (`intervation_medicale_id`) REFERENCES `intervation_medicale` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Export de données de la table suivi_patient.intervention_vaccination : ~0 rows (environ)
/*!40000 ALTER TABLE `intervention_vaccination` DISABLE KEYS */;
/*!40000 ALTER TABLE `intervention_vaccination` ENABLE KEYS */;

-- Export de la structure de la table suivi_patient. ordonnace
DROP TABLE IF EXISTS `ordonnace`;
CREATE TABLE IF NOT EXISTS `ordonnace` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `praticien_id` int(11) DEFAULT NULL,
  `medecin_traitant_id` int(11) DEFAULT NULL,
  `date_prescription` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_CA1B5CF72391866B` (`praticien_id`),
  KEY `IDX_CA1B5CF7B572964A` (`medecin_traitant_id`),
  CONSTRAINT `FK_CA1B5CF72391866B` FOREIGN KEY (`praticien_id`) REFERENCES `praticien` (`id`),
  CONSTRAINT `FK_CA1B5CF7B572964A` FOREIGN KEY (`medecin_traitant_id`) REFERENCES `praticien` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Export de données de la table suivi_patient.ordonnace : ~0 rows (environ)
/*!40000 ALTER TABLE `ordonnace` DISABLE KEYS */;
INSERT INTO `ordonnace` (`id`, `praticien_id`, `medecin_traitant_id`, `date_prescription`) VALUES
	(1, 1, 1, '2020-03-04 12:00:00');
/*!40000 ALTER TABLE `ordonnace` ENABLE KEYS */;

-- Export de la structure de la table suivi_patient. ordo_consultation
DROP TABLE IF EXISTS `ordo_consultation`;
CREATE TABLE IF NOT EXISTS `ordo_consultation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ordonnance_id` int(11) DEFAULT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `date_rdv` datetime NOT NULL,
  `objet_consultation` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_consultation` int(11) DEFAULT NULL,
  `reference_praticient_executant` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type_praticien` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `etat` int(11) DEFAULT NULL,
  `proposition_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_F52CB9CE2BF23B8F` (`ordonnance_id`),
  KEY `IDX_F52CB9CE6B899279` (`patient_id`),
  KEY `IDX_F52CB9CEDB96F9E` (`proposition_id`),
  CONSTRAINT `FK_F52CB9CE2BF23B8F` FOREIGN KEY (`ordonnance_id`) REFERENCES `ordonnace` (`id`),
  CONSTRAINT `FK_F52CB9CE6B899279` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`id`),
  CONSTRAINT `FK_F52CB9CEDB96F9E` FOREIGN KEY (`proposition_id`) REFERENCES `proposition_rdv` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Export de données de la table suivi_patient.ordo_consultation : ~4 rows (environ)
/*!40000 ALTER TABLE `ordo_consultation` DISABLE KEYS */;
INSERT INTO `ordo_consultation` (`id`, `ordonnance_id`, `patient_id`, `date_rdv`, `objet_consultation`, `status_consultation`, `reference_praticient_executant`, `type_praticien`, `etat`, `proposition_id`) VALUES
	(1, 1, 5, '2020-07-18 23:00:00', 'consultation de notre bebe', 1, NULL, NULL, NULL, NULL),
	(2, 1, 3, '2020-12-12 20:20:00', 'description', 0, NULL, NULL, 0, NULL),
	(3, 1, 3, '2020-12-12 20:20:00', 'description', 0, NULL, NULL, 0, NULL),
	(4, 1, 3, '2020-12-12 20:20:00', 'description', 0, NULL, NULL, 0, NULL);
/*!40000 ALTER TABLE `ordo_consultation` ENABLE KEYS */;

-- Export de la structure de la table suivi_patient. ordo_medicaments
DROP TABLE IF EXISTS `ordo_medicaments`;
CREATE TABLE IF NOT EXISTS `ordo_medicaments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ordonnance_id` int(11) DEFAULT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `nom_medicament` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `posologie` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `statut_medicament` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference_praticien_executant` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_B1C83BD62BF23B8F` (`ordonnance_id`),
  KEY `IDX_B1C83BD66B899279` (`patient_id`),
  CONSTRAINT `FK_B1C83BD62BF23B8F` FOREIGN KEY (`ordonnance_id`) REFERENCES `ordonnace` (`id`),
  CONSTRAINT `FK_B1C83BD66B899279` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Export de données de la table suivi_patient.ordo_medicaments : ~0 rows (environ)
/*!40000 ALTER TABLE `ordo_medicaments` DISABLE KEYS */;
/*!40000 ALTER TABLE `ordo_medicaments` ENABLE KEYS */;

-- Export de la structure de la table suivi_patient. ordo_vaccination
DROP TABLE IF EXISTS `ordo_vaccination`;
CREATE TABLE IF NOT EXISTS `ordo_vaccination` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vaccin_id` int(11) DEFAULT NULL,
  `ordonnance_id` int(11) DEFAULT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `reference_praticien_executant_id` int(11) DEFAULT NULL,
  `date_prise` datetime NOT NULL,
  `status_vaccin` int(11) DEFAULT NULL,
  `etat` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_775928849B14AC76` (`vaccin_id`),
  KEY `IDX_775928842BF23B8F` (`ordonnance_id`),
  KEY `IDX_775928846B899279` (`patient_id`),
  KEY `IDX_7759288458082B8A` (`reference_praticien_executant_id`),
  CONSTRAINT `FK_775928842BF23B8F` FOREIGN KEY (`ordonnance_id`) REFERENCES `ordonnace` (`id`),
  CONSTRAINT `FK_7759288458082B8A` FOREIGN KEY (`reference_praticien_executant_id`) REFERENCES `praticien` (`id`),
  CONSTRAINT `FK_775928846B899279` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`id`),
  CONSTRAINT `FK_775928849B14AC76` FOREIGN KEY (`vaccin_id`) REFERENCES `vaccin` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Export de données de la table suivi_patient.ordo_vaccination : ~5 rows (environ)
/*!40000 ALTER TABLE `ordo_vaccination` DISABLE KEYS */;
INSERT INTO `ordo_vaccination` (`id`, `vaccin_id`, `ordonnance_id`, `patient_id`, `reference_praticien_executant_id`, `date_prise`, `status_vaccin`, `etat`) VALUES
	(1, 1, 1, 5, 1, '2020-07-18 00:00:00', 2, NULL),
	(2, 1, NULL, 5, 1, '2020-07-17 00:00:00', 0, NULL),
	(3, 2, 1, 5, 1, '2020-07-18 00:00:00', 1, NULL),
	(4, 28, 1, 3, 1, '2020-07-29 06:07:00', 1, NULL),
	(5, 27, 1, 3, 1, '2020-07-29 06:07:00', 0, NULL);
/*!40000 ALTER TABLE `ordo_vaccination` ENABLE KEYS */;

-- Export de la structure de la table suivi_patient. patient
DROP TABLE IF EXISTS `patient`;
CREATE TABLE IF NOT EXISTS `patient` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `address_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `type_patient_id` int(11) DEFAULT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sexe` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_on_born` datetime DEFAULT NULL,
  `father_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mother_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `etat` tinyint(1) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_enceinte` tinyint(1) DEFAULT NULL,
  `address_on_born_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `city_id` int(11) DEFAULT NULL,
  `num_rue` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quartier` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_1ADAD7EBA76ED395` (`user_id`),
  KEY `IDX_1ADAD7EB768A445C` (`type_patient_id`),
  KEY `IDX_1ADAD7EBF5B7AF75` (`address_id`),
  KEY `IDX_1ADAD7EB873CCBB7` (`address_on_born_id`),
  KEY `IDX_1ADAD7EB8BAC62AF` (`city_id`),
  CONSTRAINT `FK_1ADAD7EB768A445C` FOREIGN KEY (`type_patient_id`) REFERENCES `type_patient` (`id`),
  CONSTRAINT `FK_1ADAD7EB873CCBB7` FOREIGN KEY (`address_on_born_id`) REFERENCES `city` (`id`),
  CONSTRAINT `FK_1ADAD7EB8BAC62AF` FOREIGN KEY (`city_id`) REFERENCES `city` (`id`),
  CONSTRAINT `FK_1ADAD7EBA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_1ADAD7EBF5B7AF75` FOREIGN KEY (`address_id`) REFERENCES `city` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Export de données de la table suivi_patient.patient : ~2 rows (environ)
/*!40000 ALTER TABLE `patient` DISABLE KEYS */;
INSERT INTO `patient` (`id`, `address_id`, `user_id`, `type_patient_id`, `first_name`, `last_name`, `sexe`, `date_on_born`, `father_name`, `mother_name`, `etat`, `updated_at`, `phone`, `is_enceinte`, `address_on_born_id`, `created_at`, `city_id`, `num_rue`, `quartier`) VALUES
	(3, 7, 5, 1, 'Jean', 'managnora', 'Feminin', '2020-07-17 00:00:00', NULL, NULL, 1, '2020-07-02 13:47:22', '2543565464', NULL, 7, '2020-07-02 13:47:22', NULL, NULL, NULL),
	(5, NULL, 8, 1, 'dev', 'dev', 'femme', '1995-07-01 00:00:00', NULL, NULL, 0, '2020-07-10 05:22:34', NULL, NULL, NULL, '2020-07-10 05:22:34', NULL, NULL, NULL),
	(8, NULL, 12, 1, 'dev_paticien', 'devpaticien', 'femme', '1995-07-01 00:00:00', NULL, NULL, 0, '2020-07-10 06:56:06', NULL, NULL, NULL, '2020-07-10 06:56:06', 7, '4567', 'Mahazoarivo');
/*!40000 ALTER TABLE `patient` ENABLE KEYS */;

-- Export de la structure de la table suivi_patient. patient_carnet_vaccination
DROP TABLE IF EXISTS `patient_carnet_vaccination`;
CREATE TABLE IF NOT EXISTS `patient_carnet_vaccination` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) DEFAULT NULL,
  `carnet_vaccination_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_D528A5196B899279` (`patient_id`),
  KEY `IDX_D528A5192BA64CD3` (`carnet_vaccination_id`),
  CONSTRAINT `FK_D528A5192BA64CD3` FOREIGN KEY (`carnet_vaccination_id`) REFERENCES `carnet_vaccination` (`id`),
  CONSTRAINT `FK_D528A5196B899279` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Export de données de la table suivi_patient.patient_carnet_vaccination : ~0 rows (environ)
/*!40000 ALTER TABLE `patient_carnet_vaccination` DISABLE KEYS */;
/*!40000 ALTER TABLE `patient_carnet_vaccination` ENABLE KEYS */;

-- Export de la structure de la table suivi_patient. patient_intervation_consultation
DROP TABLE IF EXISTS `patient_intervation_consultation`;
CREATE TABLE IF NOT EXISTS `patient_intervation_consultation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) DEFAULT NULL,
  `intervention_consultation_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_1FFB53866B899279` (`patient_id`),
  KEY `IDX_1FFB5386E1BE2592` (`intervention_consultation_id`),
  CONSTRAINT `FK_1FFB53866B899279` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`id`),
  CONSTRAINT `FK_1FFB5386E1BE2592` FOREIGN KEY (`intervention_consultation_id`) REFERENCES `intervation_consultation` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Export de données de la table suivi_patient.patient_intervation_consultation : ~0 rows (environ)
/*!40000 ALTER TABLE `patient_intervation_consultation` DISABLE KEYS */;
/*!40000 ALTER TABLE `patient_intervation_consultation` ENABLE KEYS */;

-- Export de la structure de la table suivi_patient. patient_ordo_consultation
DROP TABLE IF EXISTS `patient_ordo_consultation`;
CREATE TABLE IF NOT EXISTS `patient_ordo_consultation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) DEFAULT NULL,
  `ordo_consultation_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_1E8D645E6B899279` (`patient_id`),
  KEY `IDX_1E8D645EFAAF079D` (`ordo_consultation_id`),
  CONSTRAINT `FK_1E8D645E6B899279` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`id`),
  CONSTRAINT `FK_1E8D645EFAAF079D` FOREIGN KEY (`ordo_consultation_id`) REFERENCES `ordo_consultation` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Export de données de la table suivi_patient.patient_ordo_consultation : ~0 rows (environ)
/*!40000 ALTER TABLE `patient_ordo_consultation` DISABLE KEYS */;
/*!40000 ALTER TABLE `patient_ordo_consultation` ENABLE KEYS */;

-- Export de la structure de la table suivi_patient. patient_ordo_medicaments
DROP TABLE IF EXISTS `patient_ordo_medicaments`;
CREATE TABLE IF NOT EXISTS `patient_ordo_medicaments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) DEFAULT NULL,
  `ordo_medicaments_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_1E2394136B899279` (`patient_id`),
  KEY `IDX_1E239413464829B9` (`ordo_medicaments_id`),
  CONSTRAINT `FK_1E239413464829B9` FOREIGN KEY (`ordo_medicaments_id`) REFERENCES `ordo_medicaments` (`id`),
  CONSTRAINT `FK_1E2394136B899279` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Export de données de la table suivi_patient.patient_ordo_medicaments : ~0 rows (environ)
/*!40000 ALTER TABLE `patient_ordo_medicaments` DISABLE KEYS */;
/*!40000 ALTER TABLE `patient_ordo_medicaments` ENABLE KEYS */;

-- Export de la structure de la table suivi_patient. patient_ordo_vaccination
DROP TABLE IF EXISTS `patient_ordo_vaccination`;
CREATE TABLE IF NOT EXISTS `patient_ordo_vaccination` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) DEFAULT NULL,
  `ordo_vaccination_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_D8B287416B899279` (`patient_id`),
  KEY `IDX_D8B28741CF9731E1` (`ordo_vaccination_id`),
  CONSTRAINT `FK_D8B287416B899279` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`id`),
  CONSTRAINT `FK_D8B28741CF9731E1` FOREIGN KEY (`ordo_vaccination_id`) REFERENCES `ordo_vaccination` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Export de données de la table suivi_patient.patient_ordo_vaccination : ~0 rows (environ)
/*!40000 ALTER TABLE `patient_ordo_vaccination` DISABLE KEYS */;
/*!40000 ALTER TABLE `patient_ordo_vaccination` ENABLE KEYS */;

-- Export de la structure de la table suivi_patient. patient_vaccin
DROP TABLE IF EXISTS `patient_vaccin`;
CREATE TABLE IF NOT EXISTS `patient_vaccin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) DEFAULT NULL,
  `vaccin_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_BE92495D6B899279` (`patient_id`),
  KEY `IDX_BE92495D9B14AC76` (`vaccin_id`),
  CONSTRAINT `FK_BE92495D6B899279` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`id`),
  CONSTRAINT `FK_BE92495D9B14AC76` FOREIGN KEY (`vaccin_id`) REFERENCES `vaccin` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Export de données de la table suivi_patient.patient_vaccin : ~0 rows (environ)
/*!40000 ALTER TABLE `patient_vaccin` DISABLE KEYS */;
/*!40000 ALTER TABLE `patient_vaccin` ENABLE KEYS */;

-- Export de la structure de la table suivi_patient. praticien
DROP TABLE IF EXISTS `praticien`;
CREATE TABLE IF NOT EXISTS `praticien` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_professional` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fonction` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_born` date NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `address_id` int(11) DEFAULT NULL,
  `etat` tinyint(1) DEFAULT NULL,
  `city_id` int(11) DEFAULT NULL,
  `num_rue` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quartier` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_D9A27D3A76ED395` (`user_id`),
  KEY `IDX_D9A27D3F5B7AF75` (`address_id`),
  KEY `IDX_D9A27D38BAC62AF` (`city_id`),
  CONSTRAINT `FK_D9A27D38BAC62AF` FOREIGN KEY (`city_id`) REFERENCES `city` (`id`),
  CONSTRAINT `FK_D9A27D3A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_D9A27D3F5B7AF75` FOREIGN KEY (`address_id`) REFERENCES `city` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Export de données de la table suivi_patient.praticien : ~1 rows (environ)
/*!40000 ALTER TABLE `praticien` DISABLE KEYS */;
INSERT INTO `praticien` (`id`, `user_id`, `first_name`, `last_name`, `phone`, `phone_professional`, `fonction`, `date_born`, `created_at`, `updated_at`, `address_id`, `etat`, `city_id`, `num_rue`, `quartier`) VALUES
	(1, 2, 'test', 'Praticien', '4334654675478474', '7657865859658957', 'Dr', '1995-02-07', '2020-06-28 16:03:32', '2020-06-28 16:03:32', NULL, 1, NULL, NULL, NULL),
	(3, 11, 'dev_paticien', 'devpaticien', '0341417474', '0341417474', 'generaliste', '1995-07-01', '2020-07-10 06:56:04', '2020-07-10 06:56:04', NULL, 1, 7, '4567', 'Mahazoarivo');
/*!40000 ALTER TABLE `praticien` ENABLE KEYS */;

-- Export de la structure de la table suivi_patient. praticien_specialite
DROP TABLE IF EXISTS `praticien_specialite`;
CREATE TABLE IF NOT EXISTS `praticien_specialite` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `praticien_id` int(11) DEFAULT NULL,
  `specialite_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_7B1F47DB2391866B` (`praticien_id`),
  KEY `IDX_7B1F47DB2195E0F0` (`specialite_id`),
  CONSTRAINT `FK_7B1F47DB2195E0F0` FOREIGN KEY (`specialite_id`) REFERENCES `specialite` (`id`),
  CONSTRAINT `FK_7B1F47DB2391866B` FOREIGN KEY (`praticien_id`) REFERENCES `praticien` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Export de données de la table suivi_patient.praticien_specialite : ~0 rows (environ)
/*!40000 ALTER TABLE `praticien_specialite` DISABLE KEYS */;
/*!40000 ALTER TABLE `praticien_specialite` ENABLE KEYS */;

-- Export de la structure de la table suivi_patient. proposition_rdv
DROP TABLE IF EXISTS `proposition_rdv`;
CREATE TABLE IF NOT EXISTS `proposition_rdv` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `praticien_id` int(11) DEFAULT NULL,
  `date_proposition` datetime NOT NULL,
  `description_proposition` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_proposition` int(11) DEFAULT NULL,
  `etat` int(11) DEFAULT NULL,
  `personne_attendre` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_6CAD70392391866B` (`praticien_id`),
  CONSTRAINT `FK_6CAD70392391866B` FOREIGN KEY (`praticien_id`) REFERENCES `praticien` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Export de données de la table suivi_patient.proposition_rdv : ~0 rows (environ)
/*!40000 ALTER TABLE `proposition_rdv` DISABLE KEYS */;
INSERT INTO `proposition_rdv` (`id`, `praticien_id`, `date_proposition`, `description_proposition`, `status_proposition`, `etat`, `personne_attendre`) VALUES
	(1, 1, '2020-07-23 18:50:00', 'tatatatatatattattata', 1, 0, 0);
/*!40000 ALTER TABLE `proposition_rdv` ENABLE KEYS */;

-- Export de la structure de la table suivi_patient. region
DROP TABLE IF EXISTS `region`;
CREATE TABLE IF NOT EXISTS `region` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `state_id` int(11) NOT NULL,
  `name_region` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_F62F1765D83CC1` (`state_id`),
  CONSTRAINT `FK_F62F1765D83CC1` FOREIGN KEY (`state_id`) REFERENCES `state` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Export de données de la table suivi_patient.region : ~24 rows (environ)
/*!40000 ALTER TABLE `region` DISABLE KEYS */;
INSERT INTO `region` (`id`, `state_id`, `name_region`) VALUES
	(3, 3, 'ADAMAOUA'),
	(4, 4, 'Auvergne-Rhône-Alpes'),
	(5, 5, 'Tana'),
	(6, 3, 'CENTRE'),
	(7, 3, 'EST'),
	(8, 3, 'EXTREME-NORD'),
	(9, 3, 'LITTORAL'),
	(10, 3, 'NORD'),
	(11, 3, 'NORD-OUEST'),
	(12, 3, 'SUD'),
	(13, 3, 'SUD-OUEST'),
	(14, 3, 'OUEST'),
	(15, 4, 'Bourgogne-Franche-Comté'),
	(16, 4, 'Bretagne'),
	(17, 4, 'Centre-Val de Loire'),
	(18, 4, 'Corse'),
	(19, 4, 'Grand Est'),
	(20, 4, 'Hauts-de-France'),
	(21, 4, 'Île-de-France'),
	(22, 4, 'Normandie'),
	(23, 4, 'Nouvelle-Aquitaine'),
	(24, 4, 'Occitanie'),
	(25, 4, 'Pays de la Loire'),
	(26, 4, 'Provence-Alpes-Côte d\'Azur');
/*!40000 ALTER TABLE `region` ENABLE KEYS */;

-- Export de la structure de la table suivi_patient. rendez_vous
DROP TABLE IF EXISTS `rendez_vous`;
CREATE TABLE IF NOT EXISTS `rendez_vous` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) DEFAULT NULL,
  `praticien_id` int(11) DEFAULT NULL,
  `vaccin_id` int(11) DEFAULT NULL,
  `date_rdv` datetime NOT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `etat` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_65E8AA0A6B899279` (`patient_id`),
  KEY `IDX_65E8AA0A2391866B` (`praticien_id`),
  KEY `IDX_65E8AA0A9B14AC76` (`vaccin_id`),
  CONSTRAINT `FK_65E8AA0A2391866B` FOREIGN KEY (`praticien_id`) REFERENCES `praticien` (`id`),
  CONSTRAINT `FK_65E8AA0A6B899279` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`id`),
  CONSTRAINT `FK_65E8AA0A9B14AC76` FOREIGN KEY (`vaccin_id`) REFERENCES `vaccin` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Export de données de la table suivi_patient.rendez_vous : ~0 rows (environ)
/*!40000 ALTER TABLE `rendez_vous` DISABLE KEYS */;
/*!40000 ALTER TABLE `rendez_vous` ENABLE KEYS */;

-- Export de la structure de la table suivi_patient. specialite
DROP TABLE IF EXISTS `specialite`;
CREATE TABLE IF NOT EXISTS `specialite` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom_specialite` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `note_specialite` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Export de données de la table suivi_patient.specialite : ~0 rows (environ)
/*!40000 ALTER TABLE `specialite` DISABLE KEYS */;
/*!40000 ALTER TABLE `specialite` ENABLE KEYS */;

-- Export de la structure de la table suivi_patient. state
DROP TABLE IF EXISTS `state`;
CREATE TABLE IF NOT EXISTS `state` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name_state` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Export de données de la table suivi_patient.state : ~3 rows (environ)
/*!40000 ALTER TABLE `state` DISABLE KEYS */;
INSERT INTO `state` (`id`, `name_state`) VALUES
	(3, 'CAMEROUN'),
	(4, 'FRANCE'),
	(5, 'Madagascar');
/*!40000 ALTER TABLE `state` ENABLE KEYS */;

-- Export de la structure de la table suivi_patient. type_patient
DROP TABLE IF EXISTS `type_patient`;
CREATE TABLE IF NOT EXISTS `type_patient` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_patient_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Export de données de la table suivi_patient.type_patient : ~3 rows (environ)
/*!40000 ALTER TABLE `type_patient` DISABLE KEYS */;
INSERT INTO `type_patient` (`id`, `type_patient_name`) VALUES
	(1, 'ADULTE'),
	(2, 'FEMME ENCEINTE'),
	(3, 'ENFANT');
/*!40000 ALTER TABLE `type_patient` ENABLE KEYS */;

-- Export de la structure de la table suivi_patient. type_vaccin
DROP TABLE IF EXISTS `type_vaccin`;
CREATE TABLE IF NOT EXISTS `type_vaccin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Export de données de la table suivi_patient.type_vaccin : ~4 rows (environ)
/*!40000 ALTER TABLE `type_vaccin` DISABLE KEYS */;
INSERT INTO `type_vaccin` (`id`, `type_name`) VALUES
	(5, 'ENFANT'),
	(6, 'ADULTE'),
	(7, 'AGE3'),
	(8, 'FEMME ENCEINTE');
/*!40000 ALTER TABLE `type_vaccin` ENABLE KEYS */;

-- Export de la structure de la table suivi_patient. user
DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(180) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `roles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`roles`)),
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `etat` int(11) NOT NULL,
  `activator_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8D93D649F85E0677` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Export de données de la table suivi_patient.user : ~6 rows (environ)
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` (`id`, `email`, `roles`, `password`, `last_name`, `first_name`, `created_at`, `etat`, `activator_id`, `username`, `updated_at`) VALUES
	(1, NULL, '["ROLE_ADMIN"]', '$2y$13$Y2UwhJfIhFop/kB6q9eeI.kI8ceDGzpv1Myzy.35tTESGtTkrSWb6', 'admin', 'admin', '2020-06-25 19:52:55', 1, NULL, 'admin', NULL),
	(2, NULL, '["ROLE_PRATICIEN"]', '$2y$13$zhNTdgC4dGnV/b75IYI62ey.9MaySQ.Vmwcz5W9hbLXcxx2cY6gOu', 'Praticien', 'test', '2020-06-28 16:03:32', 1, 'AMX208', 'tpraticien911', NULL),
	(5, NULL, '["ROLE_PATIENT"]', '$2y$13$D3wkLxOBQ05KxtChKJQ4GuGujmzoWMmIdpMPKzvUW5A4L4px1wCFG', 'managnora', 'Jean', '2020-07-02 13:47:21', 1, 'TKR17V', 'jmanagnora980', NULL),
	(8, 'devenjana@gmail.com', '["ROLE_PATIENT"]', '$2y$13$Bqo6AoIswX6dvaW8qrcpoexNG1b05afl0CkWy8eNjgjUQX.F3sVZG', 'dev', 'dev', '2020-07-10 05:22:33', 1, NULL, 'dev_enjana', '2020-07-10 05:22:33'),
	(11, 'devpaticienenjana@gmail.com', '["ROLE_PRATICIENT"]', '$2y$13$DqlfzsDEq2BL6WAK6Nv31.Hu74VzLoWqdLO0RfAq/2nNukOVwwr7O', 'devpaticien', 'dev_paticien', '2020-07-10 06:56:03', 1, NULL, 'devpraticien', '2020-07-10 06:56:03'),
	(12, 'devpaticienenjana@gmail.com', '["ROLE_PATIENT"]', '$2y$13$.LIQXv6FRKYgTVXygTYiZOPIU9sQ6OaykRncJDqpLUxM/n9sYTUN2', 'devpaticien', 'dev_paticien', '2020-07-10 06:56:05', 1, NULL, 'devpraticien3', '2020-07-10 06:56:05');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;

-- Export de la structure de la table suivi_patient. vaccin
DROP TABLE IF EXISTS `vaccin`;
CREATE TABLE IF NOT EXISTS `vaccin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_vaccin_id` int(11) DEFAULT NULL,
  `vaccin_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `vaccin_description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `etat` tinyint(1) DEFAULT NULL,
  `date_prise_initiale` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rappel1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rappel2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rappel3` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rappel4` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rappel5` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rappel6` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rappel7` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rappel8` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rappel9` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rappel10` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_B5DCA0A72FD2ED66` (`type_vaccin_id`),
  CONSTRAINT `FK_B5DCA0A72FD2ED66` FOREIGN KEY (`type_vaccin_id`) REFERENCES `type_vaccin` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Export de données de la table suivi_patient.vaccin : ~28 rows (environ)
/*!40000 ALTER TABLE `vaccin` DISABLE KEYS */;
INSERT INTO `vaccin` (`id`, `type_vaccin_id`, `vaccin_name`, `vaccin_description`, `etat`, `date_prise_initiale`, `rappel1`, `rappel2`, `rappel3`, `rappel4`, `rappel5`, `rappel6`, `rappel7`, `rappel8`, `rappel9`, `rappel10`) VALUES
	(1, 5, 'Antituberculeux : B.C.G', 'VPO - 0', 0, '1 week', '', '', '', '', '', '', '', '', '', ''),
	(2, 5, 'DTC – HepB + Hib 1', 'DOSE 1', 1, '6 week', '', '', '', '', '', '', '', '', '', ''),
	(3, 5, 'DTC-HepB2 + Hib2', 'DOSE 2', 1, '', '10 week', '', '', '', '', '', '', '', '', ''),
	(4, 5, 'DTC-HepB2 + Hib3', 'DOSE 3', 1, '', '', '14 week', '', '', '', '', '', '', '', ''),
	(5, 5, 'Pneumo 13-1 (VPO-1 + Rota1)', '', 1, '6 week', '', '', '', '', '', '', '', '', '', ''),
	(6, 5, 'Pneumo 13-2 (VPO-2 + Rota2)', '', 1, '', '10 week', '', '', '', '', '', '', '', '', ''),
	(7, 5, 'Pneumo 13-3 (VPO-3)', '', 1, '', '', '14 week', '', '', '', '', '', '', '', ''),
	(8, 5, 'VAR + VAA', '', 1, '', '', '', '36 week', '', '', '', '', '', '', ''),
	(9, 6, 'Calendrier Vaccin Adulte', 'Rappel dTcaP1 ou dTP si dernier', 1, '25 year', '45 year', '65 year', '75 year', '85 year', '95 year', '105 year', '', '', '', ''),
	(10, 6, 'Coqueluche acellulaire (ca)', 'Rappel de dTcaP < 5 ans', 1, '25 year', '', '', '', '', '', '', '', '', '', ''),
	(11, 7, 'Grippe', '1 dose annuelle dès 65 ans', 1, '65 year', '', '', '', '', '', '', '', '', '', ''),
	(12, 7, 'Zona', 'Une dose', 1, '65 year', '', '', '', '', '', '', '', '', '', ''),
	(13, 8, 'VAT1', 'Dès le début de la grossesse', 1, '0 month', '', '', '', '', '', '', '', '', '', ''),
	(14, 8, 'VAT2', '1 mois au moins après VAT1', 1, '', '1 month', '', '', '', '', '', '', '', '', ''),
	(15, 8, 'VAT3', '06 mois après VAT2', 1, '', '', '', '7 month', '', '', '', '', '', '', ''),
	(16, 8, 'VAT4', '1 an après VAT3', 1, '', '', '19 month', '', '', '', '', '', '', '', ''),
	(17, 8, 'VAT5', '1 an après VAT4', 1, '', '', '31 month', '', '', '', '', '', '', '', ''),
	(18, 5, 'DTCaP', 'Diphtérie (D), Tétanos (T), coqueluche acellulaire (Ca), Poliomyélite (P)', 1, '4 month', '5 month', '12 month', '132 month', '', '', '', '', '', '', ''),
	(19, 5, 'Hib', 'HibHaemophilus influenzae b (Hib)', 1, '4 month', '5 month', '12 month', '', '', '', '', '', '', '', ''),
	(20, 5, 'Hep B', 'Hépatite B (Hep B)', 1, '4 month', '5 month', '12 month', '', '', '', '', '', '', '', ''),
	(21, 5, 'PnC', 'Pneumocoque (PnC)1', 1, '4 month', '5 month', '12 month', '', '', '', '', '', '', '', ''),
	(22, 5, 'MnC', 'Méningocoque C (vaccin conjugué MnC)', 1, '11 month', '16 month', '', '', '', '', '', '', '', '', ''),
	(23, 5, 'ROR', 'Rougeole (R), Oreillons (O), Rubéole ®', 1, '16 month', '72 month', '', '', '', '', '', '', '', '', ''),
	(24, 5, 'dTcaP-ado', 'Diphtérie (d), Tétanos (T), Coqueluche acellulaire (ca), Poliomyélite (P)2', 1, '180 month', '', '', '', '', '', '', '', '', '', ''),
	(25, 5, 'HPV', 'Papillomavirus humains (HPV) chez les jeunes filles jeunes garçons', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
	(26, 5, 'BCG', 'Tuberculose (BCG)', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
	(27, 6, 'Anatrart', '', 1, '', '', '', '', '', '', '', '', '', '', ''),
	(28, 5, 'Anatrart 2', '', 0, '', '', '', '', '', '', '', '', '', '', '');
/*!40000 ALTER TABLE `vaccin` ENABLE KEYS */;

-- Export de la structure de la table suivi_patient. vaccin_centre_health
DROP TABLE IF EXISTS `vaccin_centre_health`;
CREATE TABLE IF NOT EXISTS `vaccin_centre_health` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `centre_health_id` int(11) DEFAULT NULL,
  `vaccin_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_FD93942E57EA3DF5` (`centre_health_id`),
  KEY `IDX_FD93942E9B14AC76` (`vaccin_id`),
  CONSTRAINT `FK_FD93942E57EA3DF5` FOREIGN KEY (`centre_health_id`) REFERENCES `centre_health` (`id`),
  CONSTRAINT `FK_FD93942E9B14AC76` FOREIGN KEY (`vaccin_id`) REFERENCES `vaccin` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Export de données de la table suivi_patient.vaccin_centre_health : ~0 rows (environ)
/*!40000 ALTER TABLE `vaccin_centre_health` DISABLE KEYS */;
INSERT INTO `vaccin_centre_health` (`id`, `centre_health_id`, `vaccin_id`) VALUES
	(9, 2, 10);
/*!40000 ALTER TABLE `vaccin_centre_health` ENABLE KEYS */;

-- Export de la structure de la table suivi_patient. vaccin_praticien
DROP TABLE IF EXISTS `vaccin_praticien`;
CREATE TABLE IF NOT EXISTS `vaccin_praticien` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `praticien_id` int(11) DEFAULT NULL,
  `vaccin_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_CCD916DE2391866B` (`praticien_id`),
  KEY `IDX_CCD916DE9B14AC76` (`vaccin_id`),
  CONSTRAINT `FK_CCD916DE2391866B` FOREIGN KEY (`praticien_id`) REFERENCES `praticien` (`id`),
  CONSTRAINT `FK_CCD916DE9B14AC76` FOREIGN KEY (`vaccin_id`) REFERENCES `vaccin` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Export de données de la table suivi_patient.vaccin_praticien : ~0 rows (environ)
/*!40000 ALTER TABLE `vaccin_praticien` DISABLE KEYS */;
/*!40000 ALTER TABLE `vaccin_praticien` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
