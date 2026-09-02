<?php
declare(strict_types=1);
require_once __DIR__ . '/config.php';
$products = getProducts();
$categories = array_values(array_unique(array_map(
    static fn (array $product): string => (string) $product['category'],
    $products,
)));
$categoryIcons = [
    'All items' => '✦',
    'Combos' => '▦',
    'Sound Crackers' => '◉',
    'Fountains' => '✺',
    'Sparklers' => '✧',
    'Ground Spinners' => '◌',
    'Rockets' => '↗',
    'Aerial Effects' => '✹',
];
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Udaya Crackers — quality fireworks for bright family celebrations.">
  <title>Udaya Crackers · Celebrate the light</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="public/style.css">
</head>
<body>
  <div class="announcement">
    <div class="container announcement-inner">
      <span data-i18n="announcement.top">Direct from Sivakasi · Quality checked · Packed with care</span>
      <span class="announcement-note" data-i18n="announcement.note">Safe celebrations start with Udaya</span>
    </div>
  </div>

  <header class="site-header">
    <div class="container header-inner">
      <a class="brand" href="#" aria-label="Udaya Crackers home">
        <span class="brand-mark"><span>U</span></span>
        <span class="brand-copy"><strong>udaya</strong><small>CRACKERS</small></span>
      </a>
      <nav class="main-nav" aria-label="Primary navigation">
        <a href="#shop" data-i18n="nav.shop">Shop</a>
        <a href="#why-udaya" data-i18n="nav.why">Why Udaya</a>
        <a href="#safety" data-i18n="nav.safety">Safety first</a>
        <a href="#contact" data-i18n="nav.contact">Contact</a>
      </nav>
      <button class="language-toggle" id="language-toggle" type="button" data-language-toggle>தமிழ்</button>
      <button class="cart-button" id="open-cart" type="button" aria-label="Open shopping bag">
        <span data-i18n="nav.bag">Bag</span><span class="cart-count" id="cart-count">0</span>
      </button>
      <button class="menu-button" id="menu-toggle" type="button" aria-label="Open menu"><span></span><span></span></button>
    </div>
    <div class="mobile-menu" id="mobile-menu">
      <a href="#shop" data-i18n="nav.shop">Shop</a><a href="#why-udaya" data-i18n="nav.why">Why Udaya</a><a href="#safety" data-i18n="nav.safety">Safety first</a><a href="#contact" data-i18n="nav.contact">Contact</a>
    </div>
  </header>

  <main>
    <section class="hero container">
      <div class="hero-art">
        <img src="public/images/hero.jpg" alt="Colourful fireworks lighting up a festive night">
        <div class="hero-shade"></div>
        <div class="hero-content">
        <p class="eyebrow light" data-i18n="hero.eyebrow">THE JOY OF LIGHT</p>
        <h1><span data-i18n="hero.line1">Make every</span><br><em data-i18n="hero.line2">moment</em> sparkle.</h1>
        <p class="hero-description" data-i18n="hero.description">Thoughtfully packed crackers, bright colours, and memories made together.</p>
        <a href="#shop" class="button button-gold"><span data-i18n="hero.cta">Shop the collection</span> <span>↗</span></a>
        </div>
        <div class="hero-stamp"><strong>Up to</strong><b>70%</b><span>OFF</span></div>
      <div class="hero-bottom-note"><span data-i18n="hero.season">Festive season 2026</span> <span>·</span> <span data-i18n="hero.note">Bring home the light</span></div>
      </div>
    </section>

    <section class="shop-layout container" id="shop">
      <div class="shop-main">
        <div class="shop-intro">
          <div>
          <p class="eyebrow" data-i18n="shop.eyebrow">THE UDAYA EDIT</p>
          <h2><span data-i18n="shop.title1">Pick your kind</span><br><em data-i18n="shop.title2">of magic.</em></h2>
          </div>
        <p class="intro-copy" data-i18n="shop.intro">From tiny sparks to sky-high colour, find something for every person and every kind of celebration.</p>
        </div>
        <div class="shop-tools">
          <label class="search-wrap">
            <span aria-hidden="true">⌕</span>
          <input type="search" id="search" data-i18n-placeholder="search.placeholder" placeholder="Search your favourites…" autocomplete="off">
          </label>
        <button class="sort-button" id="sort-toggle" type="button"><span id="sort-label" data-i18n="sort.label">Sort</span> <span>↕</span></button>
          <div class="sort-menu" id="sort-menu">
          <button type="button" data-sort="featured" data-i18n="sort.featured">Featured</button>
          <button type="button" data-sort="low" data-i18n="sort.low">Price: low to high</button>
          <button type="button" data-sort="high" data-i18n="sort.high">Price: high to low</button>
          </div>
        </div>
        <div class="category-scroll" aria-label="Product categories">
        <button class="category-pill active" data-category="All items" type="button"><span>✦</span> <span data-i18n="category.all">All items</span></button>
          <?php foreach ($categories as $category): ?>
            <button class="category-pill" data-category="<?= e($category) ?>" type="button">
            <span><?= e($categoryIcons[$category] ?? '✦') ?></span><span data-lang-en="<?= e($category) ?>" data-lang-ta="<?= e(categoryTamil($category)) ?>"><?= e($category) ?></span>
            </button>
          <?php endforeach; ?>
        </div>
        <div class="product-grid" id="product-grid">
          <?php foreach ($products as $product):
            $saving = $product['mrp'] > 0 ? round((1 - ($product['price'] / $product['mrp'])) * 100) : 0;
          ?>
            <article class="product-card" data-category="<?= e($product['category']) ?>" data-name="<?= e(strtolower($product['name'] . ' ' . $product['tamil_name'])) ?>" data-price="<?= e($product['price']) ?>">
              <div class="product-image-wrap">
                <?php if ($product['tag']): ?><span class="product-tag"><?= e($product['tag']) ?></span><?php endif; ?>
                <span class="discount">-<?= e($saving) ?>%</span>
                <img class="product-image image-<?= e((string) $product['id']) ?>" src="public/images/<?= e($product['image']) ?>" alt="<?= e($product['name']) ?>">
              </div>
              <div class="product-info">
                <p class="product-category"><span data-lang-en="<?= e($product['category']) ?>" data-lang-ta="<?= e(categoryTamil((string) $product['category'])) ?>"><?= e($product['category']) ?></span></p>
                <h3><span data-lang-en="<?= e($product['name']) ?>" data-lang-ta="<?= e($product['tamil_name'] ?: $product['name']) ?>"><?= e($product['name']) ?></span></h3>
                <p class="product-tamil"><span data-lang-en="<?= e($product['tamil_name']) ?>" data-lang-ta="<?= e($product['name']) ?>"><?= e($product['tamil_name']) ?></span></p>
                <p class="product-unit"><span data-lang-en="<?= e($product['unit']) ?>" data-lang-ta="<?= e(tamilUnit((string) $product['unit'])) ?>"><?= e($product['unit']) ?></span></p>
                <p class="product-stock <?= (int) ($product['stock_quantity'] ?? 0) <= 0 ? 'is-out' : ((int) ($product['stock_quantity'] ?? 0) <= (int) ($product['low_stock_threshold'] ?? 10) ? 'is-low' : '') ?>" data-stock-label data-stock="<?= e($product['stock_quantity'] ?? 0) ?>" data-threshold="<?= e($product['low_stock_threshold'] ?? 10) ?>">
                  <?= (int) ($product['stock_quantity'] ?? 0) <= 0 ? 'Currently unavailable' : ((int) ($product['stock_quantity'] ?? 0) <= (int) ($product['low_stock_threshold'] ?? 10) ? 'Only ' . e($product['stock_quantity']) . ' left' : 'In stock') ?>
                </p>
                <div class="product-buy">
                  <div><strong>₹<?= e(number_format((float) $product['price'])) ?></strong><del>₹<?= e(number_format((float) $product['mrp'])) ?></del></div>
                  <div class="product-action">
                    <div class="quantity-stepper" aria-label="Quantity selector">
                      <button class="qty-button qty-minus" type="button" data-id="<?= e($product['id']) ?>" data-delta="-1" aria-label="Decrease quantity">−</button>
                      <span class="qty-value" data-id="<?= e($product['id']) ?>">1</span>
                      <button class="qty-button qty-plus" type="button" data-id="<?= e($product['id']) ?>" data-delta="1" aria-label="Increase quantity">+</button>
                    </div>
                    <button class="add-button" type="button"
                      data-id="<?= e($product['id']) ?>"
                      data-name="<?= e($product['name']) ?>"
                      data-price="<?= e($product['price']) ?>"
                      data-image="<?= e($product['image']) ?>"
                      data-stock="<?= e($product['stock_quantity'] ?? 0) ?>"
                      aria-label="Add to bag"
                      <?= (int) ($product['stock_quantity'] ?? 0) <= 0 ? 'disabled' : '' ?>><?= (int) ($product['stock_quantity'] ?? 0) <= 0 ? '<span data-i18n="product.soldOut">Sold out</span>' : '<span aria-hidden="true">👜</span>' ?></button>
                  </div>
                </div>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
        <p class="empty-results" id="empty-results">No crackers matched that search. Try another sparkle.</p>
      </div>

      <aside class="bill-card" id="bill-card">
        <div class="bill-head"><div><p class="eyebrow" data-i18n="bag.eyebrow">YOUR BAG</p><h2><span data-i18n="bag.title1">A little</span><br><em data-i18n="bag.title2">something bright.</em></h2></div><span class="bill-icon">✦</span></div>
        <div class="bill-items" id="bill-items"><div class="bill-empty"><span>✧</span><p><span data-i18n="bag.empty">Add a few favourites</span><br><span data-i18n="bag.empty2">and your total will appear here.</span></p></div></div>
        <div class="bill-summary" id="bill-summary" hidden>
          <div><span data-i18n="bag.subtotal">Subtotal</span><strong id="bill-subtotal">₹0</strong></div>
          <div><span data-i18n="bag.delivery">Delivery</span><strong class="free-delivery" data-i18n="bag.free">FREE</strong></div>
          <div class="bill-total"><span data-i18n="bag.total">Total</span><strong id="bill-total">₹0</strong></div>
          <button class="button button-dark checkout-open" type="button"><span data-i18n="bag.checkout">Continue to checkout</span> <span>↗</span></button>
        </div>
        <div class="bill-trust"><span>✓</span> <span data-i18n="bag.trust">Secure ordering</span> <span>✓</span> <span data-i18n="bag.safeDelivery">Safe delivery across India</span></div>
      </aside>
    </section>

    <section class="why-section" id="why-udaya">
      <div class="container why-grid">
        <div class="why-title"><p class="eyebrow" data-i18n="why.eyebrow">WHY PEOPLE CHOOSE US</p><h2><span data-i18n="why.title1">Good things</span><br><span data-i18n="why.title2">come</span> <em data-i18n="why.title3">bright.</em></h2></div>
        <div class="why-item"><span class="why-number">01</span><div><h3 data-i18n="why.madeTitle">Made in Sivakasi</h3><p data-i18n="why.madeCopy">We work close to the source, so every box reaches you fresh, carefully packed, and ready for the occasion.</p></div></div>
        <div class="why-item"><span class="why-number">02</span><div><h3 data-i18n="why.priceTitle">Prices worth celebrating</h3><p data-i18n="why.priceCopy">Direct pricing means more colour for your budget and more room for the people you’re celebrating with.</p></div></div>
        <div class="why-item"><span class="why-number">03</span><div><h3 data-i18n="why.qualityTitle">Family-first quality</h3><p data-i18n="why.qualityCopy">Each order is checked before it leaves us, because a beautiful celebration should also feel responsible.</p></div></div>
      </div>
    </section>

    <section class="safety-section container" id="safety">
      <div class="safety-card">
        <div class="safety-symbol">✦</div>
        <div><p class="eyebrow light" data-i18n="safety.eyebrow">A SMALL REMINDER</p><h2><span data-i18n="safety.title1">Celebrate bright.</span><br><em data-i18n="safety.title2">Celebrate safe.</em></h2><p data-i18n="safety.copy">Always light crackers outdoors, keep a bucket of water nearby, and give every sparkler plenty of space.</p></div>
        <a class="text-link" href="#contact"><span data-i18n="safety.link">Read our safety guide</span> <span>↗</span></a>
      </div>
    </section>
  </main>

  <footer class="site-footer" id="contact">
    <div class="container footer-grid">
      <div class="footer-brand"><a class="brand brand-light" href="#"><span class="brand-mark"><span>U</span></span><span class="brand-copy"><strong>udaya</strong><small>CRACKERS</small></span></a><p><span data-i18n="footer.copy">For the nights you’ll talk about</span><br><span data-i18n="footer.copy2">long after the lights go out.</span></p></div>
      <div><p class="footer-label" data-i18n="footer.explore">Explore</p><a href="#shop" data-i18n="footer.all">All crackers</a><a href="#shop" data-i18n="footer.combos">Family combos</a><a href="#safety" data-i18n="footer.safety">Safety guide</a></div>
      <div><p class="footer-label" data-i18n="footer.reach">Reach us</p><a href="tel:+919876543210">+91 98765 43210</a><a href="mailto:hello@udayacrackers.in">hello@udayacrackers.in</a><span data-i18n="footer.hours">Mon–Sat · 9am–7pm</span></div>
      <div class="footer-callout"><span>Made for the</span><strong>good times.</strong><i>✦</i></div>
    </div>
       <div class="container footer-bottom"><span>© 2026 Udaya Crackers</span><span data-i18n="footer.made">Built with care in Sivakasi, Tamil Nadu</span><span><a href="admin/" data-i18n="footer.admin">Admin</a> · Terms · Privacy</span></div>
  </footer>

  <div class="drawer-overlay" id="drawer-overlay"></div>
  <aside class="cart-drawer" id="cart-drawer" aria-label="Shopping bag">
    <div class="drawer-head"><div><p class="eyebrow" data-i18n="bag.eyebrow">YOUR BAG</p><h2><span data-i18n="drawer.ready">Ready to</span> <em data-i18n="drawer.shine">shine?</em></h2></div><button class="close-button" id="close-cart" type="button" aria-label="Close shopping bag">×</button></div>
    <div class="drawer-items" id="drawer-items"></div>
    <div class="drawer-footer"><div class="drawer-total"><span data-i18n="bag.total">Total</span><strong id="drawer-total">₹0</strong></div><button class="button button-dark checkout-open" type="button"><span data-i18n="drawer.checkout">Checkout</span> <span>↗</span></button></div>
  </aside>

  <div class="modal-backdrop" id="checkout-modal">
    <div class="checkout-modal" role="dialog" aria-modal="true" aria-labelledby="checkout-title">
      <button class="close-button modal-close" type="button" aria-label="Close checkout">×</button>
      <p class="eyebrow" data-i18n="checkout.eyebrow">ALMOST THERE</p><h2 id="checkout-title"><span data-i18n="checkout.title1">Bring the sparkle</span> <em data-i18n="checkout.title2">home.</em></h2>
      <p class="modal-copy" data-i18n="checkout.copy">Share your details and we’ll confirm your delivery with you.</p>
      <form id="checkout-form">
        <div class="form-row"><label><span data-i18n="checkout.name">Full name</span><input name="name" required data-i18n-placeholder="checkout.namePlaceholder" placeholder="Your name"></label><label><span data-i18n="checkout.phone">Phone number</span><input name="phone" type="tel" required data-i18n-placeholder="checkout.phonePlaceholder" placeholder="+91"></label></div>
        <label><span data-i18n="checkout.address">Delivery address</span><textarea name="address" required rows="3" data-i18n-placeholder="checkout.addressPlaceholder" placeholder="House no., street, city, pincode"></textarea></label>
        <button class="button button-dark" type="submit"><span data-i18n="checkout.place">Place order</span> <span>↗</span></button>
      </form>
      <div class="order-success" id="order-success" hidden><span>✦</span><h3 data-i18n="checkout.successTitle">Order received!</h3><p data-i18n="checkout.successCopy">Thank you. We’ll call shortly to confirm your Udaya order.</p><button class="button button-outline" type="button" id="success-close" data-i18n="checkout.back">Back to shopping</button></div>
    </div>
  </div>
  <script>
    window.UDAYA_PRODUCTS = <?= json_encode($products, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
  </script>
  <script src="public/i18n.js"></script>
  <script src="public/app.js"></script>
</body>
</html>
