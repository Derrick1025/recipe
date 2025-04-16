-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 16, 2025 at 02:11 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `recipeapp`
--

-- --------------------------------------------------------

--
-- Table structure for table `recipes`
--

CREATE TABLE `recipes` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `ingredients` text NOT NULL,
  `steps` text NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `cuisine` varchar(255) NOT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recipes`
--

INSERT INTO `recipes` (`id`, `title`, `ingredients`, `steps`, `category`, `image`, `created_at`, `cuisine`, `user_id`) VALUES
(17, 'Spaghetti Carbonara', '200g spaghetti, 100g pancetta or bacon, 2 large eggs, 50g grated Parmesan cheese, salt and black pepper, 1 tbsp olive oil', '1.	Cook the spaghetti in salted boiling water until al dente.\r\n2.	Fry the pancetta in olive oil until crispy.\r\n3.	Beat the eggs and mix in Parmesan cheese.\r\n4.	Drain pasta and quickly mix with egg mixture (off heat).\r\n5.	Add pancetta, season with salt and pepper, and serve hot.', 'Italian', 'carbonara-horizontal-jumbo-v2.jpg', '2025-04-16 05:54:44', '', 4),
(18, 'Kung Pao Chicken', '250g chicken breast (diced), 1 red bell pepper (diced), 3 dried chilies, 1 tbsp soy sauce, 1 tbsp vinegar, 1 tsp sugar, 1 tbsp cornstarch, 1 tbsp peanuts, 2 cloves garlic (minced), oil for frying', '1.	Marinate chicken with soy sauce and cornstarch for 15 minutes.\r\n2.	Heat oil and stir-fry dried chilies and garlic.\r\n3.	Add chicken and cook until golden.\r\n4.	Add bell peppers, sugar, vinegar, and peanuts.\r\n5.	Stir-fry everything for 2–3 minutes and serve.', 'Chinese', 'Kung-Pao-Chicken-SQ.jpg', '2025-04-16 05:58:25', '', 4),
(19, 'Beef Tacos', '200g ground beef, Taco shells, 1 small onion (chopped), 1 tomato (diced), Lettuce (shredded), Cheese (grated), Taco seasoning, Sour cream', '1.	Cook ground beef with onions until browned.\r\n2.	Add taco seasoning and a little water, simmer for 5 mins.\r\n3.	Warm taco shells.\r\n4.	Fill with beef, lettuce, tomato, cheese, and sour cream.\r\n5.	Serve immediately.', 'Mexican', 'confetti-beef-tacos-horizontal.jpg', '2025-04-16 06:01:23', '', 6),
(20, 'Chicken Tikka Masala', '300g chicken breast (cubed), 1/2 cup yogurt, 1 tsp garam masala, 1 onion (chopped), 2 cloves garlic (minced), 1 tsp ginger, 1 cup tomato puree, 1/2 cup cream, salt, oil', '1.	Marinate chicken in yogurt, garam masala, salt — 1 hour.\r\n2.	Cook chicken in a pan until browned. Set aside.\r\n3.	Sauté onion, garlic, and ginger.\r\n4.	Add tomato puree and simmer.\r\n5.	Stir in cream, add chicken, cook until thickened.\r\n6.	Serve with rice or naan.', 'Indian', 'chicken-tikka-masala-for-the-grill-recipe-hero-2_1-cb493f49e30140efbffec162d5f2d1d7.jpg', '2025-04-16 06:03:16', '', 6),
(21, 'Teriyaki Salmon', '2 salmon fillets, 3 tbsp soy sauce, 2 tbsp mirin, 1 tbsp sugar, 1 tsp sesame oil, spring onions (optional)', '1.	Mix soy sauce, mirin, sugar, and sesame oil to make sauce.\r\n2.	Marinate salmon for 15 minutes.\r\n3.	Pan-fry salmon on medium heat, 3–4 minutes per side.\r\n4.	Add marinade to pan and simmer until sauce thickens.\r\n5.	Serve with rice and garnish with spring onions.', 'Japanese', 'IMG_5965.jpeg', '2025-04-16 06:04:27', '', 7),
(22, 'Yangzhou Fried Rice', '2 cups cooked jasmine rice (cold, leftover is best), 1 egg, 50g cooked shrimp (peeled), 50g diced char siu (Chinese BBQ pork) or ham, 1/4 cup frozen peas, 1/4 cup chopped carrots, 2 spring onions (chopped), 1 tbsp soy sauce, 1/2 tsp sesame oil, 1 tbsp vegetable oil, salt and white pepper to taste', '1.	Heat oil in a wok or large pan over medium heat.\r\n2.	Scramble the egg until lightly set, then push to one side of the wok.\r\n3.	Add carrots, peas, shrimp, and char siu. Stir-fry for 2–3 minutes.\r\n4.	Add the rice and mix well with everything.\r\n5.	Stir in soy sauce, sesame oil, salt, and white pepper.\r\n6.	Add chopped spring onions and stir-fry for another minute.\r\n7.	Serve hot!', 'Chinese', '01976__Yangzhou_Fried_Rice_16x9.jpg', '2025-04-16 06:06:32', '', 7);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `reset_token` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `reset_token`) VALUES
(4, '123', '123@gmai.com', '$2y$10$9J/YPjraxqFMNWckwYsX.Ot01IuznPplGhDehItkyGCKbpk6ucYrO', 'user', NULL),
(5, 'derrick2002', 'derricklim2002@gmail.com', '$2y$10$avwIucHdX6fuOyZFukqfr.COhyKhHJffEBnWnk.IRlzMYfe5tOfYK', 'admin', NULL),
(6, '222', '222@gmail.com', '$2y$10$e2f8g6dxo59ikSiy6f0caOCPuPqIWZdLeKC9pp6z1GqrAGRj9OsCK', 'user', NULL),
(7, '333', '333@gmail.com', '$2y$10$f4EYrwFMQOTnAW8xg96I9OKkCt5xkDWr/EuWtKuV9Ik.3FVY3HdeS', 'user', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `recipes`
--
ALTER TABLE `recipes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `recipes`
--
ALTER TABLE `recipes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
