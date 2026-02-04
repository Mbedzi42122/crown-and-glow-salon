
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `admins` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `admins` (`id`, `username`, `email`, `password`, `created_at`) VALUES
(1, 'Ako', 'Ako@gmail.com', '1', '2025-10-01 21:08:54');


CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `service` varchar(50) NOT NULL,
  `booking_date` date NOT NULL,
  `booking_time` time NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `bookings` (`id`, `user_id`, `service`, `booking_date`, `booking_time`, `created_at`) VALUES
(2, 1, 'men-haircut', '2025-09-10', '19:09:00', '2025-09-28 17:10:00'),
(5, 3, 'Women’s Haircut', '2025-10-02', '13:00:00', '2025-10-02 06:57:27'),
(6, 1, 'Men’s Haircut', '2025-10-02', '10:00:00', '2025-10-02 07:23:57'),
(7, 4, 'Men’s Haircut', '2025-10-02', '15:00:00', '2025-10-02 12:21:08'),
(8, 4, 'Men’s Haircut', '2025-10-03', '10:00:00', '2025-10-02 13:46:36');


CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `mobile` varchar(10) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `users` (`id`, `username`, `email`, `mobile`, `password`, `role`, `created_at`) VALUES
(1, 'MrBuku', 'akonahombedzi4@gmail.com', '0730243864', '$2y$10$tYw9OVEuQ67zf5581DISh.vY4HuZ55M5UxsFx5lEhJ7OmifgJCKh6', 'user', '2025-09-27 19:24:32'),
(3, 'MrBuku', 'akonahombedzi@gmail.com', '0730243864', '$2y$10$5UGf39sDE846imNul/aCJ.vTh/y1yJGTp4izSJtralLa4raiBV3CG', 'user', '2025-09-27 19:33:09'),
(4, 'A', 'A@gmail.com', '0730243864', '$2y$10$Jzqq2PTfsC.j5KS4BRTxI.4jYZTVHlwATrBFBRcv/ra12TapxL2Q6', 'user', '2025-10-02 12:19:52');

ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

ALTER TABLE `admins`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

