-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 29-05-2026 a las 22:49:07
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `tienda_virtual`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `admin_activity_log`
--

CREATE TABLE `admin_activity_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL COMMENT 'ID del admin (tabla users)',
  `action` varchar(100) NOT NULL COMMENT 'Ej: product_created, order_updated',
  `entity_type` varchar(50) DEFAULT NULL COMMENT 'Ej: product, order, customer',
  `entity_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `old_values` text DEFAULT NULL COMMENT 'Valores anteriores (JSON)',
  `new_values` text DEFAULT NULL COMMENT 'Valores nuevos (JSON)',
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `banners`
--

CREATE TABLE `banners` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
  `image` varchar(255) NOT NULL,
  `image_mobile` varchar(255) DEFAULT NULL,
  `link_url` varchar(500) DEFAULT NULL,
  `link_target` enum('_self','_blank') NOT NULL DEFAULT '_self',
  `position` enum('home_slider','home_banner','category_banner','popup') NOT NULL DEFAULT 'home_slider',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `starts_at` datetime DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `brands`
--

CREATE TABLE `brands` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `description` text DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carts`
--

CREATE TABLE `carts` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL COMMENT 'NULL para carritos de invitados',
  `session_id` varchar(255) DEFAULT NULL COMMENT 'Para invitados no logueados',
  `coupon_id` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cart_items`
--

CREATE TABLE `cart_items` (
  `id` int(11) NOT NULL,
  `cart_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `variant_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `unit_price` decimal(12,2) NOT NULL COMMENT 'Precio al momento de agregar',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL COMMENT 'NULL = categor??a ra??z',
  `name` varchar(100) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `company`
--

CREATE TABLE `company` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `nit` varchar(15) NOT NULL,
  `direccion` varchar(255) NOT NULL,
  `telefono` varchar(15) NOT NULL,
  `logo` varchar(255) NOT NULL,
  `email` varchar(266) NOT NULL,
  `ciudad` varchar(255) NOT NULL,
  `web` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `name` varchar(200) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `status` enum('new','read','replied','closed') NOT NULL DEFAULT 'new',
  `reply` text DEFAULT NULL,
  `replied_by` int(11) DEFAULT NULL,
  `replied_at` datetime DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `coupons`
--

CREATE TABLE `coupons` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `type` enum('percentage','fixed_cart','fixed_product','free_shipping') NOT NULL DEFAULT 'percentage',
  `value` decimal(12,2) NOT NULL COMMENT 'Porcentaje o monto fijo',
  `min_order_amount` decimal(12,2) DEFAULT NULL COMMENT 'Monto m??nimo de pedido',
  `max_discount_amount` decimal(12,2) DEFAULT NULL COMMENT 'Descuento m??ximo (para %)',
  `usage_limit` int(11) DEFAULT NULL COMMENT 'NULL = sin l??mite',
  `usage_limit_per_customer` int(11) DEFAULT 1,
  `used_count` int(11) NOT NULL DEFAULT 0,
  `applies_to` enum('all','categories','products') NOT NULL DEFAULT 'all',
  `applicable_ids` text DEFAULT NULL COMMENT 'IDs de categor??as o productos (JSON)',
  `exclude_sale_items` tinyint(1) NOT NULL DEFAULT 0,
  `starts_at` datetime DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `coupon_usage`
--

CREATE TABLE `coupon_usage` (
  `id` int(11) NOT NULL,
  `coupon_id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL,
  `discount_amount` decimal(12,2) NOT NULL,
  `used_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `document_type` enum('CC','CE','TI','NIT','PP','PEP') NOT NULL DEFAULT 'CC',
  `document_number` varchar(20) DEFAULT NULL,
  `gender` enum('M','F','O') DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `email_verified` tinyint(1) NOT NULL DEFAULT 0,
  `email_verification_token` varchar(255) DEFAULT NULL,
  `password_reset_token` varchar(255) DEFAULT NULL,
  `password_reset_expires` datetime DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_login` datetime DEFAULT NULL,
  `login_attempts` int(11) NOT NULL DEFAULT 0,
  `locked_until` datetime DEFAULT NULL,
  `newsletter_subscribed` tinyint(1) NOT NULL DEFAULT 0,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `customer_addresses`
--

CREATE TABLE `customer_addresses` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `label` varchar(50) DEFAULT 'Casa' COMMENT 'Ej: Casa, Oficina, etc.',
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `city` varchar(100) NOT NULL,
  `neighborhood` varchar(150) DEFAULT NULL,
  `address` varchar(255) NOT NULL,
  `address_detail` varchar(255) DEFAULT NULL COMMENT 'Apto, piso, torre, etc.',
  `postal_code` varchar(20) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `address_type` enum('shipping','billing','both') NOT NULL DEFAULT 'both',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `customer_notifications`
--

CREATE TABLE `customer_notifications` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `type` enum('order','payment','shipping','promotion','system','review') NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `link` varchar(500) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `read_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `customer_sessions`
--

CREATE TABLE `customer_sessions` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `session_token` varchar(255) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `daily_sales_summary`
--

CREATE TABLE `daily_sales_summary` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `total_orders` int(11) NOT NULL DEFAULT 0,
  `total_items_sold` int(11) NOT NULL DEFAULT 0,
  `gross_sales` decimal(14,2) NOT NULL DEFAULT 0.00,
  `discount_total` decimal(14,2) NOT NULL DEFAULT 0.00,
  `shipping_total` decimal(14,2) NOT NULL DEFAULT 0.00,
  `tax_total` decimal(14,2) NOT NULL DEFAULT 0.00,
  `net_sales` decimal(14,2) NOT NULL DEFAULT 0.00,
  `refund_total` decimal(14,2) NOT NULL DEFAULT 0.00,
  `new_customers` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `departamentos`
--

CREATE TABLE `departamentos` (
  `id_departamento` int(10) UNSIGNED NOT NULL,
  `departamento` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `email_history`
--

CREATE TABLE `email_history` (
  `id` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `recipients_count` int(11) NOT NULL,
  `successful_count` int(11) NOT NULL,
  `failed_count` int(11) NOT NULL,
  `sent_by` varchar(100) NOT NULL,
  `sent_from` varchar(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `email_recipients`
--

CREATE TABLE `email_recipients` (
  `id` int(11) NOT NULL,
  `email_id` int(11) NOT NULL,
  `recipient_email` varchar(255) NOT NULL,
  `recipient_name` varchar(255) NOT NULL,
  `status` enum('success','failed') NOT NULL,
  `error_message` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `email_templates`
--

CREATE TABLE `email_templates` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `content` mediumtext NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `epayco_config`
--

CREATE TABLE `epayco_config` (
  `id` int(11) NOT NULL,
  `public_key` varchar(255) NOT NULL,
  `private_key` varchar(255) NOT NULL,
  `p_cust_id_cliente` varchar(50) NOT NULL COMMENT 'ID del cliente en ePayco',
  `p_key` varchar(255) NOT NULL COMMENT 'P_Key de ePayco (checksum)',
  `test_mode` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=Pruebas, 0=Producci??n',
  `lang` varchar(5) NOT NULL DEFAULT 'es',
  `currency` varchar(5) NOT NULL DEFAULT 'COP',
  `country` varchar(5) NOT NULL DEFAULT 'CO',
  `response_url` varchar(500) DEFAULT NULL COMMENT 'URL de respuesta tras pago',
  `confirmation_url` varchar(500) DEFAULT NULL COMMENT 'URL de confirmaci??n (webhook)',
  `checkout_type` enum('onpage','standard') NOT NULL DEFAULT 'standard',
  `auto_return` tinyint(1) NOT NULL DEFAULT 1,
  `enabled` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `epayco_transactions`
--

CREATE TABLE `epayco_transactions` (
  `id` int(11) NOT NULL,
  `payment_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `x_ref_payco` varchar(50) DEFAULT NULL COMMENT 'Referencia ePayco',
  `x_transaction_id` varchar(50) DEFAULT NULL COMMENT 'ID transacci??n ePayco',
  `x_response` varchar(50) DEFAULT NULL COMMENT 'Respuesta: Aceptada, Rechazada, Pendiente',
  `x_response_reason_text` varchar(255) DEFAULT NULL,
  `x_cod_response` varchar(10) DEFAULT NULL COMMENT 'C??digo de respuesta ePayco',
  `x_cod_transaction_state` varchar(10) DEFAULT NULL COMMENT 'Estado: 1=Aceptada, 2=Rechazada, 3=Pendiente, 4=Fallida',
  `x_franchise` varchar(30) DEFAULT NULL,
  `x_bank_name` varchar(100) DEFAULT NULL,
  `x_approval_code` varchar(50) DEFAULT NULL,
  `x_amount` decimal(12,2) DEFAULT NULL,
  `x_amount_base` decimal(12,2) DEFAULT NULL COMMENT 'Base antes de IVA',
  `x_tax` decimal(12,2) DEFAULT NULL COMMENT 'Valor del IVA',
  `x_currency_code` varchar(5) DEFAULT NULL,
  `x_description` varchar(255) DEFAULT NULL,
  `x_customer_email` varchar(255) DEFAULT NULL,
  `x_customer_name` varchar(200) DEFAULT NULL,
  `x_customer_doctype` varchar(10) DEFAULT NULL,
  `x_customer_document` varchar(20) DEFAULT NULL,
  `x_customer_phone` varchar(20) DEFAULT NULL,
  `x_customer_ip` varchar(45) DEFAULT NULL,
  `x_signature` varchar(255) DEFAULT NULL COMMENT 'Firma para validaci??n',
  `x_test_request` varchar(5) DEFAULT NULL COMMENT 'TRUE o FALSE',
  `x_extra1` varchar(255) DEFAULT NULL,
  `x_extra2` varchar(255) DEFAULT NULL,
  `x_extra3` varchar(255) DEFAULT NULL,
  `raw_request` text DEFAULT NULL COMMENT 'Datos enviados (JSON)',
  `raw_response` text DEFAULT NULL COMMENT 'Respuesta completa (JSON)',
  `raw_confirmation` text DEFAULT NULL COMMENT 'Datos del webhook (JSON)',
  `event_type` enum('checkout','response','confirmation') NOT NULL DEFAULT 'checkout',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_correos`
--

CREATE TABLE `historial_correos` (
  `id` int(11) NOT NULL,
  `destinatario` varchar(255) NOT NULL,
  `cc` varchar(255) DEFAULT NULL,
  `asunto` varchar(255) NOT NULL,
  `mensaje` text NOT NULL,
  `estado` varchar(50) NOT NULL,
  `fecha` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventory_log`
--

CREATE TABLE `inventory_log` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `variant_id` int(11) DEFAULT NULL,
  `type` enum('purchase','sale','return','adjustment','transfer') NOT NULL,
  `quantity_change` int(11) NOT NULL COMMENT 'Positivo=entrada, Negativo=salida',
  `stock_before` int(11) NOT NULL,
  `stock_after` int(11) NOT NULL,
  `reference_type` varchar(50) DEFAULT NULL COMMENT 'order, return, manual, etc.',
  `reference_id` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `municipios`
--

CREATE TABLE `municipios` (
  `id_municipio` int(10) UNSIGNED NOT NULL,
  `municipio` varchar(255) NOT NULL DEFAULT '',
  `estado` int(10) UNSIGNED NOT NULL,
  `departamento_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `newsletter_subscribers`
--

CREATE TABLE `newsletter_subscribers` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `name` varchar(200) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `confirmation_token` varchar(255) DEFAULT NULL,
  `confirmed_at` datetime DEFAULT NULL,
  `unsubscribed_at` datetime DEFAULT NULL,
  `source` varchar(50) DEFAULT 'website',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `order_number` varchar(30) NOT NULL COMMENT 'N??mero de pedido legible',
  `customer_id` int(11) DEFAULT NULL,
  `customer_email` varchar(255) NOT NULL,
  `customer_name` varchar(200) NOT NULL,
  `customer_phone` varchar(20) DEFAULT NULL,
  `customer_document_type` varchar(10) DEFAULT NULL,
  `customer_document_number` varchar(20) DEFAULT NULL,
  `shipping_first_name` varchar(100) DEFAULT NULL,
  `shipping_last_name` varchar(100) DEFAULT NULL,
  `shipping_phone` varchar(20) DEFAULT NULL,
  `shipping_department` varchar(100) DEFAULT NULL,
  `shipping_city` varchar(100) DEFAULT NULL,
  `shipping_neighborhood` varchar(150) DEFAULT NULL,
  `shipping_address` varchar(255) DEFAULT NULL,
  `shipping_address_detail` varchar(255) DEFAULT NULL,
  `shipping_postal_code` varchar(20) DEFAULT NULL,
  `billing_first_name` varchar(100) DEFAULT NULL,
  `billing_last_name` varchar(100) DEFAULT NULL,
  `billing_phone` varchar(20) DEFAULT NULL,
  `billing_department` varchar(100) DEFAULT NULL,
  `billing_city` varchar(100) DEFAULT NULL,
  `billing_address` varchar(255) DEFAULT NULL,
  `billing_postal_code` varchar(20) DEFAULT NULL,
  `subtotal` decimal(12,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `shipping_cost` decimal(12,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `currency` varchar(5) NOT NULL DEFAULT 'COP',
  `status` enum('pending','confirmed','processing','shipped','delivered','completed','cancelled','refunded','failed') NOT NULL DEFAULT 'pending',
  `payment_status` enum('pending','paid','partially_refunded','refunded','failed','expired') NOT NULL DEFAULT 'pending',
  `shipping_status` enum('pending','preparing','shipped','in_transit','delivered','returned') NOT NULL DEFAULT 'pending',
  `shipping_method_id` int(11) DEFAULT NULL,
  `shipping_method_name` varchar(100) DEFAULT NULL,
  `tracking_number` varchar(100) DEFAULT NULL,
  `tracking_url` varchar(500) DEFAULT NULL,
  `estimated_delivery` date DEFAULT NULL,
  `shipped_at` datetime DEFAULT NULL,
  `delivered_at` datetime DEFAULT NULL,
  `coupon_id` int(11) DEFAULT NULL,
  `coupon_code` varchar(50) DEFAULT NULL,
  `customer_notes` text DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `cancelled_at` datetime DEFAULT NULL,
  `cancellation_reason` text DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `variant_id` int(11) DEFAULT NULL,
  `product_name` varchar(255) NOT NULL COMMENT 'Nombre al momento de la compra',
  `variant_name` varchar(255) DEFAULT NULL,
  `sku` varchar(80) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(12,2) NOT NULL,
  `discount_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `tax_rate` decimal(5,2) DEFAULT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  `total` decimal(12,2) NOT NULL,
  `product_image` varchar(255) DEFAULT NULL,
  `product_options` text DEFAULT NULL COMMENT 'Atributos seleccionados (JSON)',
  `is_reviewed` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `order_status_history`
--

CREATE TABLE `order_status_history` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `old_status` varchar(30) DEFAULT NULL,
  `new_status` varchar(30) NOT NULL,
  `comment` text DEFAULT NULL,
  `notify_customer` tinyint(1) NOT NULL DEFAULT 0,
  `changed_by` varchar(100) DEFAULT NULL COMMENT 'Admin o sistema',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pages`
--

CREATE TABLE `pages` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(280) NOT NULL,
  `content` longtext DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` varchar(500) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `payment_method` varchar(50) NOT NULL COMMENT 'Ej: epayco_card, epayco_pse, epayco_cash, cod',
  `gateway` varchar(30) NOT NULL DEFAULT 'epayco',
  `amount` decimal(12,2) NOT NULL,
  `currency` varchar(5) NOT NULL DEFAULT 'COP',
  `status` enum('pending','approved','rejected','expired','cancelled','refunded','partially_refunded','error') NOT NULL DEFAULT 'pending',
  `gateway_transaction_id` varchar(100) DEFAULT NULL COMMENT 'ID de transacci??n en ePayco',
  `gateway_reference` varchar(100) DEFAULT NULL COMMENT 'Referencia del gateway',
  `authorization_code` varchar(50) DEFAULT NULL,
  `franchise` varchar(30) DEFAULT NULL COMMENT 'Ej: Visa, Mastercard, PSE',
  `bank_name` varchar(100) DEFAULT NULL,
  `response_code` varchar(20) DEFAULT NULL,
  `response_message` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `refund_amount` decimal(12,2) DEFAULT 0.00,
  `refund_reason` text DEFAULT NULL,
  `refunded_at` datetime DEFAULT NULL,
  `paid_at` datetime DEFAULT NULL,
  `metadata` text DEFAULT NULL COMMENT 'Datos adicionales JSON',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `sku` varchar(50) NOT NULL COMMENT 'C??digo ??nico del producto',
  `name` varchar(255) NOT NULL,
  `slug` varchar(280) NOT NULL,
  `description` text DEFAULT NULL,
  `short_description` varchar(500) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `price` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Precio base sin descuento',
  `compare_price` decimal(12,2) DEFAULT NULL COMMENT 'Precio tachado / comparaci??n',
  `cost_price` decimal(12,2) DEFAULT NULL COMMENT 'Costo del producto (interno)',
  `tax_rate` decimal(5,2) DEFAULT NULL COMMENT 'NULL = usa tasa por defecto',
  `tax_included` tinyint(1) DEFAULT NULL COMMENT 'NULL = usa config global',
  `stock` int(11) NOT NULL DEFAULT 0,
  `low_stock_threshold` int(11) DEFAULT NULL,
  `weight` decimal(8,3) DEFAULT NULL COMMENT 'Peso en kg',
  `width` decimal(8,2) DEFAULT NULL COMMENT 'Ancho en cm',
  `height` decimal(8,2) DEFAULT NULL COMMENT 'Alto en cm',
  `length` decimal(8,2) DEFAULT NULL COMMENT 'Largo en cm',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `is_digital` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Producto digital (no requiere env??o)',
  `requires_shipping` tinyint(1) NOT NULL DEFAULT 1,
  `max_purchase_qty` int(11) DEFAULT NULL COMMENT 'M??ximo por compra',
  `min_purchase_qty` int(11) NOT NULL DEFAULT 1,
  `views_count` int(11) NOT NULL DEFAULT 0,
  `sales_count` int(11) NOT NULL DEFAULT 0,
  `avg_rating` decimal(3,2) NOT NULL DEFAULT 0.00,
  `total_reviews` int(11) NOT NULL DEFAULT 0,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` varchar(500) DEFAULT NULL,
  `tags` varchar(500) DEFAULT NULL COMMENT 'Tags separados por coma',
  `created_by` int(11) DEFAULT NULL COMMENT 'ID del admin que lo cre??',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `product_attributes`
--

CREATE TABLE `product_attributes` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL COMMENT 'Ej: Color, Talla, Material',
  `slug` varchar(120) NOT NULL,
  `type` enum('select','color','text') NOT NULL DEFAULT 'select',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `product_attribute_values`
--

CREATE TABLE `product_attribute_values` (
  `id` int(11) NOT NULL,
  `attribute_id` int(11) NOT NULL,
  `value` varchar(100) NOT NULL,
  `color_hex` varchar(7) DEFAULT NULL COMMENT 'Solo para tipo color, ej: #FF0000',
  `sort_order` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `product_images`
--

CREATE TABLE `product_images` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `alt_text` varchar(255) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `product_relations`
--

CREATE TABLE `product_relations` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `related_product_id` int(11) NOT NULL,
  `relation_type` enum('related','upsell','cross_sell') NOT NULL DEFAULT 'related',
  `sort_order` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `product_reviews`
--

CREATE TABLE `product_reviews` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL COMMENT 'Pedido asociado a la rese??a',
  `rating` tinyint(1) NOT NULL COMMENT '1-5 estrellas',
  `title` varchar(255) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `pros` text DEFAULT NULL COMMENT 'Puntos a favor',
  `cons` text DEFAULT NULL COMMENT 'Puntos en contra',
  `is_verified_purchase` tinyint(1) NOT NULL DEFAULT 0,
  `is_approved` tinyint(1) NOT NULL DEFAULT 0,
  `admin_response` text DEFAULT NULL,
  `admin_response_at` datetime DEFAULT NULL,
  `helpful_count` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `product_variants`
--

CREATE TABLE `product_variants` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `sku` varchar(80) NOT NULL,
  `name` varchar(255) DEFAULT NULL COMMENT 'Nombre descriptivo de la variante',
  `price` decimal(12,2) DEFAULT NULL COMMENT 'NULL = usa precio del producto',
  `compare_price` decimal(12,2) DEFAULT NULL,
  `cost_price` decimal(12,2) DEFAULT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `weight` decimal(8,3) DEFAULT NULL,
  `image_id` int(11) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `product_variant_attributes`
--

CREATE TABLE `product_variant_attributes` (
  `id` int(11) NOT NULL,
  `variant_id` int(11) NOT NULL,
  `attribute_id` int(11) NOT NULL,
  `attribute_value_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `returns`
--

CREATE TABLE `returns` (
  `id` int(11) NOT NULL,
  `return_number` varchar(30) NOT NULL,
  `order_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `reason` enum('defective','wrong_item','not_as_described','no_longer_needed','other') NOT NULL,
  `reason_detail` text DEFAULT NULL,
  `status` enum('requested','approved','rejected','received','refunded','closed') NOT NULL DEFAULT 'requested',
  `refund_amount` decimal(12,2) DEFAULT NULL,
  `refund_method` enum('original_payment','store_credit','bank_transfer') DEFAULT 'original_payment',
  `admin_notes` text DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `received_at` datetime DEFAULT NULL,
  `refunded_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `return_items`
--

CREATE TABLE `return_items` (
  `id` int(11) NOT NULL,
  `return_id` int(11) NOT NULL,
  `order_item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `condition_received` enum('new','used','damaged') DEFAULT NULL,
  `restock` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sedes`
--

CREATE TABLE `sedes` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `creado_por` varchar(255) NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sms_credentials`
--

CREATE TABLE `sms_credentials` (
  `id` int(11) NOT NULL,
  `apiKey` varchar(255) NOT NULL,
  `apiSecret` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sms_logs`
--

CREATE TABLE `sms_logs` (
  `id` int(11) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `message` varchar(160) NOT NULL,
  `sender` varchar(100) NOT NULL,
  `sent_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `smtpconfig`
--

CREATE TABLE `smtpconfig` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `host` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `port` int(11) NOT NULL,
  `dependence` text NOT NULL,
  `Subject` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tutoriales`
--

CREATE TABLE `tutoriales` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `modulo` varchar(255) NOT NULL,
  `link` text NOT NULL,
  `descripcion` text NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` int(11) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nombre` text NOT NULL,
  `rol` int(2) NOT NULL,
  `rol_informativo` int(11) NOT NULL,
  `extra_rol` int(2) NOT NULL,
  `foto` varchar(255) NOT NULL,
  `orden` int(11) NOT NULL,
  `fechaCreacionUser` varchar(15) NOT NULL,
  `email` varchar(255) NOT NULL,
  `genero` text NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `direccion` varchar(255) NOT NULL,
  `edad` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_register`
--

CREATE TABLE `user_register` (
  `id` int(11) NOT NULL,
  `typeID` varchar(30) NOT NULL,
  `number_id` bigint(50) NOT NULL,
  `number_id_very` varchar(15) NOT NULL,
  `first_name` varchar(25) NOT NULL,
  `second_name` varchar(25) NOT NULL,
  `first_last` varchar(25) NOT NULL,
  `second_last` varchar(25) NOT NULL,
  `birthdate` date NOT NULL,
  `expedition_date` date NOT NULL,
  `gender` mediumtext NOT NULL,
  `marital_status` mediumtext NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_very` varchar(255) NOT NULL,
  `first_phone` varchar(15) NOT NULL,
  `second_phone` varchar(15) NOT NULL,
  `password` varchar(25) NOT NULL,
  `emergency_contact_name` varchar(150) NOT NULL,
  `emergency_contact_number` varchar(15) NOT NULL,
  `nationality` mediumtext NOT NULL,
  `department` varchar(50) NOT NULL,
  `municipality` varchar(50) NOT NULL,
  `address` varchar(255) NOT NULL,
  `latitud` varchar(50) NOT NULL,
  `longitud` varchar(50) NOT NULL,
  `people_charge` int(2) NOT NULL,
  `vulnerable_population` varchar(2) NOT NULL,
  `vulnerable_type` varchar(50) NOT NULL,
  `ethnic_group` varchar(255) NOT NULL,
  `stratum` int(1) NOT NULL,
  `residence_area` mediumtext NOT NULL,
  `training_level` varchar(50) NOT NULL,
  `occupation` mediumtext NOT NULL,
  `time_obligations` varchar(50) NOT NULL,
  `motivations_belong_program` varchar(255) NOT NULL,
  `current_situation` varchar(255) NOT NULL,
  `impediment_complete_course` varchar(70) NOT NULL,
  `availability` mediumtext NOT NULL,
  `mode` varchar(50) NOT NULL,
  `headquarters` varchar(255) NOT NULL,
  `program` varchar(50) NOT NULL,
  `schedules` varchar(255) NOT NULL,
  `schedules_alternative` varchar(255) NOT NULL,
  `prior_knowledge` varchar(2) NOT NULL,
  `level` varchar(50) NOT NULL,
  `languages` varchar(25) NOT NULL,
  `languages_level` varchar(25) NOT NULL,
  `medical_condition` varchar(2) NOT NULL,
  `disability` varchar(2) NOT NULL,
  `type_disability` varchar(120) NOT NULL,
  `pregnancy` varchar(2) NOT NULL,
  `country_person` varchar(2) NOT NULL,
  `technologies` varchar(25) NOT NULL,
  `internet` varchar(2) NOT NULL,
  `knowledge_program` varchar(255) NOT NULL,
  `accept_requirements` varchar(2) NOT NULL,
  `accepts_tech_talent` varchar(2) NOT NULL,
  `accept_data_policies` varchar(2) NOT NULL,
  `file_front_id` varchar(255) NOT NULL,
  `file_back_id` varchar(255) NOT NULL,
  `status` int(1) NOT NULL,
  `statusAdmin` int(1) NOT NULL,
  `lote` int(1) NOT NULL,
  `directed_base` int(1) NOT NULL,
  `idCourse` int(5) NOT NULL,
  `contactMedium` mediumtext NOT NULL,
  `institution` varchar(255) NOT NULL,
  `creationDate` datetime NOT NULL,
  `dayUpdate` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `company`
--
ALTER TABLE `company`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `departamentos`
--
ALTER TABLE `departamentos`
  ADD PRIMARY KEY (`id_departamento`);

--
-- Indices de la tabla `email_history`
--
ALTER TABLE `email_history`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `email_recipients`
--
ALTER TABLE `email_recipients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email_id` (`email_id`);

--
-- Indices de la tabla `email_templates`
--
ALTER TABLE `email_templates`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `historial_correos`
--
ALTER TABLE `historial_correos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `municipios`
--
ALTER TABLE `municipios`
  ADD PRIMARY KEY (`id_municipio`),
  ADD KEY `departamento_id` (`departamento_id`);

--
-- Indices de la tabla `sms_credentials`
--
ALTER TABLE `sms_credentials`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `sms_logs`
--
ALTER TABLE `sms_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `smtpconfig`
--
ALTER TABLE `smtpconfig`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tutoriales`
--
ALTER TABLE `tutoriales`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`username`),
  ADD KEY `id` (`id`);

--
-- Indices de la tabla `user_register`
--
ALTER TABLE `user_register`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_register_lote_number_headquarters` (`lote`,`number_id`,`headquarters`),
  ADD KEY `idx_user_register_institution` (`institution`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `company`
--
ALTER TABLE `company`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `departamentos`
--
ALTER TABLE `departamentos`
  MODIFY `id_departamento` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `email_history`
--
ALTER TABLE `email_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `email_recipients`
--
ALTER TABLE `email_recipients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `email_templates`
--
ALTER TABLE `email_templates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `historial_correos`
--
ALTER TABLE `historial_correos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `municipios`
--
ALTER TABLE `municipios`
  MODIFY `id_municipio` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sms_credentials`
--
ALTER TABLE `sms_credentials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sms_logs`
--
ALTER TABLE `sms_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `smtpconfig`
--
ALTER TABLE `smtpconfig`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tutoriales`
--
ALTER TABLE `tutoriales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `user_register`
--
ALTER TABLE `user_register`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `email_recipients`
--
ALTER TABLE `email_recipients`
  ADD CONSTRAINT `email_recipients_ibfk_1` FOREIGN KEY (`email_id`) REFERENCES `email_history` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
