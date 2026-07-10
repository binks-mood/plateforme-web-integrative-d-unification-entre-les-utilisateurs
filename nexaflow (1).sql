-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mar. 09 juin 2026 à 20:36
-- Version du serveur : 8.0.44
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `nexaflow`
--

-- --------------------------------------------------------

--
-- Structure de la table `activities`
--

CREATE TABLE `activities` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `action` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `entity_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entity_id` int DEFAULT NULL,
  `entity_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `activities`
--

INSERT INTO `activities` (`id`, `user_id`, `action`, `entity_type`, `entity_id`, `entity_name`, `created_at`) VALUES
(2, 2, 'A créé son compte', NULL, NULL, NULL, '2026-05-08 00:52:43'),
(3, 2, 'S\'est déconnecté', NULL, NULL, NULL, '2026-05-08 00:53:17'),
(4, 3, 'A créé son compte', NULL, NULL, NULL, '2026-05-08 00:54:22'),
(5, 3, 'S\'est déconnecté', NULL, NULL, NULL, '2026-05-08 01:26:42'),
(6, 3, 'S\'est connecté', NULL, NULL, NULL, '2026-05-08 01:41:18'),
(7, 3, 'S\'est déconnecté', NULL, NULL, NULL, '2026-05-08 17:57:37'),
(8, 2, 'S\'est connecté', NULL, NULL, NULL, '2026-05-08 17:57:50'),
(9, 2, 'S\'est déconnecté', NULL, NULL, NULL, '2026-05-09 13:50:00'),
(10, 3, 'S\'est connecté', NULL, NULL, NULL, '2026-05-09 13:50:05'),
(11, 3, 'S\'est déconnecté', NULL, NULL, NULL, '2026-05-09 13:53:25'),
(12, 1, 'S\'est connecté', NULL, NULL, NULL, '2026-05-09 13:56:18'),
(13, 1, 'S\'est déconnecté', NULL, NULL, NULL, '2026-05-09 13:57:09'),
(14, 1, 'S\'est connecté', NULL, NULL, NULL, '2026-05-09 13:57:12'),
(15, 1, 'S\'est déconnecté', NULL, NULL, NULL, '2026-05-09 15:41:12'),
(16, 1, 'S\'est connecté', NULL, NULL, NULL, '2026-05-09 15:41:15'),
(17, 1, 'S\'est déconnecté', NULL, NULL, NULL, '2026-05-09 15:50:39'),
(18, 1, 'S\'est connecté', NULL, NULL, NULL, '2026-05-09 15:52:02'),
(19, 1, 'S\'est connecté', NULL, NULL, NULL, '2026-05-09 15:52:38'),
(20, 1, 'S\'est déconnecté', NULL, NULL, NULL, '2026-05-09 16:21:31'),
(21, 1, 'S\'est déconnecté', NULL, NULL, NULL, '2026-05-09 16:21:48'),
(22, 2, 'S\'est connecté', NULL, NULL, NULL, '2026-05-09 16:21:54'),
(23, 2, 'a créé le projet', 'project', 1, 'nexaflow', '2026-05-09 16:23:00'),
(24, 2, 'a créé la tâche', 'task', 1, 'configurer le tableau de bord de l administrateur generale', '2026-05-09 16:24:24'),
(25, 2, 'a mis à jour le statut de', 'task', 1, 'configurer le tableau de bord de l administrateur generale', '2026-05-09 16:26:11'),
(26, 2, 'a mis à jour le statut de', 'task', 1, 'configurer le tableau de bord de l administrateur generale', '2026-05-09 16:26:28'),
(27, 2, 'S\'est déconnecté', NULL, NULL, NULL, '2026-05-09 16:26:45'),
(28, 3, 'S\'est connecté', NULL, NULL, NULL, '2026-05-09 16:26:50'),
(29, 3, 'a créé le projet', 'project', 2, 'MJ store', '2026-05-09 16:28:21'),
(30, 3, 'S\'est déconnecté', NULL, NULL, NULL, '2026-05-09 16:29:09'),
(31, 1, 'S\'est connecté', NULL, NULL, NULL, '2026-05-09 16:29:13'),
(32, 1, 'a créé l\'équipe', 'team', 1, 'MJ store', '2026-05-09 16:30:06'),
(33, 1, 'a ajouté un membre à une équipe', 'team', 1, '', '2026-05-09 16:30:13'),
(34, 1, 'a ajouté un membre à une équipe', 'team', 1, '', '2026-05-09 16:30:16'),
(35, 1, 'S\'est déconnecté', NULL, NULL, NULL, '2026-05-09 16:30:21'),
(36, 3, 'S\'est connecté', NULL, NULL, NULL, '2026-05-09 16:30:27'),
(37, 3, 'a supprimé un projet', 'project', NULL, NULL, '2026-05-09 16:30:48'),
(38, 3, 'a créé le projet', 'project', 3, 'MJ store', '2026-05-09 16:31:16'),
(39, 1, 'S\'est connecté', NULL, NULL, NULL, '2026-05-09 16:54:49'),
(40, 3, 'S\'est connecté', NULL, NULL, NULL, '2026-05-11 22:35:19'),
(41, 3, 'S\'est déconnecté', NULL, NULL, NULL, '2026-05-11 22:37:40'),
(42, 2, 'S\'est connecté', NULL, NULL, NULL, '2026-05-11 22:37:45'),
(43, 2, 'S\'est déconnecté', NULL, NULL, NULL, '2026-05-11 22:59:54'),
(44, 3, 'S\'est connecté', NULL, NULL, NULL, '2026-05-11 22:59:58'),
(45, 3, 'S\'est déconnecté', NULL, NULL, NULL, '2026-05-11 23:26:42'),
(46, 2, 'S\'est connecté', NULL, NULL, NULL, '2026-05-11 23:26:45'),
(47, 2, 'S\'est déconnecté', NULL, NULL, NULL, '2026-05-11 23:31:10'),
(48, 3, 'S\'est connecté', NULL, NULL, NULL, '2026-05-11 23:31:15'),
(49, 3, 'a créé la tâche', 'task', 2, 'faire le font end', '2026-05-11 23:31:59'),
(50, 3, 'S\'est déconnecté', NULL, NULL, NULL, '2026-05-11 23:32:25'),
(51, 2, 'S\'est connecté', NULL, NULL, NULL, '2026-05-11 23:32:30'),
(52, 2, 'a mis à jour le statut de', 'task', 2, 'faire le font end', '2026-05-11 23:40:15'),
(53, 2, 'S\'est déconnecté', NULL, NULL, NULL, '2026-05-11 23:40:43'),
(54, 3, 'S\'est connecté', NULL, NULL, NULL, '2026-05-11 23:40:47'),
(55, 3, 'S\'est déconnecté', NULL, NULL, NULL, '2026-05-11 23:50:54'),
(56, 2, 'S\'est connecté', NULL, NULL, NULL, '2026-05-11 23:50:58'),
(57, 2, 'S\'est déconnecté', NULL, NULL, NULL, '2026-05-12 00:05:52'),
(58, 3, 'S\'est connecté', NULL, NULL, NULL, '2026-05-12 00:05:58'),
(59, 3, 'S\'est déconnecté', NULL, NULL, NULL, '2026-05-12 00:13:15'),
(60, 2, 'S\'est connecté', NULL, NULL, NULL, '2026-05-12 00:13:20'),
(61, 2, 'S\'est déconnecté', NULL, NULL, NULL, '2026-05-12 13:52:07'),
(62, 3, 'S\'est connecté', NULL, NULL, NULL, '2026-05-12 13:52:11'),
(63, 3, 'a mis à jour le statut de', 'task', 2, 'faire le font end', '2026-05-12 19:55:26'),
(64, 3, 'a mis à jour le statut de', 'task', 2, 'faire le font end', '2026-05-12 19:55:34'),
(65, 3, 'a mis à jour le statut de', 'task', 2, 'faire le font end', '2026-05-12 19:55:43'),
(66, 3, 'S\'est déconnecté', NULL, NULL, NULL, '2026-05-12 19:56:57'),
(67, 2, 'S\'est connecté', NULL, NULL, NULL, '2026-05-29 12:45:49'),
(68, 2, 'S\'est déconnecté', NULL, NULL, NULL, '2026-05-29 12:51:36'),
(69, 3, 'S\'est connecté', NULL, NULL, NULL, '2026-05-29 12:51:42'),
(70, 3, 'S\'est déconnecté', NULL, NULL, NULL, '2026-05-29 12:55:56'),
(71, 1, 'S\'est connecté', NULL, NULL, NULL, '2026-05-29 12:56:03'),
(72, 1, 'S\'est déconnecté', NULL, NULL, NULL, '2026-05-29 13:06:32'),
(73, 3, 'S\'est connecté', NULL, NULL, NULL, '2026-05-29 13:06:37'),
(74, 3, 'S\'est déconnecté', NULL, NULL, NULL, '2026-05-29 14:21:41'),
(75, 2, 'S\'est connecté', NULL, NULL, NULL, '2026-05-29 14:24:49'),
(76, 2, 'S\'est déconnecté', NULL, NULL, NULL, '2026-05-29 14:44:24'),
(77, 3, 'S\'est connecté', NULL, NULL, NULL, '2026-05-29 14:44:29'),
(78, 3, 'S\'est déconnecté', NULL, NULL, NULL, '2026-05-29 14:44:35'),
(79, 1, 'S\'est connecté', NULL, NULL, NULL, '2026-05-29 14:44:40'),
(80, 1, 'a créé l\'automatisation', 'automation', 1, 'ajouter as un groupe', '2026-05-29 14:46:06'),
(81, 1, 'S\'est déconnecté', NULL, NULL, NULL, '2026-05-29 14:48:46'),
(82, 3, 'S\'est connecté', NULL, NULL, NULL, '2026-05-29 14:48:51'),
(83, 3, 'a mis à jour le statut de', 'task', 2, 'faire le font end', '2026-05-29 14:51:49'),
(84, 3, 'S\'est déconnecté', NULL, NULL, NULL, '2026-05-29 14:54:36'),
(85, 1, 'S\'est connecté', NULL, NULL, NULL, '2026-05-29 14:54:43'),
(86, 3, 'S\'est connecté', NULL, NULL, NULL, '2026-06-05 12:51:22'),
(87, 3, 'S\'est déconnecté', NULL, NULL, NULL, '2026-06-05 13:46:53'),
(88, 1, 'S\'est connecté', NULL, NULL, NULL, '2026-06-05 13:46:59'),
(89, 1, 'S\'est déconnecté', NULL, NULL, NULL, '2026-06-05 18:37:19'),
(90, 3, 'S\'est connecté', NULL, NULL, NULL, '2026-06-05 18:37:24'),
(91, 3, 'S\'est déconnecté', NULL, NULL, NULL, '2026-06-07 23:57:19'),
(92, 1, 'S\'est connecté', NULL, NULL, NULL, '2026-06-07 23:57:24'),
(93, 1, 'S\'est déconnecté', NULL, NULL, NULL, '2026-06-08 01:11:39'),
(94, 2, 'S\'est connecté', NULL, NULL, NULL, '2026-06-08 01:11:44'),
(95, 2, 'S\'est déconnecté', NULL, NULL, NULL, '2026-06-08 01:36:39'),
(96, 1, 'S\'est connecté', NULL, NULL, NULL, '2026-06-08 01:36:43'),
(97, 2, 'S\'est connecté', NULL, NULL, NULL, '2026-06-09 15:44:39'),
(98, 2, 'S\'est connecté', NULL, NULL, NULL, '2026-06-09 18:35:41');

-- --------------------------------------------------------

--
-- Structure de la table `automations`
--

CREATE TABLE `automations` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `trigger_type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action_type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `runs_count` int DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `automations`
--

INSERT INTO `automations` (`id`, `user_id`, `name`, `trigger_type`, `action_type`, `is_active`, `runs_count`, `created_at`) VALUES
(1, 1, 'ajouter as un groupe', 'status_changed', 'send_notification', 1, 0, '2026-05-29 14:46:06');

-- --------------------------------------------------------

--
-- Structure de la table `events`
--

CREATE TABLE `events` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `project_id` int DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `event_date` date NOT NULL,
  `event_time` time DEFAULT NULL,
  `color` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `events`
--

INSERT INTO `events` (`id`, `user_id`, `project_id`, `title`, `event_date`, `event_time`, `color`, `created_at`) VALUES
(2, 2, NULL, 'Test event', '2026-05-15', '10:00:00', '#ff0000', '2026-06-05 12:33:41'),
(3, 3, NULL, 'mon anniversaire', '2026-05-18', NULL, '#ec4899', '2026-06-05 12:42:47');

-- --------------------------------------------------------

--
-- Structure de la table `integrations`
--

CREATE TABLE `integrations` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `service` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_connected` tinyint(1) DEFAULT '0',
  `config_data` json DEFAULT NULL,
  `connected_at` datetime DEFAULT NULL,
  `credential` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `messages`
--

CREATE TABLE `messages` (
  `id` int NOT NULL,
  `channel` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'general',
  `sender_id` int DEFAULT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `messages`
--

INSERT INTO `messages` (`id`, `channel`, `sender_id`, `content`, `created_at`) VALUES
(1, 'team_1', 3, 'cc il as un retard au niveau du fond and', '2026-05-12 00:13:10');

-- --------------------------------------------------------

--
-- Structure de la table `notifications`
--

CREATE TABLE `notifications` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `type` enum('info','success','warning','error') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'info',
  `is_read` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `title`, `message`, `type`, `is_read`, `created_at`) VALUES
(1, 2, 'Bienvenue sur NexaFlow !', 'Votre compte a été créé avec succès. Commencez par créer votre premier projet.', 'success', 1, '2026-05-08 00:52:43'),
(2, 3, 'Bienvenue sur NexaFlow !', 'Votre compte a été créé avec succès. Commencez par créer votre premier projet.', 'success', 1, '2026-05-08 00:54:22'),
(3, 1, 'Nouveau projet créé', 'sonnet brou a créé le projet \"nexaflow\".', 'info', 1, '2026-05-09 16:23:00'),
(4, 1, 'Nouveau projet créé', 'malicia brou a créé le projet \"MJ store\".', 'info', 1, '2026-05-09 16:28:21'),
(5, 1, 'Nouveau projet créé', 'malicia brou a créé le projet \"MJ store\".', 'info', 1, '2026-05-09 16:31:16');

-- --------------------------------------------------------

--
-- Structure de la table `projects`
--

CREATE TABLE `projects` (
  `id` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` enum('active','planned','done','late') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'planned',
  `priority` enum('haute','moyenne','basse') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'moyenne',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `progress` int DEFAULT '0',
  `color` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '#3b82f6',
  `owner_id` int DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `team_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `projects`
--

INSERT INTO `projects` (`id`, `name`, `description`, `status`, `priority`, `start_date`, `end_date`, `progress`, `color`, `owner_id`, `created_at`, `updated_at`, `team_id`) VALUES
(1, 'nexaflow', 'plateforme de gestion de projet', 'planned', 'haute', '2025-11-30', '2026-05-10', 100, '#06b6d4', 2, '2026-05-09 16:23:00', '2026-05-09 16:26:28', NULL),
(3, 'MJ store', 'vente d accessoire de mode', 'planned', 'moyenne', '2025-11-29', '2026-05-10', 100, '#f59e0b', 3, '2026-05-09 16:31:16', '2026-05-29 14:51:49', 1);

-- --------------------------------------------------------

--
-- Structure de la table `project_members`
--

CREATE TABLE `project_members` (
  `id` int NOT NULL,
  `project_id` int NOT NULL,
  `user_id` int NOT NULL,
  `role` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'membre',
  `joined_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `project_members`
--

INSERT INTO `project_members` (`id`, `project_id`, `user_id`, `role`, `joined_at`) VALUES
(1, 1, 2, 'Chef de Projet', '2026-05-09 16:23:00'),
(3, 3, 3, 'Chef de Projet', '2026-05-09 16:31:16');

-- --------------------------------------------------------

--
-- Structure de la table `tasks`
--

CREATE TABLE `tasks` (
  `id` int NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `project_id` int DEFAULT NULL,
  `assigned_to` int DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `priority` enum('haute','moyenne','basse') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'moyenne',
  `status` enum('todo','in_progress','review','done') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'todo',
  `due_date` date DEFAULT NULL,
  `kanban_order` int DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `tasks`
--

INSERT INTO `tasks` (`id`, `title`, `description`, `project_id`, `assigned_to`, `created_by`, `priority`, `status`, `due_date`, `kanban_order`, `created_at`, `updated_at`) VALUES
(1, 'configurer le tableau de bord de l administrateur generale', 'LE tableau de bord de l administrateur general est melanger', 1, NULL, 2, 'haute', 'done', '2026-05-08', 0, '2026-05-09 16:24:24', '2026-05-09 16:26:28'),
(2, 'faire le font end', '', 3, 2, 3, 'haute', 'done', '2026-06-01', 0, '2026-05-11 23:31:59', '2026-05-29 14:51:49');

-- --------------------------------------------------------

--
-- Structure de la table `teams`
--

CREATE TABLE `teams` (
  `id` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `teams`
--

INSERT INTO `teams` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'MJ store', NULL, '2026-05-09 16:30:06');

-- --------------------------------------------------------

--
-- Structure de la table `team_members`
--

CREATE TABLE `team_members` (
  `id` int NOT NULL,
  `team_id` int NOT NULL,
  `user_id` int NOT NULL,
  `joined_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `team_members`
--

INSERT INTO `team_members` (`id`, `team_id`, `user_id`, `joined_at`) VALUES
(1, 1, 2, '2026-05-09 16:30:13'),
(2, 1, 3, '2026-05-09 16:30:16');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `firstname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `lastname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `organisation` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` enum('admin','chef_projet','developpeur','observateur') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'developpeur',
  `avatar_initials` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `remember_token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `firstname`, `lastname`, `email`, `password_hash`, `organisation`, `role`, `avatar_initials`, `is_active`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Emmanuel', 'Soonet', 'emmanuel.soonet@admin.com', '$2y$12$EuWXFBzPnlYAeqbxpvK/KuqcIqK5lY3pfPkrCDpXo5V4jKxSX97IW', 'Direction générale', 'admin', 'ES', 1, NULL, '2026-05-07 22:31:40', '2026-05-07 22:31:40'),
(2, 'sonnet', 'brou', 'ebrou1984@gmail.com', '$2y$12$11iBwcOpNhzyNvl3hXwS7erL0oLyRboeTGNjCkx1EC2..DRm6GblO', '', 'developpeur', 'SB', 1, NULL, '2026-05-08 00:52:43', '2026-05-08 00:52:43'),
(3, 'malicia', 'brou', 'broumalicia@gmail.com', '$2y$12$yi1.SGaAEqV2MxMitIKqsupG./p8cPQjzeE1kowvly5xbMhuhh9nK', '', 'chef_projet', 'MB', 1, NULL, '2026-05-08 00:54:22', '2026-05-08 00:54:22');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `activities`
--
ALTER TABLE `activities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `automations`
--
ALTER TABLE `automations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `integrations`
--
ALTER TABLE `integrations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`);

--
-- Index pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `owner_id` (`owner_id`),
  ADD KEY `team_id` (`team_id`);

--
-- Index pour la table `project_members`
--
ALTER TABLE `project_members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_member` (`project_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `assigned_to` (`assigned_to`),
  ADD KEY `created_by` (`created_by`);

--
-- Index pour la table `teams`
--
ALTER TABLE `teams`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `team_members`
--
ALTER TABLE `team_members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_team_member` (`team_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `activities`
--
ALTER TABLE `activities`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;

--
-- AUTO_INCREMENT pour la table `automations`
--
ALTER TABLE `automations`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `events`
--
ALTER TABLE `events`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `integrations`
--
ALTER TABLE `integrations`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `project_members`
--
ALTER TABLE `project_members`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `teams`
--
ALTER TABLE `teams`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `team_members`
--
ALTER TABLE `team_members`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `activities`
--
ALTER TABLE `activities`
  ADD CONSTRAINT `activities_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `automations`
--
ALTER TABLE `automations`
  ADD CONSTRAINT `automations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `integrations`
--
ALTER TABLE `integrations`
  ADD CONSTRAINT `integrations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `projects_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `projects_ibfk_2` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `project_members`
--
ALTER TABLE `project_members`
  ADD CONSTRAINT `project_members_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_members_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tasks_ibfk_2` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tasks_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `team_members`
--
ALTER TABLE `team_members`
  ADD CONSTRAINT `team_members_ibfk_1` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `team_members_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
