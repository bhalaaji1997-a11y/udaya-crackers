<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/config.php';

session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
    'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
]);
session_start();

$adminPassword = trim((string) (getenv('ADMIN_PASSWORD') ?: ''));
$authRequired = $adminPassword !== '';
$isAuthenticated = !$authRequired || !empty($_SESSION['udaya_admin']);
$page = (string) ($_GET['page'] ?? 'dashboard');
$allowedPages = ['dashboard', 'products', 'orders', 'categories', 'inventory'];
if (!in_array($page, $allowedPages, true)) {
    $page = 'dashboard';
}

if (empty($_SESSION['udaya_admin_csrf'])) {
    $_SESSION['udaya_admin_csrf'] = bin2hex(random_bytes(24));
}
$csrf = (string) $_SESSION['udaya_admin_csrf'];
$flash = '';
$flashType = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && (string) ($_POST['action'] ?? '') === 'login') {
    $password = (string) ($_POST['password'] ?? '');
    if ($adminPassword !== '' && hash_equals($adminPassword, $password)) {
        session_regenerate_id(true);
        $_SESSION['udaya_admin'] = true;
        header('Location: index.php');
        exit;
    }
    $flash = 'That password did not match. Try again.';
    $flashType = 'error';
}

if ((string) ($_GET['action'] ?? '') === 'logout') {
    unset($_SESSION['udaya_admin']);
    header('Location: index.php');
    exit;
}

if ($isAuthenticated && $_SERVER['REQUEST_METHOD'] === 'POST' && (string) ($_POST['action'] ?? '') !== 'login') {
    $postedCsrf = (string) ($_POST['csrf'] ?? '');
    if (!hash_equals($csrf, $postedCsrf)) {
        $flash = 'Your session has expired. Refresh and try again.';
        $flashType = 'error';
    } else {
        try {
            $action = (string) ($_POST['action'] ?? '');
            switch ($action) {
                case 'save_product':
                    adminSaveProduct($_POST);
                    $page = 'products';
                    $flash = ((int) ($_POST['id'] ?? 0) > 0) ? 'Product updated.' : 'Product added to the catalogue.';
                    break;
                case 'archive_product':
                    adminArchiveProduct((int) ($_POST['id'] ?? 0));
                    $page = 'products';
                    $flash = 'Product archived from the storefront.';
                    break;
                case 'save_category':
                    adminSaveCategory(
                        (string) ($_POST['name'] ?? ''),
                        (string) ($_POST['description'] ?? ''),
                        (int) ($_POST['id'] ?? 0),
                        (string) ($_POST['tamil_name'] ?? ''),
                    );
                    $page = 'categories';
                    $flash = 'Category saved.';
                    break;
                case 'delete_category':
                    adminDeleteCategory((int) ($_POST['id'] ?? 0));
                    $page = 'categories';
                    $flash = 'Category deleted.';
                    break;
                case 'update_inventory':
                    adminUpdateInventory(
                        (int) ($_POST['id'] ?? 0),
                        (int) ($_POST['stock_quantity'] ?? 0),
                        (int) ($_POST['low_stock_threshold'] ?? 10),
                    );
                    $page = 'inventory';
                    $flash = 'Inventory updated.';
                    break;
                case 'update_order':
                    adminUpdateOrderStatus(
                        (int) ($_POST['id'] ?? 0),
                        (string) ($_POST['status'] ?? 'new'),
                    );
                    $page = 'orders';
                    $flash = 'Order status updated.';
                    break;
                default:
                    throw new InvalidArgumentException('Unknown admin action.');
            }
        } catch (Throwable $error) {
            $flash = $error->getMessage();
            $flashType = 'error';
        }
    }
}

function adminField(mixed $value): string
{
    return e($value);
}

function adminPageUrl(string $page): string
{
    return 'index.php?page=' . rawurlencode($page);
}

function stockState(array $product): string
{
    $stock = (int) ($product['stock_quantity'] ?? 0);
    $threshold = (int) ($product['low_stock_threshold'] ?? 10);
    return $stock <= 0 ? 'out' : ($stock <= $threshold ? 'low' : 'good');
}

function statusLabel(string $status): string
{
    return ucfirst($status);
}

if (!$isAuthenticated):
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin login · Udaya Crackers</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body class="admin-login-page">
  <main class="login-card">
    <a class="admin-brand" href="../"><span class="admin-brand-mark">U</span><span><strong>udaya</strong><small>CRACKERS · ADMIN</small></span></a>
    <p class="admin-eyebrow">BACK OFFICE</p>
    <h1>Welcome back.</h1>
    <p class="login-copy">Manage the catalogue, stock, and orders that keep every celebration moving.</p>
    <?php if ($flash): ?><div class="flash flash-error"><?= adminField($flash) ?></div><?php endif; ?>
    <form method="post" class="login-form">
      <input type="hidden" name="action" value="login">
      <label>Admin password<input type="password" name="password" autocomplete="current-password" required autofocus></label>
      <button class="admin-button admin-button-primary" type="submit">Enter dashboard <span>↗</span></button>
    </form>
    <a class="back-link" href="../">← Back to storefront</a>
  </main>
</body>
</html>
<?php
exit;
endif;

$products = getAdminProducts();
$categories = getAdminCategories();
$orders = getAdminOrders();
$activeProducts = array_values(array_filter($products, static fn (array $product): bool => (int) $product['active'] === 1));
$lowStockProducts = array_values(array_filter(
    $activeProducts,
    static fn (array $product): bool => stockState($product) !== 'good',
));
$newOrders = array_values(array_filter($orders, static fn (array $order): bool => (string) $order['status'] === 'new'));
$editProduct = null;
if ($page === 'products' && isset($_GET['edit']) && (int) $_GET['edit'] > 0) {
    foreach ($products as $product) {
        if ((int) $product['id'] === (int) $_GET['edit']) {
            $editProduct = $product;
            break;
        }
    }
}
$formProduct = $editProduct ?: [
    'id' => 0,
    'name' => '',
    'tamil_name' => '',
    'category' => $categories[0]['name'] ?? 'Combos',
    'price' => '',
    'mrp' => '',
    'unit' => '1 box',
    'image' => 'combo.jpg',
    'tag' => '',
    'featured' => 0,
    'active' => 1,
    'stock_quantity' => 0,
    'low_stock_threshold' => 10,
];
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Udaya Crackers catalogue, inventory, and order management.">
  <title><?= adminField(ucfirst($page)) ?> · Udaya Admin</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body class="admin-page">
  <div class="admin-shell">
    <aside class="admin-sidebar">
      <a class="admin-brand" href="index.php"><span class="admin-brand-mark">U</span><span><strong>udaya</strong><small>CRACKERS · ADMIN</small></span></a>
      <div class="sidebar-season"><span class="season-dot"></span><span>Festive season 2026</span></div>
      <nav class="admin-nav" aria-label="Admin navigation">
        <p class="nav-label" data-i18n="admin.workspace">Workspace</p>
        <a class="<?= $page === 'dashboard' ? 'active' : '' ?>" href="<?= adminPageUrl('dashboard') ?>"><span>✦</span> <span class="nav-text" data-i18n="admin.overview">Overview</span></a>
        <a class="<?= $page === 'products' ? 'active' : '' ?>" href="<?= adminPageUrl('products') ?>"><span>▦</span> <span class="nav-text" data-i18n="admin.products">Products</span> <b><?= count($activeProducts) ?></b></a>
        <a class="<?= $page === 'inventory' ? 'active' : '' ?>" href="<?= adminPageUrl('inventory') ?>"><span>◌</span> <span class="nav-text" data-i18n="admin.inventory">Inventory</span> <?php if ($lowStockProducts): ?><b class="nav-alert"><?= count($lowStockProducts) ?></b><?php endif; ?></a>
        <a class="<?= $page === 'categories' ? 'active' : '' ?>" href="<?= adminPageUrl('categories') ?>"><span>✺</span> <span class="nav-text" data-i18n="admin.categories">Categories</span> <b><?= count($categories) ?></b></a>
        <a class="<?= $page === 'orders' ? 'active' : '' ?>" href="<?= adminPageUrl('orders') ?>"><span>↗</span> <span class="nav-text" data-i18n="admin.orders">Orders</span> <?php if ($newOrders): ?><b class="nav-alert"><?= count($newOrders) ?></b><?php endif; ?></a>
        <p class="nav-label nav-label-spaced" data-i18n="admin.shortcuts">Shortcuts</p>
        <a href="../" target="_blank"><span>↗</span> <span class="nav-text" data-i18n="admin.viewStore">View storefront</span></a>
        <a href="?action=logout"><span>×</span> <span class="nav-text" data-i18n="admin.signOut">Sign out</span></a>
      </nav>
      <div class="sidebar-note"><span>✦</span><strong>Keep it bright.</strong><p>Every well-packed box makes a better celebration.</p></div>
    </aside>

    <main class="admin-main">
      <header class="admin-topbar">
        <div class="mobile-brand"><span class="admin-brand-mark">U</span><strong>udaya admin</strong></div>
        <div class="topbar-meta"><span class="live-dot"></span> <span data-i18n="admin.live">Store is live</span> <a href="../" target="_blank" data-i18n="admin.openStore">Open storefront ↗</a><button class="language-toggle admin-language-toggle" type="button" data-language-toggle>தமிழ்</button></div>
      </header>
      <div class="admin-content">
        <?php if (!$authRequired): ?><div class="demo-banner"><span>✦</span><div><strong data-i18n="admin.preview">Preview mode</strong><p data-i18n="admin.previewCopy">Set ADMIN_PASSWORD before publishing to protect this dashboard.</p></div></div><?php endif; ?>
        <?php if ($flash): ?><div class="flash flash-<?= $flashType === 'error' ? 'error' : 'success' ?>"><span><?= $flashType === 'error' ? '!' : '✓' ?></span><?= adminField($flash) ?></div><?php endif; ?>

        <?php if ($page === 'dashboard'): ?>
          <div class="page-heading"><div><p class="admin-eyebrow" data-i18n="admin.goodMorning">GOOD MORNING, ADMIN</p><h1 data-i18n="admin.brightPicture">Here’s the bright picture.</h1><p data-i18n="admin.headingCopy">Keep the catalogue fresh, stock ready, and every order moving.</p></div><a class="admin-button admin-button-primary" href="<?= adminPageUrl('products') ?>&new=1"><span data-i18n="admin.addProduct">Add a product</span> <span>+</span></a></div>
          <section class="metric-grid">
            <article class="metric-card metric-orange"><span class="metric-icon">▦</span><p data-i18n="admin.activeProducts">Active products</p><strong><?= count($activeProducts) ?></strong><small data-i18n="admin.readyStorefront">Ready on the storefront</small></article>
            <article class="metric-card"><span class="metric-icon">✺</span><p data-i18n="admin.categoryCount">Categories</p><strong><?= count($categories) ?></strong><small data-i18n="admin.findSparkle">Ways to find the sparkle</small></article>
            <article class="metric-card metric-gold"><span class="metric-icon">◌</span><p data-i18n="admin.needAttention">Need attention</p><strong><?= count($lowStockProducts) ?></strong><small data-i18n="admin.lowOrOut">Low or out of stock</small></article>
            <article class="metric-card metric-indigo"><span class="metric-icon">↗</span><p data-i18n="admin.newOrders">New orders</p><strong><?= count($newOrders) ?></strong><small data-i18n="admin.waitingConfirmation">Waiting for confirmation</small></article>
          </section>
          <div class="dashboard-grid">
            <section class="panel recent-panel">
              <div class="panel-heading"><div><p class="admin-eyebrow" data-i18n="admin.latestActivity">LATEST ACTIVITY</p><h2 data-i18n="admin.recentOrders">Recent orders</h2></div><a class="text-action" href="<?= adminPageUrl('orders') ?>"><span data-i18n="admin.viewAll">View all</span> ↗</a></div>
              <?php if (!$orders): ?><div class="empty-state"><span>✦</span><h3>Your first order will land here.</h3><p>Orders placed through the storefront appear in this queue.</p></div>
              <?php else: ?><div class="compact-orders"><?php foreach (array_slice($orders, 0, 5) as $order): ?><div class="compact-order"><div class="order-avatar"><?= adminField(strtoupper(substr((string) $order['customer_name'], 0, 1))) ?></div><div class="compact-order-copy"><strong><?= adminField($order['customer_name']) ?></strong><span><?= adminField($order['order_number']) ?> · <?= adminField(date('d M, g:i A', strtotime((string) $order['created_at']))) ?></span></div><strong class="compact-total">₹<?= adminField(number_format((float) $order['total'])) ?></strong><span class="status status-<?= adminField($order['status']) ?>"><?= adminField(statusLabel((string) $order['status'])) ?></span></div><?php endforeach; ?></div><?php endif; ?>
            </section>
            <section class="panel attention-panel">
              <div class="panel-heading"><div><p class="admin-eyebrow" data-i18n="admin.inventoryWatch">INVENTORY WATCH</p><h2 data-i18n="admin.runningLow">Running low</h2></div><a class="text-action" href="<?= adminPageUrl('inventory') ?>"><span data-i18n="admin.manage">Manage</span> ↗</a></div>
              <?php if (!$lowStockProducts): ?><div class="empty-state small"><span>✓</span><h3>All stocked up.</h3><p>Nothing needs a restock right now.</p></div>
              <?php else: ?><div class="low-stock-list"><?php foreach (array_slice($lowStockProducts, 0, 5) as $product): ?><a class="low-stock-row" href="<?= adminPageUrl('inventory') ?>"><img src="../public/images/<?= adminField($product['image']) ?>" alt=""><span><strong><?= adminField($product['name']) ?></strong><small><?= adminField($product['category']) ?></small></span><b class="stock-number stock-<?= stockState($product) ?>"><?= adminField($product['stock_quantity']) ?><small>left</small></b></a><?php endforeach; ?></div><?php endif; ?>
            </section>
          </div>
          <section class="panel quick-panel"><div><p class="admin-eyebrow" data-i18n="admin.quickActions">QUICK ACTIONS</p><h2 data-i18n="admin.nextMove">Make the next move.</h2></div><div class="quick-actions"><a href="<?= adminPageUrl('products') ?>&new=1"><span>+</span><strong data-i18n="admin.addProduct">Add product</strong><small>Grow your collection</small></a><a href="<?= adminPageUrl('inventory') ?>"><span>◌</span><strong data-i18n="admin.updateStock">Update stock</strong><small>Keep shelves ready</small></a><a href="<?= adminPageUrl('categories') ?>&new=1"><span>✺</span><strong data-i18n="admin.newCategory">New category</strong><small>Organise the magic</small></a><a href="<?= adminPageUrl('orders') ?>"><span>↗</span><strong data-i18n="admin.processOrders">Process orders</strong><small><?= count($newOrders) ?> waiting now</small></a></div></section>

        <?php elseif ($page === 'products'): ?>
          <div class="page-heading"><div><p class="admin-eyebrow" data-i18n="admin.catalogue">CATALOGUE</p><h1 data-i18n="admin.catalogueHeading">Products that spark joy.</h1><p data-i18n="admin.catalogueCopy">Add, edit, feature, or archive items from the storefront.</p></div><a class="admin-button admin-button-primary" href="<?= adminPageUrl('products') ?>&new=1"><span data-i18n="admin.addProduct">Add product</span> <span>+</span></a></div>
          <?php $showProductForm = isset($_GET['new']) || $editProduct; ?>
          <?php if ($showProductForm): ?><section class="panel product-editor"><div class="panel-heading"><div><p class="admin-eyebrow"><?= $editProduct ? 'EDIT PRODUCT' : 'NEW PRODUCT' ?></p><h2><?= $editProduct ? 'Refine the details.' : 'Add a new sparkle.' ?></h2></div><a class="close-editor" href="<?= adminPageUrl('products') ?>">×</a></div><form method="post" class="admin-form"><input type="hidden" name="action" value="save_product"><input type="hidden" name="csrf" value="<?= adminField($csrf) ?>"><input type="hidden" name="id" value="<?= adminField($formProduct['id']) ?>"><div class="form-grid form-grid-wide"><label>Product name<input name="name" required value="<?= adminField($formProduct['name']) ?>" placeholder="e.g. Peacock Fountain"></label><label>Tamil name<input name="tamil_name" value="<?= adminField($formProduct['tamil_name']) ?>" placeholder="Optional Tamil name"></label><label>Category<select name="category" required><?php foreach ($categories as $category): ?><option value="<?= adminField($category['name']) ?>" <?= $formProduct['category'] === $category['name'] ? 'selected' : '' ?>><?= adminField($category['name']) ?></option><?php endforeach; ?></select></label><label>Image<select name="image"><?php foreach (['combo.jpg' => 'Combo image', 'fountains.jpg' => 'Fountains image', 'sparklers.jpg' => 'Sparklers image'] as $image => $label): ?><option value="<?= $image ?>" <?= $formProduct['image'] === $image ? 'selected' : '' ?>><?= $label ?></option><?php endforeach; ?></select></label><label>Sale price <span class="input-prefix">₹</span><input class="has-prefix" name="price" type="number" min="0" step="0.01" required value="<?= adminField($formProduct['price']) ?>"></label><label>MRP <span class="input-prefix">₹</span><input class="has-prefix" name="mrp" type="number" min="0" step="0.01" required value="<?= adminField($formProduct['mrp']) ?>"></label><label>Unit / pack size<input name="unit" required value="<?= adminField($formProduct['unit']) ?>" placeholder="1 box · 10 pieces"></label><label>Badge / tag<input name="tag" value="<?= adminField($formProduct['tag']) ?>" placeholder="Popular, New, Best value"></label><label>Stock quantity<input name="stock_quantity" type="number" min="0" required value="<?= adminField($formProduct['stock_quantity']) ?>"></label><label>Low stock alert at<input name="low_stock_threshold" type="number" min="0" required value="<?= adminField($formProduct['low_stock_threshold']) ?>"></label></div><div class="check-row"><label class="checkbox-label"><input type="checkbox" name="featured" <?= (int) $formProduct['featured'] === 1 ? 'checked' : '' ?>><span>Feature this product</span></label><label class="checkbox-label"><input type="checkbox" name="active" <?= (int) $formProduct['active'] === 1 ? 'checked' : '' ?>><span>Visible on storefront</span></label></div><div class="form-actions"><a class="admin-button admin-button-quiet" href="<?= adminPageUrl('products') ?>">Cancel</a><button class="admin-button admin-button-primary" type="submit">Save product <span>↗</span></button></div></form></section><?php endif; ?>
          <section class="panel"><div class="panel-heading"><div><p class="admin-eyebrow" data-i18n="admin.allItems">ALL ITEMS</p><h2><span data-i18n="admin.productCatalogue">Product catalogue</span> <span class="count-badge"><?= count($products) ?></span></h2></div><input class="table-search" type="search" placeholder="Search products" data-table-search="#products-table"></div><div class="table-scroll"><table class="admin-table" id="products-table"><thead><tr><th>Product</th><th>Category</th><th>Price</th><th>Stock</th><th>Status</th><th></th></tr></thead><tbody><?php foreach ($products as $product): ?><tr data-search="<?= adminField(strtolower($product['name'] . ' ' . $product['category'])) ?>"><td><div class="table-product"><img src="../public/images/<?= adminField($product['image']) ?>" alt=""><div><strong><?= adminField($product['name']) ?></strong><small><?= adminField($product['unit']) ?></small></div></div></td><td><?= adminField($product['category']) ?></td><td><strong>₹<?= adminField(number_format((float) $product['price'])) ?></strong><small class="mrp">₹<?= adminField(number_format((float) $product['mrp'])) ?></small></td><td><span class="stock-pill stock-pill-<?= stockState($product) ?>"><?= adminField($product['stock_quantity']) ?> units</span></td><td><span class="status <?= (int) $product['active'] === 1 ? 'status-live' : 'status-off' ?>"><?= (int) $product['active'] === 1 ? 'Live' : 'Archived' ?></span></td><td><div class="row-actions"><a href="<?= adminPageUrl('products') ?>&edit=<?= adminField($product['id']) ?>">Edit</a><?php if ((int) $product['active'] === 1): ?><form method="post" onsubmit="return confirm('Archive this product from the storefront?')"><input type="hidden" name="action" value="archive_product"><input type="hidden" name="csrf" value="<?= adminField($csrf) ?>"><input type="hidden" name="id" value="<?= adminField($product['id']) ?>"><button type="submit">Archive</button></form><?php endif; ?></div></td></tr><?php endforeach; ?></tbody></table></div></section>

        <?php elseif ($page === 'inventory'): ?>
          <div class="page-heading"><div><p class="admin-eyebrow" data-i18n="admin.stockControl">STOCK CONTROL</p><h1 data-i18n="admin.stockHeading">Keep the shelves bright.</h1><p>Set available quantities and get an alert before anything sells out.</p></div><div class="heading-stat"><strong><?= count($lowStockProducts) ?></strong><span>items need attention</span></div></div>
          <section class="panel"><div class="panel-heading"><div><p class="admin-eyebrow" data-i18n="admin.liveInventory">LIVE INVENTORY</p><h2><span data-i18n="admin.stockLevels">Stock levels</span> <span class="count-badge"><?= count($products) ?></span></h2></div><input class="table-search" type="search" placeholder="Search products" data-table-search="#inventory-table"></div><div class="table-scroll"><table class="admin-table inventory-table" id="inventory-table"><thead><tr><th>Product</th><th>Current stock</th><th>Alert threshold</th><th>Health</th><th></th></tr></thead><tbody><?php foreach ($products as $product): ?><tr data-search="<?= adminField(strtolower($product['name'] . ' ' . $product['category'])) ?>"><td><div class="table-product"><img src="../public/images/<?= adminField($product['image']) ?>" alt=""><div><strong><?= adminField($product['name']) ?></strong><small><?= adminField($product['category']) ?></small></div></div></td><td colspan="2"><form method="post" class="inline-inventory"><input type="hidden" name="action" value="update_inventory"><input type="hidden" name="csrf" value="<?= adminField($csrf) ?>"><input type="hidden" name="id" value="<?= adminField($product['id']) ?>"><label><span class="sr-only">Stock quantity</span><input name="stock_quantity" type="number" min="0" value="<?= adminField($product['stock_quantity']) ?>"></label><span class="threshold-separator">alert at</span><label><span class="sr-only">Low stock threshold</span><input name="low_stock_threshold" type="number" min="0" value="<?= adminField($product['low_stock_threshold']) ?>"></label></form></td><td><span class="stock-health stock-health-<?= stockState($product) ?>"><i></i><?= stockState($product) === 'good' ? 'Healthy' : (stockState($product) === 'low' ? 'Running low' : 'Out of stock') ?></span></td><td><button class="save-row-button" type="button" data-submit-form>Save</button></td></tr><?php endforeach; ?></tbody></table></div><p class="table-hint">Tip: press Enter in a quantity field or use Save to apply a row.</p></section>

        <?php elseif ($page === 'categories'): ?>
          <div class="page-heading"><div><p class="admin-eyebrow" data-i18n="admin.collections">COLLECTIONS</p><h1 data-i18n="admin.collectionsHeading">Organise the magic.</h1><p>Use clear categories to help customers find their kind of celebration.</p></div><a class="admin-button admin-button-primary" href="<?= adminPageUrl('categories') ?>&new=1"><span data-i18n="admin.newCategory">New category</span> <span>+</span></a></div>
          <?php $showCategoryForm = isset($_GET['new']) || isset($_GET['edit']); $editCategory = null; if (isset($_GET['edit'])) foreach ($categories as $category) if ((int) $category['id'] === (int) $_GET['edit']) $editCategory = $category; ?>
          <?php if ($showCategoryForm): ?><section class="panel compact-editor"><div class="panel-heading"><div><p class="admin-eyebrow"><?= $editCategory ? 'EDIT CATEGORY' : 'NEW CATEGORY' ?></p><h2><?= $editCategory ? 'Polish the collection.' : 'Create a new collection.' ?></h2></div><a class="close-editor" href="<?= adminPageUrl('categories') ?>">×</a></div><form method="post" class="admin-form"><input type="hidden" name="action" value="save_category"><input type="hidden" name="csrf" value="<?= adminField($csrf) ?>"><input type="hidden" name="id" value="<?= adminField($editCategory['id'] ?? 0) ?>"><div class="form-grid"><label><span data-i18n="admin.categoryName">Category name</span><input name="name" required value="<?= adminField($editCategory['name'] ?? '') ?>" placeholder="e.g. Gift boxes"></label><label><span data-i18n="admin.tamilName">Tamil name</span><input name="tamil_name" value="<?= adminField($editCategory['tamil_name'] ?? '') ?>" placeholder="e.g. பரிசுப் பெட்டிகள்"></label><label><span data-i18n="admin.description">Description</span><input name="description" value="<?= adminField($editCategory['description'] ?? '') ?>" placeholder="A short line for your team"></label></div><div class="form-actions"><a class="admin-button admin-button-quiet" href="<?= adminPageUrl('categories') ?>" data-i18n="admin.cancel">Cancel</a><button class="admin-button admin-button-primary" type="submit"><span data-i18n="admin.saveCategory">Save category</span> <span>↗</span></button></div></form></section><?php endif; ?>
          <section class="category-admin-grid"><?php foreach ($categories as $category): $usedCount = count(array_filter($products, static fn (array $product): bool => (string) $product['category'] === (string) $category['name'])); ?><article class="category-card"><span class="category-mark"><?= adminField(substr((string) $category['name'], 0, 1)) ?></span><div class="category-card-copy"><h2><?= adminField($category['name']) ?></h2><p class="category-tamil"><?= adminField($category['tamil_name'] ?? '') ?></p><p><?= adminField($category['description']) ?></p><small><?= $usedCount ?> product<?= $usedCount === 1 ? '' : 's' ?></small></div><div class="category-actions"><a href="<?= adminPageUrl('categories') ?>&edit=<?= adminField($category['id']) ?>">Edit</a><form method="post" onsubmit="return confirm('Delete this category? Only unused categories can be deleted.')"><input type="hidden" name="action" value="delete_category"><input type="hidden" name="csrf" value="<?= adminField($csrf) ?>"><input type="hidden" name="id" value="<?= adminField($category['id']) ?>"><button type="submit">Delete</button></form></div></article><?php endforeach; ?></section>

        <?php elseif ($page === 'orders'): ?>
          <div class="page-heading"><div><p class="admin-eyebrow" data-i18n="admin.orderDesk">ORDER DESK</p><h1 data-i18n="admin.orderHeading">Keep every order moving.</h1><p>Confirm, pack, ship, and close the loop on every celebration.</p></div><div class="heading-stat"><strong><?= count($newOrders) ?></strong><span>new orders to review</span></div></div>
          <section class="panel"><div class="panel-heading"><div><p class="admin-eyebrow" data-i18n="admin.allOrders">ALL ORDERS</p><h2><span data-i18n="admin.orderQueue">Order queue</span> <span class="count-badge"><?= count($orders) ?></span></h2></div><input class="table-search" type="search" placeholder="Search order or customer" data-table-search="#orders-table"></div><div class="table-scroll"><table class="admin-table orders-table" id="orders-table"><thead><tr><th>Order</th><th>Customer</th><th>Items</th><th>Total</th><th>Status</th><th>Update</th></tr></thead><tbody><?php foreach ($orders as $order): ?><tr data-search="<?= adminField(strtolower($order['order_number'] . ' ' . $order['customer_name'] . ' ' . $order['phone'])) ?>"><td><strong><?= adminField($order['order_number']) ?></strong><small><?= adminField(date('d M Y, g:i A', strtotime((string) $order['created_at']))) ?></small></td><td><strong><?= adminField($order['customer_name']) ?></strong><small><?= adminField($order['phone']) ?></small><details class="customer-details"><summary>View address</summary><p><?= nl2br(adminField($order['address'])) ?></p></details></td><td><details class="item-details"><summary><?= count($order['items'] ?? []) ?> line item<?= count($order['items'] ?? []) === 1 ? '' : 's' ?></summary><?php foreach (($order['items'] ?? []) as $item): ?><p><?= adminField($item['quantity']) ?> × <?= adminField($item['product_name']) ?></p><?php endforeach; ?></details></td><td><strong class="order-total">₹<?= adminField(number_format((float) $order['total'])) ?></strong></td><td><span class="status status-<?= adminField($order['status']) ?>"><?= adminField(statusLabel((string) $order['status'])) ?></span></td><td><form method="post" class="status-form"><input type="hidden" name="action" value="update_order"><input type="hidden" name="csrf" value="<?= adminField($csrf) ?>"><input type="hidden" name="id" value="<?= adminField($order['id']) ?>"><select name="status" aria-label="Update order status"><?php foreach (['new', 'confirmed', 'packed', 'shipped', 'delivered', 'cancelled'] as $status): ?><option value="<?= $status ?>" <?= $order['status'] === $status ? 'selected' : '' ?>><?= statusLabel($status) ?></option><?php endforeach; ?></select><button type="submit">Save</button></form></td></tr><?php endforeach; ?></tbody></table></div><?php if (!$orders): ?><div class="empty-state"><span>↗</span><h3>No orders yet.</h3><p>When customers check out, you’ll see their details here.</p></div><?php endif; ?></section>
        <?php endif; ?>
      </div>
    </main>
  </div>
  <script src="../public/i18n.js"></script>
  <script src="admin.js"></script>
</body>
</html>