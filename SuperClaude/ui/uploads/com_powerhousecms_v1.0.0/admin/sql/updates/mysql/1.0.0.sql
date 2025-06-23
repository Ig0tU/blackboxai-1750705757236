--
-- Initial database schema for PowerhouseCMS component
--

CREATE TABLE IF NOT EXISTS `#__powerhousecms_content` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(255) NOT NULL DEFAULT '',
    `alias` varchar(400) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
    `description` mediumtext NOT NULL,
    `state` tinyint(4) NOT NULL DEFAULT 0,
    `catid` int(11) NOT NULL DEFAULT 0,
    `created` datetime NOT NULL,
    `created_by` int(11) NOT NULL DEFAULT 0,
    `modified` datetime NOT NULL,
    `modified_by` int(11) NOT NULL DEFAULT 0,
    `checked_out` int(11) NOT NULL DEFAULT 0,
    `checked_out_time` datetime NULL DEFAULT NULL,
    `publish_up` datetime NULL DEFAULT NULL,
    `publish_down` datetime NULL DEFAULT NULL,
    `featured` tinyint(3) unsigned NOT NULL DEFAULT 0,
    `language` char(7) NOT NULL DEFAULT '',
    `ordering` int(11) NOT NULL DEFAULT 0,
    `access` int(11) NOT NULL DEFAULT 1,
    `params` text NOT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_state` (`state`),
    KEY `idx_access` (`access`),
    KEY `idx_catid` (`catid`),
    KEY `idx_language` (`language`),
    KEY `idx_checkout` (`checked_out`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;
