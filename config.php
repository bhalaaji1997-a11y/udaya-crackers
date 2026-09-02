<?php
declare(strict_types=1);

/**
 * Udaya Crackers database and catalog helpers.
 *
 * Set DB_HOST, DB_NAME, DB_USER, DB_PASS and DB_PORT in the environment
 * before deploying. The storefront stays previewable with the catalog below
 * until the MySQL schema is imported.
 */

function db(): ?PDO
{
    static $pdo = null;
    static $attempted = false;

    if ($attempted) {
        return $pdo;
    }

    $attempted = true;
    $host = getenv('DB_HOST') ?: '127.0.0.1';
    $name = getenv('DB_NAME') ?: 'udaya_crackers';
    $user = getenv('DB_USER') ?: 'root';
    $pass = getenv('DB_PASS') ?: '';
    $port = getenv('DB_PORT') ?: '3306';

    try {
        $pdo = new PDO(
            "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4",
            $user,
            $pass,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ],
        );
        ensureAdminSchema($pdo);
    } catch (Throwable $error) {
        error_log('Udaya database connection unavailable: ' . $error->getMessage());
        $pdo = null;
    }

    return $pdo;
}

function demoProducts(): array
{
    return [
        [
            'id' => 1,
            'name' => 'Udaya Celebration Combo',
            'tamil_name' => 'உதயா கொண்டாட்ட காம்போ',
            'category' => 'Combos',
            'price' => 2499,
            'mrp' => 8900,
            'unit' => '1 family box · 42 items',
            'image' => 'combo.jpg',
            'tag' => 'Best value',
        ],
        [
            'id' => 2,
            'name' => 'Grand Family Box',
            'tamil_name' => 'கிராண்ட் ஃபேமிலி பாக்ஸ்',
            'category' => 'Combos',
            'price' => 4999,
            'mrp' => 16500,
            'unit' => '1 family box · 68 items',
            'image' => 'combo.jpg',
            'tag' => 'Family favourite',
        ],
        [
            'id' => 3,
            'name' => 'Golden Lakshmi 4"',
            'tamil_name' => 'கோல்டன் லட்சுமி 4"',
            'category' => 'Sound Crackers',
            'price' => 145,
            'mrp' => 520,
            'unit' => '1 packet · 5 pieces',
            'image' => 'combo.jpg',
            'tag' => '',
        ],
        [
            'id' => 4,
            'name' => 'Festival Thunder',
            'tamil_name' => 'ஃபெஸ்டிவல் தண்டர்',
            'category' => 'Sound Crackers',
            'price' => 225,
            'mrp' => 790,
            'unit' => '1 packet · 5 pieces',
            'image' => 'combo.jpg',
            'tag' => 'Popular',
        ],
        [
            'id' => 5,
            'name' => 'Rainbow Fountain',
            'tamil_name' => 'ரெயின்போ ஃபவுண்டன்',
            'category' => 'Fountains',
            'price' => 99,
            'mrp' => 360,
            'unit' => '1 box · 3 pieces',
            'image' => 'fountains.jpg',
            'tag' => '',
        ],
        [
            'id' => 6,
            'name' => 'Udaya Peacock Fountain',
            'tamil_name' => 'உதயா மயில் ஃபவுண்டன்',
            'category' => 'Fountains',
            'price' => 180,
            'mrp' => 650,
            'unit' => '1 box · 1 piece',
            'image' => 'fountains.jpg',
            'tag' => 'New',
        ],
        [
            'id' => 7,
            'name' => 'Colour Sparkler Mix',
            'tamil_name' => 'கலர் மத்தாப்பு மிக்ஸ்',
            'category' => 'Sparklers',
            'price' => 119,
            'mrp' => 420,
            'unit' => '1 box · 10 pieces',
            'image' => 'sparklers.jpg',
            'tag' => '',
        ],
        [
            'id' => 8,
            'name' => 'Midnight Spinner',
            'tamil_name' => 'மிட்நைட் ஸ்பின்னர்',
            'category' => 'Ground Spinners',
            'price' => 75,
            'mrp' => 250,
            'unit' => '1 box · 5 pieces',
            'image' => 'sparklers.jpg',
            'tag' => '',
        ],
        [
            'id' => 9,
            'name' => 'Sky Dazzler Rocket',
            'tamil_name' => 'ஸ்கை டாஸ்லர் ராக்கெட்',
            'category' => 'Rockets',
            'price' => 299,
            'mrp' => 950,
            'unit' => '1 box · 5 pieces',
            'image' => 'fountains.jpg',
            'tag' => 'Crowd pleaser',
        ],
        [
            'id' => 10,
            'name' => 'Star Shower',
            'tamil_name' => 'ஸ்டார் ஷவர்',
            'category' => 'Aerial Effects',
            'price' => 399,
            'mrp' => 1250,
            'unit' => '1 box · 1 piece',
            'image' => 'fountains.jpg',
            'tag' => '',
        ],
    ];
}

function normalizeProduct(array $product): array
{
    return array_merge([
        'id' => 0,
        'name' => '',
        'tamil_name' => '',
        'category' => 'Combos',
        'price' => 0,
        'mrp' => 0,
        'unit' => '1 box',
        'image' => 'combo.jpg',
        'tag' => '',
        'featured' => 0,
        'active' => 1,
        'stock_quantity' => 0,
        'low_stock_threshold' => 10,
    ], $product);
}

function defaultCategories(): array
{
    return [
        ['id' => 1, 'name' => 'Combos', 'tamil_name' => 'காம்போ பெட்டிகள்', 'description' => 'Ready-to-celebrate family boxes'],
        ['id' => 2, 'name' => 'Sound Crackers', 'tamil_name' => 'சத்த வெடிகள்', 'description' => 'Classic celebration favourites'],
        ['id' => 3, 'name' => 'Fountains', 'tamil_name' => 'தரைவாணங்கள்', 'description' => 'Colourful ground fountains'],
        ['id' => 4, 'name' => 'Sparklers', 'tamil_name' => 'மத்தாப்புகள்', 'description' => 'Bright handheld sparklers'],
        ['id' => 5, 'name' => 'Ground Spinners', 'tamil_name' => 'சக்கரங்கள்', 'description' => 'Whirling ground effects'],
        ['id' => 6, 'name' => 'Rockets', 'tamil_name' => 'ராக்கெட்டுகள்', 'description' => 'Sky-high festival colour'],
        ['id' => 7, 'name' => 'Aerial Effects', 'tamil_name' => 'வானவேடிக்கைகள்', 'description' => 'Big sky moments'],
    ];
}

function categoryTamil(string $category): string
{
    return [
        'Combos' => 'காம்போ பெட்டிகள்',
        'Sound Crackers' => 'சத்த வெடிகள்',
        'Fountains' => 'தரைவாணங்கள்',
        'Sparklers' => 'மத்தாப்புகள்',
        'Ground Spinners' => 'சக்கரங்கள்',
        'Rockets' => 'ராக்கெட்டுகள்',
        'Aerial Effects' => 'வானவேடிக்கைகள்',
    ][$category] ?? $category;
}

function tamilUnit(string $unit): string
{
    $translated = str_replace(
        ['family box', 'packet', 'box', 'pieces', 'piece', 'items', 'item'],
        ['குடும்ப பெட்டி', 'பாக்கெட்', 'பெட்டி', 'துண்டுகள்', 'துண்டு', 'பொருட்கள்', 'பொருள்'],
        $unit,
    );
    return $translated;
}

function localStorePath(): string
{
    return __DIR__ . '/data/store.json';
}

function defaultLocalStore(): array
{
    return [
        'products' => array_map(
            static fn (array $product): array => normalizeProduct($product),
            demoProducts(),
        ),
        'categories' => defaultCategories(),
        'orders' => [],
    ];
}

function localStore(): array
{
    $path = localStorePath();
    if (!is_file($path)) {
        return defaultLocalStore();
    }

    $decoded = json_decode((string) file_get_contents($path), true);
    if (!is_array($decoded)) {
        return defaultLocalStore();
    }

    $store = defaultLocalStore();
    $store['products'] = array_map(
        static fn (array $product): array => normalizeProduct($product),
        is_array($decoded['products'] ?? null) ? $decoded['products'] : $store['products'],
    );
    $store['categories'] = is_array($decoded['categories'] ?? null) && $decoded['categories']
        ? array_map(
            static fn (array $category): array => array_merge(
                ['tamil_name' => categoryTamil((string) ($category['name'] ?? ''))],
                $category,
            ),
            $decoded['categories'],
        )
        : $store['categories'];
    $store['orders'] = is_array($decoded['orders'] ?? null) ? $decoded['orders'] : [];
    return $store;
}

function saveLocalStore(array $store): void
{
    $directory = dirname(localStorePath());
    if (!is_dir($directory)) {
        mkdir($directory, 0775, true);
    }
    file_put_contents(
        localStorePath(),
        json_encode($store, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        LOCK_EX,
    );
}

function ensureAdminSchema(PDO $connection): void
{
    $connection->exec(
        "CREATE TABLE IF NOT EXISTS categories (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(80) NOT NULL UNIQUE,
            tamil_name VARCHAR(120) NOT NULL DEFAULT '',
            description VARCHAR(180) NOT NULL DEFAULT '',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    );

    $columns = $connection->query('SHOW COLUMNS FROM products')->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('stock_quantity', $columns, true)) {
        $connection->exec('ALTER TABLE products ADD COLUMN stock_quantity INT UNSIGNED NOT NULL DEFAULT 0');
    }
    if (!in_array('low_stock_threshold', $columns, true)) {
        $connection->exec('ALTER TABLE products ADD COLUMN low_stock_threshold INT UNSIGNED NOT NULL DEFAULT 10');
    }
    if (!in_array('featured', $columns, true)) {
        $connection->exec('ALTER TABLE products ADD COLUMN featured TINYINT(1) NOT NULL DEFAULT 0');
    }
    if (!in_array('active', $columns, true)) {
        $connection->exec('ALTER TABLE products ADD COLUMN active TINYINT(1) NOT NULL DEFAULT 1');
    }
    $categoryColumns = $connection->query('SHOW COLUMNS FROM categories')->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('tamil_name', $categoryColumns, true)) {
        $connection->exec("ALTER TABLE categories ADD COLUMN tamil_name VARCHAR(120) NOT NULL DEFAULT '' AFTER name");
    }
}

function getProducts(): array
{
    $connection = db();
    if (!$connection) {
        return array_values(array_filter(
            localStore()['products'],
            static fn (array $product): bool => (int) ($product['active'] ?? 1) === 1,
        ));
    }

    try {
        $statement = $connection->query(
            'SELECT id, name, tamil_name, category, price, mrp, unit, image, tag, featured, active,
                    stock_quantity, low_stock_threshold
             FROM products WHERE active = 1 ORDER BY featured DESC, id ASC',
        );
        $products = array_map(
            static fn (array $product): array => normalizeProduct($product),
            $statement->fetchAll(),
        );
        return $products ?: array_map(
            static fn (array $product): array => normalizeProduct($product),
            demoProducts(),
        );
    } catch (Throwable $error) {
        error_log('Udaya product query failed: ' . $error->getMessage());
        return array_values(array_filter(
            localStore()['products'],
            static fn (array $product): bool => (int) ($product['active'] ?? 1) === 1,
        ));
    }
}

function getAdminProducts(): array
{
    $connection = db();
    if ($connection) {
        try {
            $statement = $connection->query(
                'SELECT id, name, tamil_name, category, price, mrp, unit, image, tag, featured, active,
                        stock_quantity, low_stock_threshold
                 FROM products ORDER BY active DESC, featured DESC, id DESC',
            );
            return array_map(
                static fn (array $product): array => normalizeProduct($product),
                $statement->fetchAll(),
            );
        } catch (Throwable $error) {
            error_log('Udaya admin product query failed: ' . $error->getMessage());
        }
    }

    return localStore()['products'];
}

function getAdminCategories(): array
{
    $connection = db();
    if ($connection) {
        try {
            $statement = $connection->query('SELECT id, name, tamil_name, description FROM categories ORDER BY name ASC');
            $categories = $statement->fetchAll();
            if ($categories) {
                return $categories;
            }
        } catch (Throwable $error) {
            error_log('Udaya admin category query failed: ' . $error->getMessage());
        }
    }

    return localStore()['categories'];
}

function getAdminOrders(): array
{
    $connection = db();
    if ($connection) {
        try {
            $orders = $connection->query(
                'SELECT id, order_number, customer_name, phone, address, total, status, created_at
                 FROM orders ORDER BY created_at DESC, id DESC',
            )->fetchAll();
            $items = $connection->query(
                'SELECT order_id, product_name, quantity, unit_price, line_total
                 FROM order_items ORDER BY id ASC',
            )->fetchAll();
            $itemsByOrder = [];
            foreach ($items as $item) {
                $itemsByOrder[(int) $item['order_id']][] = $item;
            }
            foreach ($orders as &$order) {
                $order['items'] = $itemsByOrder[(int) $order['id']] ?? [];
            }
            unset($order);
            return $orders;
        } catch (Throwable $error) {
            error_log('Udaya admin order query failed: ' . $error->getMessage());
        }
    }

    $orders = localStore()['orders'];
    usort($orders, static fn (array $a, array $b): int => strcmp(
        (string) ($b['created_at'] ?? ''),
        (string) ($a['created_at'] ?? ''),
    ));
    return $orders;
}

function adminSaveProduct(array $input): void
{
    $name = trim((string) ($input['name'] ?? ''));
    $category = trim((string) ($input['category'] ?? ''));
    if ($name === '' || $category === '') {
        throw new InvalidArgumentException('Product name and category are required.');
    }

    $product = [
        'name' => $name,
        'tamil_name' => trim((string) ($input['tamil_name'] ?? '')),
        'category' => $category,
        'price' => max(0, (float) ($input['price'] ?? 0)),
        'mrp' => max(0, (float) ($input['mrp'] ?? 0)),
        'unit' => trim((string) ($input['unit'] ?? '1 box')) ?: '1 box',
        'image' => basename(trim((string) ($input['image'] ?? 'combo.jpg'))) ?: 'combo.jpg',
        'tag' => trim((string) ($input['tag'] ?? '')),
        'featured' => isset($input['featured']) ? 1 : 0,
        'active' => isset($input['active']) ? 1 : 0,
        'stock_quantity' => max(0, (int) ($input['stock_quantity'] ?? 0)),
        'low_stock_threshold' => max(0, (int) ($input['low_stock_threshold'] ?? 10)),
    ];
    $id = max(0, (int) ($input['id'] ?? 0));
    $connection = db();

    if ($connection) {
        $params = [
            ':name' => $product['name'],
            ':tamil_name' => $product['tamil_name'],
            ':category' => $product['category'],
            ':price' => $product['price'],
            ':mrp' => $product['mrp'],
            ':unit' => $product['unit'],
            ':image' => $product['image'],
            ':tag' => $product['tag'],
            ':featured' => $product['featured'],
            ':active' => $product['active'],
            ':stock_quantity' => $product['stock_quantity'],
            ':low_stock_threshold' => $product['low_stock_threshold'],
        ];
        if ($id > 0) {
            $params[':id'] = $id;
            $statement = $connection->prepare(
                'UPDATE products SET name=:name, tamil_name=:tamil_name, category=:category, price=:price,
                 mrp=:mrp, unit=:unit, image=:image, tag=:tag, featured=:featured, active=:active,
                 stock_quantity=:stock_quantity, low_stock_threshold=:low_stock_threshold WHERE id=:id',
            );
        } else {
            $statement = $connection->prepare(
                'INSERT INTO products
                 (name, tamil_name, category, price, mrp, unit, image, tag, featured, active, stock_quantity, low_stock_threshold)
                 VALUES (:name, :tamil_name, :category, :price, :mrp, :unit, :image, :tag, :featured, :active, :stock_quantity, :low_stock_threshold)',
            );
        }
        $statement->execute($params);
        return;
    }

    $store = localStore();
    if ($id > 0) {
        $updated = false;
        foreach ($store['products'] as &$existing) {
            if ((int) $existing['id'] === $id) {
                $existing = normalizeProduct(array_merge($existing, $product, ['id' => $id]));
                $updated = true;
                break;
            }
        }
        unset($existing);
        if (!$updated) {
            throw new RuntimeException('Product could not be found.');
        }
    } else {
        $ids = array_map(static fn (array $item): int => (int) $item['id'], $store['products']);
        $product['id'] = $ids ? max($ids) + 1 : 1;
        $store['products'][] = normalizeProduct($product);
    }
    saveLocalStore($store);
}

function adminArchiveProduct(int $id): void
{
    if ($id <= 0) {
        throw new InvalidArgumentException('Invalid product.');
    }
    $connection = db();
    if ($connection) {
        $statement = $connection->prepare('UPDATE products SET active = 0 WHERE id = :id');
        $statement->execute([':id' => $id]);
        return;
    }

    $store = localStore();
    foreach ($store['products'] as &$product) {
        if ((int) $product['id'] === $id) {
            $product['active'] = 0;
            saveLocalStore($store);
            return;
        }
    }
    unset($product);
    throw new RuntimeException('Product could not be found.');
}

function adminSaveCategory(string $name, string $description = '', int $id = 0, string $tamilName = ''): void
{
    $name = trim($name);
    $tamilName = trim($tamilName) ?: categoryTamil($name);
    $description = trim($description);
    if ($name === '') {
        throw new InvalidArgumentException('Category name is required.');
    }
    $connection = db();
    if ($connection) {
        if ($id > 0) {
            $find = $connection->prepare('SELECT name FROM categories WHERE id = :id');
            $find->execute([':id' => $id]);
            $existing = $find->fetch();
            if (!$existing) {
                throw new RuntimeException('Category could not be found.');
            }
            $statement = $connection->prepare(
                'UPDATE categories SET name=:name, tamil_name=:tamil_name, description=:description WHERE id=:id',
            );
            $statement->execute([
                ':name' => $name,
                ':tamil_name' => $tamilName,
                ':description' => $description,
                ':id' => $id,
            ]);
            if ((string) $existing['name'] !== $name) {
                $products = $connection->prepare('UPDATE products SET category=:new_name WHERE category=:old_name');
                $products->execute([':new_name' => $name, ':old_name' => $existing['name']]);
            }
        } else {
            $statement = $connection->prepare(
                'INSERT INTO categories (name, tamil_name, description) VALUES (:name, :tamil_name, :description)',
            );
            $statement->execute([
                ':name' => $name,
                ':tamil_name' => $tamilName,
                ':description' => $description,
            ]);
        }
        return;
    }

    $store = localStore();
    if ($id > 0) {
        $oldName = '';
        foreach ($store['categories'] as &$category) {
            if ((int) $category['id'] === $id) {
                $oldName = (string) $category['name'];
                $category['name'] = $name;
                $category['tamil_name'] = $tamilName;
                $category['description'] = $description;
                break;
            }
        }
        unset($category);
        if ($oldName === '') {
            throw new RuntimeException('Category could not be found.');
        }
        foreach ($store['products'] as &$product) {
            if ((string) $product['category'] === $oldName) {
                $product['category'] = $name;
            }
        }
        unset($product);
    } else {
        $ids = array_map(static fn (array $item): int => (int) $item['id'], $store['categories']);
        $store['categories'][] = [
            'id' => $ids ? max($ids) + 1 : 1,
            'name' => $name,
            'tamil_name' => $tamilName,
            'description' => $description,
        ];
    }
    saveLocalStore($store);
}

function adminDeleteCategory(int $id): void
{
    if ($id <= 0) {
        throw new InvalidArgumentException('Invalid category.');
    }
    $connection = db();
    if ($connection) {
        $find = $connection->prepare('SELECT name FROM categories WHERE id = :id');
        $find->execute([':id' => $id]);
        $category = $find->fetch();
        if (!$category) {
            throw new RuntimeException('Category could not be found.');
        }
        $used = $connection->prepare('SELECT COUNT(*) FROM products WHERE category = :name AND active = 1');
        $used->execute([':name' => $category['name']]);
        if ((int) $used->fetchColumn() > 0) {
            throw new RuntimeException('Move or archive its products before deleting this category.');
        }
        $delete = $connection->prepare('DELETE FROM categories WHERE id = :id');
        $delete->execute([':id' => $id]);
        return;
    }

    $store = localStore();
    $categoryName = '';
    foreach ($store['categories'] as $category) {
        if ((int) $category['id'] === $id) {
            $categoryName = (string) $category['name'];
            break;
        }
    }
    if ($categoryName === '') {
        throw new RuntimeException('Category could not be found.');
    }
    foreach ($store['products'] as $product) {
        if ((string) $product['category'] === $categoryName && (int) ($product['active'] ?? 1) === 1) {
            throw new RuntimeException('Move or archive its products before deleting this category.');
        }
    }
    $store['categories'] = array_values(array_filter(
        $store['categories'],
        static fn (array $category): bool => (int) $category['id'] !== $id,
    ));
    saveLocalStore($store);
}

function adminUpdateInventory(int $id, int $stock, int $threshold): void
{
    $stock = max(0, $stock);
    $threshold = max(0, $threshold);
    $connection = db();
    if ($connection) {
        $statement = $connection->prepare(
            'UPDATE products SET stock_quantity=:stock_quantity, low_stock_threshold=:low_stock_threshold WHERE id=:id',
        );
        $statement->execute([
            ':stock_quantity' => $stock,
            ':low_stock_threshold' => $threshold,
            ':id' => $id,
        ]);
        return;
    }
    $store = localStore();
    foreach ($store['products'] as &$product) {
        if ((int) $product['id'] === $id) {
            $product['stock_quantity'] = $stock;
            $product['low_stock_threshold'] = $threshold;
            saveLocalStore($store);
            return;
        }
    }
    unset($product);
    throw new RuntimeException('Product could not be found.');
}

function adminUpdateOrderStatus(int $id, string $status): void
{
    $allowed = ['new', 'confirmed', 'packed', 'shipped', 'delivered', 'cancelled'];
    if (!in_array($status, $allowed, true)) {
        throw new InvalidArgumentException('Invalid order status.');
    }
    $connection = db();
    if ($connection) {
        $statement = $connection->prepare('UPDATE orders SET status=:status WHERE id=:id');
        $statement->execute([':status' => $status, ':id' => $id]);
        return;
    }
    $store = localStore();
    foreach ($store['orders'] as &$order) {
        if ((int) $order['id'] === $id) {
            $order['status'] = $status;
            saveLocalStore($store);
            return;
        }
    }
    unset($order);
    throw new RuntimeException('Order could not be found.');
}

function e(string|int|float|null $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
