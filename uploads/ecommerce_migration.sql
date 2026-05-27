-- =====================================================
-- MIGRACIÓN E-COMMERCE - tienda_virtual
-- Pasarela de pagos: ePayco
-- Fecha: 2026-04-17
-- =====================================================

USE `tienda_virtual`;

-- =====================================================
-- 1. CONFIGURACIÓN DE LA TIENDA
-- =====================================================

CREATE TABLE IF NOT EXISTS `store_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_group` varchar(50) NOT NULL DEFAULT 'general',
  `description` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `store_settings` (`setting_key`, `setting_value`, `setting_group`, `description`) VALUES
('store_name', 'Tienda Virtual', 'general', 'Nombre de la tienda'),
('store_description', '', 'general', 'Descripción de la tienda'),
('store_email', '', 'general', 'Email principal de la tienda'),
('store_phone', '', 'general', 'Teléfono de la tienda'),
('store_address', '', 'general', 'Dirección física de la tienda'),
('store_city', '', 'general', 'Ciudad de la tienda'),
('store_country', 'CO', 'general', 'País de la tienda (código ISO)'),
('currency', 'COP', 'general', 'Moneda principal'),
('currency_symbol', '$', 'general', 'Símbolo de la moneda'),
('tax_included', '1', 'tax', '¿Precios incluyen impuesto? 1=Sí, 0=No'),
('default_tax_rate', '19.00', 'tax', 'Tasa de IVA por defecto (%)'),
('min_order_amount', '0', 'orders', 'Monto mínimo de pedido'),
('max_order_items', '50', 'orders', 'Máximo de ítems por pedido'),
('allow_guest_checkout', '0', 'checkout', 'Permitir compra sin registro 1=Sí, 0=No'),
('stock_management', '1', 'inventory', 'Control de inventario activo 1=Sí, 0=No'),
('low_stock_threshold', '5', 'inventory', 'Umbral de stock bajo'),
('items_per_page', '12', 'catalog', 'Productos por página en catálogo'),
('enable_reviews', '1', 'catalog', 'Habilitar reseñas de productos'),
('review_moderation', '1', 'catalog', 'Moderar reseñas antes de publicar'),
('maintenance_mode', '0', 'general', 'Modo mantenimiento 1=Activo, 0=Inactivo');

-- =====================================================
-- 2. CONFIGURACIÓN ePAYCO
-- =====================================================

CREATE TABLE IF NOT EXISTS `epayco_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `public_key` varchar(255) NOT NULL,
  `private_key` varchar(255) NOT NULL,
  `p_cust_id_cliente` varchar(50) NOT NULL COMMENT 'ID del cliente en ePayco',
  `p_key` varchar(255) NOT NULL COMMENT 'P_Key de ePayco (checksum)',
  `test_mode` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=Pruebas, 0=Producción',
  `lang` varchar(5) NOT NULL DEFAULT 'es',
  `currency` varchar(5) NOT NULL DEFAULT 'COP',
  `country` varchar(5) NOT NULL DEFAULT 'CO',
  `response_url` varchar(500) DEFAULT NULL COMMENT 'URL de respuesta tras pago',
  `confirmation_url` varchar(500) DEFAULT NULL COMMENT 'URL de confirmación (webhook)',
  `checkout_type` enum('onpage','standard') NOT NULL DEFAULT 'standard',
  `auto_return` tinyint(1) NOT NULL DEFAULT 1,
  `enabled` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `epayco_config` (`public_key`, `private_key`, `p_cust_id_cliente`, `p_key`, `test_mode`) VALUES
('', '', '', '', 1);

-- =====================================================
-- 3. CLIENTES / COMPRADORES
-- =====================================================

CREATE TABLE IF NOT EXISTS `customers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_customer_email` (`email`),
  KEY `idx_customer_document` (`document_type`, `document_number`),
  KEY `idx_customer_active` (`is_active`),
  KEY `idx_customer_name` (`first_name`, `last_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `customer_addresses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_customer_address` (`customer_id`),
  CONSTRAINT `fk_address_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `customer_sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `session_token` varchar(255) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_session_customer` (`customer_id`),
  KEY `idx_session_token` (`session_token`),
  CONSTRAINT `fk_session_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 4. CATEGORÍAS Y MARCAS
-- =====================================================

CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL COMMENT 'NULL = categoría raíz',
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_category_slug` (`slug`),
  KEY `idx_category_parent` (`parent_id`),
  KEY `idx_category_active` (`is_active`),
  CONSTRAINT `fk_category_parent` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `brands` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `description` text DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_brand_slug` (`slug`),
  KEY `idx_brand_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 5. PRODUCTOS
-- =====================================================

CREATE TABLE IF NOT EXISTS `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sku` varchar(50) NOT NULL COMMENT 'Código único del producto',
  `name` varchar(255) NOT NULL,
  `slug` varchar(280) NOT NULL,
  `description` text DEFAULT NULL,
  `short_description` varchar(500) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `price` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Precio base sin descuento',
  `compare_price` decimal(12,2) DEFAULT NULL COMMENT 'Precio tachado / comparación',
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
  `is_digital` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Producto digital (no requiere envío)',
  `requires_shipping` tinyint(1) NOT NULL DEFAULT 1,
  `max_purchase_qty` int(11) DEFAULT NULL COMMENT 'Máximo por compra',
  `min_purchase_qty` int(11) NOT NULL DEFAULT 1,
  `views_count` int(11) NOT NULL DEFAULT 0,
  `sales_count` int(11) NOT NULL DEFAULT 0,
  `avg_rating` decimal(3,2) NOT NULL DEFAULT 0.00,
  `total_reviews` int(11) NOT NULL DEFAULT 0,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` varchar(500) DEFAULT NULL,
  `tags` varchar(500) DEFAULT NULL COMMENT 'Tags separados por coma',
  `created_by` int(11) DEFAULT NULL COMMENT 'ID del admin que lo creó',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_product_sku` (`sku`),
  UNIQUE KEY `uk_product_slug` (`slug`),
  KEY `idx_product_category` (`category_id`),
  KEY `idx_product_brand` (`brand_id`),
  KEY `idx_product_active` (`is_active`),
  KEY `idx_product_featured` (`is_featured`),
  KEY `idx_product_price` (`price`),
  KEY `idx_product_sales` (`sales_count`),
  FULLTEXT KEY `ft_product_search` (`name`, `description`, `short_description`, `tags`),
  CONSTRAINT `fk_product_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_product_brand` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `product_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `alt_text` varchar(255) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_image_product` (`product_id`),
  CONSTRAINT `fk_image_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 6. ATRIBUTOS Y VARIANTES DE PRODUCTO
-- =====================================================

CREATE TABLE IF NOT EXISTS `product_attributes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT 'Ej: Color, Talla, Material',
  `slug` varchar(120) NOT NULL,
  `type` enum('select','color','text') NOT NULL DEFAULT 'select',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_attribute_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `product_attributes` (`name`, `slug`, `type`) VALUES
('Color', 'color', 'color'),
('Talla', 'talla', 'select'),
('Material', 'material', 'select');

CREATE TABLE IF NOT EXISTS `product_attribute_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `attribute_id` int(11) NOT NULL,
  `value` varchar(100) NOT NULL,
  `color_hex` varchar(7) DEFAULT NULL COMMENT 'Solo para tipo color, ej: #FF0000',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_attrval_attribute` (`attribute_id`),
  CONSTRAINT `fk_attrval_attribute` FOREIGN KEY (`attribute_id`) REFERENCES `product_attributes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `product_variants` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_variant_sku` (`sku`),
  KEY `idx_variant_product` (`product_id`),
  CONSTRAINT `fk_variant_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_variant_image` FOREIGN KEY (`image_id`) REFERENCES `product_images` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `product_variant_attributes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `variant_id` int(11) NOT NULL,
  `attribute_id` int(11) NOT NULL,
  `attribute_value_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_variant_attr` (`variant_id`, `attribute_id`),
  KEY `idx_pva_variant` (`variant_id`),
  KEY `idx_pva_attribute` (`attribute_id`),
  KEY `idx_pva_value` (`attribute_value_id`),
  CONSTRAINT `fk_pva_variant` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pva_attribute` FOREIGN KEY (`attribute_id`) REFERENCES `product_attributes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pva_value` FOREIGN KEY (`attribute_value_id`) REFERENCES `product_attribute_values` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 7. RESEÑAS DE PRODUCTOS
-- =====================================================

CREATE TABLE IF NOT EXISTS `product_reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL COMMENT 'Pedido asociado a la reseña',
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_review_product` (`product_id`),
  KEY `idx_review_customer` (`customer_id`),
  KEY `idx_review_approved` (`is_approved`),
  CONSTRAINT `fk_review_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_review_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 8. CARRITO DE COMPRAS
-- =====================================================

CREATE TABLE IF NOT EXISTS `carts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) DEFAULT NULL COMMENT 'NULL para carritos de invitados',
  `session_id` varchar(255) DEFAULT NULL COMMENT 'Para invitados no logueados',
  `coupon_id` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_cart_customer` (`customer_id`),
  KEY `idx_cart_session` (`session_id`),
  CONSTRAINT `fk_cart_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `cart_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cart_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `variant_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `unit_price` decimal(12,2) NOT NULL COMMENT 'Precio al momento de agregar',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_cart_product_variant` (`cart_id`, `product_id`, `variant_id`),
  KEY `idx_cartitem_cart` (`cart_id`),
  KEY `idx_cartitem_product` (`product_id`),
  CONSTRAINT `fk_cartitem_cart` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_cartitem_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_cartitem_variant` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 9. LISTA DE DESEOS
-- =====================================================

CREATE TABLE IF NOT EXISTS `wishlists` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `variant_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_wishlist_item` (`customer_id`, `product_id`, `variant_id`),
  KEY `idx_wishlist_customer` (`customer_id`),
  KEY `idx_wishlist_product` (`product_id`),
  CONSTRAINT `fk_wishlist_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_wishlist_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 10. CUPONES DE DESCUENTO
-- =====================================================

CREATE TABLE IF NOT EXISTS `coupons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `type` enum('percentage','fixed_cart','fixed_product','free_shipping') NOT NULL DEFAULT 'percentage',
  `value` decimal(12,2) NOT NULL COMMENT 'Porcentaje o monto fijo',
  `min_order_amount` decimal(12,2) DEFAULT NULL COMMENT 'Monto mínimo de pedido',
  `max_discount_amount` decimal(12,2) DEFAULT NULL COMMENT 'Descuento máximo (para %)',
  `usage_limit` int(11) DEFAULT NULL COMMENT 'NULL = sin límite',
  `usage_limit_per_customer` int(11) DEFAULT 1,
  `used_count` int(11) NOT NULL DEFAULT 0,
  `applies_to` enum('all','categories','products') NOT NULL DEFAULT 'all',
  `applicable_ids` text DEFAULT NULL COMMENT 'IDs de categorías o productos (JSON)',
  `exclude_sale_items` tinyint(1) NOT NULL DEFAULT 0,
  `starts_at` datetime DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_coupon_code` (`code`),
  KEY `idx_coupon_active` (`is_active`),
  KEY `idx_coupon_dates` (`starts_at`, `expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `coupon_usage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `coupon_id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL,
  `discount_amount` decimal(12,2) NOT NULL,
  `used_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_couponuse_coupon` (`coupon_id`),
  KEY `idx_couponuse_customer` (`customer_id`),
  CONSTRAINT `fk_couponuse_coupon` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_couponuse_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 11. ENVÍOS
-- =====================================================

CREATE TABLE IF NOT EXISTS `shipping_methods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `carrier` varchar(100) DEFAULT NULL COMMENT 'Ej: Servientrega, Coordinadora, etc.',
  `base_cost` decimal(12,2) NOT NULL DEFAULT 0.00,
  `cost_per_kg` decimal(12,2) DEFAULT 0.00 COMMENT 'Costo adicional por kg',
  `free_shipping_threshold` decimal(12,2) DEFAULT NULL COMMENT 'Envío gratis sobre este monto',
  `estimated_days_min` int(11) DEFAULT NULL,
  `estimated_days_max` int(11) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_shipping_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `shipping_methods` (`name`, `slug`, `description`, `base_cost`, `estimated_days_min`, `estimated_days_max`) VALUES
('Envío estándar', 'envio-estandar', 'Entrega en 3-5 días hábiles', 8000.00, 3, 5),
('Envío express', 'envio-express', 'Entrega en 1-2 días hábiles', 15000.00, 1, 2),
('Recogida en tienda', 'recogida-tienda', 'Recoge tu pedido en nuestra tienda', 0.00, 0, 1);

CREATE TABLE IF NOT EXISTS `shipping_zones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `departments` text DEFAULT NULL COMMENT 'Departamentos cubiertos (JSON)',
  `cities` text DEFAULT NULL COMMENT 'Ciudades específicas (JSON)',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `shipping_rates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shipping_method_id` int(11) NOT NULL,
  `shipping_zone_id` int(11) NOT NULL,
  `cost` decimal(12,2) NOT NULL,
  `cost_per_kg` decimal(12,2) DEFAULT 0.00,
  `free_shipping_threshold` decimal(12,2) DEFAULT NULL,
  `estimated_days_min` int(11) DEFAULT NULL,
  `estimated_days_max` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_rate_method_zone` (`shipping_method_id`, `shipping_zone_id`),
  CONSTRAINT `fk_rate_method` FOREIGN KEY (`shipping_method_id`) REFERENCES `shipping_methods` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_rate_zone` FOREIGN KEY (`shipping_zone_id`) REFERENCES `shipping_zones` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 12. PEDIDOS / ÓRDENES
-- =====================================================

CREATE TABLE IF NOT EXISTS `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_number` varchar(30) NOT NULL COMMENT 'Número de pedido legible',
  `customer_id` int(11) DEFAULT NULL,
  `customer_email` varchar(255) NOT NULL,
  `customer_name` varchar(200) NOT NULL,
  `customer_phone` varchar(20) DEFAULT NULL,
  `customer_document_type` varchar(10) DEFAULT NULL,
  `customer_document_number` varchar(20) DEFAULT NULL,

  -- Dirección de envío
  `shipping_first_name` varchar(100) DEFAULT NULL,
  `shipping_last_name` varchar(100) DEFAULT NULL,
  `shipping_phone` varchar(20) DEFAULT NULL,
  `shipping_department` varchar(100) DEFAULT NULL,
  `shipping_city` varchar(100) DEFAULT NULL,
  `shipping_neighborhood` varchar(150) DEFAULT NULL,
  `shipping_address` varchar(255) DEFAULT NULL,
  `shipping_address_detail` varchar(255) DEFAULT NULL,
  `shipping_postal_code` varchar(20) DEFAULT NULL,

  -- Dirección de facturación
  `billing_first_name` varchar(100) DEFAULT NULL,
  `billing_last_name` varchar(100) DEFAULT NULL,
  `billing_phone` varchar(20) DEFAULT NULL,
  `billing_department` varchar(100) DEFAULT NULL,
  `billing_city` varchar(100) DEFAULT NULL,
  `billing_address` varchar(255) DEFAULT NULL,
  `billing_postal_code` varchar(20) DEFAULT NULL,

  -- Montos
  `subtotal` decimal(12,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `shipping_cost` decimal(12,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `currency` varchar(5) NOT NULL DEFAULT 'COP',

  -- Estado
  `status` enum('pending','confirmed','processing','shipped','delivered','completed','cancelled','refunded','failed') NOT NULL DEFAULT 'pending',
  `payment_status` enum('pending','paid','partially_refunded','refunded','failed','expired') NOT NULL DEFAULT 'pending',
  `shipping_status` enum('pending','preparing','shipped','in_transit','delivered','returned') NOT NULL DEFAULT 'pending',

  -- Envío
  `shipping_method_id` int(11) DEFAULT NULL,
  `shipping_method_name` varchar(100) DEFAULT NULL,
  `tracking_number` varchar(100) DEFAULT NULL,
  `tracking_url` varchar(500) DEFAULT NULL,
  `estimated_delivery` date DEFAULT NULL,
  `shipped_at` datetime DEFAULT NULL,
  `delivered_at` datetime DEFAULT NULL,

  -- Cupón
  `coupon_id` int(11) DEFAULT NULL,
  `coupon_code` varchar(50) DEFAULT NULL,

  -- Notas
  `customer_notes` text DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,

  -- IP y metadata
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(500) DEFAULT NULL,

  `cancelled_at` datetime DEFAULT NULL,
  `cancellation_reason` text DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_order_number` (`order_number`),
  KEY `idx_order_customer` (`customer_id`),
  KEY `idx_order_status` (`status`),
  KEY `idx_order_payment_status` (`payment_status`),
  KEY `idx_order_date` (`created_at`),
  KEY `idx_order_email` (`customer_email`),
  CONSTRAINT `fk_order_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_order_shipping_method` FOREIGN KEY (`shipping_method_id`) REFERENCES `shipping_methods` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_order_coupon` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_orderitem_order` (`order_id`),
  KEY `idx_orderitem_product` (`product_id`),
  CONSTRAINT `fk_orderitem_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_orderitem_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_orderitem_variant` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `order_status_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `old_status` varchar(30) DEFAULT NULL,
  `new_status` varchar(30) NOT NULL,
  `comment` text DEFAULT NULL,
  `notify_customer` tinyint(1) NOT NULL DEFAULT 0,
  `changed_by` varchar(100) DEFAULT NULL COMMENT 'Admin o sistema',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_osh_order` (`order_id`),
  CONSTRAINT `fk_osh_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 13. PAGOS Y TRANSACCIONES ePAYCO
-- =====================================================

CREATE TABLE IF NOT EXISTS `payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `payment_method` varchar(50) NOT NULL COMMENT 'Ej: epayco_card, epayco_pse, epayco_cash, cod',
  `gateway` varchar(30) NOT NULL DEFAULT 'epayco',
  `amount` decimal(12,2) NOT NULL,
  `currency` varchar(5) NOT NULL DEFAULT 'COP',
  `status` enum('pending','approved','rejected','expired','cancelled','refunded','partially_refunded','error') NOT NULL DEFAULT 'pending',
  `gateway_transaction_id` varchar(100) DEFAULT NULL COMMENT 'ID de transacción en ePayco',
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_payment_order` (`order_id`),
  KEY `idx_payment_customer` (`customer_id`),
  KEY `idx_payment_status` (`status`),
  KEY `idx_payment_gateway_txn` (`gateway_transaction_id`),
  CONSTRAINT `fk_payment_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_payment_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `epayco_transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payment_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,

  -- Datos enviados a ePayco
  `x_ref_payco` varchar(50) DEFAULT NULL COMMENT 'Referencia ePayco',
  `x_transaction_id` varchar(50) DEFAULT NULL COMMENT 'ID transacción ePayco',
  `x_response` varchar(50) DEFAULT NULL COMMENT 'Respuesta: Aceptada, Rechazada, Pendiente',
  `x_response_reason_text` varchar(255) DEFAULT NULL,
  `x_cod_response` varchar(10) DEFAULT NULL COMMENT 'Código de respuesta ePayco',
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
  `x_signature` varchar(255) DEFAULT NULL COMMENT 'Firma para validación',
  `x_test_request` varchar(5) DEFAULT NULL COMMENT 'TRUE o FALSE',
  `x_extra1` varchar(255) DEFAULT NULL,
  `x_extra2` varchar(255) DEFAULT NULL,
  `x_extra3` varchar(255) DEFAULT NULL,

  -- Raw data
  `raw_request` text DEFAULT NULL COMMENT 'Datos enviados (JSON)',
  `raw_response` text DEFAULT NULL COMMENT 'Respuesta completa (JSON)',
  `raw_confirmation` text DEFAULT NULL COMMENT 'Datos del webhook (JSON)',

  `event_type` enum('checkout','response','confirmation') NOT NULL DEFAULT 'checkout',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_epayco_payment` (`payment_id`),
  KEY `idx_epayco_order` (`order_id`),
  KEY `idx_epayco_ref` (`x_ref_payco`),
  KEY `idx_epayco_txn` (`x_transaction_id`),
  KEY `idx_epayco_state` (`x_cod_transaction_state`),
  CONSTRAINT `fk_epayco_payment` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_epayco_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 14. DEVOLUCIONES Y REEMBOLSOS
-- =====================================================

CREATE TABLE IF NOT EXISTS `returns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_return_number` (`return_number`),
  KEY `idx_return_order` (`order_id`),
  KEY `idx_return_customer` (`customer_id`),
  KEY `idx_return_status` (`status`),
  CONSTRAINT `fk_return_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_return_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `return_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `return_id` int(11) NOT NULL,
  `order_item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `condition_received` enum('new','used','damaged') DEFAULT NULL,
  `restock` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_returnitem_return` (`return_id`),
  CONSTRAINT `fk_returnitem_return` FOREIGN KEY (`return_id`) REFERENCES `returns` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_returnitem_orderitem` FOREIGN KEY (`order_item_id`) REFERENCES `order_items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 15. INVENTARIO - LOG DE MOVIMIENTOS
-- =====================================================

CREATE TABLE IF NOT EXISTS `inventory_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_invlog_product` (`product_id`),
  KEY `idx_invlog_variant` (`variant_id`),
  KEY `idx_invlog_type` (`type`),
  KEY `idx_invlog_date` (`created_at`),
  CONSTRAINT `fk_invlog_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_invlog_variant` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 16. BANNERS Y PROMOCIONES
-- =====================================================

CREATE TABLE IF NOT EXISTS `banners` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_banner_active` (`is_active`),
  KEY `idx_banner_position` (`position`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 17. NOTIFICACIONES A CLIENTES
-- =====================================================

CREATE TABLE IF NOT EXISTS `customer_notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `type` enum('order','payment','shipping','promotion','system','review') NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `link` varchar(500) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `read_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_notif_customer` (`customer_id`),
  KEY `idx_notif_read` (`is_read`),
  CONSTRAINT `fk_notif_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 18. IMPUESTOS / TAXES
-- =====================================================

CREATE TABLE IF NOT EXISTS `taxes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `rate` decimal(5,2) NOT NULL COMMENT 'Porcentaje',
  `type` enum('included','excluded') NOT NULL DEFAULT 'included',
  `applies_to` enum('all','category','product') NOT NULL DEFAULT 'all',
  `applicable_id` int(11) DEFAULT NULL COMMENT 'ID de categoría o producto',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `taxes` (`name`, `rate`, `type`, `applies_to`) VALUES
('IVA General', 19.00, 'included', 'all');

-- =====================================================
-- 19. CRÉDITO DE TIENDA (SALDO A FAVOR)
-- =====================================================

CREATE TABLE IF NOT EXISTS `store_credits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `type` enum('credit','debit') NOT NULL,
  `balance_after` decimal(12,2) NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `reference_type` varchar(50) DEFAULT NULL COMMENT 'return, promotion, manual',
  `reference_id` int(11) DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_credit_customer` (`customer_id`),
  CONSTRAINT `fk_credit_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 20. PRODUCTOS RELACIONADOS
-- =====================================================

CREATE TABLE IF NOT EXISTS `product_relations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `related_product_id` int(11) NOT NULL,
  `relation_type` enum('related','upsell','cross_sell') NOT NULL DEFAULT 'related',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_product_relation` (`product_id`, `related_product_id`, `relation_type`),
  CONSTRAINT `fk_relation_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_relation_related` FOREIGN KEY (`related_product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 21. PÁGINAS ESTÁTICAS (Términos, Políticas, etc.)
-- =====================================================

CREATE TABLE IF NOT EXISTS `pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(280) NOT NULL,
  `content` longtext DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` varchar(500) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_page_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `pages` (`title`, `slug`, `content`) VALUES
('Términos y Condiciones', 'terminos-y-condiciones', ''),
('Política de Privacidad', 'politica-de-privacidad', ''),
('Política de Devoluciones', 'politica-de-devoluciones', ''),
('Política de Envíos', 'politica-de-envios', ''),
('Preguntas Frecuentes', 'preguntas-frecuentes', ''),
('Sobre Nosotros', 'sobre-nosotros', '');

-- =====================================================
-- 22. SUSCRIPTORES NEWSLETTER
-- =====================================================

CREATE TABLE IF NOT EXISTS `newsletter_subscribers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `name` varchar(200) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `confirmation_token` varchar(255) DEFAULT NULL,
  `confirmed_at` datetime DEFAULT NULL,
  `unsubscribed_at` datetime DEFAULT NULL,
  `source` varchar(50) DEFAULT 'website',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_subscriber_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 23. LOG DE ACTIVIDAD ADMIN
-- =====================================================

CREATE TABLE IF NOT EXISTS `admin_activity_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL COMMENT 'ID del admin (tabla users)',
  `action` varchar(100) NOT NULL COMMENT 'Ej: product_created, order_updated',
  `entity_type` varchar(50) DEFAULT NULL COMMENT 'Ej: product, order, customer',
  `entity_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `old_values` text DEFAULT NULL COMMENT 'Valores anteriores (JSON)',
  `new_values` text DEFAULT NULL COMMENT 'Valores nuevos (JSON)',
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_actlog_user` (`user_id`),
  KEY `idx_actlog_entity` (`entity_type`, `entity_id`),
  KEY `idx_actlog_date` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 24. CONTACTO / MENSAJES DE CLIENTES
-- =====================================================

CREATE TABLE IF NOT EXISTS `contact_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_contact_status` (`status`),
  KEY `idx_contact_customer` (`customer_id`),
  KEY `idx_contact_date` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 25. REPORTES - RESÚMENES DIARIOS DE VENTAS
-- =====================================================

CREATE TABLE IF NOT EXISTS `daily_sales_summary` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_daily_date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
