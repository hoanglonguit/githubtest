
CREATE TABLE `user` (
  `increment_id` int(11) NOT NULL,
  `login` varchar(255) NOT NULL DEFAULT '0',
  `id` int(11) DEFAULT NULL,
  `avatar_url` varchar(255) DEFAULT NULL,
  `gravatar_id` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `html_url` varchar(255) DEFAULT NULL,
  `followers_url` varchar(255) DEFAULT NULL,
  `following_url` varchar(255) DEFAULT NULL,
  `gists_url` varchar(255) DEFAULT NULL,
  `starred_url` varchar(255) DEFAULT NULL,
  `subscriptions_url` varchar(255) DEFAULT NULL,
  `organizations_url` varchar(255) DEFAULT NULL,
  `repos_url` varchar(255) DEFAULT NULL,
  `events_url` varchar(255) DEFAULT NULL,
  `received_events_url` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `site_admin` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `company` varchar(255) DEFAULT NULL,
  `blog` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `hireable` varchar(255) DEFAULT NULL,
  `bio` varchar(255) DEFAULT NULL,
  `public_repos` varchar(255) DEFAULT NULL,
  `public_gists` varchar(255) DEFAULT NULL,
  `followers` varchar(255) DEFAULT NULL,
  `following` varchar(255) DEFAULT NULL,
  `created_at` varchar(255) DEFAULT NULL,
  `updated_at` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`increment_id`),
  ADD UNIQUE KEY `login` (`login`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `increment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;