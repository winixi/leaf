-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- 主机： localhost
-- 生成日期： 2021-06-12 03:26:36
-- 服务器版本： 5.7.11
-- PHP 版本： 7.4.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `leaf`
--

-- --------------------------------------------------------

--
-- 表的结构 `s_task`
--

CREATE TABLE `s_task` (
  `id` int(11) NOT NULL COMMENT '自增',
  `name` varchar(256) COLLATE utf8mb4_bin NOT NULL COMMENT '名称，可能是一个url或一个class',
  `start` float NOT NULL COMMENT '开始时间',
  `end` float NOT NULL COMMENT '结束时间',
  `duration` float NOT NULL COMMENT '耗时秒数',
  `task_type` varchar(16) COLLATE utf8mb4_bin NOT NULL COMMENT '任务类型[FUN,URL]',
  `result` text COLLATE utf8mb4_bin NOT NULL COMMENT '执行结果',
  `create_time` datetime NOT NULL COMMENT '创建时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='任务执行结果表';

-- --------------------------------------------------------

--
-- 表的结构 `s_time`
--

CREATE TABLE `s_time` (
  `id` int(11) NOT NULL COMMENT '自增',
  `time` varchar(64) COLLATE utf8mb4_bin NOT NULL COMMENT '定时设置',
  `name` varchar(256) COLLATE utf8mb4_bin NOT NULL COMMENT '名称，可能是一个url或一个class',
  `task_type` varchar(16) COLLATE utf8mb4_bin NOT NULL COMMENT '任务类型[FUN,URL]',
  `count` int(11) NOT NULL DEFAULT '0' COMMENT '已经执行次数',
  `memo` text COLLATE utf8mb4_bin COMMENT '备注',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `modify_time` datetime DEFAULT NULL COMMENT '修改时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='定时任务表';

--
-- 转储表的索引
--

--
-- 表的索引 `s_task`
--
ALTER TABLE `s_task`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_type` (`task_type`),
  ADD KEY `duration` (`duration`),
  ADD KEY `create_time` (`create_time`);

--
-- 表的索引 `s_time`
--
ALTER TABLE `s_time`
  ADD PRIMARY KEY (`id`),
  ADD KEY `create_time` (`create_time`),
  ADD KEY `task_type` (`task_type`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `s_task`
--
ALTER TABLE `s_task`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增';

--
-- 使用表AUTO_INCREMENT `s_time`
--
ALTER TABLE `s_time`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增';
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
