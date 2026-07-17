-- ============================================================
-- Script: Poblar productos, categorías, variantes y stock
-- Ejecutar después de seed_atributos.sql
-- ============================================================

-- ------------------------------------------------------------
-- 1. ASEGURAR VALORES DE ATRIBUTOS (por si no existen)
-- ------------------------------------------------------------
INSERT IGNORE INTO product_attributes (name, slug, type, sort_order) VALUES
('Corte', 'corte', 'select', 3);

INSERT IGNORE INTO product_attribute_values (attribute_id, value, color_hex, sort_order)
SELECT a.id, v.value, v.color_hex, v.sort_order
FROM product_attributes a
CROSS JOIN (
    SELECT 'S' AS value, NULL AS color_hex, 0 AS sort_order, 'talla' AS attr
    UNION ALL SELECT 'M', NULL, 1, 'talla'
    UNION ALL SELECT 'L', NULL, 2, 'talla'
    UNION ALL SELECT 'Negro', '#000000', 0, 'color'
    UNION ALL SELECT 'Azul', '#0000FF', 1, 'color'
    UNION ALL SELECT 'Amarillo', '#FFFF00', 2, 'color'
    UNION ALL SELECT 'Rojo', '#FF0000', 3, 'color'
    UNION ALL SELECT 'Rosado', '#FFC0CB', 4, 'color'
    UNION ALL SELECT 'Hombre', NULL, 0, 'corte'
    UNION ALL SELECT 'Mujer', NULL, 1, 'corte'
) v
WHERE a.slug = v.attr
AND NOT EXISTS (
    SELECT 1 FROM product_attribute_values pav
    WHERE pav.attribute_id = a.id AND pav.value = v.value
);

-- ------------------------------------------------------------
-- 2. OBTENER IDs DE ATRIBUTOS EN VARIABLES (evita error 1242)
-- ------------------------------------------------------------
SET @attr_color := (SELECT id FROM product_attributes WHERE slug = 'color' LIMIT 1);
SET @attr_talla := (SELECT id FROM product_attributes WHERE slug = 'talla' LIMIT 1);
SET @attr_corte := (SELECT id FROM product_attributes WHERE slug = 'corte' LIMIT 1);

-- ------------------------------------------------------------
-- 3. CATEGORÍAS
-- ------------------------------------------------------------
INSERT IGNORE INTO categories (id, parent_id, name, slug, description, icon, sort_order, is_active, is_featured) VALUES
(12, 2,  'Camisetas',        'camisetas',         'Camisetas con diseños exclusivos de los artistas de Poder Down',        'bi bi-tshirt',           1, 1, 1),
(13, 4,  'Cuadernos',        'cuadernos',         'Cuadernos con arte y propósito',                                        'bi bi-journal',          2, 1, 0),
(14, 4,  'Tarjetas de Regalo','tarjetas-regalo',  'Tarjetas con mensajes de inclusión y amor',                            'bi bi-gift',             3, 1, 0),
(15, 4,  'Lapiceros',        'lapiceros',         'Lapiceros Poder Down con colores vibrantes',                            'bi bi-pencil',           4, 1, 0),
(16, NULL, 'Kits',           'kits',              'Kits combinados para regalar o consentirte',                            'bi bi-box-seam',         5, 1, 1);

-- ------------------------------------------------------------
-- 4. PRODUCTOS
-- ------------------------------------------------------------

-- 4.1 CAMISETA TIGRE FLACUCHÓN
INSERT INTO products (sku, name, slug, description, short_description, category_id, price, stock, is_active, is_featured, requires_shipping, tags) VALUES
('PD-CAM-TIG', 'Camiseta Tigre Flacuchón', 'camiseta-tigre-flacuchon',
'Imagina un tigre que, por amor y convicción, decide dejar de comer carne. Esta es la entrañable historia detrás de "El Tigre Flacuchón", una obra de la artista Camila que cobra vida en esta camiseta única. La inspiración surgió de un momento de profunda preocupación de Camila por su hermana, quien decidió adoptar el veganismo.',
'Camiseta 100% algodón con diseño exclusivo del Tigre Flacuchón. Disponible en corte hombre y mujer.',
12, 32000.00, 120, 1, 1, 1, 'camiseta,tigre,vegano,camila,arte');

-- 4.2 CAMISETA MANDALA
INSERT INTO products (sku, name, slug, description, short_description, category_id, price, stock, is_active, is_featured, requires_shipping, tags) VALUES
('PD-CAM-MAN', 'Camiseta Mandala', 'camiseta-mandala',
'La mandala es una de las expresiones artísticas que más disfruta crear Camila. En esta prenda, su pasión por las formas y colores se convierte en el diseño protagonista de la espalda, mientras que el frente mantiene la identidad de la marca. Al elegir esta camiseta, apoyas el talento de Camila y la misión de inclusión de Poder Down.',
'Camiseta 100% algodón con diseño exclusivo de mandala de Camila. Disponible en corte hombre y mujer.',
12, 32000.00, 120, 1, 1, 1, 'camiseta,mandala,camila,arte,color');

-- 4.3 CAMISETA TRISOMÍA 21
INSERT INTO products (sku, name, slug, description, short_description, category_id, price, stock, is_active, is_featured, requires_shipping, tags) VALUES
('PD-CAM-TRI', 'Camiseta Trisomía 21', 'camiseta-trisomia-21',
'Esta camiseta es un homenaje a la diversidad. El diseño de los tres puntos representa la trisomía 21, recordándonos la belleza de lo que nos hace únicos. El elemento azul es un saludo personal de Camila, mientras que el detalle amarillo simboliza la integración y la fuerza con la que las personas con síndrome de Down participan activamente en cada entorno de nuestra sociedad.',
'Camiseta 100% algodón con diseño Trisomía 21. Disponible en corte hombre y mujer.',
12, 32000.00, 120, 1, 1, 1, 'camiseta,trisomia21,inclusion,camila,down');

-- 4.4 CUADERNO TRISOMÍA 21
INSERT INTO products (sku, name, slug, description, short_description, category_id, price, stock, is_active, is_featured, requires_shipping, tags) VALUES
('PD-CUA-TRI', 'Cuaderno Trisomía 21', 'cuaderno-trisomia-21',
'Organiza tus ideas con propósito. Este cuaderno no es solo una herramienta de trabajo, es una pieza de arte. La portada presenta el diseño "Trisomía 21", mientras que la contraportada destaca una de las obras favoritas de Camila. Perfecto para quienes buscan inspiración y orden en su día a día mientras apoyan la misión de Poder Down. Incluye 80 hojas rayadas de alta calidad, con espacio dedicado para fecha y logo Poder Down en cada página. Incluye una hoja de calendario 2026. Pasta dura resistente para mayor durabilidad.',
'Cuaderno pasta dura con diseño Trisomía 21. 80 hojas rayadas + calendario 2026.',
13, 23000.00, 50, 1, 1, 1, 'cuaderno,trisomia21,camila,papeleria,arte');

-- 4.5 TARJETAS DE REGALO DE/PARA
INSERT INTO products (sku, name, slug, description, short_description, category_id, price, stock, is_active, is_featured, requires_shipping, tags) VALUES
('PD-TAR-DP', 'Tarjetas de Regalo De / Para', 'tarjetas-regalo-de-para',
'Haz que cada regalo cuente una historia. Estas tarjetas "De / Para" son el complemento ideal para darle un toque único a tus obsequios. Cada set incluye 10 tarjetas con diseños exclusivos, acompañado de una frase inspiradora que celebra la inclusión y la diversidad, convirtiendo un simple detalle en un mensaje de amor y respeto. Impresión de alta definición en papel de excelente gramaje, con un acabado impecable.',
'Set de 10 tarjetas de regalo con frases de inclusión y arte original de Camila.',
14, 5000.00, 100, 1, 1, 1, 'tarjetas,regalo,inclusion,camila');

-- 4.6 LAPICERO PODER DOWN
INSERT INTO products (sku, name, slug, description, short_description, category_id, price, stock, is_active, is_featured, requires_shipping, tags) VALUES
('PD-LAP', 'Lapicero Poder Down', 'lapicero-poder-down',
'Escribe tu mundo con propósito. Estos lapiceros son la pieza final para completar tu kit de escritura. Con un diseño moderno, ergonómico y disponible en una variedad de colores vibrantes para el cuerpo del lapicero, llevan contigo la marca Poder Down a donde quiera que vayas. Ideales para tomar apuntes en tu cuaderno, firmar documentos o dejar volar tu creatividad. Tinta negra de alta calidad para una escritura fluida y clara.',
'Lapicero ergonómico con tinta negra. Disponible en 5 colores vibrantes.',
15, 5000.00, 375, 1, 1, 1, 'lapicero,escritura,papeleria,color');

-- 4.7 KIT ESCRITURA
INSERT INTO products (sku, name, slug, description, short_description, category_id, price, stock, is_active, is_featured, requires_shipping, tags) VALUES
('PD-KIT-ESC', 'Kit Escritura Poder Down', 'kit-escritura-poder-down',
'Todo lo que necesitas para inspirar y crear, en un solo lugar. Este kit ha sido diseñado pensando en aquellos que aman los detalles y buscan llenar su día de propósito. Incluye: 1 Cuaderno Poder Down (tu espacio personal para plasmar ideas, metas y sueños), 1 Set de 10 Tarjetas "De / Para" (perfectas para acompañar tus obsequios con mensajes cargados de inclusión y amor), 1 Lapicero Poder Down (tinta negra de alta calidad, ergonómico y con estilo vibrante). Ahorro y practicidad: todo el conjunto diseñado para combinarse a la perfección. El regalo ideal para sorprender a alguien especial con un detalle con propósito.',
'Kit completo: Cuaderno + Set de 10 Tarjetas + Lapicero. Todo en uno.',
16, 27000.00, 30, 1, 1, 1, 'kit,escritura,cuaderno,tarjetas,lapicero,regalo');

-- ------------------------------------------------------------
-- 5. VARIANTES
-- ------------------------------------------------------------

-- 5.1 VARIANTES CAMISETA TIGRE FLACUCHÓN (6 variantes: S/M/L x Hombre/Mujer)
INSERT INTO product_variants (product_id, sku, name, stock, is_active, sort_order)
SELECT p.id,
       CONCAT('PD-CAM-TIG-', tv.value, '-', cv.value),
       CONCAT('Tigre Flacuchón - Talla ', tv.value, ' Corte ', cv.value),
       20, 1, 0
FROM products p
CROSS JOIN product_attribute_values tv
CROSS JOIN product_attribute_values cv
WHERE p.sku = 'PD-CAM-TIG'
  AND tv.attribute_id = @attr_talla
  AND tv.value IN ('S', 'M', 'L')
  AND cv.attribute_id = @attr_corte
  AND cv.value IN ('Hombre', 'Mujer');

-- 5.2 VARIANTES CAMISETA MANDALA
INSERT INTO product_variants (product_id, sku, name, stock, is_active, sort_order)
SELECT p.id,
       CONCAT('PD-CAM-MAN-', tv.value, '-', cv.value),
       CONCAT('Mandala - Talla ', tv.value, ' Corte ', cv.value),
       20, 1, 0
FROM products p
CROSS JOIN product_attribute_values tv
CROSS JOIN product_attribute_values cv
WHERE p.sku = 'PD-CAM-MAN'
  AND tv.attribute_id = @attr_talla
  AND tv.value IN ('S', 'M', 'L')
  AND cv.attribute_id = @attr_corte
  AND cv.value IN ('Hombre', 'Mujer');

-- 5.3 VARIANTES CAMISETA TRISOMÍA 21
INSERT INTO product_variants (product_id, sku, name, stock, is_active, sort_order)
SELECT p.id,
       CONCAT('PD-CAM-TRI-', tv.value, '-', cv.value),
       CONCAT('Trisomía 21 - Talla ', tv.value, ' Corte ', cv.value),
       20, 1, 0
FROM products p
CROSS JOIN product_attribute_values tv
CROSS JOIN product_attribute_values cv
WHERE p.sku = 'PD-CAM-TRI'
  AND tv.attribute_id = @attr_talla
  AND tv.value IN ('S', 'M', 'L')
  AND cv.attribute_id = @attr_corte
  AND cv.value IN ('Hombre', 'Mujer');

-- 5.4 VARIANTES LAPICERO PODER DOWN (5 variantes por color)
INSERT INTO product_variants (product_id, sku, name, stock, is_active, sort_order)
SELECT p.id,
       CONCAT('PD-LAP-', UPPER(LEFT(cv.value, 3))),
       CONCAT('Lapicero Poder Down - ', cv.value),
       75, 1, 0
FROM products p
CROSS JOIN product_attribute_values cv
WHERE p.sku = 'PD-LAP'
  AND cv.attribute_id = @attr_color
  AND cv.value IN ('Negro', 'Azul', 'Amarillo', 'Rojo', 'Rosado');

-- ------------------------------------------------------------
-- 6. VINCULAR VARIANTES CON ATRIBUTOS (product_variant_attributes)
-- ------------------------------------------------------------

-- 6.1 TIGRE FLACUCHÓN — Atributo Talla
INSERT INTO product_variant_attributes (variant_id, attribute_id, attribute_value_id)
SELECT v.id, @attr_talla, tv.id
FROM product_variants v
JOIN products p ON v.product_id = p.id
JOIN product_attribute_values tv ON tv.attribute_id = @attr_talla AND v.name LIKE CONCAT('%Talla ', tv.value, '%')
WHERE p.sku = 'PD-CAM-TIG';

-- 6.2 TIGRE FLACUCHÓN — Atributo Corte
INSERT INTO product_variant_attributes (variant_id, attribute_id, attribute_value_id)
SELECT v.id, @attr_corte, cv.id
FROM product_variants v
JOIN products p ON v.product_id = p.id
JOIN product_attribute_values cv ON cv.attribute_id = @attr_corte AND v.name LIKE CONCAT('%Corte ', cv.value, '%')
WHERE p.sku = 'PD-CAM-TIG';

-- 6.3 MANDALA — Atributo Talla
INSERT INTO product_variant_attributes (variant_id, attribute_id, attribute_value_id)
SELECT v.id, @attr_talla, tv.id
FROM product_variants v
JOIN products p ON v.product_id = p.id
JOIN product_attribute_values tv ON tv.attribute_id = @attr_talla AND v.name LIKE CONCAT('%Talla ', tv.value, '%')
WHERE p.sku = 'PD-CAM-MAN';

-- 6.4 MANDALA — Atributo Corte
INSERT INTO product_variant_attributes (variant_id, attribute_id, attribute_value_id)
SELECT v.id, @attr_corte, cv.id
FROM product_variants v
JOIN products p ON v.product_id = p.id
JOIN product_attribute_values cv ON cv.attribute_id = @attr_corte AND v.name LIKE CONCAT('%Corte ', cv.value, '%')
WHERE p.sku = 'PD-CAM-MAN';

-- 6.5 TRISOMÍA 21 — Atributo Talla
INSERT INTO product_variant_attributes (variant_id, attribute_id, attribute_value_id)
SELECT v.id, @attr_talla, tv.id
FROM product_variants v
JOIN products p ON v.product_id = p.id
JOIN product_attribute_values tv ON tv.attribute_id = @attr_talla AND v.name LIKE CONCAT('%Talla ', tv.value, '%')
WHERE p.sku = 'PD-CAM-TRI';

-- 6.6 TRISOMÍA 21 — Atributo Corte
INSERT INTO product_variant_attributes (variant_id, attribute_id, attribute_value_id)
SELECT v.id, @attr_corte, cv.id
FROM product_variants v
JOIN products p ON v.product_id = p.id
JOIN product_attribute_values cv ON cv.attribute_id = @attr_corte AND v.name LIKE CONCAT('%Corte ', cv.value, '%')
WHERE p.sku = 'PD-CAM-TRI';

-- 6.7 LAPICERO — Atributo Color
INSERT INTO product_variant_attributes (variant_id, attribute_id, attribute_value_id)
SELECT v.id, @attr_color, cv.id
FROM product_variants v
JOIN products p ON v.product_id = p.id
JOIN product_attribute_values cv ON cv.attribute_id = @attr_color AND v.name LIKE CONCAT('%', cv.value, '%')
WHERE p.sku = 'PD-LAP';

-- ------------------------------------------------------------
-- 7. SINCRONIZAR STOCK DE PRODUCTOS CON VARIANTES
-- ------------------------------------------------------------
UPDATE products p
SET p.stock = (
    SELECT COALESCE(SUM(pv.stock), 0)
    FROM product_variants pv
    WHERE pv.product_id = p.id AND pv.is_active = 1
)
WHERE p.id IN (
    SELECT DISTINCT pv2.product_id FROM product_variants pv2
);

-- ------------------------------------------------------------
-- 8. PRODUCTOS RELACIONADOS (cross-sell / upsell)
-- ------------------------------------------------------------
INSERT IGNORE INTO product_relations (product_id, related_product_id, relation_type, sort_order)
SELECT kit.id, rel.id, 'cross_sell', 1
FROM products kit
CROSS JOIN products rel
WHERE kit.sku = 'PD-KIT-ESC'
  AND rel.sku IN ('PD-CUA-TRI', 'PD-TAR-DP', 'PD-LAP');

INSERT IGNORE INTO product_relations (product_id, related_product_id, relation_type, sort_order)
SELECT cuad.id, rel.id, 'upsell', 1
FROM products cuad
CROSS JOIN products rel
WHERE cuad.sku = 'PD-CUA-TRI'
  AND rel.sku IN ('PD-TAR-DP', 'PD-LAP');

-- ------------------------------------------------------------
-- 9. RESUMEN DE LO INSERTADO
-- ------------------------------------------------------------
SELECT '=== CATEGORÍAS ===' AS info;
SELECT id, parent_id, name, slug FROM categories WHERE id >= 12 ORDER BY id;

SELECT '=== PRODUCTOS ===' AS info;
SELECT id, sku, name, price, stock, category_id FROM products WHERE sku LIKE 'PD-%' ORDER BY id;

SELECT '=== VARIANTES ===' AS info;
SELECT pv.id, p.name AS producto, pv.sku, pv.name AS variante, pv.stock
FROM product_variants pv
JOIN products p ON pv.product_id = p.id
WHERE p.sku LIKE 'PD-%'
ORDER BY p.id, pv.id;

SELECT '=== STOCK POR PRODUCTO ===' AS info;
SELECT p.sku, p.name, p.stock AS stock_producto,
       COUNT(pv.id) AS total_variantes,
       COALESCE(SUM(pv.stock), p.stock) AS stock_calculado
FROM products p
LEFT JOIN product_variants pv ON pv.product_id = p.id AND pv.is_active = 1
WHERE p.sku LIKE 'PD-%'
GROUP BY p.id
ORDER BY p.id;
