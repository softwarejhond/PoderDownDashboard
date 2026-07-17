-- Tabla de auditoría: registra quién confirmó cada envío desde el panel admin
-- Ejecutar sobre la base de datos tienda_virtual / poderdow_3C0M3rs

CREATE TABLE IF NOT EXISTS `order_shipment_audit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `shipment_id` int(11) NOT NULL,
  `admin_username` varchar(100) NOT NULL,
  `action` varchar(50) NOT NULL DEFAULT 'ship_confirmed',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_sa_order` (`order_id`),
  KEY `idx_sa_admin` (`admin_username`),
  CONSTRAINT `fk_sa_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  CONSTRAINT `fk_sa_shipment` FOREIGN KEY (`shipment_id`) REFERENCES `order_shipments` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
