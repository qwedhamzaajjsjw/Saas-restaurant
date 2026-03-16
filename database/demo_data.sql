-- ============================================================
-- Demo Restaurant Data — Burger House
-- Compatible with: MySQL / MariaDB
-- Usage: Run this script on your production database
--        after running migrations (php artisan migrate)
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;

-- ─────────────────────────────────────────────────────────────
-- 1. PLANS (create or ignore if already exist)
-- ─────────────────────────────────────────────────────────────

INSERT INTO `plans` (`name`, `slug`, `description`, `price`, `currency`, `max_products`, `max_orders_per_month`, `max_categories`, `features`, `is_active`, `sort_order`, `created_at`, `updated_at`)
VALUES
  ('Starter', 'starter', 'Perfect for new restaurants just getting started.', 0.00, 'EUR', 20, 100, 5,
   '["online_menu","basic_analytics"]', 1, 1, NOW(), NOW()),

  ('Pro', 'pro', 'For growing restaurants that need more power.', 29.99, 'EUR', 100, 1000, 20,
   '["online_menu","advanced_analytics","export","custom_domain"]', 1, 2, NOW(), NOW()),

  ('Enterprise', 'enterprise', 'Unlimited everything for large restaurant chains.', 99.99, 'EUR', 9999, 9999, 9999,
   '["online_menu","advanced_analytics","export","custom_domain","priority_support","api_access"]', 1, 3, NOW(), NOW())
ON DUPLICATE KEY UPDATE `updated_at` = NOW();

-- ─────────────────────────────────────────────────────────────
-- 2. RESTAURANT
-- ─────────────────────────────────────────────────────────────

-- Remove old demo data if exists
SET @old_restaurant_id = (SELECT `id` FROM `restaurants` WHERE `slug` = 'burger-house-demo' LIMIT 1);

DELETE FROM `banners`      WHERE `restaurant_id` = @old_restaurant_id;
DELETE FROM `products`     WHERE `restaurant_id` = @old_restaurant_id;
DELETE FROM `categories`   WHERE `restaurant_id` = @old_restaurant_id;
DELETE FROM `menus`        WHERE `restaurant_id` = @old_restaurant_id;
DELETE FROM `subscriptions` WHERE `restaurant_id` = @old_restaurant_id;
DELETE FROM `users`        WHERE `restaurant_id` = @old_restaurant_id;
DELETE FROM `restaurants`  WHERE `id` = @old_restaurant_id;

-- Get Pro plan ID
SET @plan_id = (SELECT `id` FROM `plans` WHERE `slug` = 'pro' LIMIT 1);
IF @plan_id IS NULL THEN
    SET @plan_id = (SELECT `id` FROM `plans` ORDER BY `id` LIMIT 1);
END IF;

-- Insert restaurant
INSERT INTO `restaurants` (`name`, `slug`, `description`, `logo`, `cover_image`, `primary_color`,
  `phone`, `email`, `address`, `city`, `country`, `plan_id`, `status`,
  `accepts_orders`, `timezone`, `currency`, `created_at`, `updated_at`)
VALUES (
  'Burger House',
  'burger-house-demo',
  'Serving the juiciest burgers in town since 2010. Fresh ingredients, bold flavors, and unforgettable taste.',
  NULL,
  NULL,
  '#f97316',
  '+49 30 1234567',
  'hello@burgerhouse.demo',
  'Hauptstraße 1',
  'Berlin',
  'DE',
  @plan_id,
  'active',
  1,
  'Europe/Berlin',
  'EUR',
  NOW(), NOW()
);

SET @restaurant_id = LAST_INSERT_ID();

-- ─────────────────────────────────────────────────────────────
-- 3. RESTAURANT OWNER USER
-- ─────────────────────────────────────────────────────────────

-- Password: Demo@123456 (bcrypt hash)
INSERT INTO `users` (`name`, `email`, `password`, `role`, `restaurant_id`, `is_active`, `email_verified_at`, `created_at`, `updated_at`)
VALUES (
  'Demo Owner',
  'owner@burgerhouse.demo',
  '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
  'restaurant_owner',
  @restaurant_id,
  1,
  NOW(),
  NOW(), NOW()
);

-- ─────────────────────────────────────────────────────────────
-- 4. SUBSCRIPTION (active, 1 year)
-- ─────────────────────────────────────────────────────────────

INSERT INTO `subscriptions` (`restaurant_id`, `plan_id`, `starts_at`, `ends_at`, `status`, `amount_paid`, `created_at`, `updated_at`)
VALUES (
  @restaurant_id,
  @plan_id,
  NOW(),
  DATE_ADD(NOW(), INTERVAL 1 YEAR),
  'active',
  0.00,
  NOW(), NOW()
);

-- ─────────────────────────────────────────────────────────────
-- 5. MENU
-- ─────────────────────────────────────────────────────────────

INSERT INTO `menus` (`restaurant_id`, `name`, `description`, `is_active`, `sort_order`, `created_at`, `updated_at`)
VALUES (@restaurant_id, 'القائمة الرئيسية', 'تشكيلتنا الكاملة من الأطعمة والمشروبات اللذيذة', 1, 0, NOW(), NOW());

SET @menu_id = LAST_INSERT_ID();

-- ─────────────────────────────────────────────────────────────
-- 6. CATEGORIES
-- ─────────────────────────────────────────────────────────────

INSERT INTO `categories` (`restaurant_id`, `menu_id`, `name`, `description`, `image`, `is_active`, `sort_order`, `created_at`, `updated_at`)
VALUES
  (@restaurant_id, @menu_id, 'البرغر', 'برغر طازج يُعدّ يومياً بأفضل المكونات', NULL, 1, 1, NOW(), NOW()),
  (@restaurant_id, @menu_id, 'المقبلات والمشاوي الجانبية', 'رفيق مثالي لوجبتك الرئيسية', NULL, 1, 2, NOW(), NOW()),
  (@restaurant_id, @menu_id, 'المشروبات', 'مشروبات باردة وعصائر طازجة', NULL, 1, 3, NOW(), NOW()),
  (@restaurant_id, @menu_id, 'الحلويات', 'نهاية حلوة لوجبة مثالية', NULL, 1, 4, NOW(), NOW());

SET @cat_burgers  = (SELECT `id` FROM `categories` WHERE `restaurant_id` = @restaurant_id AND `name` = 'البرغر' LIMIT 1);
SET @cat_sides    = (SELECT `id` FROM `categories` WHERE `restaurant_id` = @restaurant_id AND `name` = 'المقبلات والمشاوي الجانبية' LIMIT 1);
SET @cat_drinks   = (SELECT `id` FROM `categories` WHERE `restaurant_id` = @restaurant_id AND `name` = 'المشروبات' LIMIT 1);
SET @cat_desserts = (SELECT `id` FROM `categories` WHERE `restaurant_id` = @restaurant_id AND `name` = 'الحلويات' LIMIT 1);

-- ─────────────────────────────────────────────────────────────
-- 7. PRODUCTS — Burgers
-- ─────────────────────────────────────────────────────────────

INSERT INTO `products` (`restaurant_id`, `category_id`, `name`, `slug`, `description`, `image`, `price`, `sale_price`, `is_available`, `is_featured`, `preparation_time`, `calories`, `options`, `sort_order`, `created_at`, `updated_at`)
VALUES
  (@restaurant_id, @cat_burgers,
   'كلاسيك سماش برغر',
   'classic-smash-burger',
   'باتي مزدوج مسحوق، جبن شيدر، خس، طماطم، مخلل، وصلصتنا السرية.',
   NULL, 12.99, NULL, 1, 1, 15, 620,
   '[{"name":"جبن إضافي","price":1.00},{"name":"باتي مزدوج","price":2.50},{"name":"بيكون","price":1.50},{"name":"فلفل حار","price":0.50},{"name":"أفوكادو","price":1.00}]',
   1, NOW(), NOW()),

  (@restaurant_id, @cat_burgers,
   'برغر بيكون BBQ',
   'bbq-bacon-burger',
   'صلصة BBQ مدخنة، بيكون مقرمش، بصل مكرمل، وجبن بيبر جاك.',
   NULL, 14.99, 11.99, 1, 1, 15, 780,
   '[{"name":"جبن إضافي","price":1.00},{"name":"باتي مزدوج","price":2.50},{"name":"صلصة BBQ","price":0.50},{"name":"فلفل حار","price":0.50}]',
   2, NOW(), NOW()),

  (@restaurant_id, @cat_burgers,
   'برغر دجاج مقرمش حار',
   'spicy-crispy-chicken-burger',
   'فخذ دجاج مقلي مقرمش، كولسلو حار، فلفل حار مخلل، مايو سريراتشا.',
   NULL, 13.49, NULL, 1, 1, 15, 690,
   '[{"name":"مايو","price":0.00},{"name":"كاتشاب","price":0.00},{"name":"صلصة BBQ","price":0.50},{"name":"صلصة حارة","price":0.50}]',
   3, NOW(), NOW()),

  (@restaurant_id, @cat_burgers,
   'برغر الفطر والجبن السويسري',
   'mushroom-swiss-burger',
   'فطر مقلي، جبن سويسري، أيولي بالثوم، جرجير على خبز البريوش.',
   NULL, 13.99, NULL, 1, 0, 15, 650,
   '[{"name":"جبن إضافي","price":1.00},{"name":"باتي مزدوج","price":2.50},{"name":"مايو","price":0.00}]',
   4, NOW(), NOW()),

  (@restaurant_id, @cat_burgers,
   'برغر الخضار',
   'veggie-bean-burger',
   'باتي فول أسود وكينوا، جواكامولي، فلفل أحمر محمص، براعم.',
   NULL, 11.99, 9.99, 1, 0, 15, 480,
   '[{"name":"مايو","price":0.00},{"name":"كاتشاب","price":0.00},{"name":"صلصة BBQ","price":0.50}]',
   5, NOW(), NOW()),

  (@restaurant_id, @cat_burgers,
   'برغر البرج',
   'tower-burger',
   'برج ثلاثي الباتي مع بيكون، بيضة، جبن، وجميع المقبلات.',
   NULL, 18.99, NULL, 1, 0, 20, 1050,
   '[{"name":"جبن إضافي","price":1.00},{"name":"باتي إضافي","price":2.50},{"name":"بيكون","price":1.50},{"name":"فلفل حار","price":0.50}]',
   6, NOW(), NOW());

-- ─────────────────────────────────────────────────────────────
-- 7b. PRODUCTS — Sides & Starters
-- ─────────────────────────────────────────────────────────────

INSERT INTO `products` (`restaurant_id`, `category_id`, `name`, `slug`, `description`, `image`, `price`, `sale_price`, `is_available`, `is_featured`, `preparation_time`, `calories`, `options`, `sort_order`, `created_at`, `updated_at`)
VALUES
  (@restaurant_id, @cat_sides,
   'بطاطس محملة بالجبن',
   'loaded-cheese-fries',
   'بطاطس مقلية مقرمشة مع صلصة الشيدر، قطع بيكون، قشطة حامضة، وثوم معمر.',
   NULL, 7.99, 5.99, 1, 1, 10, 520,
   '[{"name":"صلصة جبن إضافية","price":1.00},{"name":"فلفل حار","price":0.50}]',
   1, NOW(), NOW()),

  (@restaurant_id, @cat_sides,
   'حلقات البصل',
   'onion-rings',
   'حلقات بصل مقلية بعجينة ذهبية مع صلصة رانش.',
   NULL, 5.99, NULL, 1, 0, 10, 380,
   '[{"name":"مايو","price":0.00},{"name":"كاتشاب","price":0.00},{"name":"صلصة حارة","price":0.50}]',
   2, NOW(), NOW()),

  (@restaurant_id, @cat_sides,
   'قطع دجاج مقرمشة',
   'crispy-chicken-nuggets',
   'قطع دجاج مقلية بالحجم المناسب مع صلصة الغمس التي تختارها.',
   NULL, 10.99, NULL, 1, 0, 10, 450,
   '[{"name":"صلصة BBQ","price":0.00},{"name":"خردل عسلي","price":0.00},{"name":"رانش","price":0.00},{"name":"صلصة إضافية","price":0.50}]',
   3, NOW(), NOW()),

  (@restaurant_id, @cat_sides,
   'بطاطا حلوة مقلية',
   'sweet-potato-fries',
   'بطاطا حلوة محضرة بتوابل مميزة مع غموس الشيبوتل مايو.',
   NULL, 6.49, NULL, 1, 0, 10, 340,
   '[]',
   4, NOW(), NOW());

-- ─────────────────────────────────────────────────────────────
-- 7c. PRODUCTS — Drinks
-- ─────────────────────────────────────────────────────────────

INSERT INTO `products` (`restaurant_id`, `category_id`, `name`, `slug`, `description`, `image`, `price`, `sale_price`, `is_available`, `is_featured`, `preparation_time`, `calories`, `options`, `sort_order`, `created_at`, `updated_at`)
VALUES
  (@restaurant_id, @cat_drinks,
   'ميلك شيك كلاسيكي',
   'classic-milkshake',
   'شيك كثيف وكريمي. اختر من فانيلا أو شوكولاتة أو فراولة.',
   NULL, 6.99, NULL, 1, 1, 5, 420,
   '[{"name":"فانيلا","price":0.00},{"name":"شوكولاتة","price":0.00},{"name":"فراولة","price":0.00},{"name":"كريمة مخفوقة","price":0.50}]',
   1, NOW(), NOW()),

  (@restaurant_id, @cat_drinks,
   'عصير برتقال طازج',
   'fresh-orange-juice',
   'عصير برتقال معصور طازج، يُقدم بارداً مع الثلج.',
   NULL, 4.49, 3.49, 1, 0, 5, 180,
   '[{"name":"ثلج إضافي","price":0.00},{"name":"بدون ثلج","price":0.00},{"name":"حجم كبير","price":1.00}]',
   2, NOW(), NOW()),

  (@restaurant_id, @cat_drinks,
   'صودا كرافت',
   'craft-soda',
   'صودا كرافت منزلية الصنع بنكهات موسمية متنوعة.',
   NULL, 3.99, NULL, 1, 0, 5, 150,
   '[{"name":"ثلج إضافي","price":0.00},{"name":"بدون ثلج","price":0.00},{"name":"حجم كبير","price":1.00}]',
   3, NOW(), NOW());

-- ─────────────────────────────────────────────────────────────
-- 7d. PRODUCTS — Desserts
-- ─────────────────────────────────────────────────────────────

INSERT INTO `products` (`restaurant_id`, `category_id`, `name`, `slug`, `description`, `image`, `price`, `sale_price`, `is_available`, `is_featured`, `preparation_time`, `calories`, `options`, `sort_order`, `created_at`, `updated_at`)
VALUES
  (@restaurant_id, @cat_desserts,
   'صندي آيس كريم',
   'ice-cream-sundae',
   'ثلاث كرات من المثلجات الفاخرة مع صلصة الشوكولاتة الساخنة، كريمة مخفوقة، وكرز.',
   NULL, 7.99, NULL, 1, 1, 8, 520,
   '[{"name":"فاج إضافي","price":0.50},{"name":"نثار ملون","price":0.00}]',
   1, NOW(), NOW()),

  (@restaurant_id, @cat_desserts,
   'شريحة كيك عيد الميلاد',
   'birthday-cake-slice',
   'كيك متعدد الطبقات مع كريمة الزبدة وزينة ملونة.',
   NULL, 6.99, 5.49, 1, 0, 8, 480,
   '[{"name":"شوكولاتة","price":0.00},{"name":"فانيلا","price":0.00},{"name":"ريد فيلفيت","price":0.00}]',
   2, NOW(), NOW());

-- ─────────────────────────────────────────────────────────────
-- 8. BANNERS (storefront promotional banners)
-- ─────────────────────────────────────────────────────────────

INSERT INTO `banners` (`restaurant_id`, `title`, `subtitle`, `image`, `link_url`, `button_text`, `is_active`, `sort_order`, `created_at`, `updated_at`)
VALUES
  (@restaurant_id,
   'أفضل البرغر في المدينة',
   'جرّب كلاسيك سماش برغر الآن — طازج يومياً بمكونات مختارة',
   NULL,
   '#menu',
   'اطلب الآن',
   1, 1, NOW(), NOW()),

  (@restaurant_id,
   'عروض اليوم 🔥',
   'خصم 20% على بطاطس محملة بالجبن + برغر BBQ بيكون',
   NULL,
   '#menu',
   'اغتنم العرض',
   1, 2, NOW(), NOW()),

  (@restaurant_id,
   'مشروبات باردة منعشة',
   'ميلك شيك كلاسيكي وعصائر طازجة — الصيف يبدأ من هنا',
   NULL,
   '#drinks',
   'اكتشف المشروبات',
   1, 3, NOW(), NOW());

-- ─────────────────────────────────────────────────────────────
-- 9. SETTINGS (restaurant configuration)
-- ─────────────────────────────────────────────────────────────

INSERT INTO `settings` (`restaurant_id`, `key`, `value`, `type`, `created_at`, `updated_at`)
VALUES
  (@restaurant_id, 'delivery_fee',       '2.50',                   'string', NOW(), NOW()),
  (@restaurant_id, 'min_order_amount',   '8.00',                   'string', NOW(), NOW()),
  (@restaurant_id, 'tax_rate',           '19',                     'string', NOW(), NOW()),
  (@restaurant_id, 'working_hours',      '11:00-23:00',            'string', NOW(), NOW()),
  (@restaurant_id, 'delivery_enabled',   '1',                      'boolean',NOW(), NOW()),
  (@restaurant_id, 'pickup_enabled',     '1',                      'boolean',NOW(), NOW()),
  (@restaurant_id, 'dine_in_enabled',    '1',                      'boolean',NOW(), NOW()),
  (@restaurant_id, 'whatsapp_number',    '+4930123456',             'string', NOW(), NOW()),
  (@restaurant_id, 'footer_note',        'جميع الحقوق محفوظة © Burger House 2025', 'string', NOW(), NOW())
ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), `updated_at` = NOW();

-- ─────────────────────────────────────────────────────────────
-- 10. SAMPLE ORDERS (for dashboard stats)
-- ─────────────────────────────────────────────────────────────

SET @product_1 = (SELECT `id` FROM `products` WHERE `restaurant_id` = @restaurant_id AND `slug` = 'classic-smash-burger' LIMIT 1);
SET @product_2 = (SELECT `id` FROM `products` WHERE `restaurant_id` = @restaurant_id AND `slug` = 'loaded-cheese-fries' LIMIT 1);
SET @product_3 = (SELECT `id` FROM `products` WHERE `restaurant_id` = @restaurant_id AND `slug` = 'classic-milkshake' LIMIT 1);
SET @product_4 = (SELECT `id` FROM `products` WHERE `restaurant_id` = @restaurant_id AND `slug` = 'bbq-bacon-burger' LIMIT 1);

-- Order 1 — delivered
INSERT INTO `orders` (`restaurant_id`, `order_number`, `customer_name`, `customer_email`, `customer_phone`,
  `type`, `delivery_address`, `delivery_city`, `subtotal`, `tax_amount`, `delivery_fee`, `discount_amount`, `total`,
  `payment_method`, `payment_status`, `status`, `notes`, `created_at`, `updated_at`)
VALUES (@restaurant_id, 'ORD-0001', 'أحمد المنصوري', 'ahmed@example.com', '+491701234567',
  'delivery', 'Berliner Str. 42', 'Berlin', 28.97, 5.50, 2.50, 0.00, 36.97,
  'cash', 'paid', 'delivered', NULL, DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_SUB(NOW(), INTERVAL 2 DAY));
SET @order_1 = LAST_INSERT_ID();

INSERT INTO `order_items` (`order_id`, `product_id`, `product_name`, `unit_price`, `quantity`, `subtotal`, `options`, `notes`, `created_at`, `updated_at`)
VALUES
  (@order_1, @product_1, 'كلاسيك سماش برغر', 12.99, 2, 25.98, '[]', NULL, DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_SUB(NOW(), INTERVAL 2 DAY)),
  (@order_1, @product_2, 'بطاطس محملة بالجبن', 2.99, 1, 2.99,  '[]', NULL, DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_SUB(NOW(), INTERVAL 2 DAY));

-- Order 2 — delivered
INSERT INTO `orders` (`restaurant_id`, `order_number`, `customer_name`, `customer_email`, `customer_phone`,
  `type`, `delivery_address`, `delivery_city`, `subtotal`, `tax_amount`, `delivery_fee`, `discount_amount`, `total`,
  `payment_method`, `payment_status`, `status`, `notes`, `created_at`, `updated_at`)
VALUES (@restaurant_id, 'ORD-0002', 'سارة الرشيدي', 'sara@example.com', '+491709876543',
  'pickup', NULL, NULL, 21.98, 4.18, 0.00, 0.00, 26.16,
  'card', 'paid', 'delivered', NULL, DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY));
SET @order_2 = LAST_INSERT_ID();

INSERT INTO `order_items` (`order_id`, `product_id`, `product_name`, `unit_price`, `quantity`, `subtotal`, `options`, `notes`, `created_at`, `updated_at`)
VALUES
  (@order_2, @product_4, 'برغر بيكون BBQ', 11.99, 1, 11.99, '[{"name":"جبن إضافي","price":1.00}]', NULL, DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY)),
  (@order_2, @product_3, 'ميلك شيك كلاسيكي', 6.99, 1, 6.99,  '[{"name":"شوكولاتة","price":0.00}]', NULL, DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY)),
  (@order_2, @product_2, 'بطاطس محملة بالجبن', 5.99, 1, 5.99, '[]', NULL, DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY));

-- Order 3 — today / pending
INSERT INTO `orders` (`restaurant_id`, `order_number`, `customer_name`, `customer_email`, `customer_phone`,
  `type`, `delivery_address`, `delivery_city`, `subtotal`, `tax_amount`, `delivery_fee`, `discount_amount`, `total`,
  `payment_method`, `payment_status`, `status`, `notes`, `created_at`, `updated_at`)
VALUES (@restaurant_id, 'ORD-0003', 'خالد العمري', 'khalid@example.com', '+491711111111',
  'delivery', 'Musterstraße 7', 'Hamburg', 32.97, 6.26, 2.50, 0.00, 41.73,
  'cash', 'pending', 'pending', 'بدون بصل من فضلك', NOW(), NOW());
SET @order_3 = LAST_INSERT_ID();

INSERT INTO `order_items` (`order_id`, `product_id`, `product_name`, `unit_price`, `quantity`, `subtotal`, `options`, `notes`, `created_at`, `updated_at`)
VALUES
  (@order_3, @product_1, 'كلاسيك سماش برغر',  12.99, 1, 12.99, '[]', 'بدون بصل', NOW(), NOW()),
  (@order_3, @product_4, 'برغر بيكون BBQ',      14.99, 1, 14.99, '[]', NULL,        NOW(), NOW()),
  (@order_3, @product_2, 'بطاطس محملة بالجبن',  5.99, 1, 5.99,  '[]', NULL,        NOW(), NOW());

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- ✅ Done!
-- Restaurant: Burger House
-- Owner login: owner@burgerhouse.demo / Demo@123456
-- Restaurant ID: (last inserted)
-- ============================================================
