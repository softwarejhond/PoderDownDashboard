-- ============================================================
-- Script: Poblar atributos y valores para el catálogo
-- Ejecutar después de importar la BD base
-- ============================================================

-- Atributo "Corte" (nuevo)
INSERT INTO product_attributes (name, slug, type, sort_order) VALUES
('Corte', 'corte', 'select', 3);

-- Valores para "Talla" (attribute_id = 2)
INSERT INTO product_attribute_values (attribute_id, value, sort_order) VALUES
(2, 'S', 0),
(2, 'M', 1),
(2, 'L', 2);

-- Valores para "Color" (attribute_id = 1) — lapiceros
INSERT INTO product_attribute_values (attribute_id, value, color_hex, sort_order) VALUES
(1, 'Negro',  '#000000', 0),
(1, 'Azul',   '#0000FF', 1),
(1, 'Amarillo','#FFFF00', 2),
(1, 'Rojo',   '#FF0000', 3),
(1, 'Rosado', '#FFC0CB', 4);

-- Valores para "Corte" (attribute_id = 4)
INSERT INTO product_attribute_values (attribute_id, value, sort_order) VALUES
(4, 'Hombre', 0),
(4, 'Mujer',  1);
