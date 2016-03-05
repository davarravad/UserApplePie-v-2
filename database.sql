-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 22, 2016
-- Server version: 5.5.46-0ubuntu0.14.04.2
-- PHP Version: 5.5.9-1ubuntu4.14

-- --------------------------------------------------------

--
-- Table structure for table `uap_activitylog`
--

CREATE TABLE IF NOT EXISTS `uap_activitylog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `username` varchar(30) NOT NULL,
  `action` varchar(100) NOT NULL,
  `additionalinfo` varchar(500) NOT NULL DEFAULT 'none',
  `ip` varchar(39) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `uap_attempts`
--

CREATE TABLE IF NOT EXISTS `uap_attempts` (
  `ip` varchar(39) NOT NULL,
  `count` int(11) NOT NULL,
  `expiredate` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `uap_forum_cat`
--

CREATE TABLE IF NOT EXISTS `uap_forum_cat` (
  `forum_id` int(20) NOT NULL AUTO_INCREMENT,
  `forum_name` varchar(255) NOT NULL,
  `forum_title` varchar(255) NOT NULL,
  `forum_cat` varchar(255) DEFAULT NULL,
  `forum_des` text NOT NULL,
  `forum_perm` int(20) NOT NULL DEFAULT '1',
  `forum_order_title` int(11) NOT NULL DEFAULT '1',
  `forum_order_cat` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`forum_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `uap_forum_groups`
--

CREATE TABLE IF NOT EXISTS `uap_forum_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `forum_group` varchar(50) NOT NULL,
  `groupID` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `uap_forum_images`
--

CREATE TABLE IF NOT EXISTS `uap_forum_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `imageName` varchar(255) NOT NULL,
  `imageLocation` varchar(255) NOT NULL,
  `imageSize` int(11) NOT NULL,
  `forumID` int(11) DEFAULT NULL,
  `forumTopicID` int(11) DEFAULT NULL,
  `forumTopicReplyID` int(11) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `uap_forum_posts`
--

CREATE TABLE IF NOT EXISTS `uap_forum_posts` (
  `forum_post_id` int(20) NOT NULL AUTO_INCREMENT,
  `forum_id` int(20) NOT NULL,
  `forum_user_id` int(20) NOT NULL,
  `forum_title` varchar(255) NOT NULL,
  `forum_content` text NOT NULL,
  `forum_edit_date` varchar(20) DEFAULT NULL,
  `forum_status` int(11) NOT NULL DEFAULT '1',
  `subscribe_email` varchar(10) NOT NULL DEFAULT 'true',
  `forum_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`forum_post_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `uap_forum_posts_replys`
--

CREATE TABLE IF NOT EXISTS `uap_forum_posts_replys` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `fpr_post_id` int(20) NOT NULL,
  `fpr_id` int(20) NOT NULL,
  `fpr_user_id` int(20) NOT NULL,
  `fpr_title` varchar(255) NOT NULL,
  `fpr_content` text NOT NULL,
  `subscribe_email` varchar(10) NOT NULL DEFAULT 'true',
  `fpr_edit_date` varchar(20) DEFAULT NULL,
  `fpr_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `uap_forum_settings`
--

CREATE TABLE IF NOT EXISTS `uap_forum_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_title` varchar(255) NOT NULL,
  `setting_value` varchar(255) NOT NULL,
  `setting_value_2` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `uap_groups`
--

CREATE TABLE IF NOT EXISTS `uap_groups` (
  `groupID` int(11) NOT NULL AUTO_INCREMENT,
  `groupName` varchar(150) NOT NULL,
  `groupDescription` varchar(255) NOT NULL,
  `groupFontColor` varchar(20) NOT NULL,
  `groupFontWeight` varchar(20) NOT NULL,
  PRIMARY KEY (`groupID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Table structure for table `uap_messages`
--

CREATE TABLE IF NOT EXISTS `uap_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `to_userID` int(11) NOT NULL,
  `from_userID` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `date_read` datetime DEFAULT NULL,
  `date_sent` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `to_delete` varchar(5) NOT NULL DEFAULT 'false',
  `from_delete` varchar(5) NOT NULL DEFAULT 'false',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `uap_pages`
--

CREATE TABLE IF NOT EXISTS `uap_pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_url` varchar(255) NOT NULL,
  `page_title` varchar(255) NOT NULL,
  `page_content` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `uap_sessions`
--

CREATE TABLE IF NOT EXISTS `uap_sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `username` varchar(30) NOT NULL,
  `hash` varchar(32) NOT NULL,
  `expiredate` datetime NOT NULL,
  `ip` varchar(39) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `uap_sweets`
--

CREATE TABLE IF NOT EXISTS `uap_sweets` (
  `sid` int(10) NOT NULL AUTO_INCREMENT,
  `sweet_id` int(10) DEFAULT NULL,
  `sweet_sec_id` int(10) DEFAULT NULL,
  `sweet_location` varchar(255) DEFAULT NULL,
  `sweet_user_ip` varchar(50) DEFAULT NULL,
  `sweet_server` varchar(255) DEFAULT NULL,
  `sweet_uri` varchar(255) DEFAULT NULL,
  `sweet_owner_userid` int(10) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`sid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `uap_users`
--

CREATE TABLE IF NOT EXISTS `uap_users` (
  `userID` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL,
  `password` varchar(128) NOT NULL,
  `email` varchar(100) NOT NULL,
  `firstName` varchar(100) NOT NULL,
  `gender` varchar(8) NOT NULL,
  `userImage` varchar(255) NOT NULL,
  `isactive` tinyint(1) NOT NULL DEFAULT '0',
  `activekey` varchar(15) NOT NULL DEFAULT '0',
  `resetkey` varchar(15) NOT NULL DEFAULT '0',
  `LastLogin` datetime DEFAULT NULL,
  `SignUp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`userID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `uap_users_extprofile`
--

CREATE TABLE IF NOT EXISTS `uap_users_extprofile` (
  `userID` int(11) NOT NULL,
  `website` varchar(100) NOT NULL,
  `aboutme` text NOT NULL,
  PRIMARY KEY (`userID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `uap_users_groups`
--

CREATE TABLE IF NOT EXISTS `uap_users_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL,
  `groupID` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `uap_users_online`
--

CREATE TABLE IF NOT EXISTS `uap_users_online` (
  `userId` int(11) NOT NULL DEFAULT '0',
  `lastAccess` datetime DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`userId`),
  KEY `lastAccess` (`lastAccess`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `uap_views`
--

CREATE TABLE IF NOT EXISTS `uap_views` (
  `vid` int(10) NOT NULL AUTO_INCREMENT,
  `view_id` int(10) NOT NULL,
  `view_sec_id` int(10) DEFAULT NULL,
  `view_location` varchar(255) NOT NULL,
  `view_user_ip` varchar(50) NOT NULL,
  `view_server` varchar(255) NOT NULL,
  `view_uri` varchar(255) NOT NULL,
  `view_owner_userid` int(10) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`vid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Dumping data for table `uap_groups`
--

INSERT INTO `uap_groups` (`groupID`, `groupName`, `groupDescription`, `groupFontColor`, `groupFontWeight`) VALUES
(1, 'New Member', 'Site Members that Recently Registered to the Web Site.', 'GREEN', 'BOLD'),
(2, 'Member', 'Site Members That Have Been Here a While.', 'BLUE', 'BOLD'),
(3, 'Moderator', 'Site Members That Have a Little Extra Privilege on the Site.', 'ORANGE', 'BOLD'),
(4, 'Administrator', 'Site Members That Have Full Access To The Site.', 'RED', 'BOLD');

-- --------------------------------------------------------

--
-- Dumping data for table `uap_users_groups`
--

INSERT INTO `uap_users_groups` (`userID`, `groupID`) VALUES
(1, 4);

-- --------------------------------------------------------

--
-- Dumping data for table `uap_forum_cat`
--

INSERT INTO `uap_forum_cat` (`forum_id`, `forum_name`, `forum_title`, `forum_cat`, `forum_des`, `forum_perm`, `forum_order_title`, `forum_order_cat`) VALUES
(1, 'forum', 'Forum', 'Welcome', 'Welcome to the Forum.', 1, 1, 1);

-- --------------------------------------------------------

--
-- Dumping data for table `uap_forum_settings`
--

INSERT INTO `uap_forum_settings` (`id`, `setting_title`, `setting_value`, `setting_value_2`) VALUES
(1, 'forum_on_off', 'Disabled', ''),
(2, 'forum_title', 'Forum', ''),
(3, 'forum_description', 'Welcome to the Forum', ''),
(4, 'forum_topic_limit', '20', ''),
(5, 'forum_topic_reply_limit', '10', '');
