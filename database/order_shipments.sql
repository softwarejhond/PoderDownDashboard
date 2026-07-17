-- Tabla de envíos de pedidos (registrada desde el panel admin)
-- Ejecutar sobre la base de datos tienda_virtual / poderdow_3C0M3rs

CREATE TABLE IF NOT EXISTS `order_shipments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `carrier` varchar(100) NOT NULL,
  `tracking_number` varchar(100) NOT NULL,
  `notes` text DEFAULT NULL,
  `shipped_by` varchar(100) DEFAULT NULL,
  `email_sent` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_order_shipments_order` (`order_id`),
  CONSTRAINT `fk_order_shipments_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
