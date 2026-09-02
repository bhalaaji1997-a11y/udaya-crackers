<?php
declare(strict_types=1);
require_once __DIR__ . '/config.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$payload = json_decode(file_get_contents('php://input'), true);
$name = trim((string) ($payload['name'] ?? ''));
$phone = trim((string) ($payload['phone'] ?? ''));
$address = trim((string) ($payload['address'] ?? ''));
$items = is_array($payload['items'] ?? null) ? $payload['items'] : [];

if ($name === '' || $phone === '' || $address === '' || count($items) === 0) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Please complete your details and add at least one item.']);
    exit;
}

$catalog = [];
foreach (getProducts() as $product) {
    $catalog[(int) $product['id']] = $product;
}

$cleanItems = [];
$total = 0.0;
foreach ($items as $item) {
    $id = (int) ($item['id'] ?? 0);
    $quantity = max(1, min(99, (int) ($item['quantity'] ?? 1)));
    if (!isset($catalog[$id])) {
        continue;
    }
    $product = $catalog[$id];
    $lineTotal = (float) $product['price'] * $quantity;
    $total += $lineTotal;
    $cleanItems[] = [
        'product_id' => $id,
        'name' => $product['name'],
        'quantity' => $quantity,
        'price' => (float) $product['price'],
        'line_total' => $lineTotal,
    ];
}

if (!$cleanItems) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Your bag is empty.']);
    exit;
}

$orderNumber = 'UD' . date('ymd') . strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));
$connection = db();

if ($connection) {
    try {
        $connection->beginTransaction();
        $stockCheck = $connection->prepare(
            'SELECT name, stock_quantity FROM products WHERE id = :id AND active = 1 FOR UPDATE',
        );
        foreach ($cleanItems as $item) {
            $stockCheck->execute([':id' => $item['product_id']]);
            $stock = $stockCheck->fetch();
            if (!$stock || (int) $stock['stock_quantity'] < $item['quantity']) {
                throw new InvalidArgumentException(
                    $item['name'] . ' has only ' . (int) ($stock['stock_quantity'] ?? 0) . ' left in stock.',
                );
            }
        }
        $order = $connection->prepare(
            'INSERT INTO orders (order_number, customer_name, phone, address, total, status)
             VALUES (:order_number, :customer_name, :phone, :address, :total, :status)',
        );
        $order->execute([
            ':order_number' => $orderNumber,
            ':customer_name' => $name,
            ':phone' => $phone,
            ':address' => $address,
            ':total' => $total,
            ':status' => 'new',
        ]);
        $orderId = (int) $connection->lastInsertId();
        $line = $connection->prepare(
            'INSERT INTO order_items (order_id, product_id, product_name, quantity, unit_price, line_total)
             VALUES (:order_id, :product_id, :product_name, :quantity, :unit_price, :line_total)',
        );
        foreach ($cleanItems as $item) {
            $line->execute([
                ':order_id' => $orderId,
                ':product_id' => $item['product_id'],
                ':product_name' => $item['name'],
                ':quantity' => $item['quantity'],
                ':unit_price' => $item['price'],
                ':line_total' => $item['line_total'],
            ]);
        }
        $decrement = $connection->prepare(
            'UPDATE products SET stock_quantity = stock_quantity - :quantity WHERE id = :id',
        );
        foreach ($cleanItems as $item) {
            $decrement->execute([
                ':quantity' => $item['quantity'],
                ':id' => $item['product_id'],
            ]);
        }
        $connection->commit();
    } catch (Throwable $error) {
        if ($connection->inTransaction()) {
            $connection->rollBack();
        }
        error_log('Udaya order save failed: ' . $error->getMessage());
        http_response_code($error instanceof InvalidArgumentException ? 422 : 500);
        echo json_encode([
            'success' => false,
            'message' => $error instanceof InvalidArgumentException
                ? $error->getMessage()
                : 'We could not save your order. Please try again.',
        ]);
        exit;
    }
} else {
    $store = localStore();
    foreach ($cleanItems as $item) {
        foreach ($store['products'] as $product) {
            if ((int) $product['id'] === $item['product_id'] && (int) ($product['stock_quantity'] ?? 0) < $item['quantity']) {
                http_response_code(422);
                echo json_encode([
                    'success' => false,
                    'message' => $item['name'] . ' has only ' . (int) ($product['stock_quantity'] ?? 0) . ' left in stock.',
                ]);
                exit;
            }
        }
    }

    foreach ($cleanItems as $item) {
        foreach ($store['products'] as &$product) {
            if ((int) $product['id'] === $item['product_id']) {
                $product['stock_quantity'] = max(0, (int) $product['stock_quantity'] - $item['quantity']);
                break;
            }
        }
        unset($product);
    }
    $orderIds = array_map(static fn (array $order): int => (int) ($order['id'] ?? 0), $store['orders']);
    $store['orders'][] = [
        'id' => $orderIds ? max($orderIds) + 1 : 1,
        'order_number' => $orderNumber,
        'customer_name' => $name,
        'phone' => $phone,
        'address' => $address,
        'total' => $total,
        'status' => 'new',
        'created_at' => date('c'),
        'items' => array_map(
            static fn (array $item): array => [
                'product_name' => $item['name'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['price'],
                'line_total' => $item['line_total'],
            ],
            $cleanItems,
        ),
    ];
    saveLocalStore($store);
}

echo json_encode(['success' => true, 'order_number' => $orderNumber, 'total' => $total]);
