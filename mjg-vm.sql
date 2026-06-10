-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 18, 2026 at 09:24 AM
-- Server version: 11.7.2-MariaDB-log
-- PHP Version: 8.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `medquest_atk`
--
CREATE DATABASE IF NOT EXISTS `medquest_atk` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_ai_ci;
USE `medquest_atk`;

-- --------------------------------------------------------

--
-- Table structure for table `approvals`
--

CREATE TABLE `approvals` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `approvable_type` varchar(255) NOT NULL,
  `approvable_id` bigint(20) UNSIGNED NOT NULL,
  `flow_id` bigint(20) UNSIGNED NOT NULL,
  `current_step` int(11) NOT NULL DEFAULT 0,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `approval_flows`
--

CREATE TABLE `approval_flows` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `model_type` varchar(255) NOT NULL,
  `division_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`division_ids`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `approval_flow_steps`
--

CREATE TABLE `approval_flow_steps` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `flow_id` bigint(20) UNSIGNED NOT NULL,
  `step_name` varchar(255) NOT NULL,
  `step_number` int(11) NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `division_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `allow_resubmission` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `approval_histories`
--

CREATE TABLE `approval_histories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `approvable_type` varchar(255) NOT NULL,
  `approvable_id` bigint(20) UNSIGNED NOT NULL,
  `document_id` varchar(255) DEFAULT NULL,
  `approval_id` bigint(20) UNSIGNED NOT NULL,
  `step_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `action` enum('approved','rejected','pending','submitted','returned') NOT NULL DEFAULT 'pending',
  `rejection_reason` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `performed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `approval_step_approvals`
--

CREATE TABLE `approval_step_approvals` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `approval_id` bigint(20) UNSIGNED NOT NULL,
  `step_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `atk_budgetings`
--

CREATE TABLE `atk_budgetings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `division_id` bigint(20) UNSIGNED NOT NULL,
  `budget_amount` int(11) NOT NULL DEFAULT 0,
  `used_amount` int(11) NOT NULL DEFAULT 0,
  `remaining_amount` int(11) NOT NULL DEFAULT 0,
  `fiscal_year` year(4) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `atk_categories`
--

CREATE TABLE `atk_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `atk_division_stocks`
--

CREATE TABLE `atk_division_stocks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `division_id` bigint(20) UNSIGNED NOT NULL,
  `item_id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `current_stock` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `moving_average_cost` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `atk_division_stock_settings`
--

CREATE TABLE `atk_division_stock_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `division_id` bigint(20) UNSIGNED NOT NULL,
  `item_id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `max_limit` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `atk_floating_stocks`
--

CREATE TABLE `atk_floating_stocks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `item_id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `current_stock` int(11) NOT NULL DEFAULT 0,
  `moving_average_cost` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `atk_floating_stock_trx`
--

CREATE TABLE `atk_floating_stock_trx` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `item_id` bigint(20) UNSIGNED NOT NULL,
  `source_division_id` bigint(20) UNSIGNED DEFAULT NULL,
  `destination_division_id` bigint(20) UNSIGNED DEFAULT NULL,
  `type` enum('in','out','adjustment','transfer') NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_cost` int(11) NOT NULL DEFAULT 0,
  `total_cost` int(11) NOT NULL DEFAULT 0,
  `mac_snapshot` int(11) NOT NULL DEFAULT 0,
  `balance_snapshot` int(11) NOT NULL,
  `trx_src_type` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `trx_src_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `atk_items`
--

CREATE TABLE `atk_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `unit_of_measure` varchar(255) NOT NULL,
  `notes` text DEFAULT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `atk_item_prices`
--

CREATE TABLE `atk_item_prices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `item_id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `unit_price` int(11) NOT NULL,
  `effective_date` date NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `atk_item_price_histories`
--

CREATE TABLE `atk_item_price_histories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `item_id` bigint(20) UNSIGNED NOT NULL,
  `old_price` int(11) DEFAULT NULL,
  `new_price` int(11) NOT NULL,
  `effective_date` date NOT NULL,
  `changed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `atk_requests_from_floating_stock`
--

CREATE TABLE `atk_requests_from_floating_stock` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `request_number` varchar(255) NOT NULL,
  `requester_id` bigint(20) UNSIGNED NOT NULL,
  `division_id` bigint(20) UNSIGNED NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `atk_requests_from_floating_stock_items`
--

CREATE TABLE `atk_requests_from_floating_stock_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `request_id` bigint(20) UNSIGNED NOT NULL,
  `item_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `atk_stock_requests`
--

CREATE TABLE `atk_stock_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `request_number` varchar(255) NOT NULL,
  `request_type` varchar(255) NOT NULL DEFAULT 'addition',
  `status` enum('draft','published') NOT NULL DEFAULT 'draft',
  `requester_id` bigint(20) UNSIGNED NOT NULL,
  `division_id` bigint(20) UNSIGNED NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `atk_stock_request_items`
--

CREATE TABLE `atk_stock_request_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `request_id` bigint(20) UNSIGNED NOT NULL,
  `item_id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL,
  `received_quantity` int(11) NOT NULL DEFAULT 0,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `atk_stock_trx`
--

CREATE TABLE `atk_stock_trx` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `division_id` bigint(20) UNSIGNED NOT NULL,
  `item_id` bigint(20) UNSIGNED NOT NULL,
  `type` enum('request','usage','adjustment','transfer') NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_cost` int(11) NOT NULL DEFAULT 0,
  `total_cost` int(11) NOT NULL DEFAULT 0,
  `mac_snapshot` int(11) NOT NULL DEFAULT 0,
  `balance_snapshot` int(11) NOT NULL,
  `trx_src_type` varchar(255) NOT NULL,
  `trx_src_id` bigint(20) UNSIGNED NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `atk_stock_usages`
--

CREATE TABLE `atk_stock_usages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `request_number` varchar(255) NOT NULL,
  `request_type` varchar(255) NOT NULL DEFAULT 'reduction',
  `requester_id` bigint(20) UNSIGNED NOT NULL,
  `division_id` bigint(20) UNSIGNED NOT NULL,
  `notes` text DEFAULT NULL,
  `potential_cost` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `atk_stock_usage_items`
--

CREATE TABLE `atk_stock_usage_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `usage_id` bigint(20) UNSIGNED NOT NULL,
  `item_id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL,
  `moving_average_cost` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `atk_transfer_stocks`
--

CREATE TABLE `atk_transfer_stocks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `transfer_number` varchar(255) NOT NULL,
  `requester_id` bigint(20) UNSIGNED NOT NULL,
  `requesting_division_id` bigint(20) UNSIGNED NOT NULL,
  `source_division_id` bigint(20) UNSIGNED DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `atk_transfer_stock_items`
--

CREATE TABLE `atk_transfer_stock_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `transfer_stock_id` bigint(20) UNSIGNED NOT NULL,
  `item_id` bigint(20) UNSIGNED NOT NULL,
  `item_category_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `division_user`
--

CREATE TABLE `division_user` (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `division_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `marketing_media_categories`
--

CREATE TABLE `marketing_media_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `marketing_media_division_stocks`
--

CREATE TABLE `marketing_media_division_stocks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `division_id` bigint(20) UNSIGNED NOT NULL,
  `item_id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `current_stock` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `marketing_media_division_stock_settings`
--

CREATE TABLE `marketing_media_division_stock_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `division_id` bigint(20) UNSIGNED NOT NULL,
  `item_id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `max_limit` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `marketing_media_items`
--

CREATE TABLE `marketing_media_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `unit_of_measure` varchar(255) NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `marketing_media_stock_requests`
--

CREATE TABLE `marketing_media_stock_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `request_number` varchar(255) NOT NULL,
  `request_type` varchar(255) NOT NULL DEFAULT 'addition',
  `requester_id` bigint(20) UNSIGNED NOT NULL,
  `division_id` bigint(20) UNSIGNED NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `marketing_media_stock_request_items`
--

CREATE TABLE `marketing_media_stock_request_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `request_id` bigint(20) UNSIGNED NOT NULL,
  `item_id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `marketing_media_stock_usages`
--

CREATE TABLE `marketing_media_stock_usages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `request_number` varchar(255) NOT NULL,
  `request_type` varchar(255) NOT NULL DEFAULT 'reduction',
  `requester_id` bigint(20) UNSIGNED NOT NULL,
  `division_id` bigint(20) UNSIGNED NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `marketing_media_stock_usage_items`
--

CREATE TABLE `marketing_media_stock_usage_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `usage_id` bigint(20) UNSIGNED NOT NULL,
  `item_id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `monitoring_emails`
--

CREATE TABLE `monitoring_emails` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `from` varchar(255) DEFAULT NULL,
  `to` text DEFAULT NULL,
  `cc` text DEFAULT NULL,
  `bcc` text DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `action_type` varchar(255) DEFAULT NULL,
  `action_by_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action_at` timestamp NULL DEFAULT NULL,
  `status_code` int(11) DEFAULT NULL,
  `content_html` longtext DEFAULT NULL,
  `content_text` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` uuid NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint(20) UNSIGNED NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sent_emails`
--

CREATE TABLE `sent_emails` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `from` varchar(255) DEFAULT NULL,
  `to` text DEFAULT NULL,
  `cc` text DEFAULT NULL,
  `bcc` text DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `content_html` longtext DEFAULT NULL,
  `content_text` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `initial` varchar(5) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `has_changed_password` tinyint(1) NOT NULL DEFAULT 0,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_divisions`
--

CREATE TABLE `user_divisions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `initial` varchar(10) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `approvals`
--
ALTER TABLE `approvals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `approvals_approvable_type_approvable_id_index` (`approvable_type`,`approvable_id`),
  ADD KEY `approvals_flow_id_foreign` (`flow_id`);

--
-- Indexes for table `approval_flows`
--
ALTER TABLE `approval_flows`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `approval_flow_steps`
--
ALTER TABLE `approval_flow_steps`
  ADD PRIMARY KEY (`id`),
  ADD KEY `approval_flow_steps_flow_id_foreign` (`flow_id`),
  ADD KEY `approval_flow_steps_role_id_foreign` (`role_id`),
  ADD KEY `approval_flow_steps_division_id_foreign` (`division_id`),
  ADD KEY `approval_flow_steps_user_id_foreign` (`user_id`);

--
-- Indexes for table `approval_histories`
--
ALTER TABLE `approval_histories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `approval_histories_approvable_type_approvable_id_index` (`approvable_type`,`approvable_id`),
  ADD KEY `approval_histories_step_id_foreign` (`step_id`),
  ADD KEY `approval_histories_approvable_type_index` (`approvable_type`),
  ADD KEY `approval_histories_document_id_index` (`document_id`),
  ADD KEY `approval_histories_approval_id_index` (`approval_id`),
  ADD KEY `approval_histories_user_id_index` (`user_id`),
  ADD KEY `approval_histories_action_index` (`action`),
  ADD KEY `approval_histories_performed_at_index` (`performed_at`);

--
-- Indexes for table `approval_step_approvals`
--
ALTER TABLE `approval_step_approvals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `approval_step_approvals_approval_id_foreign` (`approval_id`),
  ADD KEY `approval_step_approvals_step_id_foreign` (`step_id`),
  ADD KEY `approval_step_approvals_user_id_foreign` (`user_id`);

--
-- Indexes for table `atk_budgetings`
--
ALTER TABLE `atk_budgetings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `atk_budgetings_division_id_fiscal_year_unique` (`division_id`,`fiscal_year`);

--
-- Indexes for table `atk_categories`
--
ALTER TABLE `atk_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `atk_division_stocks`
--
ALTER TABLE `atk_division_stocks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `atk_division_stocks_division_id_foreign` (`division_id`),
  ADD KEY `atk_division_stocks_item_id_foreign` (`item_id`),
  ADD KEY `atk_division_stocks_category_id_foreign` (`category_id`);

--
-- Indexes for table `atk_division_stock_settings`
--
ALTER TABLE `atk_division_stock_settings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `atk_division_stock_settings_division_id_foreign` (`division_id`),
  ADD KEY `atk_division_stock_settings_item_id_foreign` (`item_id`),
  ADD KEY `atk_division_stock_settings_category_id_foreign` (`category_id`);

--
-- Indexes for table `atk_floating_stocks`
--
ALTER TABLE `atk_floating_stocks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `atk_floating_stocks_item_id_unique` (`item_id`),
  ADD KEY `atk_floating_stocks_category_id_foreign` (`category_id`);

--
-- Indexes for table `atk_floating_stock_trx`
--
ALTER TABLE `atk_floating_stock_trx`
  ADD PRIMARY KEY (`id`),
  ADD KEY `atk_floating_stock_trx_item_id_foreign` (`item_id`),
  ADD KEY `atk_floating_stock_trx_trx_src_type_trx_src_id_index` (`trx_src_type`,`trx_src_id`),
  ADD KEY `atk_floating_stock_trx_source_division_id_foreign` (`source_division_id`),
  ADD KEY `atk_floating_stock_trx_destination_division_id_foreign` (`destination_division_id`);

--
-- Indexes for table `atk_items`
--
ALTER TABLE `atk_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `atk_items_category_id_foreign` (`category_id`);

--
-- Indexes for table `atk_item_prices`
--
ALTER TABLE `atk_item_prices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `atk_item_prices_item_id_foreign` (`item_id`),
  ADD KEY `atk_item_prices_category_id_foreign` (`category_id`);

--
-- Indexes for table `atk_item_price_histories`
--
ALTER TABLE `atk_item_price_histories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `atk_item_price_histories_item_id_foreign` (`item_id`),
  ADD KEY `atk_item_price_histories_changed_by_foreign` (`changed_by`);

--
-- Indexes for table `atk_requests_from_floating_stock`
--
ALTER TABLE `atk_requests_from_floating_stock`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `atk_requests_from_floating_stock_request_number_unique` (`request_number`),
  ADD KEY `atk_requests_from_floating_stock_requester_id_foreign` (`requester_id`),
  ADD KEY `atk_requests_from_floating_stock_division_id_foreign` (`division_id`);

--
-- Indexes for table `atk_requests_from_floating_stock_items`
--
ALTER TABLE `atk_requests_from_floating_stock_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `atk_requests_from_floating_stock_items_request_id_foreign` (`request_id`),
  ADD KEY `atk_requests_from_floating_stock_items_item_id_foreign` (`item_id`);

--
-- Indexes for table `atk_stock_requests`
--
ALTER TABLE `atk_stock_requests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `atk_stock_requests_request_number_unique` (`request_number`),
  ADD KEY `atk_stock_requests_requester_id_foreign` (`requester_id`),
  ADD KEY `atk_stock_requests_division_id_foreign` (`division_id`);

--
-- Indexes for table `atk_stock_request_items`
--
ALTER TABLE `atk_stock_request_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `atk_stock_request_items_request_id_foreign` (`request_id`),
  ADD KEY `atk_stock_request_items_item_id_foreign` (`item_id`),
  ADD KEY `atk_stock_request_items_category_id_foreign` (`category_id`);

--
-- Indexes for table `atk_stock_trx`
--
ALTER TABLE `atk_stock_trx`
  ADD PRIMARY KEY (`id`),
  ADD KEY `atk_stock_trx_division_id_foreign` (`division_id`),
  ADD KEY `atk_stock_trx_item_id_foreign` (`item_id`),
  ADD KEY `atk_stock_trx_trx_src_type_trx_src_id_index` (`trx_src_type`,`trx_src_id`);

--
-- Indexes for table `atk_stock_usages`
--
ALTER TABLE `atk_stock_usages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `atk_stock_usages_request_number_unique` (`request_number`),
  ADD KEY `atk_stock_usages_requester_id_foreign` (`requester_id`),
  ADD KEY `atk_stock_usages_division_id_foreign` (`division_id`);

--
-- Indexes for table `atk_stock_usage_items`
--
ALTER TABLE `atk_stock_usage_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `atk_stock_usage_items_usage_id_foreign` (`usage_id`),
  ADD KEY `atk_stock_usage_items_item_id_foreign` (`item_id`);

--
-- Indexes for table `atk_transfer_stocks`
--
ALTER TABLE `atk_transfer_stocks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `atk_transfer_stocks_transfer_number_unique` (`transfer_number`),
  ADD KEY `atk_transfer_stocks_requester_id_foreign` (`requester_id`),
  ADD KEY `atk_transfer_stocks_requesting_division_id_foreign` (`requesting_division_id`),
  ADD KEY `atk_transfer_stocks_source_division_id_foreign` (`source_division_id`);

--
-- Indexes for table `atk_transfer_stock_items`
--
ALTER TABLE `atk_transfer_stock_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `atk_transfer_stock_items_transfer_stock_id_foreign` (`transfer_stock_id`),
  ADD KEY `atk_transfer_stock_items_item_id_foreign` (`item_id`),
  ADD KEY `atk_transfer_stock_items_item_category_id_foreign` (`item_category_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `division_user`
--
ALTER TABLE `division_user`
  ADD PRIMARY KEY (`user_id`,`division_id`),
  ADD KEY `division_user_division_id_foreign` (`division_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `marketing_media_categories`
--
ALTER TABLE `marketing_media_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `marketing_media_division_stocks`
--
ALTER TABLE `marketing_media_division_stocks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `marketing_media_division_stocks_division_id_foreign` (`division_id`),
  ADD KEY `marketing_media_division_stocks_item_id_foreign` (`item_id`),
  ADD KEY `marketing_media_division_stocks_category_id_foreign` (`category_id`);

--
-- Indexes for table `marketing_media_division_stock_settings`
--
ALTER TABLE `marketing_media_division_stock_settings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `marketing_media_division_stock_settings_division_id_foreign` (`division_id`),
  ADD KEY `marketing_media_division_stock_settings_item_id_foreign` (`item_id`),
  ADD KEY `marketing_media_division_stock_settings_category_id_foreign` (`category_id`);

--
-- Indexes for table `marketing_media_items`
--
ALTER TABLE `marketing_media_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `marketing_media_items_category_id_foreign` (`category_id`);

--
-- Indexes for table `marketing_media_stock_requests`
--
ALTER TABLE `marketing_media_stock_requests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `marketing_media_stock_requests_request_number_unique` (`request_number`),
  ADD KEY `marketing_media_stock_requests_requester_id_foreign` (`requester_id`),
  ADD KEY `marketing_media_stock_requests_division_id_foreign` (`division_id`);

--
-- Indexes for table `marketing_media_stock_request_items`
--
ALTER TABLE `marketing_media_stock_request_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `marketing_media_stock_request_items_request_id_foreign` (`request_id`),
  ADD KEY `marketing_media_stock_request_items_item_id_foreign` (`item_id`),
  ADD KEY `marketing_media_stock_request_items_category_id_foreign` (`category_id`);

--
-- Indexes for table `marketing_media_stock_usages`
--
ALTER TABLE `marketing_media_stock_usages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `marketing_media_stock_usages_request_number_unique` (`request_number`),
  ADD KEY `marketing_media_stock_usages_requester_id_foreign` (`requester_id`),
  ADD KEY `marketing_media_stock_usages_division_id_foreign` (`division_id`);

--
-- Indexes for table `marketing_media_stock_usage_items`
--
ALTER TABLE `marketing_media_stock_usage_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `marketing_media_stock_usage_items_usage_id_foreign` (`usage_id`),
  ADD KEY `marketing_media_stock_usage_items_item_id_foreign` (`item_id`),
  ADD KEY `marketing_media_stock_usage_items_category_id_foreign` (`category_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `monitoring_emails`
--
ALTER TABLE `monitoring_emails`
  ADD PRIMARY KEY (`id`),
  ADD KEY `monitoring_emails_action_by_id_foreign` (`action_by_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `sent_emails`
--
ALTER TABLE `sent_emails`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_initial_unique` (`initial`);

--
-- Indexes for table `user_divisions`
--
ALTER TABLE `user_divisions`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `approvals`
--
ALTER TABLE `approvals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `approval_flows`
--
ALTER TABLE `approval_flows`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `approval_flow_steps`
--
ALTER TABLE `approval_flow_steps`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `approval_histories`
--
ALTER TABLE `approval_histories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `approval_step_approvals`
--
ALTER TABLE `approval_step_approvals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `atk_budgetings`
--
ALTER TABLE `atk_budgetings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `atk_categories`
--
ALTER TABLE `atk_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `atk_division_stocks`
--
ALTER TABLE `atk_division_stocks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `atk_division_stock_settings`
--
ALTER TABLE `atk_division_stock_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `atk_floating_stocks`
--
ALTER TABLE `atk_floating_stocks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `atk_floating_stock_trx`
--
ALTER TABLE `atk_floating_stock_trx`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `atk_items`
--
ALTER TABLE `atk_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `atk_item_prices`
--
ALTER TABLE `atk_item_prices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `atk_item_price_histories`
--
ALTER TABLE `atk_item_price_histories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `atk_requests_from_floating_stock`
--
ALTER TABLE `atk_requests_from_floating_stock`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `atk_requests_from_floating_stock_items`
--
ALTER TABLE `atk_requests_from_floating_stock_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `atk_stock_requests`
--
ALTER TABLE `atk_stock_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `atk_stock_request_items`
--
ALTER TABLE `atk_stock_request_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `atk_stock_trx`
--
ALTER TABLE `atk_stock_trx`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `atk_stock_usages`
--
ALTER TABLE `atk_stock_usages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `atk_stock_usage_items`
--
ALTER TABLE `atk_stock_usage_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `atk_transfer_stocks`
--
ALTER TABLE `atk_transfer_stocks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `atk_transfer_stock_items`
--
ALTER TABLE `atk_transfer_stock_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `marketing_media_categories`
--
ALTER TABLE `marketing_media_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `marketing_media_division_stocks`
--
ALTER TABLE `marketing_media_division_stocks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `marketing_media_division_stock_settings`
--
ALTER TABLE `marketing_media_division_stock_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `marketing_media_items`
--
ALTER TABLE `marketing_media_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `marketing_media_stock_requests`
--
ALTER TABLE `marketing_media_stock_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `marketing_media_stock_request_items`
--
ALTER TABLE `marketing_media_stock_request_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `marketing_media_stock_usages`
--
ALTER TABLE `marketing_media_stock_usages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `marketing_media_stock_usage_items`
--
ALTER TABLE `marketing_media_stock_usage_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `monitoring_emails`
--
ALTER TABLE `monitoring_emails`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sent_emails`
--
ALTER TABLE `sent_emails`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_divisions`
--
ALTER TABLE `user_divisions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `approvals`
--
ALTER TABLE `approvals`
  ADD CONSTRAINT `approvals_flow_id_foreign` FOREIGN KEY (`flow_id`) REFERENCES `approval_flows` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `approval_flow_steps`
--
ALTER TABLE `approval_flow_steps`
  ADD CONSTRAINT `approval_flow_steps_division_id_foreign` FOREIGN KEY (`division_id`) REFERENCES `user_divisions` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `approval_flow_steps_flow_id_foreign` FOREIGN KEY (`flow_id`) REFERENCES `approval_flows` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `approval_flow_steps_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `approval_flow_steps_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `approval_histories`
--
ALTER TABLE `approval_histories`
  ADD CONSTRAINT `approval_histories_approval_id_foreign` FOREIGN KEY (`approval_id`) REFERENCES `approvals` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `approval_histories_step_id_foreign` FOREIGN KEY (`step_id`) REFERENCES `approval_flow_steps` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `approval_histories_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `approval_step_approvals`
--
ALTER TABLE `approval_step_approvals`
  ADD CONSTRAINT `approval_step_approvals_approval_id_foreign` FOREIGN KEY (`approval_id`) REFERENCES `approvals` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `approval_step_approvals_step_id_foreign` FOREIGN KEY (`step_id`) REFERENCES `approval_flow_steps` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `approval_step_approvals_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `atk_budgetings`
--
ALTER TABLE `atk_budgetings`
  ADD CONSTRAINT `atk_budgetings_division_id_foreign` FOREIGN KEY (`division_id`) REFERENCES `user_divisions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `atk_division_stocks`
--
ALTER TABLE `atk_division_stocks`
  ADD CONSTRAINT `atk_division_stocks_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `atk_categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `atk_division_stocks_division_id_foreign` FOREIGN KEY (`division_id`) REFERENCES `user_divisions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `atk_division_stocks_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `atk_items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `atk_division_stock_settings`
--
ALTER TABLE `atk_division_stock_settings`
  ADD CONSTRAINT `atk_division_stock_settings_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `atk_categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `atk_division_stock_settings_division_id_foreign` FOREIGN KEY (`division_id`) REFERENCES `user_divisions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `atk_division_stock_settings_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `atk_items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `atk_floating_stocks`
--
ALTER TABLE `atk_floating_stocks`
  ADD CONSTRAINT `atk_floating_stocks_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `atk_categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `atk_floating_stocks_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `atk_items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `atk_floating_stock_trx`
--
ALTER TABLE `atk_floating_stock_trx`
  ADD CONSTRAINT `atk_floating_stock_trx_destination_division_id_foreign` FOREIGN KEY (`destination_division_id`) REFERENCES `user_divisions` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `atk_floating_stock_trx_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `atk_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `atk_floating_stock_trx_source_division_id_foreign` FOREIGN KEY (`source_division_id`) REFERENCES `user_divisions` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `atk_items`
--
ALTER TABLE `atk_items`
  ADD CONSTRAINT `atk_items_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `atk_categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `atk_item_prices`
--
ALTER TABLE `atk_item_prices`
  ADD CONSTRAINT `atk_item_prices_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `atk_categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `atk_item_prices_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `atk_items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `atk_item_price_histories`
--
ALTER TABLE `atk_item_price_histories`
  ADD CONSTRAINT `atk_item_price_histories_changed_by_foreign` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `atk_item_price_histories_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `atk_items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `atk_requests_from_floating_stock`
--
ALTER TABLE `atk_requests_from_floating_stock`
  ADD CONSTRAINT `atk_requests_from_floating_stock_division_id_foreign` FOREIGN KEY (`division_id`) REFERENCES `user_divisions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `atk_requests_from_floating_stock_requester_id_foreign` FOREIGN KEY (`requester_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `atk_requests_from_floating_stock_items`
--
ALTER TABLE `atk_requests_from_floating_stock_items`
  ADD CONSTRAINT `atk_requests_from_floating_stock_items_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `atk_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `atk_requests_from_floating_stock_items_request_id_foreign` FOREIGN KEY (`request_id`) REFERENCES `atk_requests_from_floating_stock` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `atk_stock_requests`
--
ALTER TABLE `atk_stock_requests`
  ADD CONSTRAINT `atk_stock_requests_division_id_foreign` FOREIGN KEY (`division_id`) REFERENCES `user_divisions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `atk_stock_requests_requester_id_foreign` FOREIGN KEY (`requester_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `atk_stock_request_items`
--
ALTER TABLE `atk_stock_request_items`
  ADD CONSTRAINT `atk_stock_request_items_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `atk_categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `atk_stock_request_items_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `atk_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `atk_stock_request_items_request_id_foreign` FOREIGN KEY (`request_id`) REFERENCES `atk_stock_requests` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `atk_stock_trx`
--
ALTER TABLE `atk_stock_trx`
  ADD CONSTRAINT `atk_stock_trx_division_id_foreign` FOREIGN KEY (`division_id`) REFERENCES `user_divisions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `atk_stock_trx_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `atk_items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `atk_stock_usages`
--
ALTER TABLE `atk_stock_usages`
  ADD CONSTRAINT `atk_stock_usages_division_id_foreign` FOREIGN KEY (`division_id`) REFERENCES `user_divisions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `atk_stock_usages_requester_id_foreign` FOREIGN KEY (`requester_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `atk_stock_usage_items`
--
ALTER TABLE `atk_stock_usage_items`
  ADD CONSTRAINT `atk_stock_usage_items_atk_category_id_foreign` FOREIGN KEY (`id`) REFERENCES `atk_categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `atk_stock_usage_items_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `atk_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `atk_stock_usage_items_usage_id_foreign` FOREIGN KEY (`usage_id`) REFERENCES `atk_stock_usages` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `atk_transfer_stocks`
--
ALTER TABLE `atk_transfer_stocks`
  ADD CONSTRAINT `atk_transfer_stocks_requester_id_foreign` FOREIGN KEY (`requester_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `atk_transfer_stocks_requesting_division_id_foreign` FOREIGN KEY (`requesting_division_id`) REFERENCES `user_divisions` (`id`),
  ADD CONSTRAINT `atk_transfer_stocks_source_division_id_foreign` FOREIGN KEY (`source_division_id`) REFERENCES `user_divisions` (`id`);

--
-- Constraints for table `atk_transfer_stock_items`
--
ALTER TABLE `atk_transfer_stock_items`
  ADD CONSTRAINT `atk_transfer_stock_items_item_category_id_foreign` FOREIGN KEY (`item_category_id`) REFERENCES `atk_categories` (`id`),
  ADD CONSTRAINT `atk_transfer_stock_items_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `atk_items` (`id`),
  ADD CONSTRAINT `atk_transfer_stock_items_transfer_stock_id_foreign` FOREIGN KEY (`transfer_stock_id`) REFERENCES `atk_transfer_stocks` (`id`);

--
-- Constraints for table `division_user`
--
ALTER TABLE `division_user`
  ADD CONSTRAINT `division_user_division_id_foreign` FOREIGN KEY (`division_id`) REFERENCES `user_divisions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `division_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `marketing_media_division_stocks`
--
ALTER TABLE `marketing_media_division_stocks`
  ADD CONSTRAINT `marketing_media_division_stocks_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `marketing_media_categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `marketing_media_division_stocks_division_id_foreign` FOREIGN KEY (`division_id`) REFERENCES `user_divisions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `marketing_media_division_stocks_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `marketing_media_items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `marketing_media_division_stock_settings`
--
ALTER TABLE `marketing_media_division_stock_settings`
  ADD CONSTRAINT `marketing_media_division_stock_settings_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `marketing_media_categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `marketing_media_division_stock_settings_division_id_foreign` FOREIGN KEY (`division_id`) REFERENCES `user_divisions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `marketing_media_division_stock_settings_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `marketing_media_items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `marketing_media_items`
--
ALTER TABLE `marketing_media_items`
  ADD CONSTRAINT `marketing_media_items_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `marketing_media_categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `marketing_media_stock_requests`
--
ALTER TABLE `marketing_media_stock_requests`
  ADD CONSTRAINT `marketing_media_stock_requests_division_id_foreign` FOREIGN KEY (`division_id`) REFERENCES `user_divisions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `marketing_media_stock_requests_requester_id_foreign` FOREIGN KEY (`requester_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `marketing_media_stock_request_items`
--
ALTER TABLE `marketing_media_stock_request_items`
  ADD CONSTRAINT `marketing_media_stock_request_items_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `marketing_media_categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `marketing_media_stock_request_items_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `marketing_media_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `marketing_media_stock_request_items_request_id_foreign` FOREIGN KEY (`request_id`) REFERENCES `marketing_media_stock_requests` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `marketing_media_stock_usages`
--
ALTER TABLE `marketing_media_stock_usages`
  ADD CONSTRAINT `marketing_media_stock_usages_division_id_foreign` FOREIGN KEY (`division_id`) REFERENCES `user_divisions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `marketing_media_stock_usages_requester_id_foreign` FOREIGN KEY (`requester_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `marketing_media_stock_usage_items`
--
ALTER TABLE `marketing_media_stock_usage_items`
  ADD CONSTRAINT `marketing_media_stock_usage_items_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `marketing_media_categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `marketing_media_stock_usage_items_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `marketing_media_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `marketing_media_stock_usage_items_usage_id_foreign` FOREIGN KEY (`usage_id`) REFERENCES `marketing_media_stock_usages` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `monitoring_emails`
--
ALTER TABLE `monitoring_emails`
  ADD CONSTRAINT `monitoring_emails_action_by_id_foreign` FOREIGN KEY (`action_by_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
--
-- Database: `medquest_calibration`
--
CREATE DATABASE IF NOT EXISTS `medquest_calibration` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_ai_ci;
USE `medquest_calibration`;

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `phone_number` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `devices`
--

CREATE TABLE `devices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `deviceId` uuid NOT NULL,
  `device_name_id` bigint(20) UNSIGNED DEFAULT NULL,
  `device_number` varchar(255) DEFAULT NULL,
  `serial_number` varchar(255) DEFAULT NULL,
  `brand_id` bigint(20) UNSIGNED DEFAULT NULL,
  `type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `location_id` bigint(20) UNSIGNED DEFAULT NULL,
  `procurement_year` year(4) DEFAULT NULL,
  `pic_id` bigint(20) UNSIGNED DEFAULT NULL,
  `customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `calibration_date` date DEFAULT NULL,
  `next_calibration_date` date DEFAULT NULL,
  `cert_number` varchar(255) DEFAULT NULL,
  `barcode` varchar(255) DEFAULT NULL,
  `result` enum('Fit For Use','Not Fit For Use') DEFAULT NULL,
  `status` enum('Available','Unavailable') NOT NULL DEFAULT 'Available',
  `notes` text DEFAULT NULL,
  `admin_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `device_names`
--

CREATE TABLE `device_names` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventories`
--

CREATE TABLE `inventories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `inventory_number` varchar(255) NOT NULL,
  `serial_number` varchar(50) DEFAULT NULL,
  `procurement_year` year(4) NOT NULL,
  `device_name_id` bigint(20) UNSIGNED NOT NULL,
  `brand_id` bigint(20) UNSIGNED NOT NULL,
  `type_id` bigint(20) UNSIGNED NOT NULL,
  `location_id` bigint(20) UNSIGNED NOT NULL,
  `last_calibration_date` date NOT NULL,
  `next_calibration_date` date DEFAULT NULL,
  `status` enum('Available','Unavailable') NOT NULL DEFAULT 'Available',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE `locations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `locationId` uuid NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `logbooks`
--

CREATE TABLE `logbooks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `log_number` varchar(255) NOT NULL,
  `inventory_id` bigint(20) UNSIGNED NOT NULL,
  `accessories` text DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `location_id` bigint(20) UNSIGNED NOT NULL,
  `pic_id` bigint(20) UNSIGNED NOT NULL,
  `status` enum('Available','Borrowed','Returned') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `types`
--

CREATE TABLE `types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `brand_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `userId` uuid NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `initial` varchar(5) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `brands_slug_unique` (`slug`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customers_slug_unique` (`slug`);

--
-- Indexes for table `devices`
--
ALTER TABLE `devices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `devices_deviceid_unique` (`deviceId`),
  ADD UNIQUE KEY `devices_device_number_unique` (`device_number`),
  ADD KEY `devices_device_name_id_foreign` (`device_name_id`),
  ADD KEY `devices_brand_id_foreign` (`brand_id`),
  ADD KEY `devices_type_id_foreign` (`type_id`),
  ADD KEY `devices_location_id_foreign` (`location_id`),
  ADD KEY `devices_pic_id_foreign` (`pic_id`),
  ADD KEY `devices_customer_id_foreign` (`customer_id`),
  ADD KEY `devices_admin_id_foreign` (`admin_id`);

--
-- Indexes for table `device_names`
--
ALTER TABLE `device_names`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `device_names_slug_unique` (`slug`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `inventories`
--
ALTER TABLE `inventories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `inventories_inventory_number_unique` (`inventory_number`),
  ADD KEY `inventories_device_name_id_foreign` (`device_name_id`),
  ADD KEY `inventories_brand_id_foreign` (`brand_id`),
  ADD KEY `inventories_type_id_foreign` (`type_id`),
  ADD KEY `inventories_location_id_foreign` (`location_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `locations_locationid_unique` (`locationId`);

--
-- Indexes for table `logbooks`
--
ALTER TABLE `logbooks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `logbooks_log_number_unique` (`log_number`),
  ADD KEY `logbooks_inventory_id_foreign` (`inventory_id`),
  ADD KEY `logbooks_location_id_foreign` (`location_id`),
  ADD KEY `logbooks_pic_id_foreign` (`pic_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `types`
--
ALTER TABLE `types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `types_slug_unique` (`slug`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_userid_unique` (`userId`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_initial_unique` (`initial`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `devices`
--
ALTER TABLE `devices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `device_names`
--
ALTER TABLE `device_names`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventories`
--
ALTER TABLE `inventories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `logbooks`
--
ALTER TABLE `logbooks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `types`
--
ALTER TABLE `types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `devices`
--
ALTER TABLE `devices`
  ADD CONSTRAINT `devices_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `devices_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`),
  ADD CONSTRAINT `devices_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  ADD CONSTRAINT `devices_device_name_id_foreign` FOREIGN KEY (`device_name_id`) REFERENCES `device_names` (`id`),
  ADD CONSTRAINT `devices_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`),
  ADD CONSTRAINT `devices_pic_id_foreign` FOREIGN KEY (`pic_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `devices_type_id_foreign` FOREIGN KEY (`type_id`) REFERENCES `types` (`id`);

--
-- Constraints for table `inventories`
--
ALTER TABLE `inventories`
  ADD CONSTRAINT `inventories_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`),
  ADD CONSTRAINT `inventories_device_name_id_foreign` FOREIGN KEY (`device_name_id`) REFERENCES `device_names` (`id`),
  ADD CONSTRAINT `inventories_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`),
  ADD CONSTRAINT `inventories_type_id_foreign` FOREIGN KEY (`type_id`) REFERENCES `types` (`id`);

--
-- Constraints for table `logbooks`
--
ALTER TABLE `logbooks`
  ADD CONSTRAINT `logbooks_inventory_id_foreign` FOREIGN KEY (`inventory_id`) REFERENCES `inventories` (`id`),
  ADD CONSTRAINT `logbooks_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`),
  ADD CONSTRAINT `logbooks_pic_id_foreign` FOREIGN KEY (`pic_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
--
-- Database: `medquest_group_asset`
--
CREATE DATABASE IF NOT EXISTS `medquest_group_asset` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_ai_ci;
USE `medquest_group_asset`;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `companyId` uuid NOT NULL,
  `company_logo` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `initials` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employeeId` uuid NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `division_id` bigint(20) UNSIGNED DEFAULT NULL,
  `position_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `initial` varchar(3) DEFAULT NULL,
  `employee_number` varchar(10) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_departments`
--

CREATE TABLE `employee_departments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `departmentId` uuid NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_divisions`
--

CREATE TABLE `employee_divisions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `divisionId` uuid NOT NULL,
  `abbreviation` varchar(25) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_positions`
--

CREATE TABLE `employee_positions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `positionId` uuid NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `it_assets`
--

CREATE TABLE `it_assets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `assetId` uuid NOT NULL,
  `asset_name` varchar(255) NOT NULL,
  `asset_code` varchar(255) NOT NULL,
  `asset_year_bought` year(4) NOT NULL,
  `asset_brand` varchar(255) NOT NULL,
  `asset_model` varchar(255) NOT NULL,
  `asset_serial_number` varchar(255) NOT NULL,
  `asset_category_id` bigint(20) UNSIGNED NOT NULL,
  `asset_condition` enum('New','First Hand','Used','Defect','Disposed') NOT NULL,
  `asset_payment_status` enum('Paid','Pending Paid') NOT NULL DEFAULT 'Pending Paid',
  `asset_price` int(11) DEFAULT NULL,
  `asset_notes` text DEFAULT NULL,
  `asset_remarks` text DEFAULT NULL,
  `asset_location_id` bigint(20) UNSIGNED DEFAULT NULL,
  `asset_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `pic_id` bigint(20) UNSIGNED NOT NULL,
  `barcode` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `it_asset_categories`
--

CREATE TABLE `it_asset_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `it_asset_locations`
--

CREATE TABLE `it_asset_locations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `locationId` uuid NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `it_asset_maintenances`
--

CREATE TABLE `it_asset_maintenances` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `maintenanceId` uuid NOT NULL,
  `maintenance_date` date NOT NULL,
  `asset_id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED DEFAULT NULL,
  `division_id` bigint(20) UNSIGNED DEFAULT NULL,
  `maintenance_condition` varchar(5000) NOT NULL,
  `maintenance_repair` varchar(5000) NOT NULL,
  `maintenance_start_time` time NOT NULL,
  `maintenance_end_time` time DEFAULT NULL,
  `pic_id` bigint(20) UNSIGNED NOT NULL,
  `reviewer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `it_asset_usage_histories`
--

CREATE TABLE `it_asset_usage_histories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `usageId` uuid NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `asset_id` bigint(20) UNSIGNED NOT NULL,
  `asset_location_id` bigint(20) UNSIGNED DEFAULT NULL,
  `employee_id` bigint(20) UNSIGNED DEFAULT NULL,
  `department_id` bigint(20) UNSIGNED DEFAULT NULL,
  `division_id` bigint(20) UNSIGNED DEFAULT NULL,
  `position_id` bigint(20) UNSIGNED DEFAULT NULL,
  `usage_start_date` date NOT NULL,
  `usage_end_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `resource_permissions`
--

CREATE TABLE `resource_permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `permissionId` uuid NOT NULL,
  `resource_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `resource_permission_role`
--

CREATE TABLE `resource_permission_role` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `resource_permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `userId` uuid NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `companies_companyid_unique` (`companyId`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employees_employeeid_unique` (`employeeId`),
  ADD UNIQUE KEY `employees_initial_unique` (`initial`),
  ADD UNIQUE KEY `employees_employee_number_unique` (`employee_number`),
  ADD KEY `employees_user_id_foreign` (`user_id`),
  ADD KEY `employees_division_id_foreign` (`division_id`),
  ADD KEY `employees_position_id_foreign` (`position_id`);

--
-- Indexes for table `employee_departments`
--
ALTER TABLE `employee_departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_departments_departmentid_unique` (`departmentId`),
  ADD UNIQUE KEY `employee_departments_name_unique` (`name`);

--
-- Indexes for table `employee_divisions`
--
ALTER TABLE `employee_divisions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_divisions_divisionid_unique` (`divisionId`),
  ADD UNIQUE KEY `employee_divisions_abbreviation_unique` (`abbreviation`);

--
-- Indexes for table `employee_positions`
--
ALTER TABLE `employee_positions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_positions_positionid_unique` (`positionId`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `it_assets`
--
ALTER TABLE `it_assets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `it_assets_assetid_unique` (`assetId`),
  ADD UNIQUE KEY `it_assets_asset_code_unique` (`asset_code`),
  ADD KEY `it_assets_asset_category_id_foreign` (`asset_category_id`),
  ADD KEY `it_assets_asset_location_id_foreign` (`asset_location_id`),
  ADD KEY `it_assets_asset_user_id_foreign` (`asset_user_id`),
  ADD KEY `it_assets_pic_id_foreign` (`pic_id`),
  ADD KEY `it_assets_company_id_foreign` (`company_id`);

--
-- Indexes for table `it_asset_categories`
--
ALTER TABLE `it_asset_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `it_asset_categories_name_unique` (`name`);

--
-- Indexes for table `it_asset_locations`
--
ALTER TABLE `it_asset_locations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `it_asset_locations_locationid_unique` (`locationId`);

--
-- Indexes for table `it_asset_maintenances`
--
ALTER TABLE `it_asset_maintenances`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `it_asset_maintenances_maintenanceid_unique` (`maintenanceId`),
  ADD KEY `it_asset_maintenances_asset_id_foreign` (`asset_id`),
  ADD KEY `it_asset_maintenances_employee_id_foreign` (`employee_id`),
  ADD KEY `it_asset_maintenances_division_id_foreign` (`division_id`),
  ADD KEY `it_asset_maintenances_pic_id_foreign` (`pic_id`),
  ADD KEY `it_asset_maintenances_reviewer_id_foreign` (`reviewer_id`);

--
-- Indexes for table `it_asset_usage_histories`
--
ALTER TABLE `it_asset_usage_histories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `it_asset_usage_histories_usageid_unique` (`usageId`),
  ADD KEY `it_asset_usage_histories_asset_id_foreign` (`asset_id`),
  ADD KEY `it_asset_usage_histories_asset_location_id_foreign` (`asset_location_id`),
  ADD KEY `it_asset_usage_histories_employee_id_foreign` (`employee_id`),
  ADD KEY `it_asset_usage_histories_department_id_foreign` (`department_id`),
  ADD KEY `it_asset_usage_histories_division_id_foreign` (`division_id`),
  ADD KEY `it_asset_usage_histories_position_id_foreign` (`position_id`),
  ADD KEY `it_asset_usage_histories_company_id_foreign` (`company_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `resource_permissions`
--
ALTER TABLE `resource_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `resource_permissions_permissionid_unique` (`permissionId`);

--
-- Indexes for table `resource_permission_role`
--
ALTER TABLE `resource_permission_role`
  ADD PRIMARY KEY (`id`),
  ADD KEY `resource_permission_role_resource_permission_id_foreign` (`resource_permission_id`),
  ADD KEY `resource_permission_role_role_id_foreign` (`role_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_userid_unique` (`userId`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_departments`
--
ALTER TABLE `employee_departments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_divisions`
--
ALTER TABLE `employee_divisions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_positions`
--
ALTER TABLE `employee_positions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `it_assets`
--
ALTER TABLE `it_assets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `it_asset_categories`
--
ALTER TABLE `it_asset_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `it_asset_locations`
--
ALTER TABLE `it_asset_locations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `it_asset_maintenances`
--
ALTER TABLE `it_asset_maintenances`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `it_asset_usage_histories`
--
ALTER TABLE `it_asset_usage_histories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `resource_permissions`
--
ALTER TABLE `resource_permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `resource_permission_role`
--
ALTER TABLE `resource_permission_role`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `employees_division_id_foreign` FOREIGN KEY (`division_id`) REFERENCES `employee_divisions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `employees_position_id_foreign` FOREIGN KEY (`position_id`) REFERENCES `employee_positions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `employees_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `it_assets`
--
ALTER TABLE `it_assets`
  ADD CONSTRAINT `it_assets_asset_category_id_foreign` FOREIGN KEY (`asset_category_id`) REFERENCES `it_asset_categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `it_assets_asset_location_id_foreign` FOREIGN KEY (`asset_location_id`) REFERENCES `it_asset_locations` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `it_assets_asset_user_id_foreign` FOREIGN KEY (`asset_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `it_assets_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `it_assets_pic_id_foreign` FOREIGN KEY (`pic_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `it_asset_maintenances`
--
ALTER TABLE `it_asset_maintenances`
  ADD CONSTRAINT `it_asset_maintenances_asset_id_foreign` FOREIGN KEY (`asset_id`) REFERENCES `it_assets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `it_asset_maintenances_division_id_foreign` FOREIGN KEY (`division_id`) REFERENCES `employee_divisions` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `it_asset_maintenances_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `it_asset_maintenances_pic_id_foreign` FOREIGN KEY (`pic_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `it_asset_maintenances_reviewer_id_foreign` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `it_asset_usage_histories`
--
ALTER TABLE `it_asset_usage_histories`
  ADD CONSTRAINT `it_asset_usage_histories_asset_id_foreign` FOREIGN KEY (`asset_id`) REFERENCES `it_assets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `it_asset_usage_histories_asset_location_id_foreign` FOREIGN KEY (`asset_location_id`) REFERENCES `it_asset_locations` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `it_asset_usage_histories_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `it_asset_usage_histories_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `employee_departments` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `it_asset_usage_histories_division_id_foreign` FOREIGN KEY (`division_id`) REFERENCES `employee_divisions` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `it_asset_usage_histories_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `it_asset_usage_histories_position_id_foreign` FOREIGN KEY (`position_id`) REFERENCES `employee_positions` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `resource_permission_role`
--
ALTER TABLE `resource_permission_role`
  ADD CONSTRAINT `resource_permission_role_resource_permission_id_foreign` FOREIGN KEY (`resource_permission_id`) REFERENCES `resource_permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `resource_permission_role_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
--
-- Database: `medquest_mjg_asset`
--
CREATE DATABASE IF NOT EXISTS `medquest_mjg_asset` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_ai_ci;
USE `medquest_mjg_asset`;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employeeId` uuid NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `initial` varchar(3) NOT NULL,
  `employee_number` varchar(6) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_departments`
--

CREATE TABLE `employee_departments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `departmentId` uuid NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_divisions`
--

CREATE TABLE `employee_divisions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `divisionId` uuid NOT NULL,
  `initial` varchar(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_positions`
--

CREATE TABLE `employee_positions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `positionId` uuid NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ga_assets`
--

CREATE TABLE `ga_assets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `assetId` uuid NOT NULL,
  `asset_code` varchar(255) NOT NULL,
  `asset_year_bought` year(4) NOT NULL,
  `asset_brand` varchar(255) NOT NULL,
  `asset_model` varchar(255) NOT NULL,
  `asset_serial_number` varchar(255) DEFAULT NULL,
  `asset_category_id` bigint(20) UNSIGNED NOT NULL,
  `asset_condition` enum('New','First Hand','Used','Defect','Disposed') NOT NULL,
  `asset_price` int(11) DEFAULT NULL,
  `asset_sell_price` int(11) DEFAULT NULL,
  `asset_notes` text DEFAULT NULL,
  `asset_remarks` text DEFAULT NULL,
  `asset_location_id` bigint(20) UNSIGNED DEFAULT NULL,
  `asset_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `pic_id` bigint(20) UNSIGNED NOT NULL,
  `barcode` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ga_asset_categories`
--

CREATE TABLE `ga_asset_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ga_asset_locations`
--

CREATE TABLE `ga_asset_locations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `locationId` uuid NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ga_asset_maintenances`
--

CREATE TABLE `ga_asset_maintenances` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `maintenanceId` uuid NOT NULL,
  `maintenance_date` date NOT NULL,
  `asset_id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `division_id` bigint(20) UNSIGNED NOT NULL,
  `maintenance_condition` varchar(255) NOT NULL,
  `maintenance_repair` varchar(255) NOT NULL,
  `maintenance_start_time` time NOT NULL,
  `maintenance_end_time` time NOT NULL,
  `pic_id` bigint(20) UNSIGNED NOT NULL,
  `reviewer_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ga_asset_rooms`
--

CREATE TABLE `ga_asset_rooms` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `location_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ga_asset_usage_histories`
--

CREATE TABLE `ga_asset_usage_histories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `usageId` uuid NOT NULL,
  `asset_id` bigint(20) UNSIGNED NOT NULL,
  `asset_location_id` bigint(20) UNSIGNED NOT NULL,
  `department_id` bigint(20) UNSIGNED NOT NULL,
  `division_id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `position_id` bigint(20) UNSIGNED NOT NULL,
  `usage_quantity` int(11) NOT NULL,
  `usage_start_date` date NOT NULL,
  `usage_end_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `room_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `it_assets`
--

CREATE TABLE `it_assets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `assetId` uuid NOT NULL,
  `asset_name` varchar(255) NOT NULL,
  `asset_code` varchar(255) NOT NULL,
  `asset_year_bought` year(4) NOT NULL,
  `asset_brand` varchar(255) DEFAULT NULL,
  `asset_model` varchar(255) DEFAULT NULL,
  `asset_serial_number` varchar(255) DEFAULT NULL,
  `asset_port` varchar(255) DEFAULT NULL,
  `asset_category_id` bigint(20) UNSIGNED NOT NULL,
  `asset_condition` enum('New','First Hand','Used','Defect','Disposed') NOT NULL,
  `asset_notes` text DEFAULT NULL,
  `asset_remarks` text DEFAULT NULL,
  `asset_location_id` bigint(20) UNSIGNED DEFAULT NULL,
  `asset_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `pic_id` bigint(20) UNSIGNED NOT NULL,
  `barcode` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `it_asset_categories`
--

CREATE TABLE `it_asset_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `it_asset_locations`
--

CREATE TABLE `it_asset_locations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `locationId` uuid NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `it_asset_maintenances`
--

CREATE TABLE `it_asset_maintenances` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `maintenanceId` uuid NOT NULL,
  `maintenance_date` date NOT NULL,
  `asset_id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED DEFAULT NULL,
  `maintenance_condition` varchar(5000) NOT NULL,
  `maintenance_repair` varchar(5000) NOT NULL,
  `maintenance_start_time` time NOT NULL,
  `maintenance_end_time` time DEFAULT NULL,
  `pic_id` bigint(20) UNSIGNED NOT NULL,
  `reviewer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `it_asset_usage_histories`
--

CREATE TABLE `it_asset_usage_histories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `usageId` uuid NOT NULL,
  `asset_id` bigint(20) UNSIGNED NOT NULL,
  `asset_location_id` bigint(20) UNSIGNED DEFAULT NULL,
  `employee_id` bigint(20) UNSIGNED DEFAULT NULL,
  `department_id` bigint(20) UNSIGNED DEFAULT NULL,
  `division_id` bigint(20) UNSIGNED DEFAULT NULL,
  `position_id` bigint(20) UNSIGNED DEFAULT NULL,
  `usage_start_date` date NOT NULL,
  `usage_end_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `resource_permissions`
--

CREATE TABLE `resource_permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `permissionId` uuid NOT NULL,
  `resource_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `resource_permission_role`
--

CREATE TABLE `resource_permission_role` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `resource_permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `userId` uuid NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `division_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employees_employeeid_unique` (`employeeId`),
  ADD UNIQUE KEY `employees_initial_unique` (`initial`),
  ADD KEY `employees_user_id_foreign` (`user_id`);

--
-- Indexes for table `employee_departments`
--
ALTER TABLE `employee_departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_departments_departmentid_unique` (`departmentId`),
  ADD UNIQUE KEY `employee_departments_name_unique` (`name`);

--
-- Indexes for table `employee_divisions`
--
ALTER TABLE `employee_divisions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_divisions_divisionid_unique` (`divisionId`),
  ADD UNIQUE KEY `employee_divisions_abbreviation_unique` (`initial`);

--
-- Indexes for table `employee_positions`
--
ALTER TABLE `employee_positions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_positions_positionid_unique` (`positionId`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `ga_assets`
--
ALTER TABLE `ga_assets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ga_assets_assetid_unique` (`assetId`),
  ADD UNIQUE KEY `ga_assets_asset_code_unique` (`asset_code`),
  ADD KEY `ga_assets_asset_category_id_foreign` (`asset_category_id`),
  ADD KEY `ga_assets_asset_location_id_foreign` (`asset_location_id`),
  ADD KEY `ga_assets_asset_user_id_foreign` (`asset_user_id`),
  ADD KEY `ga_assets_pic_id_foreign` (`pic_id`);

--
-- Indexes for table `ga_asset_categories`
--
ALTER TABLE `ga_asset_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ga_asset_categories_name_unique` (`name`);

--
-- Indexes for table `ga_asset_locations`
--
ALTER TABLE `ga_asset_locations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ga_asset_locations_locationid_unique` (`locationId`);

--
-- Indexes for table `ga_asset_maintenances`
--
ALTER TABLE `ga_asset_maintenances`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ga_asset_maintenances_maintenanceid_unique` (`maintenanceId`),
  ADD KEY `ga_asset_maintenances_asset_id_foreign` (`asset_id`),
  ADD KEY `ga_asset_maintenances_employee_id_foreign` (`employee_id`),
  ADD KEY `ga_asset_maintenances_division_id_foreign` (`division_id`),
  ADD KEY `ga_asset_maintenances_pic_id_foreign` (`pic_id`),
  ADD KEY `ga_asset_maintenances_reviewer_id_foreign` (`reviewer_id`);

--
-- Indexes for table `ga_asset_rooms`
--
ALTER TABLE `ga_asset_rooms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ga_asset_rooms_location_id_foreign` (`location_id`);

--
-- Indexes for table `ga_asset_usage_histories`
--
ALTER TABLE `ga_asset_usage_histories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ga_asset_usage_histories_usageid_unique` (`usageId`),
  ADD KEY `ga_asset_usage_histories_asset_id_foreign` (`asset_id`),
  ADD KEY `ga_asset_usage_histories_asset_location_id_foreign` (`asset_location_id`),
  ADD KEY `ga_asset_usage_histories_department_id_foreign` (`department_id`),
  ADD KEY `ga_asset_usage_histories_division_id_foreign` (`division_id`),
  ADD KEY `ga_asset_usage_histories_employee_id_foreign` (`employee_id`),
  ADD KEY `ga_asset_usage_histories_position_id_foreign` (`position_id`),
  ADD KEY `ga_asset_usage_histories_room_id_foreign` (`room_id`);

--
-- Indexes for table `it_assets`
--
ALTER TABLE `it_assets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `it_assets_assetid_unique` (`assetId`),
  ADD UNIQUE KEY `it_assets_asset_code_unique` (`asset_code`),
  ADD KEY `it_assets_asset_category_id_foreign` (`asset_category_id`),
  ADD KEY `it_assets_asset_location_id_foreign` (`asset_location_id`),
  ADD KEY `it_assets_asset_user_id_foreign` (`asset_user_id`),
  ADD KEY `it_assets_pic_id_foreign` (`pic_id`);

--
-- Indexes for table `it_asset_categories`
--
ALTER TABLE `it_asset_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `it_asset_categories_name_unique` (`name`);

--
-- Indexes for table `it_asset_locations`
--
ALTER TABLE `it_asset_locations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `it_asset_locations_locationid_unique` (`locationId`);

--
-- Indexes for table `it_asset_maintenances`
--
ALTER TABLE `it_asset_maintenances`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `it_asset_maintenances_maintenanceid_unique` (`maintenanceId`),
  ADD KEY `it_asset_maintenances_asset_id_foreign` (`asset_id`),
  ADD KEY `it_asset_maintenances_employee_id_foreign` (`employee_id`),
  ADD KEY `it_asset_maintenances_pic_id_foreign` (`pic_id`),
  ADD KEY `it_asset_maintenances_reviewer_id_foreign` (`reviewer_id`);

--
-- Indexes for table `it_asset_usage_histories`
--
ALTER TABLE `it_asset_usage_histories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `it_asset_usage_histories_usageid_unique` (`usageId`),
  ADD KEY `it_asset_usage_histories_asset_id_foreign` (`asset_id`),
  ADD KEY `it_asset_usage_histories_asset_location_id_foreign` (`asset_location_id`),
  ADD KEY `it_asset_usage_histories_employee_id_foreign` (`employee_id`),
  ADD KEY `it_asset_usage_histories_employee_department_id_foreign` (`department_id`),
  ADD KEY `it_asset_usage_histories_employee_division_id_foreign` (`division_id`),
  ADD KEY `it_asset_usage_histories_employee_position_id_foreign` (`position_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `resource_permissions`
--
ALTER TABLE `resource_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `resource_permissions_permissionid_unique` (`permissionId`);

--
-- Indexes for table `resource_permission_role`
--
ALTER TABLE `resource_permission_role`
  ADD PRIMARY KEY (`id`),
  ADD KEY `resource_permission_role_resource_permission_id_foreign` (`resource_permission_id`),
  ADD KEY `resource_permission_role_role_id_foreign` (`role_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_userid_unique` (`userId`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_departments`
--
ALTER TABLE `employee_departments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_divisions`
--
ALTER TABLE `employee_divisions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_positions`
--
ALTER TABLE `employee_positions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ga_assets`
--
ALTER TABLE `ga_assets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ga_asset_categories`
--
ALTER TABLE `ga_asset_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ga_asset_locations`
--
ALTER TABLE `ga_asset_locations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ga_asset_maintenances`
--
ALTER TABLE `ga_asset_maintenances`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ga_asset_rooms`
--
ALTER TABLE `ga_asset_rooms`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ga_asset_usage_histories`
--
ALTER TABLE `ga_asset_usage_histories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `it_assets`
--
ALTER TABLE `it_assets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `it_asset_categories`
--
ALTER TABLE `it_asset_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `it_asset_locations`
--
ALTER TABLE `it_asset_locations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `it_asset_maintenances`
--
ALTER TABLE `it_asset_maintenances`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `it_asset_usage_histories`
--
ALTER TABLE `it_asset_usage_histories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `resource_permissions`
--
ALTER TABLE `resource_permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `resource_permission_role`
--
ALTER TABLE `resource_permission_role`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `employees_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ga_assets`
--
ALTER TABLE `ga_assets`
  ADD CONSTRAINT `ga_assets_asset_category_id_foreign` FOREIGN KEY (`asset_category_id`) REFERENCES `ga_asset_categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ga_assets_asset_location_id_foreign` FOREIGN KEY (`asset_location_id`) REFERENCES `ga_asset_locations` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `ga_assets_asset_user_id_foreign` FOREIGN KEY (`asset_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `ga_assets_pic_id_foreign` FOREIGN KEY (`pic_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ga_asset_maintenances`
--
ALTER TABLE `ga_asset_maintenances`
  ADD CONSTRAINT `ga_asset_maintenances_asset_id_foreign` FOREIGN KEY (`asset_id`) REFERENCES `ga_assets` (`id`),
  ADD CONSTRAINT `ga_asset_maintenances_division_id_foreign` FOREIGN KEY (`division_id`) REFERENCES `employee_divisions` (`id`),
  ADD CONSTRAINT `ga_asset_maintenances_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`),
  ADD CONSTRAINT `ga_asset_maintenances_pic_id_foreign` FOREIGN KEY (`pic_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `ga_asset_maintenances_reviewer_id_foreign` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `ga_asset_rooms`
--
ALTER TABLE `ga_asset_rooms`
  ADD CONSTRAINT `ga_asset_rooms_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `ga_asset_locations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ga_asset_usage_histories`
--
ALTER TABLE `ga_asset_usage_histories`
  ADD CONSTRAINT `ga_asset_usage_histories_asset_id_foreign` FOREIGN KEY (`asset_id`) REFERENCES `ga_assets` (`id`),
  ADD CONSTRAINT `ga_asset_usage_histories_asset_location_id_foreign` FOREIGN KEY (`asset_location_id`) REFERENCES `ga_asset_locations` (`id`),
  ADD CONSTRAINT `ga_asset_usage_histories_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `employee_departments` (`id`),
  ADD CONSTRAINT `ga_asset_usage_histories_division_id_foreign` FOREIGN KEY (`division_id`) REFERENCES `employee_divisions` (`id`),
  ADD CONSTRAINT `ga_asset_usage_histories_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`),
  ADD CONSTRAINT `ga_asset_usage_histories_position_id_foreign` FOREIGN KEY (`position_id`) REFERENCES `employee_positions` (`id`),
  ADD CONSTRAINT `ga_asset_usage_histories_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `ga_asset_rooms` (`id`);

--
-- Constraints for table `it_assets`
--
ALTER TABLE `it_assets`
  ADD CONSTRAINT `it_assets_asset_category_id_foreign` FOREIGN KEY (`asset_category_id`) REFERENCES `it_asset_categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `it_assets_asset_location_id_foreign` FOREIGN KEY (`asset_location_id`) REFERENCES `it_asset_locations` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `it_assets_asset_user_id_foreign` FOREIGN KEY (`asset_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `it_assets_pic_id_foreign` FOREIGN KEY (`pic_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `it_asset_maintenances`
--
ALTER TABLE `it_asset_maintenances`
  ADD CONSTRAINT `it_asset_maintenances_asset_id_foreign` FOREIGN KEY (`asset_id`) REFERENCES `it_assets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `it_asset_maintenances_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `it_asset_maintenances_pic_id_foreign` FOREIGN KEY (`pic_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `it_asset_maintenances_reviewer_id_foreign` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `it_asset_usage_histories`
--
ALTER TABLE `it_asset_usage_histories`
  ADD CONSTRAINT `it_asset_usage_histories_asset_id_foreign` FOREIGN KEY (`asset_id`) REFERENCES `it_assets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `it_asset_usage_histories_asset_location_id_foreign` FOREIGN KEY (`asset_location_id`) REFERENCES `it_asset_locations` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `it_asset_usage_histories_employee_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `employee_departments` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `it_asset_usage_histories_employee_division_id_foreign` FOREIGN KEY (`division_id`) REFERENCES `employee_divisions` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `it_asset_usage_histories_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `it_asset_usage_histories_employee_position_id_foreign` FOREIGN KEY (`position_id`) REFERENCES `employee_positions` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `resource_permission_role`
--
ALTER TABLE `resource_permission_role`
  ADD CONSTRAINT `resource_permission_role_resource_permission_id_foreign` FOREIGN KEY (`resource_permission_id`) REFERENCES `resource_permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `resource_permission_role_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
--
-- Database: `medquest_scm`
--
CREATE DATABASE IF NOT EXISTS `medquest_scm` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_ai_ci;
USE `medquest_scm`;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE `locations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `locationId` uuid NOT NULL,
  `location_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` uuid NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint(20) UNSIGNED NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notification_logs`
--

CREATE TABLE `notification_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `notification_type` varchar(255) NOT NULL,
  `mailable_class` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint(20) UNSIGNED NOT NULL,
  `recipient_email` varchar(255) NOT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data`)),
  `status` varchar(255) NOT NULL DEFAULT 'sent',
  `error_message` text DEFAULT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notification_settings`
--

CREATE TABLE `notification_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `event_key` varchar(255) NOT NULL,
  `event_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notification_setting_role`
--

CREATE TABLE `notification_setting_role` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `notification_setting_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notification_setting_user`
--

CREATE TABLE `notification_setting_user` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `notification_setting_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `roomId` uuid NOT NULL,
  `location_id` bigint(20) UNSIGNED NOT NULL,
  `room_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `room_temperatures`
--

CREATE TABLE `room_temperatures` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `roomTemperatureId` uuid NOT NULL,
  `room_id` bigint(20) UNSIGNED NOT NULL,
  `temperature_start` tinytext NOT NULL,
  `temperature_end` tinytext NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `serial_numbers`
--

CREATE TABLE `serial_numbers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `serialNumberId` uuid NOT NULL,
  `room_id` bigint(20) UNSIGNED NOT NULL,
  `serial_number` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `temperature_deviations`
--

CREATE TABLE `temperature_deviations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `temperatureDeviationId` uuid DEFAULT NULL,
  `temperature_humidity_id` bigint(20) UNSIGNED DEFAULT NULL,
  `location_id` bigint(20) UNSIGNED NOT NULL,
  `room_id` bigint(20) UNSIGNED NOT NULL,
  `room_temperature_id` bigint(20) UNSIGNED NOT NULL,
  `serial_number_id` bigint(20) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `temperature_deviation` decimal(5,2) NOT NULL,
  `length_temperature_deviation` varchar(50) DEFAULT NULL,
  `deviation_reason` varchar(255) DEFAULT NULL,
  `pic` varchar(255) NOT NULL,
  `risk_analysis` varchar(255) DEFAULT NULL,
  `analyzer_pic` varchar(255) DEFAULT NULL,
  `is_reviewed` tinyint(1) NOT NULL DEFAULT 0,
  `reviewed_by` varchar(255) DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `is_acknowledged` tinyint(1) NOT NULL DEFAULT 0,
  `acknowledged_by` varchar(255) DEFAULT NULL,
  `acknowledged_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `temperature_humidities`
--

CREATE TABLE `temperature_humidities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `temperatureId` uuid NOT NULL,
  `date` date NOT NULL,
  `period` date NOT NULL,
  `location_id` bigint(20) UNSIGNED NOT NULL,
  `room_id` bigint(20) UNSIGNED NOT NULL,
  `room_temperature_id` bigint(20) UNSIGNED NOT NULL,
  `serial_number_id` bigint(20) UNSIGNED NOT NULL,
  `time_0200` time DEFAULT NULL,
  `temp_0200` decimal(5,2) DEFAULT NULL,
  `rh_0200` int(11) DEFAULT NULL,
  `pic_0200` varchar(255) DEFAULT NULL,
  `pic_0200_id` bigint(20) UNSIGNED DEFAULT NULL,
  `time_0500` time DEFAULT NULL,
  `temp_0500` decimal(5,2) DEFAULT NULL,
  `rh_0500` int(11) DEFAULT NULL,
  `pic_0500` varchar(255) DEFAULT NULL,
  `pic_0500_id` bigint(20) UNSIGNED DEFAULT NULL,
  `time_0800` time DEFAULT NULL,
  `temp_0800` decimal(5,2) DEFAULT NULL,
  `rh_0800` int(11) DEFAULT NULL,
  `pic_0800` varchar(255) DEFAULT NULL,
  `pic_0800_id` bigint(20) UNSIGNED DEFAULT NULL,
  `time_1100` time DEFAULT NULL,
  `temp_1100` decimal(5,2) DEFAULT NULL,
  `rh_1100` int(11) DEFAULT NULL,
  `pic_1100` varchar(255) DEFAULT NULL,
  `pic_1100_id` bigint(20) UNSIGNED DEFAULT NULL,
  `time_1400` time DEFAULT NULL,
  `temp_1400` decimal(5,2) DEFAULT NULL,
  `rh_1400` int(11) DEFAULT NULL,
  `pic_1400` varchar(255) DEFAULT NULL,
  `pic_1400_id` bigint(20) UNSIGNED DEFAULT NULL,
  `time_1700` time DEFAULT NULL,
  `temp_1700` decimal(5,2) DEFAULT NULL,
  `rh_1700` int(11) DEFAULT NULL,
  `pic_1700` varchar(255) DEFAULT NULL,
  `pic_1700_id` bigint(20) UNSIGNED DEFAULT NULL,
  `time_2000` time DEFAULT NULL,
  `temp_2000` decimal(5,2) DEFAULT NULL,
  `rh_2000` int(11) DEFAULT NULL,
  `pic_2000` varchar(255) DEFAULT NULL,
  `pic_2000_id` bigint(20) UNSIGNED DEFAULT NULL,
  `time_2300` time DEFAULT NULL,
  `temp_2300` decimal(5,2) DEFAULT NULL,
  `rh_2300` int(11) DEFAULT NULL,
  `pic_2300` varchar(255) DEFAULT NULL,
  `pic_2300_id` bigint(20) UNSIGNED DEFAULT NULL,
  `is_reviewed` tinyint(1) NOT NULL DEFAULT 0,
  `reviewed_by` varchar(255) DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `is_acknowledged` tinyint(1) NOT NULL DEFAULT 0,
  `acknowledged_by` varchar(255) DEFAULT NULL,
  `acknowledged_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `userId` uuid NOT NULL,
  `location_id` bigint(10) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `username` varchar(8) NOT NULL,
  `initial` varchar(3) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `password_change_required` tinyint(1) NOT NULL DEFAULT 0,
  `password_changed_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`);

--
-- Indexes for table `notification_logs`
--
ALTER TABLE `notification_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notification_logs_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`);

--
-- Indexes for table `notification_settings`
--
ALTER TABLE `notification_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `notification_settings_event_key_unique` (`event_key`);

--
-- Indexes for table `notification_setting_role`
--
ALTER TABLE `notification_setting_role`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notification_setting_role_notification_setting_id_foreign` (`notification_setting_id`),
  ADD KEY `notification_setting_role_role_id_foreign` (`role_id`);

--
-- Indexes for table `notification_setting_user`
--
ALTER TABLE `notification_setting_user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notification_setting_user_notification_setting_id_foreign` (`notification_setting_id`),
  ADD KEY `notification_setting_user_user_id_foreign` (`user_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `rooms_roomid_unique` (`roomId`),
  ADD KEY `rooms_location_id_foreign` (`location_id`);

--
-- Indexes for table `room_temperatures`
--
ALTER TABLE `room_temperatures`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `room_temperatures_roomtemperatureid_unique` (`roomTemperatureId`),
  ADD KEY `room_temperatures_room_id_foreign` (`room_id`);

--
-- Indexes for table `serial_numbers`
--
ALTER TABLE `serial_numbers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `serial_numbers_serialnumberid_unique` (`serialNumberId`),
  ADD UNIQUE KEY `serial_numbers_serial_number_unique` (`serial_number`),
  ADD KEY `serial_numbers_room_id_foreign` (`room_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `temperature_deviations`
--
ALTER TABLE `temperature_deviations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `temperature_deviations_temperaturedeviationid_unique` (`temperatureDeviationId`),
  ADD KEY `temperature_deviations_temperature_humidity_id_foreign` (`temperature_humidity_id`),
  ADD KEY `temperature_deviations_location_id_foreign` (`location_id`),
  ADD KEY `temperature_deviations_room_id_foreign` (`room_id`),
  ADD KEY `temperature_deviations_room_temperature_id_foreign` (`room_temperature_id`),
  ADD KEY `temperature_deviations_serial_number_id_foreign` (`serial_number_id`),
  ADD KEY `idx_td_lookup` (`temperature_humidity_id`,`time`,`temperature_deviation`);

--
-- Indexes for table `temperature_humidities`
--
ALTER TABLE `temperature_humidities`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `temperature_humidities_temperatureid_unique` (`temperatureId`),
  ADD KEY `temperature_humidities_location_id_foreign` (`location_id`),
  ADD KEY `temperature_humidities_room_id_foreign` (`room_id`),
  ADD KEY `temperature_humidities_room_temperature_id_foreign` (`room_temperature_id`),
  ADD KEY `temperature_humidities_serial_number_id_foreign` (`serial_number_id`),
  ADD KEY `temperature_humidities_pic_0200_id_foreign` (`pic_0200_id`),
  ADD KEY `temperature_humidities_pic_0500_id_foreign` (`pic_0500_id`),
  ADD KEY `temperature_humidities_pic_0800_id_foreign` (`pic_0800_id`),
  ADD KEY `temperature_humidities_pic_1100_id_foreign` (`pic_1100_id`),
  ADD KEY `temperature_humidities_pic_1400_id_foreign` (`pic_1400_id`),
  ADD KEY `temperature_humidities_pic_1700_id_foreign` (`pic_1700_id`),
  ADD KEY `temperature_humidities_pic_2000_id_foreign` (`pic_2000_id`),
  ADD KEY `temperature_humidities_pic_2300_id_foreign` (`pic_2300_id`),
  ADD KEY `idx_th_room_id_date` (`room_id`,`date`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_userid_unique` (`userId`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_location_id_foreign` (`location_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notification_logs`
--
ALTER TABLE `notification_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notification_settings`
--
ALTER TABLE `notification_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notification_setting_role`
--
ALTER TABLE `notification_setting_role`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notification_setting_user`
--
ALTER TABLE `notification_setting_user`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `room_temperatures`
--
ALTER TABLE `room_temperatures`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `serial_numbers`
--
ALTER TABLE `serial_numbers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `temperature_deviations`
--
ALTER TABLE `temperature_deviations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `temperature_humidities`
--
ALTER TABLE `temperature_humidities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notification_setting_role`
--
ALTER TABLE `notification_setting_role`
  ADD CONSTRAINT `notification_setting_role_notification_setting_id_foreign` FOREIGN KEY (`notification_setting_id`) REFERENCES `notification_settings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notification_setting_role_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notification_setting_user`
--
ALTER TABLE `notification_setting_user`
  ADD CONSTRAINT `notification_setting_user_notification_setting_id_foreign` FOREIGN KEY (`notification_setting_id`) REFERENCES `notification_settings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notification_setting_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `rooms_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `room_temperatures`
--
ALTER TABLE `room_temperatures`
  ADD CONSTRAINT `room_temperatures_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `serial_numbers`
--
ALTER TABLE `serial_numbers`
  ADD CONSTRAINT `serial_numbers_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `temperature_deviations`
--
ALTER TABLE `temperature_deviations`
  ADD CONSTRAINT `temperature_deviations_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `temperature_deviations_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `temperature_deviations_room_temperature_id_foreign` FOREIGN KEY (`room_temperature_id`) REFERENCES `room_temperatures` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `temperature_deviations_serial_number_id_foreign` FOREIGN KEY (`serial_number_id`) REFERENCES `serial_numbers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `temperature_deviations_temperature_humidity_id_foreign` FOREIGN KEY (`temperature_humidity_id`) REFERENCES `temperature_humidities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `temperature_humidities`
--
ALTER TABLE `temperature_humidities`
  ADD CONSTRAINT `temperature_humidities_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `temperature_humidities_pic_0200_id_foreign` FOREIGN KEY (`pic_0200_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `temperature_humidities_pic_0500_id_foreign` FOREIGN KEY (`pic_0500_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `temperature_humidities_pic_0800_id_foreign` FOREIGN KEY (`pic_0800_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `temperature_humidities_pic_1100_id_foreign` FOREIGN KEY (`pic_1100_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `temperature_humidities_pic_1400_id_foreign` FOREIGN KEY (`pic_1400_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `temperature_humidities_pic_1700_id_foreign` FOREIGN KEY (`pic_1700_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `temperature_humidities_pic_2000_id_foreign` FOREIGN KEY (`pic_2000_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `temperature_humidities_pic_2300_id_foreign` FOREIGN KEY (`pic_2300_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `temperature_humidities_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `temperature_humidities_room_temperature_id_foreign` FOREIGN KEY (`room_temperature_id`) REFERENCES `room_temperatures` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `temperature_humidities_serial_number_id_foreign` FOREIGN KEY (`serial_number_id`) REFERENCES `serial_numbers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
--
-- Database: `mmg_sales`
--
CREATE DATABASE IF NOT EXISTS `mmg_sales` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_ai_ci;
USE `mmg_sales`;

-- --------------------------------------------------------

--
-- Table structure for table `activities`
--

CREATE TABLE `activities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `activity_code` varchar(20) DEFAULT NULL,
  `project_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `contact_id` bigint(20) UNSIGNED DEFAULT NULL,
  `type` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `performed_at` datetime NOT NULL,
  `visit_started_at` datetime DEFAULT NULL,
  `visit_ended_at` datetime DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `purpose` varchar(255) DEFAULT NULL,
  `expectations` text DEFAULT NULL,
  `targets` text DEFAULT NULL,
  `stakeholder_feedback` text DEFAULT NULL,
  `is_worth_keeping` tinyint(1) NOT NULL DEFAULT 1,
  `confidence_level` int(11) NOT NULL DEFAULT 0,
  `next_contact_date` date DEFAULT NULL,
  `follow_up_notes` text DEFAULT NULL,
  `meeting_link` varchar(255) DEFAULT NULL,
  `messaging_platform` varchar(255) DEFAULT NULL,
  `duration_minutes` int(11) DEFAULT NULL,
  `outcome` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `activity_user`
--

CREATE TABLE `activity_user` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `activity_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `code_sequences`
--

CREATE TABLE `code_sequences` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `prefix` varchar(10) NOT NULL,
  `partition` varchar(10) NOT NULL DEFAULT '',
  `sequence_value` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `contact_code` varchar(20) DEFAULT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `position` varchar(255) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `mobile` varchar(255) DEFAULT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `is_billing_contact` tinyint(1) NOT NULL DEFAULT 0,
  `notes` text DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contact_phones`
--

CREATE TABLE `contact_phones` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `contact_id` bigint(20) UNSIGNED NOT NULL,
  `type` enum('work','mobile','home','fax','other') NOT NULL DEFAULT 'mobile',
  `number` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer_code` varchar(20) DEFAULT NULL,
  `customer_acc_code` varchar(50) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `type` enum('hospital_clinic','pt_cv','university','government','other') NOT NULL DEFAULT 'other',
  `other_type` varchar(255) DEFAULT NULL,
  `tax_number` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `postal_code` varchar(255) DEFAULT NULL,
  `country` varchar(255) NOT NULL DEFAULT 'Indonesia',
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `phone_purchasing` varchar(255) DEFAULT NULL,
  `phone_finance` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `status` enum('active','passive') DEFAULT 'active',
  `customer_group_id` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `cd_ncd_type` enum('CD','NCD') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customer_groups`
--

CREATE TABLE `customer_groups` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer_group_code` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT 'MMG',
  `code` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `distributors`
--

CREATE TABLE `distributors` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `internal_code` varchar(255) NOT NULL,
  `principle_code` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `principal_id` bigint(20) UNSIGNED NOT NULL,
  `description` text DEFAULT NULL,
  `unit_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `ecatalog_price` decimal(12,2) DEFAULT NULL,
  `unit` varchar(255) NOT NULL DEFAULT 'unit',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `milestones`
--

CREATE TABLE `milestones` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `weight` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` uuid NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint(20) UNSIGNED NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tahun` int(11) NOT NULL,
  `bulan` int(11) NOT NULL,
  `department_id` bigint(20) UNSIGNED NOT NULL,
  `head_position_id` bigint(20) UNSIGNED NOT NULL,
  `pm_jpm_pe_position_id` bigint(20) UNSIGNED DEFAULT NULL,
  `rsm_asm_position_id` bigint(20) UNSIGNED NOT NULL,
  `spv_position_id` bigint(20) UNSIGNED NOT NULL,
  `sr_position_id` bigint(20) UNSIGNED NOT NULL,
  `area_city_id` bigint(20) UNSIGNED NOT NULL,
  `end_customer_id` bigint(20) UNSIGNED NOT NULL,
  `customer_group_id` bigint(20) UNSIGNED DEFAULT NULL,
  `cd_ncd_type` varchar(255) NOT NULL,
  `ncd_subtype` varchar(255) DEFAULT NULL,
  `segment_id` bigint(20) UNSIGNED NOT NULL,
  `principal_id` bigint(20) UNSIGNED NOT NULL,
  `reg_inst` varchar(255) NOT NULL,
  `sales_type_id` bigint(20) UNSIGNED NOT NULL,
  `item_id` bigint(20) UNSIGNED NOT NULL,
  `qty_hna` int(11) NOT NULL,
  `total_hna_gross_sales` bigint(20) NOT NULL,
  `discount_on` decimal(5,2) NOT NULL,
  `net_sales_total` bigint(20) NOT NULL,
  `sub_segment_id` bigint(20) UNSIGNED DEFAULT NULL,
  `jual_kso` varchar(255) NOT NULL,
  `distributor_id` bigint(20) UNSIGNED NOT NULL,
  `order_number` varchar(255) NOT NULL,
  `original_customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `project_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` enum('draft','pending','confirmed','processing','shipped','delivered','cancelled','returned') NOT NULL DEFAULT 'draft',
  `subtotal` decimal(16,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(16,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(16,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(16,2) NOT NULL DEFAULT 0.00,
  `order_date` date NOT NULL,
  `expected_delivery_date` date DEFAULT NULL,
  `actual_delivery_date` date DEFAULT NULL,
  `shipping_address` text DEFAULT NULL,
  `billing_address` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `payment_method` varchar(255) DEFAULT NULL,
  `payment_status` enum('pending','partial','paid','overdue') NOT NULL DEFAULT 'pending',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `item_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(12,2) NOT NULL,
  `current_price` decimal(12,2) DEFAULT NULL,
  `price_type` varchar(20) DEFAULT NULL,
  `discount_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `subtotal` decimal(12,2) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `principal_id` bigint(20) UNSIGNED DEFAULT NULL,
  `product_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `positions`
--

CREATE TABLE `positions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `level` int(11) DEFAULT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `department_id` bigint(20) UNSIGNED DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `principals`
--

CREATE TABLE `principals` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `principal_code` varchar(20) DEFAULT NULL,
  `principal_acc_code` varchar(50) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `initial` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_code` varchar(20) DEFAULT NULL,
  `product_acc_code` varchar(50) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `category` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `unit_price` decimal(12,2) NOT NULL,
  `ecatalog_price` decimal(12,2) DEFAULT NULL,
  `unit_of_measure` varchar(255) NOT NULL DEFAULT 'pcs',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `principal_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `project_code` varchar(20) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `contact_person` bigint(20) UNSIGNED DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `status` enum('new','contacted','qualified','proposal','negotiation','won','lost') NOT NULL DEFAULT 'new',
  `source` enum('website','referral','cold_call','trade_show','partner','other') NOT NULL DEFAULT 'other',
  `priority` enum('low','medium','high','urgent') NOT NULL DEFAULT 'medium',
  `confidence_level` int(11) NOT NULL DEFAULT 0,
  `estimated_revenue` decimal(16,2) DEFAULT NULL,
  `estimated_completion_date` date DEFAULT NULL,
  `closed_at` timestamp NULL DEFAULT NULL,
  `estimated_value` decimal(16,2) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `converted_at` timestamp NULL DEFAULT NULL,
  `last_contacted_at` timestamp NULL DEFAULT NULL,
  `assigned_to` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `position` varchar(255) DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `project_collaborators`
--

CREATE TABLE `project_collaborators` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `project_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `added_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `project_milestone`
--

CREATE TABLE `project_milestone` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `project_id` bigint(20) UNSIGNED NOT NULL,
  `milestone_id` bigint(20) UNSIGNED NOT NULL,
  `is_completed` tinyint(1) NOT NULL DEFAULT 0,
  `completed_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `project_product`
--

CREATE TABLE `project_product` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `project_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `department_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sales_types`
--

CREATE TABLE `sales_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `segments`
--

CREATE TABLE `segments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `segment_code` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sub_segments`
--

CREATE TABLE `sub_segments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sub_segment_code` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `segment_id` bigint(20) UNSIGNED NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `targets`
--

CREATE TABLE `targets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `year` smallint(6) NOT NULL,
  `month` tinyint(4) DEFAULT NULL,
  `annual_target` decimal(15,2) DEFAULT NULL,
  `monthly_target` decimal(15,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `territories`
--

CREATE TABLE `territories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `wilayah_code` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `type` enum('region','province','city') NOT NULL,
  `level` int(11) NOT NULL COMMENT '1=region, 2=province, 3=city',
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `manager_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `department_id` bigint(20) UNSIGNED DEFAULT NULL,
  `position_id` bigint(20) UNSIGNED DEFAULT NULL,
  `territory_id` bigint(20) UNSIGNED DEFAULT NULL,
  `manager_id` bigint(20) UNSIGNED DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activities`
--
ALTER TABLE `activities`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `activities_activity_code_unique` (`activity_code`),
  ADD KEY `activities_user_id_foreign` (`user_id`),
  ADD KEY `activities_project_id_performed_at_index` (`project_id`,`performed_at`),
  ADD KEY `activities_type_index` (`type`),
  ADD KEY `activities_customer_id_foreign` (`customer_id`),
  ADD KEY `activities_contact_id_foreign` (`contact_id`);

--
-- Indexes for table `activity_user`
--
ALTER TABLE `activity_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `activity_user_activity_id_user_id_unique` (`activity_id`,`user_id`),
  ADD KEY `activity_user_user_id_foreign` (`user_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `code_sequences`
--
ALTER TABLE `code_sequences`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code_sequences_prefix_partition_unique` (`prefix`,`partition`),
  ADD KEY `code_sequences_prefix_index` (`prefix`),
  ADD KEY `code_sequences_partition_index` (`partition`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `contacts_contact_code_unique` (`contact_code`),
  ADD KEY `contacts_customer_id_foreign` (`customer_id`);

--
-- Indexes for table `contact_phones`
--
ALTER TABLE `contact_phones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `contact_phones_contact_id_foreign` (`contact_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customers_customer_code_unique` (`customer_code`),
  ADD KEY `customers_customer_group_id_foreign` (`customer_group_id`);

--
-- Indexes for table `customer_groups`
--
ALTER TABLE `customer_groups`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customer_groups_code_unique` (`code`),
  ADD UNIQUE KEY `customer_groups_customer_group_code_unique` (`customer_group_code`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `departments_code_unique` (`code`);

--
-- Indexes for table `distributors`
--
ALTER TABLE `distributors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `distributors_code_unique` (`code`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `items_internal_code_unique` (`internal_code`),
  ADD KEY `items_principal_id_foreign` (`principal_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `milestones`
--
ALTER TABLE `milestones`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `orders_order_number_unique` (`order_number`),
  ADD KEY `orders_department_id_foreign` (`department_id`),
  ADD KEY `orders_head_position_id_foreign` (`head_position_id`),
  ADD KEY `orders_pm_jpm_pe_position_id_foreign` (`pm_jpm_pe_position_id`),
  ADD KEY `orders_rsm_asm_position_id_foreign` (`rsm_asm_position_id`),
  ADD KEY `orders_spv_position_id_foreign` (`spv_position_id`),
  ADD KEY `orders_sr_position_id_foreign` (`sr_position_id`),
  ADD KEY `orders_area_city_id_foreign` (`area_city_id`),
  ADD KEY `orders_end_customer_id_foreign` (`end_customer_id`),
  ADD KEY `orders_customer_group_id_foreign` (`customer_group_id`),
  ADD KEY `orders_segment_id_foreign` (`segment_id`),
  ADD KEY `orders_principal_id_foreign` (`principal_id`),
  ADD KEY `orders_sales_type_id_foreign` (`sales_type_id`),
  ADD KEY `orders_item_id_foreign` (`item_id`),
  ADD KEY `orders_sub_segment_id_foreign` (`sub_segment_id`),
  ADD KEY `orders_distributor_id_foreign` (`distributor_id`),
  ADD KEY `orders_original_customer_id_foreign` (`original_customer_id`),
  ADD KEY `orders_project_id_foreign` (`project_id`),
  ADD KEY `orders_created_by_foreign` (`created_by`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_items_order_id_foreign` (`order_id`),
  ADD KEY `order_items_product_id_foreign` (`item_id`),
  ADD KEY `order_items_principal_id_foreign` (`principal_id`),
  ADD KEY `order_items_product_id_foreign_new` (`product_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `positions`
--
ALTER TABLE `positions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `positions_code_unique` (`code`),
  ADD KEY `positions_parent_id_foreign` (`parent_id`),
  ADD KEY `positions_department_id_foreign` (`department_id`);

--
-- Indexes for table `principals`
--
ALTER TABLE `principals`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `principals_initial_unique` (`initial`),
  ADD UNIQUE KEY `principals_principal_code_unique` (`principal_code`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `products_product_code_unique` (`product_code`),
  ADD KEY `products_principal_id_foreign` (`principal_id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `projects_project_code_unique` (`project_code`),
  ADD KEY `projects_customer_id_foreign` (`customer_id`),
  ADD KEY `projects_assigned_to_foreign` (`assigned_to`),
  ADD KEY `projects_created_by_foreign` (`created_by`),
  ADD KEY `projects_contact_person_foreign` (`contact_person`);

--
-- Indexes for table `project_collaborators`
--
ALTER TABLE `project_collaborators`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `project_collaborators_project_id_user_id_unique` (`project_id`,`user_id`),
  ADD KEY `project_collaborators_user_id_foreign` (`user_id`),
  ADD KEY `project_collaborators_added_by_foreign` (`added_by`);

--
-- Indexes for table `project_milestone`
--
ALTER TABLE `project_milestone`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_milestone_project_id_foreign` (`project_id`),
  ADD KEY `project_milestone_milestone_id_foreign` (`milestone_id`);

--
-- Indexes for table `project_product`
--
ALTER TABLE `project_product`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_product_project_id_foreign` (`project_id`),
  ADD KEY `project_product_product_id_foreign` (`product_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_dept_unique` (`name`,`guard_name`,`department_id`),
  ADD KEY `roles_department_id_foreign` (`department_id`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `sales_types`
--
ALTER TABLE `sales_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sales_types_code_unique` (`code`);

--
-- Indexes for table `segments`
--
ALTER TABLE `segments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `segments_code_unique` (`code`),
  ADD UNIQUE KEY `segments_segment_code_unique` (`segment_code`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `sub_segments`
--
ALTER TABLE `sub_segments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sub_segments_code_unique` (`code`),
  ADD UNIQUE KEY `sub_segments_sub_segment_code_unique` (`sub_segment_code`),
  ADD KEY `sub_segments_segment_id_foreign` (`segment_id`);

--
-- Indexes for table `targets`
--
ALTER TABLE `targets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `targets_user_id_year_month_unique` (`user_id`,`year`,`month`);

--
-- Indexes for table `territories`
--
ALTER TABLE `territories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `territories_parent_id_foreign` (`parent_id`),
  ADD KEY `territories_manager_id_foreign` (`manager_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_code_unique` (`code`),
  ADD KEY `users_department_id_foreign` (`department_id`),
  ADD KEY `users_position_id_foreign` (`position_id`),
  ADD KEY `users_territory_id_foreign` (`territory_id`),
  ADD KEY `users_manager_id_foreign` (`manager_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activities`
--
ALTER TABLE `activities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `activity_user`
--
ALTER TABLE `activity_user`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `code_sequences`
--
ALTER TABLE `code_sequences`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contact_phones`
--
ALTER TABLE `contact_phones`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customer_groups`
--
ALTER TABLE `customer_groups`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `distributors`
--
ALTER TABLE `distributors`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `milestones`
--
ALTER TABLE `milestones`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `positions`
--
ALTER TABLE `positions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `principals`
--
ALTER TABLE `principals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project_collaborators`
--
ALTER TABLE `project_collaborators`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project_milestone`
--
ALTER TABLE `project_milestone`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project_product`
--
ALTER TABLE `project_product`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sales_types`
--
ALTER TABLE `sales_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `segments`
--
ALTER TABLE `segments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sub_segments`
--
ALTER TABLE `sub_segments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `targets`
--
ALTER TABLE `targets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `territories`
--
ALTER TABLE `territories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activities`
--
ALTER TABLE `activities`
  ADD CONSTRAINT `activities_contact_id_foreign` FOREIGN KEY (`contact_id`) REFERENCES `contacts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `activities_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `activities_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `activities_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `activity_user`
--
ALTER TABLE `activity_user`
  ADD CONSTRAINT `activity_user_activity_id_foreign` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `activity_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `contacts`
--
ALTER TABLE `contacts`
  ADD CONSTRAINT `contacts_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `contact_phones`
--
ALTER TABLE `contact_phones`
  ADD CONSTRAINT `contact_phones_contact_id_foreign` FOREIGN KEY (`contact_id`) REFERENCES `contacts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `customers`
--
ALTER TABLE `customers`
  ADD CONSTRAINT `customers_customer_group_id_foreign` FOREIGN KEY (`customer_group_id`) REFERENCES `customer_groups` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `items`
--
ALTER TABLE `items`
  ADD CONSTRAINT `items_principal_id_foreign` FOREIGN KEY (`principal_id`) REFERENCES `principals` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_area_city_id_foreign` FOREIGN KEY (`area_city_id`) REFERENCES `territories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `orders_customer_group_id_foreign` FOREIGN KEY (`customer_group_id`) REFERENCES `customer_groups` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `orders_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_distributor_id_foreign` FOREIGN KEY (`distributor_id`) REFERENCES `distributors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_end_customer_id_foreign` FOREIGN KEY (`end_customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_head_position_id_foreign` FOREIGN KEY (`head_position_id`) REFERENCES `positions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_original_customer_id_foreign` FOREIGN KEY (`original_customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `orders_pm_jpm_pe_position_id_foreign` FOREIGN KEY (`pm_jpm_pe_position_id`) REFERENCES `positions` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `orders_principal_id_foreign` FOREIGN KEY (`principal_id`) REFERENCES `principals` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `orders_rsm_asm_position_id_foreign` FOREIGN KEY (`rsm_asm_position_id`) REFERENCES `positions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_sales_type_id_foreign` FOREIGN KEY (`sales_type_id`) REFERENCES `sales_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_segment_id_foreign` FOREIGN KEY (`segment_id`) REFERENCES `segments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_spv_position_id_foreign` FOREIGN KEY (`spv_position_id`) REFERENCES `positions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_sr_position_id_foreign` FOREIGN KEY (`sr_position_id`) REFERENCES `positions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_sub_segment_id_foreign` FOREIGN KEY (`sub_segment_id`) REFERENCES `sub_segments` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_principal_id_foreign` FOREIGN KEY (`principal_id`) REFERENCES `principals` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `order_items_product_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `order_items_product_id_foreign_new` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `positions`
--
ALTER TABLE `positions`
  ADD CONSTRAINT `positions_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `positions_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `positions` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_principal_id_foreign` FOREIGN KEY (`principal_id`) REFERENCES `principals` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `projects_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `projects_contact_person_foreign` FOREIGN KEY (`contact_person`) REFERENCES `contacts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `projects_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `projects_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `project_collaborators`
--
ALTER TABLE `project_collaborators`
  ADD CONSTRAINT `project_collaborators_added_by_foreign` FOREIGN KEY (`added_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_collaborators_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_collaborators_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `project_milestone`
--
ALTER TABLE `project_milestone`
  ADD CONSTRAINT `project_milestone_milestone_id_foreign` FOREIGN KEY (`milestone_id`) REFERENCES `milestones` (`id`),
  ADD CONSTRAINT `project_milestone_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `project_product`
--
ALTER TABLE `project_product`
  ADD CONSTRAINT `project_product_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_product_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `roles`
--
ALTER TABLE `roles`
  ADD CONSTRAINT `roles_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sub_segments`
--
ALTER TABLE `sub_segments`
  ADD CONSTRAINT `sub_segments_segment_id_foreign` FOREIGN KEY (`segment_id`) REFERENCES `segments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `targets`
--
ALTER TABLE `targets`
  ADD CONSTRAINT `targets_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `territories`
--
ALTER TABLE `territories`
  ADD CONSTRAINT `territories_manager_id_foreign` FOREIGN KEY (`manager_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `territories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `territories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `users_manager_id_foreign` FOREIGN KEY (`manager_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `users_position_id_foreign` FOREIGN KEY (`position_id`) REFERENCES `positions` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `users_territory_id_foreign` FOREIGN KEY (`territory_id`) REFERENCES `territories` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
