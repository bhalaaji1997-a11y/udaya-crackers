(() => {
  const products = Array.isArray(window.UDAYA_PRODUCTS) ? window.UDAYA_PRODUCTS : [];
  const state = { category: 'All items', query: '', sort: 'featured', sortLabel: 'sort.label', cart: loadCart(), productQuantities: {} };
  const $ = (selector) => document.querySelector(selector);
  const $$ = (selector) => [...document.querySelectorAll(selector)];
  const money = (value) => `₹${Number(value).toLocaleString('en-IN', { maximumFractionDigits: 0 })}`;

  function productQuantity(id) {
    const productId = Number(id);
    if (!Number.isFinite(productId)) return 1;
    const quantity = Number(state.productQuantities[productId] ?? 1);
    return Number.isFinite(quantity) && quantity > 0 ? quantity : 1;
  }

  function setProductQuantity(id, nextQuantity) {
    const productId = Number(id);
    const max = Number(products.find((product) => Number(product.id) === productId)?.stock_quantity ?? 99);
    const safeQuantity = Math.min(Math.max(nextQuantity, 1), max || 99);
    state.productQuantities[productId] = safeQuantity;
    const value = document.querySelector(`.qty-value[data-id="${CSS.escape(String(productId))}"]`);
    if (value) value.textContent = String(safeQuantity);
  }

  function loadCart() {
    try { return JSON.parse(localStorage.getItem('udaya-cart') || '[]'); } catch { return []; }
  }

  function saveCart() {
    localStorage.setItem('udaya-cart', JSON.stringify(state.cart));
  }

  function filteredProducts() {
    return products
      .filter((product) => state.category === 'All items' || product.category === state.category)
      .filter((product) => `${product.name} ${product.tamil_name}`.toLowerCase().includes(state.query))
      .sort((a, b) => state.sort === 'low' ? a.price - b.price : state.sort === 'high' ? b.price - a.price : a.id - b.id);
  }

  function renderProductVisibility() {
    const orderedProducts = filteredProducts();
    const visible = new Set(orderedProducts.map((product) => String(product.id)));
    const cards = $$('.product-card');
    orderedProducts.forEach((product) => {
      const card = cards.find((entry) => entry.querySelector('.add-button')?.dataset.id === String(product.id));
      if (card) $('#product-grid').appendChild(card);
    });
    cards.forEach((card) => card.classList.toggle('is-hidden', !visible.has(card.querySelector('.add-button')?.dataset.id || '')));
    $('#empty-results').classList.toggle('is-visible', visible.size === 0);
  }

  function addToCart(id, name, price, image, stock, quantity = 1) {
    const available = Number(stock);
    if (available <= 0) return;
    const requested = Math.max(1, Number(quantity) || 1);
    const found = state.cart.find((item) => item.id === id);
    if (found) found.quantity = Math.min(found.quantity + requested, available);
    else state.cart.push({ id, name, price: Number(price), image, quantity: requested, stock: available });
    saveCart();
    renderCart();
    if (!found || found.quantity < available) flashButton(id);
  }

  function flashButton(id) {
    const button = document.querySelector(`.add-button[data-id="${CSS.escape(String(id))}"]`);
    if (!button) return;
    const original = button.innerHTML;
    button.innerHTML = '<span aria-hidden="true">✓</span>';
    button.classList.add('added');
    setTimeout(() => { button.innerHTML = original; button.classList.remove('added'); }, 1100);
  }

  function changeQuantity(id, delta) {
    const item = state.cart.find((entry) => entry.id === id);
    if (!item) return;
    const product = products.find((entry) => Number(entry.id) === id);
    const stock = Number(product?.stock_quantity ?? item.stock ?? 99);
    item.quantity = Math.min(item.quantity + delta, stock);
    if (item.quantity <= 0) state.cart = state.cart.filter((entry) => entry.id !== id);
    saveCart();
    renderCart();
  }

  function cartTotal() {
    return state.cart.reduce((total, item) => total + item.price * item.quantity, 0);
  }

  function itemMarkup(item) {
    const product = products.find((entry) => Number(entry.id) === Number(item.id));
    const itemName = UdayaI18n.getLanguage() === 'ta' ? (product?.tamil_name || item.name) : item.name;
    const stock = Number(product?.stock_quantity ?? item.stock ?? 99);
    return `<div class="cart-line">
      <img src="public/images/${item.image}" alt="">
      <div class="cart-line-info"><h3>${escapeHtml(itemName)}</h3><strong>${money(item.price * item.quantity)}</strong>
      <div class="quantity"><button type="button" data-change="-1" data-id="${item.id}" aria-label="Decrease quantity">−</button><span>${item.quantity}</span><button type="button" data-change="1" data-id="${item.id}" aria-label="Increase quantity" ${item.quantity >= stock ? 'disabled' : ''}>+</button></div></div>
    </div>`;
  }

  function emptyBagMarkup(drawer = false) {
    const first = UdayaI18n.t(drawer ? 'drawer.empty1' : 'bag.empty');
    const second = UdayaI18n.t(drawer ? 'drawer.empty2' : 'bag.empty2');
    return `<div class="${drawer ? 'drawer-empty' : 'bill-empty'}"><span>${drawer ? '✦' : '✧'}</span><p>${escapeHtml(first)}<br>${escapeHtml(second)}</p></div>`;
  }

  function updateStockLabels() {
    $$('[data-stock-label]').forEach((label) => {
      const stock = Number(label.dataset.stock || 0);
      const threshold = Number(label.dataset.threshold || 10);
      label.classList.toggle('is-out', stock <= 0);
      label.classList.toggle('is-low', stock > 0 && stock <= threshold);
      label.textContent = stock <= 0
        ? UdayaI18n.t('product.soldOut')
        : stock <= threshold
          ? `${UdayaI18n.t('product.only')} ${stock} ${UdayaI18n.t('product.left')}`
          : UdayaI18n.t('product.inStock');
    });
  }

  function renderCart() {
    const count = state.cart.reduce((total, item) => total + item.quantity, 0);
    const total = cartTotal();
    $('#cart-count').textContent = count;
    $('#drawer-total').textContent = money(total);
    $('#bill-subtotal').textContent = money(total);
    $('#bill-total').textContent = money(total);
    $('#bill-items').innerHTML = state.cart.length
      ? state.cart.map(itemMarkup).join('')
      : emptyBagMarkup();
    $('#drawer-items').innerHTML = state.cart.length
      ? state.cart.map(itemMarkup).join('')
      : emptyBagMarkup(true);
    $('#bill-summary').hidden = !state.cart.length;
    $$('.quantity button').forEach((button) => button.addEventListener('click', () => changeQuantity(Number(button.dataset.id), Number(button.dataset.change))));
  }

  function escapeHtml(value) {
    return String(value).replace(/[&<>"']/g, (char) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[char]));
  }

  function openDrawer() {
    $('#cart-drawer').classList.add('is-open');
    $('#drawer-overlay').classList.add('is-open');
    document.body.classList.add('no-scroll');
  }

  function closeDrawer() {
    $('#cart-drawer').classList.remove('is-open');
    $('#drawer-overlay').classList.remove('is-open');
    document.body.classList.remove('no-scroll');
  }

  function openCheckout() {
    if (!state.cart.length) { openDrawer(); return; }
    closeDrawer();
    $('#checkout-modal').classList.add('is-open');
    document.body.classList.add('no-scroll');
  }

  function closeCheckout() {
    $('#checkout-modal').classList.remove('is-open');
    document.body.classList.remove('no-scroll');
  }

  $$('.qty-button').forEach((button) => button.addEventListener('click', () => {
    const id = Number(button.dataset.id);
    const delta = Number(button.dataset.delta || 0);
    const current = productQuantity(id);
    const product = products.find((entry) => Number(entry.id) === id);
    const stock = Number(product?.stock_quantity ?? 99);
    const next = Math.min(Math.max(current + delta, 1), stock || 99);
    setProductQuantity(id, next);
  }));

  $$('.add-button').forEach((button) => button.addEventListener('click', () => {
    const id = Number(button.dataset.id);
    addToCart(id, button.dataset.name, button.dataset.price, button.dataset.image, button.dataset.stock, productQuantity(id));
    setProductQuantity(id, 1);
  }));
  $$('.category-pill').forEach((button) => button.addEventListener('click', () => {
    state.category = button.dataset.category;
    $$('.category-pill').forEach((pill) => pill.classList.toggle('active', pill === button));
    renderProductVisibility();
  }));
  $('#search').addEventListener('input', (event) => { state.query = event.target.value.toLowerCase().trim(); renderProductVisibility(); });
  $('#sort-toggle').addEventListener('click', () => $('#sort-menu').classList.toggle('is-open'));
  $$('#sort-menu button').forEach((button) => button.addEventListener('click', () => {
    state.sort = button.dataset.sort;
    state.sortLabel = `sort.${button.dataset.sort}`;
    $('#sort-menu').classList.remove('is-open');
    $('#sort-label').textContent = UdayaI18n.t(state.sortLabel);
    renderProductVisibility();
  }));
  renderProductVisibility();
  $('#open-cart').addEventListener('click', openDrawer);
  $('#close-cart').addEventListener('click', closeDrawer);
  $('#drawer-overlay').addEventListener('click', closeDrawer);
  $$('.checkout-open').forEach((button) => button.addEventListener('click', openCheckout));
  $$('.modal-close').forEach((button) => button.addEventListener('click', closeCheckout));
  $('#success-close').addEventListener('click', closeCheckout);
  $('#menu-toggle').addEventListener('click', () => $('#mobile-menu').classList.toggle('is-open'));
  $$('#mobile-menu a').forEach((link) => link.addEventListener('click', () => $('#mobile-menu').classList.remove('is-open')));

  $('#language-toggle').addEventListener('click', () => {
    UdayaI18n.setLanguage(UdayaI18n.getLanguage() === 'ta' ? 'en' : 'ta');
  });
  document.addEventListener('udaya:language', () => {
    $('#sort-label').textContent = UdayaI18n.t(state.sortLabel);
    updateStockLabels();
    renderCart();
  });
  updateStockLabels();

  $('#checkout-form').addEventListener('submit', async (event) => {
    event.preventDefault();
    const form = event.currentTarget;
    const submit = form.querySelector('button[type="submit"]');
    submit.disabled = true;
    submit.innerHTML = UdayaI18n.t('checkout.saving');
    const payload = Object.fromEntries(new FormData(form).entries());
    payload.items = state.cart.map(({ id, quantity }) => ({ id, quantity }));
    try {
      const response = await fetch('checkout.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) });
      const result = await response.json();
      if (!response.ok || !result.success) throw new Error(result.message || 'Unable to place order');
      form.hidden = true;
      $('#order-success').hidden = false;
      state.cart = [];
      saveCart();
      renderCart();
    } catch (error) {
      submit.disabled = false;
      submit.innerHTML = `${UdayaI18n.t('checkout.place')} <span>↗</span>`;
      window.alert(error.message);
    }
  });
  renderCart();
})();
