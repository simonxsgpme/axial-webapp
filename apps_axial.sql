-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : jeu. 12 mars 2026 à 19:36
-- Version du serveur : 8.4.3
-- Version de PHP : 8.2.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `apps_axial`
--

-- --------------------------------------------------------

--
-- Structure de la table `campaigns`
--

CREATE TABLE `campaigns` (
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `year` year NOT NULL,
  `objective_starts_at` date DEFAULT NULL,
  `objective_stops_at` date DEFAULT NULL,
  `midterm_starts_at` date DEFAULT NULL,
  `midterm_stops_at` date DEFAULT NULL,
  `evaluation_starts_at` date DEFAULT NULL,
  `evaluation_stops_at` date DEFAULT NULL,
  `status` enum('draft','objective_in_progress','objective_completed','midterm_in_progress','midterm_completed','evaluation_in_progress','evaluation_completed','archived') COLLATE utf8mb4_unicode_ci DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `campaigns`
--

INSERT INTO `campaigns` (`uuid`, `name`, `description`, `year`, `objective_starts_at`, `objective_stops_at`, `midterm_starts_at`, `midterm_stops_at`, `evaluation_starts_at`, `evaluation_stops_at`, `status`, `created_at`, `updated_at`) VALUES
('a1482634-712c-4aea-b5ea-29ff8c7e0517', 'Evaluation Effectif SGPME', NULL, '2026', '2026-03-12', '2026-03-26', '2026-04-01', '2026-04-30', '2026-05-01', '2026-06-30', 'evaluation_completed', '2026-03-12 10:48:26', '2026-03-12 11:30:47'),
('a1483218-1ecb-42d0-9ed1-de826bac3f58', 'Evaluation SSI', NULL, '2026', '2026-03-13', '2026-03-19', '2026-03-26', '2026-04-02', '2026-04-01', '2026-04-30', 'midterm_in_progress', '2026-03-12 11:21:41', '2026-03-12 11:53:26');

-- --------------------------------------------------------

--
-- Structure de la table `entities`
--

CREATE TABLE `entities` (
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `acronym` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category` enum('direction','service','departement') COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `entities`
--

INSERT INTO `entities` (`uuid`, `name`, `acronym`, `category`, `parent_uuid`, `created_at`, `updated_at`) VALUES
('a148259d-846b-481c-8780-ff736cf01cd7', 'Direction Générale', 'DG', 'direction', NULL, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259d-8699-47a8-ac41-d3efa9911d08', 'Direction Administration et Ressources', 'DAR', 'direction', NULL, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259d-882f-4800-971f-62129095ab9f', 'Direction des Risques', 'DR', 'direction', NULL, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259d-89ad-480b-9734-fcbd74a836c8', 'Direction Commerciale', 'DCOM', 'direction', NULL, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259d-8afb-491b-8ed1-de2b991ea641', 'Direction Octrois et Engagements', 'DOE', 'direction', NULL, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259d-8d35-4210-baed-35dbf20d2e47', 'Moyens Généraux', 'MG', 'service', 'a148259d-8699-47a8-ac41-d3efa9911d08', '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259d-8ea2-40e5-a78a-6c82ba9bf171', 'Finances & Comptabilité', 'FC', 'service', 'a148259d-8699-47a8-ac41-d3efa9911d08', '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259d-900c-48f9-afbe-8ef98ea08448', 'Systèmes d\'Information', 'SI', 'service', 'a148259d-8699-47a8-ac41-d3efa9911d08', '2026-03-12 10:46:48', '2026-03-12 10:46:48'),
('a148259d-91ed-4dd7-ab59-2f609bb0703d', 'Ressources Humaines', 'RH', 'service', 'a148259d-8699-47a8-ac41-d3efa9911d08', '2026-03-12 10:46:48', '2026-03-12 10:46:48'),
('a148259d-936b-4b21-b76d-0d1633c16e14', 'Gestion des risques et contrôle permanent', 'GRCP', 'service', 'a148259d-882f-4800-971f-62129095ab9f', '2026-03-12 10:46:48', '2026-03-12 10:46:48'),
('a148259d-94e0-4c28-9978-cec30da6c1c8', 'RSE', 'RSE', 'service', 'a148259d-882f-4800-971f-62129095ab9f', '2026-03-12 10:46:48', '2026-03-12 10:46:48'),
('a148259d-966b-4e10-b0bc-59eabdbef7eb', 'Conformité', 'CONF', 'service', 'a148259d-882f-4800-971f-62129095ab9f', '2026-03-12 10:46:48', '2026-03-12 10:46:48'),
('a148259d-9818-47f5-8c93-c1c84fca8f89', 'Juridique & Contentieux', 'JC', 'departement', 'a148259d-846b-481c-8780-ff736cf01cd7', '2026-03-12 10:46:48', '2026-03-12 10:46:48'),
('a148259d-99c5-4dd8-b5e5-a84c56dccbd7', 'Audit Interne', 'AI', 'departement', 'a148259d-846b-481c-8780-ff736cf01cd7', '2026-03-12 10:46:48', '2026-03-12 10:46:48');

-- --------------------------------------------------------

--
-- Structure de la table `evaluation_comments`
--

CREATE TABLE `evaluation_comments` (
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `objective_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `evaluation_decisions`
--

CREATE TABLE `evaluation_decisions` (
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_campaign_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `actor_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `action` enum('submitted_to_employee','returned_to_supervisor','validated') COLLATE utf8mb4_unicode_ci NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_11_000000_create_roles_table', 1),
(2, '2014_10_11_100000_create_permissions_table', 1),
(3, '2014_10_11_200000_create_role_permissions_table', 1),
(4, '2014_10_11_300000_create_entities_table', 1),
(5, '2014_10_12_000000_create_users_table', 1),
(6, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(7, '2014_10_13_000000_create_campaigns_table', 1),
(8, '2014_10_13_100000_create_user_campaigns_table', 1),
(9, '2014_10_14_000000_create_objective_categories_table', 1),
(10, '2014_10_14_100000_create_objectives_table', 1),
(11, '2014_10_14_200000_create_objective_comments_table', 1),
(12, '2014_10_14_300000_create_objective_decisions_table', 1),
(13, '2014_10_15_100000_create_evaluation_comments_table', 1),
(14, '2014_10_15_200000_create_evaluation_decisions_table', 1),
(15, '2019_08_19_000000_create_failed_jobs_table', 1),
(16, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(17, '2026_02_26_000001_add_parent_uuid_and_acronym_to_entities_table', 1),
(18, '2026_02_26_000002_add_midterm_phase_to_campaigns_table', 1),
(19, '2026_02_26_000003_add_midterm_fields_to_user_campaigns_table', 1),
(20, '2026_02_26_000004_create_objective_histories_table', 1),
(21, '2026_03_09_130323_add_hire_date_to_users_table', 1),
(22, '2026_03_12_104211_add_category_to_permissions_table', 1),
(23, '2026_03_12_105609_add_not_evaluated_to_user_campaigns_status', 2);

-- --------------------------------------------------------

--
-- Structure de la table `objectives`
--

CREATE TABLE `objectives` (
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_campaign_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `objective_category_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `weight` int NOT NULL DEFAULT '0',
  `status` enum('pending','validated','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `rejection_reason` text COLLATE utf8mb4_unicode_ci,
  `score` decimal(5,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `objectives`
--

INSERT INTO `objectives` (`uuid`, `user_campaign_uuid`, `objective_category_uuid`, `title`, `description`, `weight`, `status`, `rejection_reason`, `score`, `created_at`, `updated_at`) VALUES
('a1482767-a8d7-4d24-9ad7-8a628f15f6e6', 'a1482671-98a3-4d58-824c-30574e273589', 'a14825a9-c96c-45cf-9f8d-7c96ee2a8e17', 'objectif 1', NULL, 40, 'validated', NULL, NULL, '2026-03-12 10:51:48', '2026-03-12 10:54:35'),
('a148277a-c36c-47a4-8a01-64e0f432193b', 'a1482671-98a3-4d58-824c-30574e273589', 'a14825a9-c96c-45cf-9f8d-7c96ee2a8e17', 'Objectif 2', NULL, 40, 'validated', NULL, NULL, '2026-03-12 10:52:00', '2026-03-12 10:54:35'),
('a148278f-7b31-4dd3-9ee4-e978c54eda60', 'a1482671-98a3-4d58-824c-30574e273589', 'a14825a9-c96c-45cf-9f8d-7c96ee2a8e17', 'Objectif 3', NULL, 5, 'validated', NULL, NULL, '2026-03-12 10:52:14', '2026-03-12 10:54:35'),
('a14827a6-8116-40f2-bd25-976c53b7fd20', 'a1482671-98a3-4d58-824c-30574e273589', 'a14825a9-cb8e-4d42-9fb0-b02693c18e61', 'Objectif 4', NULL, 10, 'validated', NULL, NULL, '2026-03-12 10:52:29', '2026-03-12 10:54:35'),
('a14827bd-fdb0-4c1d-bdae-f9e1c4200de2', 'a1482671-98a3-4d58-824c-30574e273589', 'a14825a9-cd4f-4a7f-b8fc-25bc74b42535', 'Objectif 5', NULL, 5, 'validated', NULL, NULL, '2026-03-12 10:52:44', '2026-03-12 10:54:35'),
('a148360f-b850-4b64-9fc6-62367357b48b', 'a14833d0-1225-4897-a9bf-76adee888fc3', 'a14825a9-c96c-45cf-9f8d-7c96ee2a8e17', 'objectif 1', NULL, 40, 'validated', NULL, NULL, '2026-03-12 11:32:47', '2026-03-12 11:47:48'),
('a148361e-51ed-41cb-a834-f8e4ec1d626c', 'a14833d0-1225-4897-a9bf-76adee888fc3', 'a14825a9-c96c-45cf-9f8d-7c96ee2a8e17', 'Objectif 2', NULL, 40, 'validated', NULL, NULL, '2026-03-12 11:32:56', '2026-03-12 11:47:48'),
('a148363a-e49a-4148-9c4e-0d14804db6d4', 'a14833d0-1225-4897-a9bf-76adee888fc3', 'a14825a9-c96c-45cf-9f8d-7c96ee2a8e17', 'Objectif 3', NULL, 5, 'validated', NULL, NULL, '2026-03-12 11:33:15', '2026-03-12 11:47:48'),
('a1483650-214a-40c7-ae06-4b5a74c39544', 'a14833d0-1225-4897-a9bf-76adee888fc3', 'a14825a9-cb8e-4d42-9fb0-b02693c18e61', 'Objectif 4', NULL, 10, 'validated', NULL, NULL, '2026-03-12 11:33:29', '2026-03-12 11:47:48'),
('a14839d9-2ab0-4636-a6ab-df66c9ef276b', 'a14833d0-1225-4897-a9bf-76adee888fc3', 'a14825a9-cd4f-4a7f-b8fc-25bc74b42535', 'Objectif 5', NULL, 5, 'validated', NULL, NULL, '2026-03-12 11:43:22', '2026-03-12 11:47:48');

-- --------------------------------------------------------

--
-- Structure de la table `objective_categories`
--

CREATE TABLE `objective_categories` (
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `percentage` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `objective_categories`
--

INSERT INTO `objective_categories` (`uuid`, `name`, `description`, `percentage`, `created_at`, `updated_at`) VALUES
('a14825a9-c96c-45cf-9f8d-7c96ee2a8e17', 'Individuel', 'Objectifs Individuels', 85, '2026-03-12 10:46:56', '2026-03-12 10:46:56'),
('a14825a9-cb8e-4d42-9fb0-b02693c18e61', 'Collectif', 'Objectifs Collectifs', 10, '2026-03-12 10:46:56', '2026-03-12 10:46:56'),
('a14825a9-cd4f-4a7f-b8fc-25bc74b42535', 'Comportemental', 'Objectifs Comportementaux', 5, '2026-03-12 10:46:56', '2026-03-12 10:46:56');

-- --------------------------------------------------------

--
-- Structure de la table `objective_comments`
--

CREATE TABLE `objective_comments` (
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `objective_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `objective_decisions`
--

CREATE TABLE `objective_decisions` (
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_campaign_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `actor_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `action` enum('submitted','returned','completed') COLLATE utf8mb4_unicode_ci NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `objective_decisions`
--

INSERT INTO `objective_decisions` (`uuid`, `user_campaign_uuid`, `actor_uuid`, `action`, `comment`, `created_at`, `updated_at`) VALUES
('a14827c5-e3d5-457a-a589-7845cf624042', 'a1482671-98a3-4d58-824c-30574e273589', 'a14825a9-33b8-4940-a5ac-05f6b1e47bf3', 'submitted', 'Objectifs soumis pour validation.', '2026-03-12 10:52:49', '2026-03-12 10:52:49'),
('a1482867-25b2-4e1e-919e-8a546fda9494', 'a1482671-98a3-4d58-824c-30574e273589', 'a14825a4-cb76-47b7-af7d-989234da5037', 'completed', 'Tous les objectifs ont été validés. Phase objectifs terminée.', '2026-03-12 10:54:35', '2026-03-12 10:54:35'),
('a14839e1-220f-41f4-8cac-eafdaad29b6a', 'a14833d0-1225-4897-a9bf-76adee888fc3', 'a14825a9-33b8-4940-a5ac-05f6b1e47bf3', 'submitted', 'Objectifs soumis pour validation.', '2026-03-12 11:43:27', '2026-03-12 11:43:27'),
('a1483b6e-c3c0-49b2-8234-843728b9de18', 'a14833d0-1225-4897-a9bf-76adee888fc3', 'a14825a4-cb76-47b7-af7d-989234da5037', 'completed', 'Tous les objectifs ont été validés. Phase objectifs terminée.', '2026-03-12 11:47:48', '2026-03-12 11:47:48');

-- --------------------------------------------------------

--
-- Structure de la table `objective_histories`
--

CREATE TABLE `objective_histories` (
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `objective_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `changed_by_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `field` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `old_value` text COLLATE utf8mb4_unicode_ci,
  `new_value` text COLLATE utf8mb4_unicode_ci,
  `phase` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `permissions`
--

CREATE TABLE `permissions` (
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `permissions`
--

INSERT INTO `permissions` (`uuid`, `name`, `category`, `slug`, `created_at`, `updated_at`) VALUES
('a148259c-302f-457e-bf88-9140d339535c', 'Voir les utilisateurs', 'Utilisateurs', 'voir-utilisateurs', '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-3b10-487a-b965-49ddbc6a3cdb', 'Créer des utilisateurs', 'Utilisateurs', 'creer-utilisateurs', '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-4598-41d0-9719-c805ccbcd677', 'Modifier des utilisateurs', 'Utilisateurs', 'modifier-utilisateurs', '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-5066-44cb-9897-9ef08541d452', 'Supprimer des utilisateurs', 'Utilisateurs', 'supprimer-utilisateurs', '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-5bd1-4af1-b4d2-afb87d16026a', 'Importer des utilisateurs', 'Utilisateurs', 'importer-utilisateurs', '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-663f-4faa-beab-12c9e6de19fc', 'Voir les rôles', 'Rôles & Permissions', 'voir-roles', '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-7081-4bd4-89dd-83261b031eff', 'Gérer les rôles', 'Rôles & Permissions', 'gerer-roles', '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-7ac4-4b48-962b-f8dc0e3f9c99', 'Gérer les permissions', 'Rôles & Permissions', 'gerer-permissions', '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-8621-4e2a-8bad-14188fa618d5', 'Voir les entités', 'Entités', 'voir-entites', '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-915c-4f0a-aa0d-d11d5946a22f', 'Gérer les entités', 'Entités', 'gerer-entites', '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-9bdf-4160-9729-cdbd83481b46', 'Voir les campagnes', 'Campagnes', 'voir-campagnes', '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-a60c-40f1-9468-24a70e91eb20', 'Créer des campagnes', 'Campagnes', 'creer-campagnes', '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-afec-41ba-9160-235ba69026dd', 'Modifier des campagnes', 'Campagnes', 'modifier-campagnes', '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-b8b2-4531-ad39-08a4a70b7cf8', 'Supprimer des campagnes', 'Campagnes', 'supprimer-campagnes', '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-c0ca-4823-bffb-bebb750f5a47', 'Gérer les phases de campagne', 'Campagnes', 'gerer-phases-campagnes', '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-c7bc-44d6-8688-136c23c6ceaa', 'Voir mes objectifs', 'Objectifs', 'voir-mes-objectifs', '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-cea4-42ec-87e1-5d2e8ab50eb1', 'Créer mes objectifs', 'Objectifs', 'creer-mes-objectifs', '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-d59f-4f4e-96a6-8be942b04c2b', 'Modifier mes objectifs', 'Objectifs', 'modifier-mes-objectifs', '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-dc87-4606-80ab-743e2f4d080b', 'Supprimer mes objectifs', 'Objectifs', 'supprimer-mes-objectifs', '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-e374-46f7-adba-0f7af0ceac0a', 'Soumettre mes objectifs', 'Objectifs', 'soumettre-mes-objectifs', '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-eacd-43c7-9a99-427c73efd58f', 'Voir les objectifs des collaborateurs', 'Validation', 'voir-objectifs-collaborateurs', '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-f28c-411b-9a62-1778cbb2e403', 'Valider les objectifs', 'Validation', 'valider-objectifs', '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-f9cd-424e-8866-8c071f9446b5', 'Rejeter les objectifs', 'Validation', 'rejeter-objectifs', '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259d-0125-49ae-a206-e6179747ab1f', 'Télécharger fiche objectifs', 'Validation', 'telecharger-fiche-objectifs', '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259d-09d3-46a2-b36d-abda82d4123f', 'Modifier objectifs mi-parcours', 'Mi-parcours', 'modifier-objectifs-midterm', '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259d-12df-473d-b3c5-f2f7f5153e23', 'Voir évaluations mi-parcours', 'Mi-parcours', 'voir-evaluations-midterm', '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259d-1e28-4448-bbb9-0baaf9cafaed', 'Télécharger fiche mi-parcours', 'Mi-parcours', 'telecharger-fiche-midterm', '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259d-2940-4e22-a34e-b677b2ed090e', 'Importer fiche mi-parcours', 'Mi-parcours', 'importer-fiche-midterm', '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259d-352a-4f38-9c82-5200ee605da1', 'Voir les évaluations', 'Évaluation', 'voir-evaluations', '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259d-40a6-40cd-9caf-c3bc6362c255', 'Évaluer les collaborateurs', 'Évaluation', 'evaluer-collaborateurs', '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259d-4bd1-4c43-8e09-076d9f7f9a28', 'Valider les évaluations', 'Évaluation', 'valider-evaluations', '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259d-55c4-4971-8497-47a4925ed3ec', 'Voir mon évaluation', 'Évaluation', 'voir-mon-evaluation', '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259d-5ff0-491a-a889-3a4b8a02caca', 'Voir les catégories d\'objectifs', 'Catégories', 'voir-categories-objectifs', '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259d-6a40-469e-b85d-237115135348', 'Gérer les catégories d\'objectifs', 'Catégories', 'gerer-categories-objectifs', '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259d-7382-420c-908b-fa45e5ed35ee', 'Voir le tableau de bord', 'Dashboard', 'voir-tableau-de-bord', '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259d-7c02-4a24-8669-0834b1d6a078', 'Voir les statistiques globales', 'Dashboard', 'voir-statistiques-globales', '2026-03-12 10:46:47', '2026-03-12 10:46:47');

-- --------------------------------------------------------

--
-- Structure de la table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `roles`
--

CREATE TABLE `roles` (
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `roles`
--

INSERT INTO `roles` (`uuid`, `name`, `slug`, `created_at`, `updated_at`) VALUES
('a148259c-26e1-403e-a782-2ff412a67b50', 'Administrateur', 'administrateur', '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-2a2e-44f5-854c-3f17e6227c62', 'Manager', 'manager', '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-2cd8-4181-a1aa-33b3fd4c1bad', 'Collaborateur', 'collaborateur', '2026-03-12 10:46:47', '2026-03-12 10:46:47');

-- --------------------------------------------------------

--
-- Structure de la table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `permission_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `role_permissions`
--

INSERT INTO `role_permissions` (`uuid`, `role_uuid`, `permission_uuid`, `status`, `created_at`, `updated_at`) VALUES
('a148259c-33f8-462e-960b-dd39dfa6adcc', 'a148259c-26e1-403e-a782-2ff412a67b50', 'a148259c-302f-457e-bf88-9140d339535c', 1, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-364f-4eb3-b408-5e961707def4', 'a148259c-2a2e-44f5-854c-3f17e6227c62', 'a148259c-302f-457e-bf88-9140d339535c', 0, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-38ab-4899-ada0-e79f14f3cd14', 'a148259c-2cd8-4181-a1aa-33b3fd4c1bad', 'a148259c-302f-457e-bf88-9140d339535c', 0, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-3d55-4f84-bd31-0ac8df95f777', 'a148259c-26e1-403e-a782-2ff412a67b50', 'a148259c-3b10-487a-b965-49ddbc6a3cdb', 1, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-409c-4224-aa5e-7636c6811a8c', 'a148259c-2a2e-44f5-854c-3f17e6227c62', 'a148259c-3b10-487a-b965-49ddbc6a3cdb', 0, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-4367-4f1a-a1e2-5a8281939445', 'a148259c-2cd8-4181-a1aa-33b3fd4c1bad', 'a148259c-3b10-487a-b965-49ddbc6a3cdb', 0, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-47cb-4df5-a4c7-b158baa93ca3', 'a148259c-26e1-403e-a782-2ff412a67b50', 'a148259c-4598-41d0-9719-c805ccbcd677', 1, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-4a6a-4058-b7dd-db28b47e8805', 'a148259c-2a2e-44f5-854c-3f17e6227c62', 'a148259c-4598-41d0-9719-c805ccbcd677', 0, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-4cf5-4ae0-9346-a7dc68d93fce', 'a148259c-2cd8-4181-a1aa-33b3fd4c1bad', 'a148259c-4598-41d0-9719-c805ccbcd677', 0, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-5321-4903-b610-e53f74ed5d45', 'a148259c-26e1-403e-a782-2ff412a67b50', 'a148259c-5066-44cb-9897-9ef08541d452', 1, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-55a8-4aaf-a273-eb4dabfce2f0', 'a148259c-2a2e-44f5-854c-3f17e6227c62', 'a148259c-5066-44cb-9897-9ef08541d452', 0, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-58c6-4d58-8ab3-656db0bc4b0e', 'a148259c-2cd8-4181-a1aa-33b3fd4c1bad', 'a148259c-5066-44cb-9897-9ef08541d452', 0, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-5eaf-4c5e-940a-e4848b64f71b', 'a148259c-26e1-403e-a782-2ff412a67b50', 'a148259c-5bd1-4af1-b4d2-afb87d16026a', 1, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-614b-4d57-9170-dc1b5e637cca', 'a148259c-2a2e-44f5-854c-3f17e6227c62', 'a148259c-5bd1-4af1-b4d2-afb87d16026a', 0, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-63d1-46a9-9105-4855dc53c447', 'a148259c-2cd8-4181-a1aa-33b3fd4c1bad', 'a148259c-5bd1-4af1-b4d2-afb87d16026a', 0, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-68a7-4a70-8912-3b234b1422f5', 'a148259c-26e1-403e-a782-2ff412a67b50', 'a148259c-663f-4faa-beab-12c9e6de19fc', 1, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-6b47-4a4e-b398-5f236d14e0a8', 'a148259c-2a2e-44f5-854c-3f17e6227c62', 'a148259c-663f-4faa-beab-12c9e6de19fc', 0, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-6db6-42ff-89ab-c7a83d7b029b', 'a148259c-2cd8-4181-a1aa-33b3fd4c1bad', 'a148259c-663f-4faa-beab-12c9e6de19fc', 0, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-7341-4b65-8aa0-7839a35543f9', 'a148259c-26e1-403e-a782-2ff412a67b50', 'a148259c-7081-4bd4-89dd-83261b031eff', 1, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-75a9-47c5-a794-c672c84bca3f', 'a148259c-2a2e-44f5-854c-3f17e6227c62', 'a148259c-7081-4bd4-89dd-83261b031eff', 0, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-7820-48f9-9df2-b2010a864b59', 'a148259c-2cd8-4181-a1aa-33b3fd4c1bad', 'a148259c-7081-4bd4-89dd-83261b031eff', 0, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-7d82-4b65-8018-e992dda62c6b', 'a148259c-26e1-403e-a782-2ff412a67b50', 'a148259c-7ac4-4b48-962b-f8dc0e3f9c99', 1, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-8024-4389-8e44-f5c4ab79bd5d', 'a148259c-2a2e-44f5-854c-3f17e6227c62', 'a148259c-7ac4-4b48-962b-f8dc0e3f9c99', 0, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-8319-4269-a2a2-54148c2682e3', 'a148259c-2cd8-4181-a1aa-33b3fd4c1bad', 'a148259c-7ac4-4b48-962b-f8dc0e3f9c99', 0, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-8945-492a-b47a-ae6b4ce01860', 'a148259c-26e1-403e-a782-2ff412a67b50', 'a148259c-8621-4e2a-8bad-14188fa618d5', 1, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-8c30-49e2-8ea7-373c802eb42d', 'a148259c-2a2e-44f5-854c-3f17e6227c62', 'a148259c-8621-4e2a-8bad-14188fa618d5', 0, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-8e8c-44fc-9050-db30b01b0c65', 'a148259c-2cd8-4181-a1aa-33b3fd4c1bad', 'a148259c-8621-4e2a-8bad-14188fa618d5', 0, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-9413-43ea-b591-43089f0fc4e7', 'a148259c-26e1-403e-a782-2ff412a67b50', 'a148259c-915c-4f0a-aa0d-d11d5946a22f', 1, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-96c1-4ccf-a146-7a466e9b941d', 'a148259c-2a2e-44f5-854c-3f17e6227c62', 'a148259c-915c-4f0a-aa0d-d11d5946a22f', 0, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-9954-47c4-8aee-017234901b65', 'a148259c-2cd8-4181-a1aa-33b3fd4c1bad', 'a148259c-915c-4f0a-aa0d-d11d5946a22f', 0, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-9e81-4f24-a621-f761a587aeaa', 'a148259c-26e1-403e-a782-2ff412a67b50', 'a148259c-9bdf-4160-9729-cdbd83481b46', 1, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-a0fb-423b-a664-fc1296ab8d13', 'a148259c-2a2e-44f5-854c-3f17e6227c62', 'a148259c-9bdf-4160-9729-cdbd83481b46', 0, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-a393-4e8f-99d8-b0a2a61ddbcd', 'a148259c-2cd8-4181-a1aa-33b3fd4c1bad', 'a148259c-9bdf-4160-9729-cdbd83481b46', 0, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-a8ad-4a8b-8c40-1b464c02ac97', 'a148259c-26e1-403e-a782-2ff412a67b50', 'a148259c-a60c-40f1-9468-24a70e91eb20', 1, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-ab32-4194-9557-21e1d720740e', 'a148259c-2a2e-44f5-854c-3f17e6227c62', 'a148259c-a60c-40f1-9468-24a70e91eb20', 0, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-ad8c-4f1b-a315-51bbb39cf964', 'a148259c-2cd8-4181-a1aa-33b3fd4c1bad', 'a148259c-a60c-40f1-9468-24a70e91eb20', 0, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-b241-4732-94e9-b734205adea8', 'a148259c-26e1-403e-a782-2ff412a67b50', 'a148259c-afec-41ba-9160-235ba69026dd', 1, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-b48b-430c-b138-a58e02585fd5', 'a148259c-2a2e-44f5-854c-3f17e6227c62', 'a148259c-afec-41ba-9160-235ba69026dd', 0, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-b6c8-44b6-89f0-cfd3eee6c236', 'a148259c-2cd8-4181-a1aa-33b3fd4c1bad', 'a148259c-afec-41ba-9160-235ba69026dd', 0, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-bb00-48ff-855a-532b65f46ced', 'a148259c-26e1-403e-a782-2ff412a67b50', 'a148259c-b8b2-4531-ad39-08a4a70b7cf8', 1, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-bcf6-48dd-8712-ff48a2bd96d2', 'a148259c-2a2e-44f5-854c-3f17e6227c62', 'a148259c-b8b2-4531-ad39-08a4a70b7cf8', 0, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-bead-492a-acb9-1c5f02c80279', 'a148259c-2cd8-4181-a1aa-33b3fd4c1bad', 'a148259c-b8b2-4531-ad39-08a4a70b7cf8', 0, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-c258-409f-87d1-85709fed59e2', 'a148259c-26e1-403e-a782-2ff412a67b50', 'a148259c-c0ca-4823-bffb-bebb750f5a47', 1, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-c3ea-4f2b-8261-72729109f9b1', 'a148259c-2a2e-44f5-854c-3f17e6227c62', 'a148259c-c0ca-4823-bffb-bebb750f5a47', 0, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-c608-4fcf-9f53-a60719f4bc84', 'a148259c-2cd8-4181-a1aa-33b3fd4c1bad', 'a148259c-c0ca-4823-bffb-bebb750f5a47', 0, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-c96e-4f04-adc1-2b76fa732f01', 'a148259c-26e1-403e-a782-2ff412a67b50', 'a148259c-c7bc-44d6-8688-136c23c6ceaa', 1, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-cb61-4b14-ae61-776dd182f392', 'a148259c-2a2e-44f5-854c-3f17e6227c62', 'a148259c-c7bc-44d6-8688-136c23c6ceaa', 1, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-cd0e-4b48-bdeb-7483d3719f55', 'a148259c-2cd8-4181-a1aa-33b3fd4c1bad', 'a148259c-c7bc-44d6-8688-136c23c6ceaa', 1, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-d066-4306-a92d-54667d9e78fd', 'a148259c-26e1-403e-a782-2ff412a67b50', 'a148259c-cea4-42ec-87e1-5d2e8ab50eb1', 1, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-d210-402d-af7a-27de0de2ba4e', 'a148259c-2a2e-44f5-854c-3f17e6227c62', 'a148259c-cea4-42ec-87e1-5d2e8ab50eb1', 1, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-d3f6-413d-8992-bcb038c1fed3', 'a148259c-2cd8-4181-a1aa-33b3fd4c1bad', 'a148259c-cea4-42ec-87e1-5d2e8ab50eb1', 1, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-d7f5-466f-93e6-1a2532bb75b3', 'a148259c-26e1-403e-a782-2ff412a67b50', 'a148259c-d59f-4f4e-96a6-8be942b04c2b', 1, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-d97f-4477-9c5a-89e6f5107df4', 'a148259c-2a2e-44f5-854c-3f17e6227c62', 'a148259c-d59f-4f4e-96a6-8be942b04c2b', 1, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-db05-48eb-a052-0b8d1abec5a6', 'a148259c-2cd8-4181-a1aa-33b3fd4c1bad', 'a148259c-d59f-4f4e-96a6-8be942b04c2b', 1, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-de3a-4672-9689-b72f1863ea9c', 'a148259c-26e1-403e-a782-2ff412a67b50', 'a148259c-dc87-4606-80ab-743e2f4d080b', 1, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-dffa-4855-8ba6-f15d317557ae', 'a148259c-2a2e-44f5-854c-3f17e6227c62', 'a148259c-dc87-4606-80ab-743e2f4d080b', 1, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-e1a4-43e3-a9c2-0481b35e17ec', 'a148259c-2cd8-4181-a1aa-33b3fd4c1bad', 'a148259c-dc87-4606-80ab-743e2f4d080b', 1, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-e558-4d36-b284-686e373dd521', 'a148259c-26e1-403e-a782-2ff412a67b50', 'a148259c-e374-46f7-adba-0f7af0ceac0a', 1, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-e737-49a4-8d7e-a079069fc2b0', 'a148259c-2a2e-44f5-854c-3f17e6227c62', 'a148259c-e374-46f7-adba-0f7af0ceac0a', 1, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-e8fc-4564-9394-6ef4234aa90d', 'a148259c-2cd8-4181-a1aa-33b3fd4c1bad', 'a148259c-e374-46f7-adba-0f7af0ceac0a', 1, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-ecb2-4e9b-a4ff-90ab313036ee', 'a148259c-26e1-403e-a782-2ff412a67b50', 'a148259c-eacd-43c7-9a99-427c73efd58f', 1, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-eea1-4c3d-b331-4935a133bebc', 'a148259c-2a2e-44f5-854c-3f17e6227c62', 'a148259c-eacd-43c7-9a99-427c73efd58f', 1, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-f094-4d65-b164-67838137f674', 'a148259c-2cd8-4181-a1aa-33b3fd4c1bad', 'a148259c-eacd-43c7-9a99-427c73efd58f', 0, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-f465-43d3-bd62-d9280c9ca9bc', 'a148259c-26e1-403e-a782-2ff412a67b50', 'a148259c-f28c-411b-9a62-1778cbb2e403', 1, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-f61e-4675-a21e-9d1ebd7a63d9', 'a148259c-2a2e-44f5-854c-3f17e6227c62', 'a148259c-f28c-411b-9a62-1778cbb2e403', 1, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-f7ff-4109-b51a-da5f95d6112b', 'a148259c-2cd8-4181-a1aa-33b3fd4c1bad', 'a148259c-f28c-411b-9a62-1778cbb2e403', 0, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-fbbe-4abd-bd40-b6e2f30d4397', 'a148259c-26e1-403e-a782-2ff412a67b50', 'a148259c-f9cd-424e-8866-8c071f9446b5', 1, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-fd8b-46e8-b1dc-21f845b70a78', 'a148259c-2a2e-44f5-854c-3f17e6227c62', 'a148259c-f9cd-424e-8866-8c071f9446b5', 1, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259c-ff53-4f8f-9329-240d8874b662', 'a148259c-2cd8-4181-a1aa-33b3fd4c1bad', 'a148259c-f9cd-424e-8866-8c071f9446b5', 0, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259d-02fd-4255-8585-fe52eacc9eec', 'a148259c-26e1-403e-a782-2ff412a67b50', 'a148259d-0125-49ae-a206-e6179747ab1f', 1, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259d-05a3-4e0d-9892-01cd4c82c8bf', 'a148259c-2a2e-44f5-854c-3f17e6227c62', 'a148259d-0125-49ae-a206-e6179747ab1f', 1, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259d-07a3-4ca6-8ecd-52414f552193', 'a148259c-2cd8-4181-a1aa-33b3fd4c1bad', 'a148259d-0125-49ae-a206-e6179747ab1f', 0, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259d-0b94-4cc4-9da7-769662b0b746', 'a148259c-26e1-403e-a782-2ff412a67b50', 'a148259d-09d3-46a2-b36d-abda82d4123f', 1, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259d-0d85-4257-a2ac-9be148914d9a', 'a148259c-2a2e-44f5-854c-3f17e6227c62', 'a148259d-09d3-46a2-b36d-abda82d4123f', 1, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259d-0fde-4b81-828c-4d071459b2e1', 'a148259c-2cd8-4181-a1aa-33b3fd4c1bad', 'a148259d-09d3-46a2-b36d-abda82d4123f', 1, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259d-15d7-4dd9-b310-28973d3a95b8', 'a148259c-26e1-403e-a782-2ff412a67b50', 'a148259d-12df-473d-b3c5-f2f7f5153e23', 1, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259d-18e4-465b-b1e7-16648a18720c', 'a148259c-2a2e-44f5-854c-3f17e6227c62', 'a148259d-12df-473d-b3c5-f2f7f5153e23', 1, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259d-1b66-4eb8-b34e-72c1dd013f73', 'a148259c-2cd8-4181-a1aa-33b3fd4c1bad', 'a148259d-12df-473d-b3c5-f2f7f5153e23', 0, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259d-20cb-49ed-9f19-217b31f55b86', 'a148259c-26e1-403e-a782-2ff412a67b50', 'a148259d-1e28-4448-bbb9-0baaf9cafaed', 1, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259d-2392-428a-9358-753d25615bbb', 'a148259c-2a2e-44f5-854c-3f17e6227c62', 'a148259d-1e28-4448-bbb9-0baaf9cafaed', 1, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259d-2644-455b-b99b-e02064939259', 'a148259c-2cd8-4181-a1aa-33b3fd4c1bad', 'a148259d-1e28-4448-bbb9-0baaf9cafaed', 0, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259d-2c55-41bf-9e34-f86fab418dda', 'a148259c-26e1-403e-a782-2ff412a67b50', 'a148259d-2940-4e22-a34e-b677b2ed090e', 1, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259d-2f53-43a2-8612-37beb665ae63', 'a148259c-2a2e-44f5-854c-3f17e6227c62', 'a148259d-2940-4e22-a34e-b677b2ed090e', 1, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259d-3254-417d-bcd5-7b3e6a2e6a4d', 'a148259c-2cd8-4181-a1aa-33b3fd4c1bad', 'a148259d-2940-4e22-a34e-b677b2ed090e', 0, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259d-37e1-4835-b53a-a3696d1a6f1b', 'a148259c-26e1-403e-a782-2ff412a67b50', 'a148259d-352a-4f38-9c82-5200ee605da1', 1, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259d-3a90-47d0-8518-5429a2b13f76', 'a148259c-2a2e-44f5-854c-3f17e6227c62', 'a148259d-352a-4f38-9c82-5200ee605da1', 1, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259d-3dd5-4ec1-96ee-55a8e728de6d', 'a148259c-2cd8-4181-a1aa-33b3fd4c1bad', 'a148259d-352a-4f38-9c82-5200ee605da1', 0, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259d-432e-4d20-a612-dcf03a07aad5', 'a148259c-26e1-403e-a782-2ff412a67b50', 'a148259d-40a6-40cd-9caf-c3bc6362c255', 1, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259d-45fb-4242-9779-51ee955e6010', 'a148259c-2a2e-44f5-854c-3f17e6227c62', 'a148259d-40a6-40cd-9caf-c3bc6362c255', 1, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259d-4919-4aef-9eb6-2c51c036b620', 'a148259c-2cd8-4181-a1aa-33b3fd4c1bad', 'a148259d-40a6-40cd-9caf-c3bc6362c255', 0, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259d-4e5d-49c6-985e-444f2d217c7a', 'a148259c-26e1-403e-a782-2ff412a67b50', 'a148259d-4bd1-4c43-8e09-076d9f7f9a28', 1, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259d-50c2-4364-896c-e84d76a01373', 'a148259c-2a2e-44f5-854c-3f17e6227c62', 'a148259d-4bd1-4c43-8e09-076d9f7f9a28', 1, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259d-5346-4393-99b0-9b3f56cbb985', 'a148259c-2cd8-4181-a1aa-33b3fd4c1bad', 'a148259d-4bd1-4c43-8e09-076d9f7f9a28', 0, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259d-5887-4b65-8374-bb1927820878', 'a148259c-26e1-403e-a782-2ff412a67b50', 'a148259d-55c4-4971-8497-47a4925ed3ec', 1, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259d-5af8-4079-aea2-9c56802db7fe', 'a148259c-2a2e-44f5-854c-3f17e6227c62', 'a148259d-55c4-4971-8497-47a4925ed3ec', 1, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259d-5d57-46b7-92ee-ee4be85c8c59', 'a148259c-2cd8-4181-a1aa-33b3fd4c1bad', 'a148259d-55c4-4971-8497-47a4925ed3ec', 1, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259d-62c8-4101-bef8-55d00a0bab1b', 'a148259c-26e1-403e-a782-2ff412a67b50', 'a148259d-5ff0-491a-a889-3a4b8a02caca', 1, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259d-657b-4a64-a796-0c7301ff8de4', 'a148259c-2a2e-44f5-854c-3f17e6227c62', 'a148259d-5ff0-491a-a889-3a4b8a02caca', 0, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259d-681a-421d-9ebb-d742ee39eb93', 'a148259c-2cd8-4181-a1aa-33b3fd4c1bad', 'a148259d-5ff0-491a-a889-3a4b8a02caca', 0, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259d-6c58-443c-8b28-f6d8f502cfbb', 'a148259c-26e1-403e-a782-2ff412a67b50', 'a148259d-6a40-469e-b85d-237115135348', 1, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259d-6ea2-40cd-bf1d-39ffe6242837', 'a148259c-2a2e-44f5-854c-3f17e6227c62', 'a148259d-6a40-469e-b85d-237115135348', 0, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259d-713f-4cf2-a1ef-cdefcbee4c36', 'a148259c-2cd8-4181-a1aa-33b3fd4c1bad', 'a148259d-6a40-469e-b85d-237115135348', 0, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259d-7616-4b51-8133-647934cdd9ef', 'a148259c-26e1-403e-a782-2ff412a67b50', 'a148259d-7382-420c-908b-fa45e5ed35ee', 1, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259d-7815-4758-9d46-a0335e9cdf8c', 'a148259c-2a2e-44f5-854c-3f17e6227c62', 'a148259d-7382-420c-908b-fa45e5ed35ee', 1, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259d-7a50-4d91-a8a8-7c4bed0aece2', 'a148259c-2cd8-4181-a1aa-33b3fd4c1bad', 'a148259d-7382-420c-908b-fa45e5ed35ee', 1, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259d-7dee-4c76-b500-95794ee2c7bd', 'a148259c-26e1-403e-a782-2ff412a67b50', 'a148259d-7c02-4a24-8669-0834b1d6a078', 1, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259d-7fc2-4707-91b5-8e733b5ef439', 'a148259c-2a2e-44f5-854c-3f17e6227c62', 'a148259d-7c02-4a24-8669-0834b1d6a078', 0, '2026-03-12 10:46:47', '2026-03-12 10:46:47'),
('a148259d-8171-44f8-87cd-1b84756bc7a8', 'a148259c-2cd8-4181-a1aa-33b3fd4c1bad', 'a148259d-7c02-4a24-8669-0834b1d6a078', 0, '2026-03-12 10:46:47', '2026-03-12 10:46:47');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hire_date` date DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `last_login_at` timestamp NULL DEFAULT NULL,
  `password_changed_at` timestamp NULL DEFAULT NULL,
  `role_uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entity_uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `supervisor_uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`uuid`, `first_name`, `last_name`, `full_name`, `email`, `email_verified_at`, `password`, `phone`, `position`, `hire_date`, `avatar`, `is_active`, `last_login_at`, `password_changed_at`, `role_uuid`, `entity_uuid`, `supervisor_uuid`, `remember_token`, `created_at`, `updated_at`) VALUES
('a148259d-e254-45db-b582-90bfb9d528f6', 'Admin', 'AXIAL', 'AXIAL Admin', 'admin@axial.com', '2026-03-12 10:46:48', '$2y$12$AWF6hF7s.cEnZfHMoBgniOszB3TaOqymGPL1EJmSgPwEmSCVbAxpS', NULL, 'Administrateur Système', NULL, NULL, 1, '2026-03-12 10:47:02', '2026-03-12 10:47:09', 'a148259c-26e1-403e-a782-2ff412a67b50', 'a148259d-846b-481c-8780-ff736cf01cd7', NULL, NULL, '2026-03-12 10:46:48', '2026-03-12 10:47:09'),
('a148259e-283a-43d9-8cb5-fbcd0aa41cd9', 'Kouadio', 'KONAN', 'KONAN Kouadio', 'kouadio.konan@sgpme.ci', '2026-03-12 10:46:48', '$2y$12$.jfWSLKmaEu7iJ33lyJXauaXBULLTzSe8CQRuot2WX0Fg/m5MH2im', NULL, 'Directeur Général', NULL, NULL, 1, NULL, NULL, 'a148259c-2a2e-44f5-854c-3f17e6227c62', 'a148259d-846b-481c-8780-ff736cf01cd7', NULL, NULL, '2026-03-12 10:46:48', '2026-03-12 10:46:48'),
('a148259e-6d7a-4beb-a8d9-5741da1a5cd7', 'Aminata', 'DIALLO', 'DIALLO Aminata', 'aminata.diallo@sgpme.ci', '2026-03-12 10:46:48', '$2y$12$IxZsVSKnHOb4IzINkzNWW.tuQ2qqpg8uJcrSKMuApSLVAwl58J1.6', NULL, 'Directrice des Ressources Humaines', NULL, NULL, 1, NULL, NULL, 'a148259c-2a2e-44f5-854c-3f17e6227c62', NULL, 'a148259e-283a-43d9-8cb5-fbcd0aa41cd9', NULL, '2026-03-12 10:46:48', '2026-03-12 10:46:50'),
('a148259e-b3bb-4732-9a17-434c54f1eb22', 'Jean-Marc', 'BROU', 'BROU Jean-Marc', 'jeanmarc.brou@sgpme.ci', '2026-03-12 10:46:48', '$2y$12$k9tQCwYx0WdTgCfbIuU5a.GzPyI9K2KlLWgepDLNGuebngFDqpnZe', NULL, 'Directeur Financier', NULL, NULL, 1, NULL, NULL, 'a148259c-2a2e-44f5-854c-3f17e6227c62', NULL, 'a148259e-283a-43d9-8cb5-fbcd0aa41cd9', NULL, '2026-03-12 10:46:48', '2026-03-12 10:46:50'),
('a148259e-fad7-4e7f-ae91-6e436e8943a7', 'Fatou', 'COULIBALY', 'COULIBALY Fatou', 'fatou.coulibaly@sgpme.ci', '2026-03-12 10:46:48', '$2y$12$34QTVFTxWlo5bdUTAdK2PeAMmwHxQgPGoQk0VHHLRwmtcBawuvLOC', NULL, 'Responsable Informatique', NULL, NULL, 1, NULL, NULL, 'a148259c-2a2e-44f5-854c-3f17e6227c62', NULL, 'a148259e-283a-43d9-8cb5-fbcd0aa41cd9', NULL, '2026-03-12 10:46:48', '2026-03-12 10:46:50'),
('a148259f-433f-414a-83a9-0b618bc01856', 'Moussa', 'TRAORE', 'TRAORE Moussa', 'moussa.traore@sgpme.ci', '2026-03-12 10:46:49', '$2y$12$w5ukVdALkHJ8Ja236zSy7OhLd0tveYXg3a/2vPMfkW5HEDr0RZqWG', NULL, 'Responsable Commercial', NULL, NULL, 1, NULL, NULL, 'a148259c-2a2e-44f5-854c-3f17e6227c62', NULL, 'a148259e-283a-43d9-8cb5-fbcd0aa41cd9', NULL, '2026-03-12 10:46:49', '2026-03-12 10:46:50'),
('a148259f-8923-4149-9714-e43e353e3c8e', 'Awa', 'KONE', 'KONE Awa', 'awa.kone@sgpme.ci', '2026-03-12 10:46:49', '$2y$12$VRsxf7dA1fR1ErStKJOOTuKkkt0biHgAn9BPuV3Y9QMwHFKvtEdMC', NULL, 'Chargée de Recrutement', NULL, NULL, 1, NULL, NULL, NULL, NULL, 'a148259e-6d7a-4beb-a8d9-5741da1a5cd7', NULL, '2026-03-12 10:46:49', '2026-03-12 10:46:50'),
('a148259f-cf2a-4d58-a54d-13b67e976cf9', 'Yao', 'ASSI', 'ASSI Yao', 'yao.assi@sgpme.ci', '2026-03-12 10:46:49', '$2y$12$GUZrdIhEKnugyTJmtT1gO.TMScqXokmW50Kd6g09hyVkraAEr393S', NULL, 'Comptable Senior', NULL, NULL, 1, NULL, NULL, NULL, NULL, 'a148259e-b3bb-4732-9a17-434c54f1eb22', NULL, '2026-03-12 10:46:49', '2026-03-12 10:46:50'),
('a14825a0-14d3-440f-bc7d-4d22609baa61', 'Mariam', 'OUATTARA', 'OUATTARA Mariam', 'mariam.ouattara@sgpme.ci', '2026-03-12 10:46:49', '$2y$12$waEbymTm1NU4FSGgDzSbweL8X6Rj4H/Febqo7vbLY5wFSh4BsJMDy', NULL, 'Développeuse Web', NULL, NULL, 1, NULL, NULL, NULL, NULL, 'a148259e-fad7-4e7f-ae91-6e436e8943a7', NULL, '2026-03-12 10:46:49', '2026-03-12 10:46:50'),
('a14825a0-5a6e-459b-a43c-e10e9d7bfb23', 'Ibrahim', 'SANGARE', 'SANGARE Ibrahim', 'ibrahim.sangare@sgpme.ci', '2026-03-12 10:46:49', '$2y$12$PNN0lzQL1rA/rl0M/1mia.cLY57SX8dCs0Iy5AcxGd4NIuLQZA56C', NULL, 'Commercial Terrain', NULL, NULL, 1, NULL, NULL, NULL, NULL, 'a148259f-433f-414a-83a9-0b618bc01856', NULL, '2026-03-12 10:46:49', '2026-03-12 10:46:50'),
('a14825a0-a049-47c1-9e3d-3e7df3b1a32f', 'Christelle', 'AKA', 'AKA Christelle', 'christelle.aka@sgpme.ci', '2026-03-12 10:46:50', '$2y$12$TSkQzx9HgM.cKZYe2Cclz.LAsTuxYVtGwwo3EOnq2/K4m9BoTf1du', NULL, 'Juriste', NULL, NULL, 1, NULL, NULL, NULL, NULL, 'a148259e-283a-43d9-8cb5-fbcd0aa41cd9', NULL, '2026-03-12 10:46:50', '2026-03-12 10:46:50'),
('a14825a0-fb48-4f41-97ef-078a00dcb4e9', 'N\'Guessan Joelle', 'KOUASSI', 'KOUASSI N\'Guessan Joelle', 'joelle.kouassi@sgpme.ci', '2026-03-12 10:46:50', '$2y$12$c7jSTdb0/3IVbrCYS8iM5e6TPH6Sgme73GnHP54hfCRtSyP/gDzZq', '', 'Directrice Générale', '2023-05-01', NULL, 1, NULL, NULL, 'a148259c-2a2e-44f5-854c-3f17e6227c62', 'a148259d-846b-481c-8780-ff736cf01cd7', NULL, NULL, '2026-03-12 10:46:50', '2026-03-12 10:46:50'),
('a14825a1-409e-4ea5-ac28-f8afad3774b2', 'Oho Pennina', 'HIEN', 'HIEN Oho Pennina', 'pennina.hien@sgpme.ci', '2026-03-12 10:46:50', '$2y$12$QtXt8L35U/nPwY67jg/K..dKc4wDLk9Nwh.E.33lfQPE2HJqi5xFi', '', 'Assistante de Direction', '2023-05-01', NULL, 1, NULL, NULL, 'a148259c-2cd8-4181-a1aa-33b3fd4c1bad', 'a148259d-846b-481c-8780-ff736cf01cd7', 'a14825a0-fb48-4f41-97ef-078a00dcb4e9', NULL, '2026-03-12 10:46:50', '2026-03-12 10:46:50'),
('a14825a1-8702-4d20-8c32-72a03a1ab7e8', 'Komenan Ehui', 'DJE', 'DJE Komenan Ehui', 'dje.komenan@sgpme.ci', '2026-03-12 10:46:50', '$2y$12$gkk5BJ5pAEABKxrL52n8yOWURD05Wx.3GkM5kGwLNo8fESs/OpH0K', '', 'Agent de Liaison', '2023-05-01', NULL, 1, NULL, NULL, 'a148259c-2cd8-4181-a1aa-33b3fd4c1bad', 'a148259d-8d35-4210-baed-35dbf20d2e47', NULL, NULL, '2026-03-12 10:46:50', '2026-03-12 10:46:50'),
('a14825a1-cd76-4183-abc0-852905064534', 'Moha', 'DIOMANDE', 'DIOMANDE Moha', 'moha.diomande@sgpme.ci', '2026-03-12 10:46:50', '$2y$12$L6XU2I0ntLAkbj2bJlULVe4WTiuLlYnlkfu6WHL7o1GDyuFyrXP1m', '', 'Assistante Moyens Généraux', '2023-07-01', NULL, 1, NULL, NULL, 'a148259c-2cd8-4181-a1aa-33b3fd4c1bad', 'a148259d-8d35-4210-baed-35dbf20d2e47', NULL, NULL, '2026-03-12 10:46:50', '2026-03-12 10:46:50'),
('a14825a2-1483-4065-aaa6-0b881ae73b95', 'Abain Aissata', 'DIARRASSOUBA', 'DIARRASSOUBA Abain Aissata', 'aissata.diarrassouba@sgpme.ci', '2026-03-12 10:46:50', '$2y$12$0idOFO5WMjQAyVn.ZTR3q.dz2dc3BcfLlUWOS6MmqNEg1TKzg1Kfu', '', 'Responsable des Moyens Généraux', '2023-07-01', NULL, 1, NULL, NULL, 'a148259c-2a2e-44f5-854c-3f17e6227c62', 'a148259d-8d35-4210-baed-35dbf20d2e47', NULL, NULL, '2026-03-12 10:46:50', '2026-03-12 10:46:50'),
('a14825a2-5af3-49f2-a7a2-fcda7ed92d19', 'Agbedje Datte', 'BRAWA', 'BRAWA Agbedje Datte', 'benjamin.brawa@sgpme.ci', '2026-03-12 10:46:51', '$2y$12$fO3CworpD9WBxSRV17CpDOMCJbhfdWtrJnaC0Nf1HptHBDPjVB6Yy', '', 'Directeur Otrois et Engagements', '2023-07-01', NULL, 1, NULL, NULL, 'a148259c-2a2e-44f5-854c-3f17e6227c62', 'a148259d-846b-481c-8780-ff736cf01cd7', 'a14825a0-fb48-4f41-97ef-078a00dcb4e9', NULL, '2026-03-12 10:46:51', '2026-03-12 10:46:51'),
('a14825a2-bc7f-4404-bca7-2dcb577dc0a4', 'Nadege Ghyslaine', 'KOUAKOU', 'KOUAKOU Nadege Ghyslaine', 'ghyslaine.kouakou@sgpme.ci', '2026-03-12 10:46:51', '$2y$12$vDPDvTkiKhu1sZ/RRC22seeFuCLY3N/gGq2uWqNGJauAfHVwVicmm', '', 'Contrôleur permanent', '2023-07-01', NULL, 1, NULL, NULL, 'a148259c-2cd8-4181-a1aa-33b3fd4c1bad', 'a148259d-936b-4b21-b76d-0d1633c16e14', NULL, NULL, '2026-03-12 10:46:51', '2026-03-12 10:46:51'),
('a14825a3-0663-4da7-96de-069374c4098f', 'Amy Marlaine', 'OKOUBO', 'OKOUBO Amy Marlaine', 'stephanie.okoubo@sgpme.ci', '2026-03-12 10:46:51', '$2y$12$6Xy9qZiosAIsSntOfp93Z.0gIvBk.ZjA3bqdfDr0NOThzSVEqpI7u', '', 'Analyste engagements', '2023-07-01', NULL, 1, NULL, NULL, 'a148259c-2cd8-4181-a1aa-33b3fd4c1bad', 'a148259d-8afb-491b-8ed1-de2b991ea641', 'a14825a2-5af3-49f2-a7a2-fcda7ed92d19', NULL, '2026-03-12 10:46:51', '2026-03-12 10:46:51'),
('a14825a3-4ce9-467d-b60b-e5f8e51da79a', 'Cynthia Armande', 'KOUAKOU', 'KOUAKOU Cynthia Armande', 'cynthia.kouakou@sgpme.ci', '2026-03-12 10:46:51', '$2y$12$RhfZmYY0rE/p9AcId10zR..ZP9wmoN0gxirczzQUjpl8j9cJeoN1.', '', 'Comptable', '2023-07-01', NULL, 1, NULL, NULL, 'a148259c-2cd8-4181-a1aa-33b3fd4c1bad', 'a148259d-8ea2-40e5-a78a-6c82ba9bf171', NULL, NULL, '2026-03-12 10:46:51', '2026-03-12 10:46:51'),
('a14825a3-93f8-4fbe-99d9-0c1632432e7e', 'Abdel Ousmane', 'DIABY', 'DIABY Abdel Ousmane', 'abdel.diaby@sgpme.ci', '2026-03-12 10:46:51', '$2y$12$4vJJL.nbtMRXNW6obaoqXuK44KOmBZ0Wsy4CKPFEWkW5Y5zGgmROi', '', 'Chargé du suivi des engagements', '2023-07-01', NULL, 1, NULL, NULL, 'a148259c-2cd8-4181-a1aa-33b3fd4c1bad', 'a148259d-8afb-491b-8ed1-de2b991ea641', 'a14825a2-5af3-49f2-a7a2-fcda7ed92d19', NULL, '2026-03-12 10:46:51', '2026-03-12 10:46:51'),
('a14825a3-dc44-4ad1-9b30-83864a60032f', 'Anna Laetitia Marine', 'KOFFI', 'KOFFI Anna Laetitia Marine', 'laetitia.koffi@sgpme.ci', '2026-03-12 10:46:52', '$2y$12$W2WQUuXcnlrH9OXmB2urVurkkV74SdjC7bHJWK3QDWAAte8ojH4l.', '', 'Directrice Commerciale', '2023-07-01', NULL, 1, NULL, NULL, 'a148259c-2a2e-44f5-854c-3f17e6227c62', 'a148259d-846b-481c-8780-ff736cf01cd7', 'a14825a0-fb48-4f41-97ef-078a00dcb4e9', NULL, '2026-03-12 10:46:52', '2026-03-12 10:46:52'),
('a14825a4-3e75-47a7-81c2-d40cfd39a5fc', 'Adjoua Diane Marie', 'KOUAME', 'KOUAME Adjoua Diane Marie', 'diane.kouame@sgpme.ci', '2026-03-12 10:46:52', '$2y$12$NSfXZKW9U8uTp55QoMxGPu4ybQ467.39ALE/uwHYUe3xlecVxUaf6', '', 'Chargée de Clientèle', '2023-07-01', NULL, 1, NULL, NULL, 'a148259c-2cd8-4181-a1aa-33b3fd4c1bad', 'a148259d-89ad-480b-9734-fcbd74a836c8', 'a14825a3-dc44-4ad1-9b30-83864a60032f', NULL, '2026-03-12 10:46:52', '2026-03-12 10:46:52'),
('a14825a4-854f-4343-a69c-b641a01b4de5', 'Amoin Edith Ferdy', 'KOUADIO', 'KOUADIO Amoin Edith Ferdy', 'edith.kouadio@sgpme.ci', '2026-03-12 10:46:52', '$2y$12$J1icVb5Ow95jkdwOwscuY.TeGgX4ghg7B9BqOWmFioPEBPwZg8xGK', '', 'Chargée d\'Accueil', '2023-07-01', NULL, 1, NULL, NULL, 'a148259c-2cd8-4181-a1aa-33b3fd4c1bad', 'a148259d-8d35-4210-baed-35dbf20d2e47', 'a14825a2-1483-4065-aaa6-0b881ae73b95', NULL, '2026-03-12 10:46:52', '2026-03-12 10:46:52'),
('a14825a4-cb76-47b7-af7d-989234da5037', 'Bioh Dji Fabrice', 'SONZAHI', 'SONZAHI Bioh Dji Fabrice', 'fabrice.sonzahi@sgpme.ci', '2026-03-12 10:46:52', '$2y$12$ItbuH1ltaF3i.C7v7/zyG.B2ACe3pawefZenEjyLDbKPM6PUdMWmm', '', 'Responsable Systèmes d\'information', '2023-07-01', NULL, 1, '2026-03-12 10:53:11', '2026-03-12 10:53:18', 'a148259c-2a2e-44f5-854c-3f17e6227c62', 'a148259d-900c-48f9-afbe-8ef98ea08448', NULL, NULL, '2026-03-12 10:46:52', '2026-03-12 10:53:18'),
('a14825a5-1286-4c92-a92b-6bbddbb8cd5d', 'Hoademeno', 'NAMESSI', 'NAMESSI Hoademeno', 'richarde.namessi@sgpme.ci', '2026-03-12 10:46:52', '$2y$12$KcmBW9h/IawG/xM6Xrmg3e0YgU3e4f744JtwXbUAcPT3D2.EliyKy', '', 'Assistante de Direction et chargée des ressources humaines', '2023-07-01', NULL, 1, NULL, NULL, 'a148259c-2cd8-4181-a1aa-33b3fd4c1bad', 'a148259d-91ed-4dd7-ab59-2f609bb0703d', NULL, NULL, '2026-03-12 10:46:52', '2026-03-12 10:46:52'),
('a14825a5-58ec-463b-915e-7190c8da9126', 'Anceany Sophia', 'ANET', 'ANET Anceany Sophia', 'anceany.anet@sgpme.ci', '2026-03-12 10:46:53', '$2y$12$MeT/TdwjJJBnPf.1sLue9O4vMRx9Hstvx49B1DiRyvg4fTAYilriS', '', 'Responsable des Ressources Humaines', '2023-08-28', NULL, 1, '2026-03-12 12:22:07', '2026-03-12 12:22:15', 'a148259c-2a2e-44f5-854c-3f17e6227c62', 'a148259d-91ed-4dd7-ab59-2f609bb0703d', 'a14825a8-a5b6-4024-9125-9171954396ac', NULL, '2026-03-12 10:46:53', '2026-03-12 12:22:15'),
('a14825a5-9f09-4264-945f-04feded74cdb', 'Namoin Jessica Marie', 'YAO', 'YAO Namoin Jessica Marie', 'jessica.yao@sgpme.ci', '2026-03-12 10:46:53', '$2y$12$yemjXHVWR2Pk/rRU5H1sleVit8FMcgzrHy6ndBI1GiGGtcyKQXYQ.', '', 'Chargée Marketing et Communication', '2023-09-04', NULL, 1, NULL, NULL, 'a148259c-2cd8-4181-a1aa-33b3fd4c1bad', 'a148259d-846b-481c-8780-ff736cf01cd7', 'a14825a0-fb48-4f41-97ef-078a00dcb4e9', NULL, '2026-03-12 10:46:53', '2026-03-12 10:46:53'),
('a14825a5-e5d3-4e9c-92a0-c65cc0f5e9b5', 'Animan Christine', 'AMANLAMAN', 'AMANLAMAN Animan Christine', 'christine.aka@sgpme.ci', '2026-03-12 10:46:53', '$2y$12$pDSHukC7bTi5awFMczOKp.DHm.wYu8FAoiauqEzIWOuR1rhZVpwom', '', 'Responsable Juridique et Contentieux', '2023-09-18', NULL, 1, NULL, NULL, 'a148259c-2a2e-44f5-854c-3f17e6227c62', 'a148259d-846b-481c-8780-ff736cf01cd7', 'a14825a0-fb48-4f41-97ef-078a00dcb4e9', NULL, '2026-03-12 10:46:53', '2026-03-12 10:46:53'),
('a14825a6-2b57-4a6c-8c0b-d591e1a0e124', 'Herve', 'SUINI', 'SUINI Herve', 'herve.suini@sgpme.ci', '2026-03-12 10:46:53', '$2y$12$VDdpUNsUjuIAN1goLaF0YerjPj8kHHWJHYK3iupLZLkjlGZJcXIfK', '', 'Responsable des Finances et de Comptabilité', '2023-10-23', NULL, 1, NULL, NULL, 'a148259c-2a2e-44f5-854c-3f17e6227c62', 'a148259d-8ea2-40e5-a78a-6c82ba9bf171', NULL, NULL, '2026-03-12 10:46:53', '2026-03-12 10:46:53'),
('a14825a6-715a-45b7-b0d3-89d131b84a48', 'Aissa Grace', 'DAGBO', 'DAGBO Aissa Grace', 'aissa.dagbo@sgpme.ci', '2026-03-12 10:46:53', '$2y$12$OVVb8B1t5OdKSU6QQlylyuA5JhnSTcEFcm4I1fa3tIBO0E.EWvPXK', '', 'Chargé RSE Sénior', '2023-10-26', NULL, 1, NULL, NULL, 'a148259c-2cd8-4181-a1aa-33b3fd4c1bad', 'a148259d-94e0-4c28-9978-cec30da6c1c8', 'a14825a0-fb48-4f41-97ef-078a00dcb4e9', NULL, '2026-03-12 10:46:53', '2026-03-12 10:46:53'),
('a14825a6-b899-4d6e-a02a-0447f43a8c7e', 'Kouadio Jean Claude', 'KOUASSI', 'KOUASSI Kouadio Jean Claude', 'jean-claude.kouassi@sgpme.ci', '2026-03-12 10:46:54', '$2y$12$eCHPnBwPlBc.a2xtKPhxkeRWLZ6SPMddlgY5I8aWB0qtJSPEz32ly', '', 'Directeur des Risques, de la Conformité et du Contrôle Permanent', '2023-12-01', NULL, 1, NULL, NULL, 'a148259c-2a2e-44f5-854c-3f17e6227c62', 'a148259d-846b-481c-8780-ff736cf01cd7', 'a14825a0-fb48-4f41-97ef-078a00dcb4e9', NULL, '2026-03-12 10:46:54', '2026-03-12 10:46:54'),
('a14825a6-ff25-4ef5-a854-8df26e8e59a1', 'Roger', 'OUATTARA', 'OUATTARA Roger', 'roger.ouattara@sgpme.ci', '2026-03-12 10:46:54', '$2y$12$y11SfJXRlkvgia8VZTngXe.eWvwZazkVQs/dfdTCA7Pc0L6v/J5yC', '', 'Responsable Audit Interne', '2024-03-01', NULL, 1, NULL, NULL, 'a148259c-2a2e-44f5-854c-3f17e6227c62', 'a148259d-846b-481c-8780-ff736cf01cd7', 'a14825a0-fb48-4f41-97ef-078a00dcb4e9', NULL, '2026-03-12 10:46:54', '2026-03-12 10:46:54'),
('a14825a7-45ed-4451-9090-e811c609b807', 'Thomas Rodrigue', 'APPOH', 'APPOH Thomas Rodrigue', 'rodrigue.appoh@sgpme.ci', '2026-03-12 10:46:54', '$2y$12$ZLfMpcxBx7FYNFuEgUh14eXi6ZGZMW4wbI187u5UWPLK0TkuQhy3i', '', 'Chargé d\'Affaires', '2024-08-01', NULL, 1, NULL, NULL, 'a148259c-2cd8-4181-a1aa-33b3fd4c1bad', 'a148259d-89ad-480b-9734-fcbd74a836c8', 'a14825a3-dc44-4ad1-9b30-83864a60032f', NULL, '2026-03-12 10:46:54', '2026-03-12 10:46:54'),
('a14825a7-8ba5-45f7-bdf9-cfb0308cd3a8', 'N\'Guessan', 'DOGNE', 'DOGNE N\'Guessan', 'luc.dogne@sgpme.ci', '2026-03-12 10:46:54', '$2y$12$Fp5s2bMYwuyNGx2RvUNCou7crr7DHa9CigpxX2C5Ygmt4Ii0lZhOa', '', 'Chargé de reseau et de sécurité informatique', '2024-09-02', NULL, 1, NULL, NULL, 'a148259c-2cd8-4181-a1aa-33b3fd4c1bad', 'a148259d-900c-48f9-afbe-8ef98ea08448', 'a14825a4-cb76-47b7-af7d-989234da5037', NULL, '2026-03-12 10:46:54', '2026-03-12 10:46:54'),
('a14825a7-d2a8-46b1-b11e-e3e5953e3b80', 'Hamed', 'CISSE', 'CISSE Hamed', 'hamed.cisse@sgpme.ci', '2026-03-12 10:46:54', '$2y$12$ZUzoPVqSbpXlOiN4f4lLCeE663qZ5fEVgM0Zm5jqTCZktJFcasGxW', '', 'Responsable Gestion des Risques et Contrôle Interne', '2024-09-02', NULL, 1, NULL, NULL, 'a148259c-2a2e-44f5-854c-3f17e6227c62', 'a148259d-936b-4b21-b76d-0d1633c16e14', 'a14825a0-fb48-4f41-97ef-078a00dcb4e9', NULL, '2026-03-12 10:46:54', '2026-03-12 10:46:54'),
('a14825a8-18c7-4e7c-8ea1-f5ecc302d468', 'Serge Didier Frejus', 'KOUASSI', 'KOUASSI Serge Didier Frejus', 'didier.kouassi@sgpme.ci', '2026-03-12 10:46:54', '$2y$12$ldXtxjAcXu7IXteiFWtM5OPE1Q2MkeLw0VgsAYiTmgY/MPsWUyLXC', '', 'Analyste Credit', '2025-03-03', NULL, 1, NULL, NULL, 'a148259c-2cd8-4181-a1aa-33b3fd4c1bad', 'a148259d-8afb-491b-8ed1-de2b991ea641', 'a14825a2-5af3-49f2-a7a2-fcda7ed92d19', NULL, '2026-03-12 10:46:54', '2026-03-12 10:46:54'),
('a14825a8-5f91-4618-aaeb-2d41486f20f3', 'Carine Laurinda', 'KONAN', 'KONAN Carine Laurinda', 'laurinda.konan@sgpme.ci', '2026-03-12 10:46:55', '$2y$12$YfJumhUw89udzG99my26ue8wgcab7XsLW3SZfFtToN3/yN.6SYQsu', '', 'Contrôleur de Gestion', '2025-04-01', NULL, 1, NULL, NULL, 'a148259c-2cd8-4181-a1aa-33b3fd4c1bad', 'a148259d-8ea2-40e5-a78a-6c82ba9bf171', 'a14825a6-2b57-4a6c-8c0b-d591e1a0e124', NULL, '2026-03-12 10:46:55', '2026-03-12 10:46:55'),
('a14825a8-a5b6-4024-9125-9171954396ac', 'Rahmata', 'DAGNOGO', 'DAGNOGO Rahmata', 'rahmata.dagnogo@sgpme.ci', '2026-03-12 10:46:55', '$2y$12$ZevRUrif7.KyoxATL.JxL.VOsuaSnaThHR0BuxlEl2.H3IEI1KgWi', '', 'Directeur Administration et Ressources', '2025-08-18', NULL, 1, NULL, NULL, 'a148259c-2a2e-44f5-854c-3f17e6227c62', 'a148259d-846b-481c-8780-ff736cf01cd7', 'a14825a0-fb48-4f41-97ef-078a00dcb4e9', NULL, '2026-03-12 10:46:55', '2026-03-12 10:46:55'),
('a14825a8-ec39-47ff-b804-fc90e1515aa5', 'Marie-Suzette', 'ZABAVY', 'ZABAVY Marie-Suzette', 'marie-suzette.zabavy@sgpme.ci', '2026-03-12 10:46:55', '$2y$12$XR4U4ZbO1CZfRdlaWfgfbuH/JcAJN3WH8AQz66FzhyYUD6mSj8Pum', '', 'Chargée de conformité', '2025-11-03', NULL, 1, NULL, NULL, 'a148259c-2cd8-4181-a1aa-33b3fd4c1bad', 'a148259d-966b-4e10-b0bc-59eabdbef7eb', 'a14825a0-fb48-4f41-97ef-078a00dcb4e9', NULL, '2026-03-12 10:46:55', '2026-03-12 10:46:55'),
('a14825a9-33b8-4940-a5ac-05f6b1e47bf3', 'Allhassane Simon', 'COULIBALY', 'COULIBALY Allhassane Simon', 'allhassane.coulibaly@sgpme.ci', '2026-03-12 10:46:55', '$2y$12$cBrJghVQF.q5CMDN7MLhseRFXnWtvKNLZO7f8Ad3HqYUfA.y0KFKC', '', 'Tech Lead Applicatif', '2025-12-15', NULL, 1, '2026-03-12 10:50:31', '2026-03-12 10:50:38', 'a148259c-2cd8-4181-a1aa-33b3fd4c1bad', 'a148259d-900c-48f9-afbe-8ef98ea08448', 'a14825a4-cb76-47b7-af7d-989234da5037', NULL, '2026-03-12 10:46:55', '2026-03-12 10:50:38'),
('a14825a9-7aa4-4e33-b967-a5debe74bdea', 'Raissa Manuella', 'KAMARA', 'KAMARA Raissa Manuella', 'raissa.kamara@sgpme.ci', '2026-03-12 10:46:55', '$2y$12$QKz9mr8XFwrrDTIrFNk8juY1Bx21kOUoeRdqhqieJ0nioC8NlWT9O', '', 'Juriste', '2026-01-02', NULL, 1, NULL, NULL, 'a148259c-2cd8-4181-a1aa-33b3fd4c1bad', 'a148259d-9818-47f5-8c93-c1c84fca8f89', 'a14825a5-e5d3-4e9c-92a0-c65cc0f5e9b5', NULL, '2026-03-12 10:46:55', '2026-03-12 10:46:55'),
('a14825a9-c379-4da3-898e-1724aa4eca38', 'N\'Da Leonce', 'OURA', 'OURA N\'Da Leonce', 'leonce.oura@sgpme.ci', '2026-03-12 10:46:55', '$2y$12$90rZYIbsRs6wgY8FxwVbR.H.89Q7hGZ1ooeDvuDj4xGTEcuMdiez2', '', 'Auditeur Interne', '2026-02-02', NULL, 1, NULL, NULL, 'a148259c-2cd8-4181-a1aa-33b3fd4c1bad', 'a148259d-99c5-4dd8-b5e5-a84c56dccbd7', 'a14825a0-14d3-440f-bc7d-4d22609baa61', NULL, '2026-03-12 10:46:55', '2026-03-12 10:46:55');

-- --------------------------------------------------------

--
-- Structure de la table `user_campaigns`
--

CREATE TABLE `user_campaigns` (
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `campaign_uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `supervisor_uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `objective_status` enum('draft','submitted','returned','completed','not_evaluated') COLLATE utf8mb4_unicode_ci DEFAULT 'draft',
  `evaluation_status` enum('pending','supervisor_draft','submitted_to_employee','returned_to_supervisor','validated','not_evaluated') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `midterm_file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rating` decimal(5,2) DEFAULT NULL,
  `supervisor_comment` text COLLATE utf8mb4_unicode_ci,
  `employee_comment` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `user_campaigns`
--

INSERT INTO `user_campaigns` (`uuid`, `user_uuid`, `campaign_uuid`, `supervisor_uuid`, `objective_status`, `evaluation_status`, `midterm_file`, `rating`, `supervisor_comment`, `employee_comment`, `created_at`, `updated_at`) VALUES
('a1482671-7d58-4ef1-8789-4920d27de5d8', 'a14825a0-a049-47c1-9e3d-3e7df3b1a32f', 'a1482634-712c-4aea-b5ea-29ff8c7e0517', 'a148259e-283a-43d9-8cb5-fbcd0aa41cd9', 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 10:49:06', '2026-03-12 11:11:33'),
('a1482671-822d-4930-a8f4-dc1676bc01b0', 'a14825a5-e5d3-4e9c-92a0-c65cc0f5e9b5', 'a1482634-712c-4aea-b5ea-29ff8c7e0517', 'a14825a0-fb48-4f41-97ef-078a00dcb4e9', 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 10:49:06', '2026-03-12 11:11:33'),
('a1482671-84bb-4da2-970c-aefeb019d740', 'a14825a5-58ec-463b-915e-7190c8da9126', 'a1482634-712c-4aea-b5ea-29ff8c7e0517', NULL, 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 10:49:06', '2026-03-12 11:11:33'),
('a1482671-8a10-45b4-a95b-41c23280718a', 'a14825a7-45ed-4451-9090-e811c609b807', 'a1482634-712c-4aea-b5ea-29ff8c7e0517', 'a14825a3-dc44-4ad1-9b30-83864a60032f', 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 10:49:06', '2026-03-12 11:11:33'),
('a1482671-8d13-4d27-a8e5-9e64ee7fca53', 'a148259f-cf2a-4d58-a54d-13b67e976cf9', 'a1482634-712c-4aea-b5ea-29ff8c7e0517', 'a148259e-b3bb-4732-9a17-434c54f1eb22', 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 10:49:06', '2026-03-12 11:11:33'),
('a1482671-8fd5-4221-af73-6ec5c2309ab4', 'a14825a2-5af3-49f2-a7a2-fcda7ed92d19', 'a1482634-712c-4aea-b5ea-29ff8c7e0517', 'a14825a0-fb48-4f41-97ef-078a00dcb4e9', 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 10:49:06', '2026-03-12 11:11:33'),
('a1482671-92a1-44bf-ada2-424741ab805b', 'a148259e-b3bb-4732-9a17-434c54f1eb22', 'a1482634-712c-4aea-b5ea-29ff8c7e0517', 'a148259e-283a-43d9-8cb5-fbcd0aa41cd9', 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 10:49:06', '2026-03-12 11:11:33'),
('a1482671-95c6-41e0-9891-833414002f51', 'a14825a7-d2a8-46b1-b11e-e3e5953e3b80', 'a1482634-712c-4aea-b5ea-29ff8c7e0517', 'a14825a0-fb48-4f41-97ef-078a00dcb4e9', 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 10:49:06', '2026-03-12 11:11:33'),
('a1482671-98a3-4d58-824c-30574e273589', 'a14825a9-33b8-4940-a5ac-05f6b1e47bf3', 'a1482634-712c-4aea-b5ea-29ff8c7e0517', 'a14825a4-cb76-47b7-af7d-989234da5037', 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 10:49:06', '2026-03-12 11:11:33'),
('a1482671-9b80-44b5-8294-baaf8df892e8', 'a148259e-fad7-4e7f-ae91-6e436e8943a7', 'a1482634-712c-4aea-b5ea-29ff8c7e0517', 'a148259e-283a-43d9-8cb5-fbcd0aa41cd9', 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 10:49:06', '2026-03-12 11:11:33'),
('a1482671-9e72-4f2a-b5f0-b2fb75867e45', 'a14825a6-715a-45b7-b0d3-89d131b84a48', 'a1482634-712c-4aea-b5ea-29ff8c7e0517', 'a14825a0-fb48-4f41-97ef-078a00dcb4e9', 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 10:49:06', '2026-03-12 11:11:33'),
('a1482671-a0c5-4094-9f3e-cc438634f928', 'a14825a8-a5b6-4024-9125-9171954396ac', 'a1482634-712c-4aea-b5ea-29ff8c7e0517', 'a14825a0-fb48-4f41-97ef-078a00dcb4e9', 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 10:49:06', '2026-03-12 11:11:33'),
('a1482671-a357-4eda-809f-924ff710f69d', 'a14825a3-93f8-4fbe-99d9-0c1632432e7e', 'a1482634-712c-4aea-b5ea-29ff8c7e0517', 'a14825a2-5af3-49f2-a7a2-fcda7ed92d19', 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 10:49:06', '2026-03-12 11:11:33'),
('a1482671-a63b-4391-b1b5-e5e355d14b61', 'a148259e-6d7a-4beb-a8d9-5741da1a5cd7', 'a1482634-712c-4aea-b5ea-29ff8c7e0517', 'a148259e-283a-43d9-8cb5-fbcd0aa41cd9', 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 10:49:06', '2026-03-12 11:11:33'),
('a1482671-a8f1-4946-9b14-e7b246b78262', 'a14825a2-1483-4065-aaa6-0b881ae73b95', 'a1482634-712c-4aea-b5ea-29ff8c7e0517', NULL, 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 10:49:07', '2026-03-12 11:11:33'),
('a1482671-aba2-424d-b607-7fb454dc8c7a', 'a14825a1-cd76-4183-abc0-852905064534', 'a1482634-712c-4aea-b5ea-29ff8c7e0517', NULL, 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 10:49:07', '2026-03-12 11:11:33'),
('a1482671-add8-409e-b4e1-c33d085770da', 'a14825a1-8702-4d20-8c32-72a03a1ab7e8', 'a1482634-712c-4aea-b5ea-29ff8c7e0517', NULL, 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 10:49:07', '2026-03-12 11:11:33'),
('a1482671-afeb-49dd-a8de-e5e204690cd6', 'a14825a7-8ba5-45f7-bdf9-cfb0308cd3a8', 'a1482634-712c-4aea-b5ea-29ff8c7e0517', 'a14825a4-cb76-47b7-af7d-989234da5037', 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 10:49:07', '2026-03-12 11:11:33'),
('a1482671-b293-45e1-aa3c-8b77a179b0a3', 'a14825a1-409e-4ea5-ac28-f8afad3774b2', 'a1482634-712c-4aea-b5ea-29ff8c7e0517', 'a14825a0-fb48-4f41-97ef-078a00dcb4e9', 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 10:49:07', '2026-03-12 11:11:33'),
('a1482671-b560-46b1-84f1-e89cf439ec8a', 'a14825a9-7aa4-4e33-b967-a5debe74bdea', 'a1482634-712c-4aea-b5ea-29ff8c7e0517', 'a14825a5-e5d3-4e9c-92a0-c65cc0f5e9b5', 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 10:49:07', '2026-03-12 11:11:33'),
('a1482671-b83a-408a-b12a-deb9cddb0b31', 'a14825a3-dc44-4ad1-9b30-83864a60032f', 'a1482634-712c-4aea-b5ea-29ff8c7e0517', 'a14825a0-fb48-4f41-97ef-078a00dcb4e9', 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 10:49:07', '2026-03-12 11:11:33'),
('a1482671-baac-48d9-91da-c5ebfbb12456', 'a14825a8-5f91-4618-aaeb-2d41486f20f3', 'a1482634-712c-4aea-b5ea-29ff8c7e0517', 'a14825a6-2b57-4a6c-8c0b-d591e1a0e124', 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 10:49:07', '2026-03-12 11:11:33'),
('a1482671-bc80-4da4-8376-9d51747c8d54', 'a148259e-283a-43d9-8cb5-fbcd0aa41cd9', 'a1482634-712c-4aea-b5ea-29ff8c7e0517', NULL, 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 10:49:07', '2026-03-12 11:11:33'),
('a1482671-be4e-4a1e-8ef4-71000c7226c8', 'a148259f-8923-4149-9714-e43e353e3c8e', 'a1482634-712c-4aea-b5ea-29ff8c7e0517', 'a148259e-6d7a-4beb-a8d9-5741da1a5cd7', 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 10:49:07', '2026-03-12 11:11:33'),
('a1482671-c065-4ec9-a925-b47296bdf27e', 'a14825a4-854f-4343-a69c-b641a01b4de5', 'a1482634-712c-4aea-b5ea-29ff8c7e0517', 'a14825a2-1483-4065-aaa6-0b881ae73b95', 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 10:49:07', '2026-03-12 11:11:33'),
('a1482671-c2f1-44ac-b844-46016b239304', 'a14825a3-4ce9-467d-b60b-e5f8e51da79a', 'a1482634-712c-4aea-b5ea-29ff8c7e0517', NULL, 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 10:49:07', '2026-03-12 11:11:33'),
('a1482671-c521-4589-83b3-93b7d5370cfa', 'a14825a2-bc7f-4404-bca7-2dcb577dc0a4', 'a1482634-712c-4aea-b5ea-29ff8c7e0517', NULL, 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 10:49:07', '2026-03-12 11:11:33'),
('a1482671-c788-4fdf-ad05-0063af6d8720', 'a14825a4-3e75-47a7-81c2-d40cfd39a5fc', 'a1482634-712c-4aea-b5ea-29ff8c7e0517', 'a14825a3-dc44-4ad1-9b30-83864a60032f', 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 10:49:07', '2026-03-12 11:11:33'),
('a1482671-c9d3-4003-8df0-9794af6a5931', 'a14825a6-b899-4d6e-a02a-0447f43a8c7e', 'a1482634-712c-4aea-b5ea-29ff8c7e0517', 'a14825a0-fb48-4f41-97ef-078a00dcb4e9', 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 10:49:07', '2026-03-12 11:11:33'),
('a1482671-cc09-405b-bb49-0a77a3fb68be', 'a14825a8-18c7-4e7c-8ea1-f5ecc302d468', 'a1482634-712c-4aea-b5ea-29ff8c7e0517', 'a14825a2-5af3-49f2-a7a2-fcda7ed92d19', 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 10:49:07', '2026-03-12 11:11:33'),
('a1482671-ce15-4a2c-8978-f1f3a022377e', 'a14825a5-1286-4c92-a92b-6bbddbb8cd5d', 'a1482634-712c-4aea-b5ea-29ff8c7e0517', NULL, 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 10:49:07', '2026-03-12 11:11:33'),
('a1482671-d050-4955-94d9-e97e3138ec32', 'a14825a3-0663-4da7-96de-069374c4098f', 'a1482634-712c-4aea-b5ea-29ff8c7e0517', 'a14825a2-5af3-49f2-a7a2-fcda7ed92d19', 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 10:49:07', '2026-03-12 11:11:33'),
('a1482671-d29c-4e57-aed3-8211592981f8', 'a14825a0-14d3-440f-bc7d-4d22609baa61', 'a1482634-712c-4aea-b5ea-29ff8c7e0517', 'a148259e-fad7-4e7f-ae91-6e436e8943a7', 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 10:49:07', '2026-03-12 11:11:33'),
('a1482671-d4d1-4cbe-9efa-e903e6060461', 'a14825a6-ff25-4ef5-a854-8df26e8e59a1', 'a1482634-712c-4aea-b5ea-29ff8c7e0517', 'a14825a0-fb48-4f41-97ef-078a00dcb4e9', 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 10:49:07', '2026-03-12 11:11:33'),
('a1482671-d792-4bf8-b254-67ac4404071e', 'a14825a9-c379-4da3-898e-1724aa4eca38', 'a1482634-712c-4aea-b5ea-29ff8c7e0517', 'a14825a0-14d3-440f-bc7d-4d22609baa61', 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 10:49:07', '2026-03-12 11:11:33'),
('a1482671-da64-415e-abce-0db88f1ef034', 'a14825a0-5a6e-459b-a43c-e10e9d7bfb23', 'a1482634-712c-4aea-b5ea-29ff8c7e0517', 'a148259f-433f-414a-83a9-0b618bc01856', 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 10:49:07', '2026-03-12 11:11:33'),
('a1482671-dd1a-48b5-a9b2-ba2fc5812d12', 'a14825a4-cb76-47b7-af7d-989234da5037', 'a1482634-712c-4aea-b5ea-29ff8c7e0517', NULL, 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 10:49:07', '2026-03-12 11:11:33'),
('a1482671-dfc0-451e-8703-f9e661f8cd01', 'a14825a6-2b57-4a6c-8c0b-d591e1a0e124', 'a1482634-712c-4aea-b5ea-29ff8c7e0517', NULL, 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 10:49:07', '2026-03-12 11:11:33'),
('a1482671-e293-4227-95d7-b3865f1c427b', 'a148259f-433f-414a-83a9-0b618bc01856', 'a1482634-712c-4aea-b5ea-29ff8c7e0517', 'a148259e-283a-43d9-8cb5-fbcd0aa41cd9', 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 10:49:07', '2026-03-12 11:11:33'),
('a1482671-e55a-4d31-af46-8a01f4b43144', 'a14825a5-9f09-4264-945f-04feded74cdb', 'a1482634-712c-4aea-b5ea-29ff8c7e0517', 'a14825a0-fb48-4f41-97ef-078a00dcb4e9', 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 10:49:07', '2026-03-12 11:11:33'),
('a1482671-e84c-4f33-b978-7672805ccaed', 'a14825a8-ec39-47ff-b804-fc90e1515aa5', 'a1482634-712c-4aea-b5ea-29ff8c7e0517', 'a14825a0-fb48-4f41-97ef-078a00dcb4e9', 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 10:49:07', '2026-03-12 11:11:33'),
('a14833cf-ed90-4eee-bd2d-aabc40ff3015', 'a14825a0-a049-47c1-9e3d-3e7df3b1a32f', 'a1483218-1ecb-42d0-9ed1-de826bac3f58', 'a148259e-283a-43d9-8cb5-fbcd0aa41cd9', 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 11:26:29', '2026-03-12 11:51:44'),
('a14833cf-f4de-4e0b-96d6-473c6032fb19', 'a14825a5-e5d3-4e9c-92a0-c65cc0f5e9b5', 'a1483218-1ecb-42d0-9ed1-de826bac3f58', 'a14825a0-fb48-4f41-97ef-078a00dcb4e9', 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 11:26:29', '2026-03-12 11:51:44'),
('a14833cf-f8b1-4c4f-a826-938cc98241cc', 'a14825a5-58ec-463b-915e-7190c8da9126', 'a1483218-1ecb-42d0-9ed1-de826bac3f58', NULL, 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 11:26:29', '2026-03-12 11:51:44'),
('a14833cf-fc5f-4c61-988e-3907510f1407', 'a14825a7-45ed-4451-9090-e811c609b807', 'a1483218-1ecb-42d0-9ed1-de826bac3f58', 'a14825a3-dc44-4ad1-9b30-83864a60032f', 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 11:26:29', '2026-03-12 11:51:44'),
('a14833d0-007c-4078-8308-57435b50715b', 'a148259f-cf2a-4d58-a54d-13b67e976cf9', 'a1483218-1ecb-42d0-9ed1-de826bac3f58', 'a148259e-b3bb-4732-9a17-434c54f1eb22', 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 11:26:29', '2026-03-12 11:51:44'),
('a14833d0-0431-4e0f-8afc-f02407b8d426', 'a14825a2-5af3-49f2-a7a2-fcda7ed92d19', 'a1483218-1ecb-42d0-9ed1-de826bac3f58', 'a14825a0-fb48-4f41-97ef-078a00dcb4e9', 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 11:26:29', '2026-03-12 11:51:44'),
('a14833d0-0805-4639-8bd1-9d4b41049646', 'a148259e-b3bb-4732-9a17-434c54f1eb22', 'a1483218-1ecb-42d0-9ed1-de826bac3f58', 'a148259e-283a-43d9-8cb5-fbcd0aa41cd9', 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 11:26:29', '2026-03-12 11:51:44'),
('a14833d0-0de5-4e91-934d-387301c7ab31', 'a14825a7-d2a8-46b1-b11e-e3e5953e3b80', 'a1483218-1ecb-42d0-9ed1-de826bac3f58', 'a14825a0-fb48-4f41-97ef-078a00dcb4e9', 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 11:26:29', '2026-03-12 11:51:44'),
('a14833d0-1225-4897-a9bf-76adee888fc3', 'a14825a9-33b8-4940-a5ac-05f6b1e47bf3', 'a1483218-1ecb-42d0-9ed1-de826bac3f58', 'a14825a4-cb76-47b7-af7d-989234da5037', 'completed', 'pending', 'midterm_files/dcIT5Fi75MKJjDknK78dbSQGw2XHAdyU8NoTjY7t.pdf', NULL, NULL, NULL, '2026-03-12 11:26:29', '2026-03-12 12:04:37'),
('a14833d0-158a-4e1b-ac4f-18b4778ba638', 'a148259e-fad7-4e7f-ae91-6e436e8943a7', 'a1483218-1ecb-42d0-9ed1-de826bac3f58', 'a148259e-283a-43d9-8cb5-fbcd0aa41cd9', 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 11:26:29', '2026-03-12 11:51:44'),
('a14833d0-1a8b-44ed-914e-ed5405d1c23f', 'a14825a6-715a-45b7-b0d3-89d131b84a48', 'a1483218-1ecb-42d0-9ed1-de826bac3f58', 'a14825a0-fb48-4f41-97ef-078a00dcb4e9', 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 11:26:29', '2026-03-12 11:51:44'),
('a14833d0-1e3b-4219-aead-901a81df2209', 'a14825a8-a5b6-4024-9125-9171954396ac', 'a1483218-1ecb-42d0-9ed1-de826bac3f58', 'a14825a0-fb48-4f41-97ef-078a00dcb4e9', 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 11:26:29', '2026-03-12 11:51:44'),
('a14833d0-227e-44ba-bd7a-827319dc62e1', 'a14825a3-93f8-4fbe-99d9-0c1632432e7e', 'a1483218-1ecb-42d0-9ed1-de826bac3f58', 'a14825a2-5af3-49f2-a7a2-fcda7ed92d19', 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 11:26:29', '2026-03-12 11:51:44'),
('a14833d0-2631-4e37-87eb-30b09a6dc923', 'a148259e-6d7a-4beb-a8d9-5741da1a5cd7', 'a1483218-1ecb-42d0-9ed1-de826bac3f58', 'a148259e-283a-43d9-8cb5-fbcd0aa41cd9', 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 11:26:29', '2026-03-12 11:51:44'),
('a14833d0-2997-41b7-8a69-afadb66e469d', 'a14825a2-1483-4065-aaa6-0b881ae73b95', 'a1483218-1ecb-42d0-9ed1-de826bac3f58', NULL, 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 11:26:29', '2026-03-12 11:51:44'),
('a14833d0-2ced-4442-a150-925d4ac72e06', 'a14825a1-cd76-4183-abc0-852905064534', 'a1483218-1ecb-42d0-9ed1-de826bac3f58', NULL, 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 11:26:29', '2026-03-12 11:51:44'),
('a14833d0-2fcc-4f82-adb5-ff8a66ed3783', 'a14825a1-8702-4d20-8c32-72a03a1ab7e8', 'a1483218-1ecb-42d0-9ed1-de826bac3f58', NULL, 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 11:26:29', '2026-03-12 11:51:44'),
('a14833d0-33fa-4275-a996-ae3750f56ff3', 'a14825a7-8ba5-45f7-bdf9-cfb0308cd3a8', 'a1483218-1ecb-42d0-9ed1-de826bac3f58', 'a14825a4-cb76-47b7-af7d-989234da5037', 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 11:26:30', '2026-03-12 11:51:44'),
('a14833d0-3719-4328-ad0a-a7846e49fb81', 'a14825a1-409e-4ea5-ac28-f8afad3774b2', 'a1483218-1ecb-42d0-9ed1-de826bac3f58', 'a14825a0-fb48-4f41-97ef-078a00dcb4e9', 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 11:26:30', '2026-03-12 11:51:44'),
('a14833d0-3b6c-4899-8c49-8ed28b4fbcad', 'a14825a9-7aa4-4e33-b967-a5debe74bdea', 'a1483218-1ecb-42d0-9ed1-de826bac3f58', 'a14825a5-e5d3-4e9c-92a0-c65cc0f5e9b5', 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 11:26:30', '2026-03-12 11:51:44'),
('a14833d0-401b-4896-a33b-691f7a37f88b', 'a14825a3-dc44-4ad1-9b30-83864a60032f', 'a1483218-1ecb-42d0-9ed1-de826bac3f58', 'a14825a0-fb48-4f41-97ef-078a00dcb4e9', 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 11:26:30', '2026-03-12 11:51:44'),
('a14833d0-4403-47aa-b020-f5be6b98cfb4', 'a14825a8-5f91-4618-aaeb-2d41486f20f3', 'a1483218-1ecb-42d0-9ed1-de826bac3f58', 'a14825a6-2b57-4a6c-8c0b-d591e1a0e124', 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 11:26:30', '2026-03-12 11:51:44'),
('a14833d0-4792-4da3-8c44-ca8a3e31a8c2', 'a148259e-283a-43d9-8cb5-fbcd0aa41cd9', 'a1483218-1ecb-42d0-9ed1-de826bac3f58', NULL, 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 11:26:30', '2026-03-12 11:51:44'),
('a14833d0-4b73-49df-bcd3-d20c36401a45', 'a148259f-8923-4149-9714-e43e353e3c8e', 'a1483218-1ecb-42d0-9ed1-de826bac3f58', 'a148259e-6d7a-4beb-a8d9-5741da1a5cd7', 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 11:26:30', '2026-03-12 11:51:44'),
('a14833d0-4f7e-48cb-b8a9-c7e326e1f0b1', 'a14825a4-854f-4343-a69c-b641a01b4de5', 'a1483218-1ecb-42d0-9ed1-de826bac3f58', 'a14825a2-1483-4065-aaa6-0b881ae73b95', 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 11:26:30', '2026-03-12 11:51:44'),
('a14833d0-536c-4916-a459-c2b924347de6', 'a14825a3-4ce9-467d-b60b-e5f8e51da79a', 'a1483218-1ecb-42d0-9ed1-de826bac3f58', NULL, 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 11:26:30', '2026-03-12 11:51:44'),
('a14833d0-56e9-4145-846f-2ff8baddba0e', 'a14825a2-bc7f-4404-bca7-2dcb577dc0a4', 'a1483218-1ecb-42d0-9ed1-de826bac3f58', NULL, 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 11:26:30', '2026-03-12 11:51:44'),
('a14833d0-5a69-4b50-a7c2-95b2cfce9347', 'a14825a4-3e75-47a7-81c2-d40cfd39a5fc', 'a1483218-1ecb-42d0-9ed1-de826bac3f58', 'a14825a3-dc44-4ad1-9b30-83864a60032f', 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 11:26:30', '2026-03-12 11:51:44'),
('a14833d0-5e2a-4b18-8a1a-deb678f2db9f', 'a14825a6-b899-4d6e-a02a-0447f43a8c7e', 'a1483218-1ecb-42d0-9ed1-de826bac3f58', 'a14825a0-fb48-4f41-97ef-078a00dcb4e9', 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 11:26:30', '2026-03-12 11:51:44'),
('a14833d0-61e8-4772-bbdc-6582a4e255db', 'a14825a8-18c7-4e7c-8ea1-f5ecc302d468', 'a1483218-1ecb-42d0-9ed1-de826bac3f58', 'a14825a2-5af3-49f2-a7a2-fcda7ed92d19', 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 11:26:30', '2026-03-12 11:51:44'),
('a14833d0-652b-46cf-99ba-0471ba0a79e4', 'a14825a5-1286-4c92-a92b-6bbddbb8cd5d', 'a1483218-1ecb-42d0-9ed1-de826bac3f58', NULL, 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 11:26:30', '2026-03-12 11:51:44'),
('a14833d0-68a8-4c4a-8dce-a251d71c8ac3', 'a14825a3-0663-4da7-96de-069374c4098f', 'a1483218-1ecb-42d0-9ed1-de826bac3f58', 'a14825a2-5af3-49f2-a7a2-fcda7ed92d19', 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 11:26:30', '2026-03-12 11:51:44'),
('a14833d0-6c1a-447b-b831-a1d8e3103d0b', 'a14825a0-14d3-440f-bc7d-4d22609baa61', 'a1483218-1ecb-42d0-9ed1-de826bac3f58', 'a148259e-fad7-4e7f-ae91-6e436e8943a7', 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 11:26:30', '2026-03-12 11:51:44'),
('a14833d0-705e-4879-bbd3-99bd5030061f', 'a14825a6-ff25-4ef5-a854-8df26e8e59a1', 'a1483218-1ecb-42d0-9ed1-de826bac3f58', 'a14825a0-fb48-4f41-97ef-078a00dcb4e9', 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 11:26:30', '2026-03-12 11:51:44'),
('a14833d0-76c5-4a6f-b294-bd2a95ef0636', 'a14825a9-c379-4da3-898e-1724aa4eca38', 'a1483218-1ecb-42d0-9ed1-de826bac3f58', 'a14825a0-14d3-440f-bc7d-4d22609baa61', 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 11:26:30', '2026-03-12 11:51:44'),
('a14833d0-7ada-428c-8279-a359c635eed0', 'a14825a0-5a6e-459b-a43c-e10e9d7bfb23', 'a1483218-1ecb-42d0-9ed1-de826bac3f58', 'a148259f-433f-414a-83a9-0b618bc01856', 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 11:26:30', '2026-03-12 11:51:44'),
('a14833d0-7df8-4b1d-a857-dc1507a0dcb7', 'a14825a4-cb76-47b7-af7d-989234da5037', 'a1483218-1ecb-42d0-9ed1-de826bac3f58', NULL, 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 11:26:30', '2026-03-12 11:51:44'),
('a14833d0-812a-4ed6-bb56-4d96ce4a9a8d', 'a14825a6-2b57-4a6c-8c0b-d591e1a0e124', 'a1483218-1ecb-42d0-9ed1-de826bac3f58', NULL, 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 11:26:30', '2026-03-12 11:51:44'),
('a14833d0-8430-41d0-9eee-42ee374bec4f', 'a148259f-433f-414a-83a9-0b618bc01856', 'a1483218-1ecb-42d0-9ed1-de826bac3f58', 'a148259e-283a-43d9-8cb5-fbcd0aa41cd9', 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 11:26:30', '2026-03-12 11:51:44'),
('a14833d0-879e-4511-8f4c-a45e092481ee', 'a14825a5-9f09-4264-945f-04feded74cdb', 'a1483218-1ecb-42d0-9ed1-de826bac3f58', 'a14825a0-fb48-4f41-97ef-078a00dcb4e9', 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 11:26:30', '2026-03-12 11:51:44'),
('a14833d0-8aec-4931-b2f8-6f10b165e093', 'a14825a8-ec39-47ff-b804-fc90e1515aa5', 'a1483218-1ecb-42d0-9ed1-de826bac3f58', 'a14825a0-fb48-4f41-97ef-078a00dcb4e9', 'not_evaluated', 'pending', NULL, NULL, NULL, NULL, '2026-03-12 11:26:30', '2026-03-12 11:51:44');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `campaigns`
--
ALTER TABLE `campaigns`
  ADD PRIMARY KEY (`uuid`);

--
-- Index pour la table `entities`
--
ALTER TABLE `entities`
  ADD PRIMARY KEY (`uuid`),
  ADD KEY `entities_parent_uuid_foreign` (`parent_uuid`);

--
-- Index pour la table `evaluation_comments`
--
ALTER TABLE `evaluation_comments`
  ADD PRIMARY KEY (`uuid`),
  ADD KEY `evaluation_comments_objective_uuid_foreign` (`objective_uuid`),
  ADD KEY `evaluation_comments_user_uuid_foreign` (`user_uuid`);

--
-- Index pour la table `evaluation_decisions`
--
ALTER TABLE `evaluation_decisions`
  ADD PRIMARY KEY (`uuid`),
  ADD KEY `evaluation_decisions_user_campaign_uuid_foreign` (`user_campaign_uuid`),
  ADD KEY `evaluation_decisions_actor_uuid_foreign` (`actor_uuid`);

--
-- Index pour la table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Index pour la table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `objectives`
--
ALTER TABLE `objectives`
  ADD PRIMARY KEY (`uuid`),
  ADD KEY `objectives_user_campaign_uuid_foreign` (`user_campaign_uuid`),
  ADD KEY `objectives_objective_category_uuid_foreign` (`objective_category_uuid`);

--
-- Index pour la table `objective_categories`
--
ALTER TABLE `objective_categories`
  ADD PRIMARY KEY (`uuid`);

--
-- Index pour la table `objective_comments`
--
ALTER TABLE `objective_comments`
  ADD PRIMARY KEY (`uuid`),
  ADD KEY `objective_comments_objective_uuid_foreign` (`objective_uuid`),
  ADD KEY `objective_comments_user_uuid_foreign` (`user_uuid`);

--
-- Index pour la table `objective_decisions`
--
ALTER TABLE `objective_decisions`
  ADD PRIMARY KEY (`uuid`),
  ADD KEY `objective_decisions_user_campaign_uuid_foreign` (`user_campaign_uuid`),
  ADD KEY `objective_decisions_actor_uuid_foreign` (`actor_uuid`);

--
-- Index pour la table `objective_histories`
--
ALTER TABLE `objective_histories`
  ADD PRIMARY KEY (`uuid`),
  ADD KEY `objective_histories_objective_uuid_foreign` (`objective_uuid`),
  ADD KEY `objective_histories_changed_by_uuid_foreign` (`changed_by_uuid`);

--
-- Index pour la table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Index pour la table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`uuid`),
  ADD UNIQUE KEY `permissions_name_unique` (`name`),
  ADD UNIQUE KEY `permissions_slug_unique` (`slug`);

--
-- Index pour la table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Index pour la table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`uuid`),
  ADD UNIQUE KEY `roles_name_unique` (`name`),
  ADD UNIQUE KEY `roles_slug_unique` (`slug`);

--
-- Index pour la table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`uuid`),
  ADD UNIQUE KEY `role_permissions_role_uuid_permission_uuid_unique` (`role_uuid`,`permission_uuid`),
  ADD KEY `role_permissions_permission_uuid_foreign` (`permission_uuid`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`uuid`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_role_uuid_foreign` (`role_uuid`),
  ADD KEY `users_entity_uuid_foreign` (`entity_uuid`),
  ADD KEY `users_supervisor_uuid_foreign` (`supervisor_uuid`);

--
-- Index pour la table `user_campaigns`
--
ALTER TABLE `user_campaigns`
  ADD PRIMARY KEY (`uuid`),
  ADD UNIQUE KEY `user_campaigns_user_uuid_campaign_uuid_unique` (`user_uuid`,`campaign_uuid`),
  ADD KEY `user_campaigns_campaign_uuid_foreign` (`campaign_uuid`),
  ADD KEY `user_campaigns_supervisor_uuid_foreign` (`supervisor_uuid`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT pour la table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `entities`
--
ALTER TABLE `entities`
  ADD CONSTRAINT `entities_parent_uuid_foreign` FOREIGN KEY (`parent_uuid`) REFERENCES `entities` (`uuid`) ON DELETE SET NULL;

--
-- Contraintes pour la table `evaluation_comments`
--
ALTER TABLE `evaluation_comments`
  ADD CONSTRAINT `evaluation_comments_objective_uuid_foreign` FOREIGN KEY (`objective_uuid`) REFERENCES `objectives` (`uuid`) ON DELETE CASCADE,
  ADD CONSTRAINT `evaluation_comments_user_uuid_foreign` FOREIGN KEY (`user_uuid`) REFERENCES `users` (`uuid`) ON DELETE CASCADE;

--
-- Contraintes pour la table `evaluation_decisions`
--
ALTER TABLE `evaluation_decisions`
  ADD CONSTRAINT `evaluation_decisions_actor_uuid_foreign` FOREIGN KEY (`actor_uuid`) REFERENCES `users` (`uuid`) ON DELETE CASCADE,
  ADD CONSTRAINT `evaluation_decisions_user_campaign_uuid_foreign` FOREIGN KEY (`user_campaign_uuid`) REFERENCES `user_campaigns` (`uuid`) ON DELETE CASCADE;

--
-- Contraintes pour la table `objectives`
--
ALTER TABLE `objectives`
  ADD CONSTRAINT `objectives_objective_category_uuid_foreign` FOREIGN KEY (`objective_category_uuid`) REFERENCES `objective_categories` (`uuid`) ON DELETE CASCADE,
  ADD CONSTRAINT `objectives_user_campaign_uuid_foreign` FOREIGN KEY (`user_campaign_uuid`) REFERENCES `user_campaigns` (`uuid`) ON DELETE CASCADE;

--
-- Contraintes pour la table `objective_comments`
--
ALTER TABLE `objective_comments`
  ADD CONSTRAINT `objective_comments_objective_uuid_foreign` FOREIGN KEY (`objective_uuid`) REFERENCES `objectives` (`uuid`) ON DELETE CASCADE,
  ADD CONSTRAINT `objective_comments_user_uuid_foreign` FOREIGN KEY (`user_uuid`) REFERENCES `users` (`uuid`) ON DELETE CASCADE;

--
-- Contraintes pour la table `objective_decisions`
--
ALTER TABLE `objective_decisions`
  ADD CONSTRAINT `objective_decisions_actor_uuid_foreign` FOREIGN KEY (`actor_uuid`) REFERENCES `users` (`uuid`) ON DELETE CASCADE,
  ADD CONSTRAINT `objective_decisions_user_campaign_uuid_foreign` FOREIGN KEY (`user_campaign_uuid`) REFERENCES `user_campaigns` (`uuid`) ON DELETE CASCADE;

--
-- Contraintes pour la table `objective_histories`
--
ALTER TABLE `objective_histories`
  ADD CONSTRAINT `objective_histories_changed_by_uuid_foreign` FOREIGN KEY (`changed_by_uuid`) REFERENCES `users` (`uuid`) ON DELETE CASCADE,
  ADD CONSTRAINT `objective_histories_objective_uuid_foreign` FOREIGN KEY (`objective_uuid`) REFERENCES `objectives` (`uuid`) ON DELETE CASCADE;

--
-- Contraintes pour la table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `role_permissions_permission_uuid_foreign` FOREIGN KEY (`permission_uuid`) REFERENCES `permissions` (`uuid`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_permissions_role_uuid_foreign` FOREIGN KEY (`role_uuid`) REFERENCES `roles` (`uuid`) ON DELETE CASCADE;

--
-- Contraintes pour la table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_entity_uuid_foreign` FOREIGN KEY (`entity_uuid`) REFERENCES `entities` (`uuid`) ON DELETE SET NULL,
  ADD CONSTRAINT `users_role_uuid_foreign` FOREIGN KEY (`role_uuid`) REFERENCES `roles` (`uuid`) ON DELETE SET NULL,
  ADD CONSTRAINT `users_supervisor_uuid_foreign` FOREIGN KEY (`supervisor_uuid`) REFERENCES `users` (`uuid`) ON DELETE SET NULL;

--
-- Contraintes pour la table `user_campaigns`
--
ALTER TABLE `user_campaigns`
  ADD CONSTRAINT `user_campaigns_campaign_uuid_foreign` FOREIGN KEY (`campaign_uuid`) REFERENCES `campaigns` (`uuid`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_campaigns_supervisor_uuid_foreign` FOREIGN KEY (`supervisor_uuid`) REFERENCES `users` (`uuid`) ON DELETE SET NULL,
  ADD CONSTRAINT `user_campaigns_user_uuid_foreign` FOREIGN KEY (`user_uuid`) REFERENCES `users` (`uuid`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
