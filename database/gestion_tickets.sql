-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : jeu. 10 avr. 2025 à 15:01
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `gestion_tickets`
--

-- --------------------------------------------------------

--
-- Structure de la table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `comments`
--

INSERT INTO `comments` (`id`, `ticket_id`, `user_id`, `content`, `created_at`) VALUES
(1, 1, 2, 'egfef', '2025-04-03 21:28:49'),
(3, 22, 5, 'erqtgerqg', '2025-04-10 08:45:00'),
(4, 23, 5, 'eefqqsedq', '2025-04-10 09:08:48'),
(5, 22, 10, 'zdqsd', '2025-04-10 09:35:14');

-- --------------------------------------------------------

--
-- Structure de la table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`) VALUES
(0, 'user', 'Utilisateur standard'),
(1, 'tech', 'Technicien support'),
(2, 'admin', 'Administrateur');

-- --------------------------------------------------------

--
-- Structure de la table `tickets`
--

CREATE TABLE `tickets` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `status` enum('ouvert','en_cours','ferme') DEFAULT 'ouvert',
  `priority` enum('basse','moyenne','haute') DEFAULT 'moyenne',
  `user_id` int(11) NOT NULL,
  `assigned_to` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `tickets`
--

INSERT INTO `tickets` (`id`, `title`, `description`, `status`, `priority`, `user_id`, `assigned_to`, `created_at`, `updated_at`) VALUES
(1, 'zdd', 'dd', 'ouvert', 'basse', 2, NULL, '2025-04-03 14:49:02', '2025-04-10 07:19:24'),
(2, 'sdcs', 'dv', 'ouvert', 'moyenne', 2, NULL, '2025-04-03 21:18:43', '2025-04-10 07:19:20'),
(3, 'efbqDF', 'FCQEDFd,zsd ', 'en_cours', 'moyenne', 2, NULL, '2025-04-04 07:12:33', '2025-04-04 14:36:12'),
(4, 'efbqDF', 'FCQEDF', 'ouvert', 'basse', 2, NULL, '2025-04-04 07:12:33', '2025-04-07 21:21:07'),
(5, 'JJK', ' Jujutsu kaisen', 'en_cours', 'moyenne', 2, NULL, '2025-04-04 07:22:01', '2025-04-04 14:26:52'),
(6, 'dzdsds', 'sdsqd', 'en_cours', 'basse', 2, NULL, '2025-04-04 07:35:27', '2025-04-10 07:19:13'),
(7, 'eggqrzgf', 'fsgsfdg', 'ouvert', 'moyenne', 5, NULL, '2025-04-06 09:07:09', '2025-04-06 09:07:09'),
(8, 'fg', 'sdfgf', 'ouvert', 'basse', 5, 6, '2025-04-06 09:07:23', '2025-04-10 07:14:42'),
(9, 'fc', 'qdsfsd', 'en_cours', 'moyenne', 5, 5, '2025-04-06 09:09:09', '2025-04-06 09:13:57'),
(10, 'dsfsqd', 'qsdfdsf', 'ouvert', 'basse', 5, 5, '2025-04-06 09:12:02', '2025-04-10 09:01:36'),
(11, 'df', 'sdf', 'en_cours', 'haute', 5, 4, '2025-04-06 09:13:44', '2025-04-10 07:14:29'),
(16, 'azdsqd', 'qsdqs', 'en_cours', 'moyenne', 4, 6, '2025-04-06 09:30:49', '2025-04-06 09:52:05'),
(18, 'rqg', 'swgs', 'ouvert', 'basse', 4, 5, '2025-04-10 07:18:44', '2025-04-10 07:18:44'),
(19, 'qdsfeqsdf', 'sqdfdqsfdsf', 'ouvert', 'moyenne', 10, NULL, '2025-04-10 07:50:14', '2025-04-10 08:10:05'),
(20, 'qsdg', 'qsgfdzDQZD', 'ouvert', 'moyenne', 10, 4, '2025-04-10 07:53:54', '2025-04-10 09:17:58'),
(21, 'CSQCS', 'QSQD', 'ouvert', 'moyenne', 10, 4, '2025-04-10 07:57:02', '2025-04-10 09:18:08'),
(22, 'qefdsq', 'sdqfsdsdqssdfdsdq', 'en_cours', 'basse', 10, NULL, '2025-04-10 08:12:19', '2025-04-10 09:35:32'),
(23, 'fsqfdq', 'qdsfqqfqsdsqfqs', 'ferme', 'moyenne', 5, 5, '2025-04-10 08:45:28', '2025-04-10 09:05:43'),
(24, 'qsfDSD', 'QSFSQF', 'ferme', 'moyenne', 5, 4, '2025-04-10 08:55:24', '2025-04-10 09:08:58'),
(25, 'fdqsfq', 'qfqsf', 'ouvert', 'basse', 4, 5, '2025-04-10 09:11:20', '2025-04-10 09:18:36');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `created_at`) VALUES
(2, 'david', 'davidmv930@gmail.com', '$2y$10$tX08Iz3sv0M54xAH8qnjm.ThMPEkCEjR6FYamwyAKy0hTak7iKBbm', 0, '2025-04-03 13:53:48'),
(4, 'admin', 'admin@gestion.fr', '$2y$10$dvHGNs9hn7IWRYdYbbabAewKrN7C1PMWSQw6N9GIzgEZij8QI4zhC', 2, '2025-04-04 08:04:30'),
(5, 'technicien 1', 'technicien1@gmail.com', '$2y$10$hNIdNzQ4KGxutGUbANCe1uQ9QJzK0W4FDZH7.SJ.MrqAgAdshhujm', 1, '2025-04-04 08:06:19'),
(6, 'technicien 2', 'technicien2@gmail.com', '$2y$10$PnJqQUwdVGxVLqTbykMp3eU8vN9kml.9/cFVqiTMKLpoBFUMkvljW', 1, '2025-04-04 08:07:11'),
(7, 'charles ', 'charles@gmail.com', '$2y$10$dt8R1u0u9hHllIWVKaQVC.ExyYI85.gdOK6y/W.wYdyE87HwFrHlS', 0, '2025-04-04 08:07:33'),
(8, 'test ', 'test@median15131', '$2y$10$OWakNvANVUi979y7aLJ8U.SFGNZUVGkOgzy3/qT8Ptj50Vgi6VxHG', 0, '2025-04-04 14:01:40'),
(10, 'test2', 'test2@test2.com', '$2y$10$Ud0JD/AmqElds4YA0z.WKetrOOc9AyuoRxRy2qDFBnUkCRa0oSbVK', 0, '2025-04-10 07:49:41');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_comments_ticket` (`ticket_id`),
  ADD KEY `idx_comments_user` (`user_id`);

--
-- Index pour la table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tickets_status` (`status`),
  ADD KEY `idx_tickets_priority` (`priority`),
  ADD KEY `idx_tickets_user` (`user_id`),
  ADD KEY `idx_tickets_assigned` (`assigned_to`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_users_roles` (`role`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `fk_tickets_assigned_to` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tickets_ibfk_2` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_roles` FOREIGN KEY (`role`) REFERENCES `roles` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
