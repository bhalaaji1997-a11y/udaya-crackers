(() => {
  const translations = {
    'announcement.top': ['Direct from Sivakasi · Quality checked · Packed with care', 'சிவகாசியிலிருந்து நேரடியாக · தரம் சரிபார்க்கப்பட்டது · அக்கறையுடன் பேக்கிங்'],
    'announcement.note': ['Safe celebrations start with Udaya', 'பாதுகாப்பான கொண்டாட்டம் உதயாவிலிருந்து தொடங்குகிறது'],
    'nav.shop': ['Shop', 'கடை'],
    'nav.why': ['Why Udaya', 'ஏன் உதயா'],
    'nav.safety': ['Safety first', 'பாதுகாப்பு முதலில்'],
    'nav.contact': ['Contact', 'தொடர்பு'],
    'nav.bag': ['Bag', 'பை'],
    'hero.eyebrow': ['THE JOY OF LIGHT', 'ஒளியின் மகிழ்ச்சி'],
    'hero.line1': ['Make every', 'ஒவ்வொரு'],
    'hero.line2': ['moment', 'தருணத்தையும்'],
    'hero.description': ['Thoughtfully packed crackers, bright colours, and memories made together.', 'அக்கறையுடன் பேக் செய்யப்பட்ட வெடிகள், வண்ணமயமான ஒளிகள், ஒன்றாக உருவாக்கும் நினைவுகள்.'],
    'hero.cta': ['Shop the collection', 'தொகுப்பைப் பாருங்கள்'],
    'hero.season': ['Festive season 2026', 'தீபாவளி காலம் 2026'],
    'hero.note': ['Bring home the light', 'ஒளியை வீட்டிற்கு கொண்டு வாருங்கள்'],
    'shop.eyebrow': ['THE UDAYA EDIT', 'உதயா தொகுப்பு'],
    'shop.title1': ['Pick your kind', 'உங்கள்'],
    'shop.title2': ['of magic.', 'கொண்டாட்டத்தைத் தேர்ந்தெடுங்கள்.'],
    'shop.intro': ['From tiny sparks to sky-high colour, find something for every person and every kind of celebration.', 'சிறு பொறிகளிலிருந்து வானம் நிறைக்கும் வண்ணங்கள் வரை, ஒவ்வொருவருக்கும் ஒவ்வொரு கொண்டாட்டத்திற்கும் ஏற்றதைத் தேர்ந்தெடுங்கள்.'],
    'search.placeholder': ['Search your favourites…', 'உங்களுக்குப் பிடித்தவற்றைத் தேடுங்கள்…'],
    'sort.label': ['Sort', 'வரிசைப்படுத்து'],
    'sort.featured': ['Featured', 'சிறப்பானவை'],
    'sort.low': ['Price: low to high', 'விலை: குறைவிலிருந்து அதிகம்'],
    'sort.high': ['Price: high to low', 'விலை: அதிகத்திலிருந்து குறைவு'],
    'category.all': ['All items', 'அனைத்தும்'],
    'product.add': ['Add', 'சேர்க்கவும்'],
    'product.added': ['Added', 'சேர்க்கப்பட்டது'],
    'product.soldOut': ['Sold out', 'விற்றுத் தீர்ந்தது'],
    'product.inStock': ['In stock', 'கையிருப்பில் உள்ளது'],
    'product.only': ['Only', 'மட்டும்'],
    'product.left': ['left', 'மட்டுமே உள்ளது'],
    'bag.eyebrow': ['YOUR BAG', 'உங்கள் பை'],
    'bag.title1': ['A little', 'சிறிது'],
    'bag.title2': ['something bright.', 'ஒளிமயமான கொண்டாட்டம்.'],
    'bag.empty': ['Add a few favourites', 'உங்களுக்குப் பிடித்தவற்றைச் சேர்க்கவும்'],
    'bag.empty2': ['and your total will appear here.', 'மொத்தத் தொகை இங்கே தோன்றும்.'],
    'bag.subtotal': ['Subtotal', 'கூட்டுத்தொகை'],
    'bag.delivery': ['Delivery', 'டெலிவரி'],
    'bag.free': ['FREE', 'இலவசம்'],
    'bag.total': ['Total', 'மொத்தம்'],
    'bag.checkout': ['Continue to checkout', 'செக்-அவுட்டைத் தொடரவும்'],
    'bag.trust': ['Secure ordering', 'பாதுகாப்பான ஆர்டர்'],
    'bag.safeDelivery': ['Safe delivery across India', 'இந்தியா முழுவதும் பாதுகாப்பான டெலிவரி'],
    'drawer.ready': ['Ready to', 'தயாரா'],
    'drawer.shine': ['shine?', 'ஒளிர?'],
    'drawer.checkout': ['Checkout', 'செக்-அவுட்'],
    'drawer.empty1': ['Your bag is waiting', 'உங்கள் பை காத்திருக்கிறது'],
    'drawer.empty2': ['for a little sparkle.', 'சிறிது ஒளிக்காக.'],
    'checkout.eyebrow': ['ALMOST THERE', 'கிட்டத்தட்ட முடிந்தது'],
    'checkout.title1': ['Bring the sparkle', 'ஒளியை'],
    'checkout.title2': ['home.', 'வீட்டிற்கு கொண்டு வாருங்கள்.'],
    'checkout.copy': ['Share your details and we’ll confirm your delivery with you.', 'உங்கள் விவரங்களைப் பகிருங்கள்; டெலிவரியை நாங்கள் உறுதிப்படுத்துகிறோம்.'],
    'checkout.name': ['Full name', 'முழுப் பெயர்'],
    'checkout.namePlaceholder': ['Your name', 'உங்கள் பெயர்'],
    'checkout.phone': ['Phone number', 'தொலைபேசி எண்'],
    'checkout.phonePlaceholder': ['+91', '+91'],
    'checkout.address': ['Delivery address', 'டெலிவரி முகவரி'],
    'checkout.addressPlaceholder': ['House no., street, city, pincode', 'வீட்டு எண், தெரு, நகரம், அஞ்சல் குறியீடு'],
    'checkout.place': ['Place order', 'ஆர்டர் செய்யவும்'],
    'checkout.saving': ['Saving your order…', 'உங்கள் ஆர்டர் சேமிக்கப்படுகிறது…'],
    'checkout.successTitle': ['Order received!', 'ஆர்டர் பெறப்பட்டது!'],
    'checkout.successCopy': ['Thank you. We’ll call shortly to confirm your Udaya order.', 'நன்றி. உங்கள் உதயா ஆர்டரை உறுதிப்படுத்த விரைவில் அழைக்கிறோம்.'],
    'checkout.back': ['Back to shopping', 'ஷாப்பிங்கிற்குத் திரும்பு'],
    'why.eyebrow': ['WHY PEOPLE CHOOSE US', 'எங்களை ஏன் தேர்வு செய்கிறார்கள்'],
    'why.title1': ['Good things', 'நல்ல விஷயங்கள்'],
    'why.title2': ['come', 'வரும்'],
    'why.title3': ['bright.', 'ஒளியுடன்.'],
    'why.madeTitle': ['Made in Sivakasi', 'சிவகாசியில் தயாரிப்பு'],
    'why.madeCopy': ['We work close to the source, so every box reaches you fresh, carefully packed, and ready for the occasion.', 'மூலத்தளத்திற்கு அருகில் பணிபுரிவதால், ஒவ்வொரு பெட்டியும் புத்தம் புதியதாகவும் அக்கறையுடன் பேக் செய்யப்பட்டதாகவும் உங்களை வந்தடைகிறது.'],
    'why.priceTitle': ['Prices worth celebrating', 'கொண்டாடத் தகுந்த விலைகள்'],
    'why.priceCopy': ['Direct pricing means more colour for your budget and more room for the people you’re celebrating with.', 'நேரடி விலை நிர்ணயம் உங்கள் பட்ஜெட்டில் அதிக வண்ணங்களையும், உங்களுடன் கொண்டாடுபவர்களுக்கு அதிக இடத்தையும் தருகிறது.'],
    'why.qualityTitle': ['Family-first quality', 'குடும்பத்திற்கான தரம்'],
    'why.qualityCopy': ['Each order is checked before it leaves us, because a beautiful celebration should also feel responsible.', 'ஒவ்வொரு ஆர்டரும் எங்களிடமிருந்து புறப்படுவதற்கு முன் சரிபார்க்கப்படுகிறது; அழகான கொண்டாட்டம் பொறுப்பானதாகவும் இருக்க வேண்டும்.'],
    'safety.eyebrow': ['A SMALL REMINDER', 'ஒரு சிறிய நினைவூட்டல்'],
    'safety.title1': ['Celebrate bright.', 'ஒளிமயமாகக் கொண்டாடுங்கள்.'],
    'safety.title2': ['Celebrate safe.', 'பாதுகாப்பாகக் கொண்டாடுங்கள்.'],
    'safety.copy': ['Always light crackers outdoors, keep a bucket of water nearby, and give every sparkler plenty of space.', 'வெடிகளை எப்போதும் திறந்த வெளியில் கொளுத்துங்கள், அருகில் ஒரு வாளி தண்ணீர் வைத்திருங்கள், ஒவ்வொரு மத்தாப்புக்கும் போதுமான இடம் கொடுங்கள்.'],
    'safety.link': ['Read our safety guide', 'எங்கள் பாதுகாப்பு வழிகாட்டியைப் படிக்கவும்'],
    'footer.copy': ['For the nights you’ll talk about', 'ஒளி அணைந்த பிறகும்'],
    'footer.copy2': ['long after the lights go out.', 'நீங்கள் பேசிக்கொண்டிருக்கும் இரவுகளுக்காக.'],
    'footer.explore': ['Explore', 'பாருங்கள்'],
    'footer.all': ['All crackers', 'அனைத்து வெடிகள்'],
    'footer.combos': ['Family combos', 'குடும்ப காம்போக்கள்'],
    'footer.safety': ['Safety guide', 'பாதுகாப்பு வழிகாட்டி'],
    'footer.reach': ['Reach us', 'எங்களைத் தொடர்புகொள்ளுங்கள்'],
    'footer.hours': ['Mon–Sat · 9am–7pm', 'திங்கள்–சனி · காலை 9–மாலை 7'],
    'footer.admin': ['Admin', 'நிர்வாகம்'],
    'footer.made': ['Built with care in Sivakasi, Tamil Nadu', 'தமிழ்நாடு, சிவகாசியில் அக்கறையுடன் உருவாக்கப்பட்டது'],
    'admin.live': ['Store is live', 'ஸ்டோர் இயங்குகிறது'],
    'admin.openStore': ['Open storefront', 'ஸ்டோரைத் திறக்கவும்'],
    'admin.workspace': ['Workspace', 'பணியிடம்'],
    'admin.shortcuts': ['Shortcuts', 'குறுக்குவழிகள்'],
    'admin.overview': ['Overview', 'மேலோட்டம்'],
    'admin.products': ['Products', 'தயாரிப்புகள்'],
    'admin.inventory': ['Inventory', 'கையிருப்பு'],
    'admin.categories': ['Categories', 'வகைகள்'],
    'admin.orders': ['Orders', 'ஆர்டர்கள்'],
    'admin.viewStore': ['View storefront', 'ஸ்டோரைப் பார்க்கவும்'],
    'admin.signOut': ['Sign out', 'வெளியேறு'],
    'admin.preview': ['Preview mode', 'முன்னோட்ட நிலை'],
    'admin.previewCopy': ['Set ADMIN_PASSWORD before publishing to protect this dashboard.', 'இந்த டாஷ்போர்டை பாதுகாக்க வெளியிடுவதற்கு முன் ADMIN_PASSWORD அமைக்கவும்.'],
    'admin.goodMorning': ['GOOD MORNING, ADMIN', 'காலை வணக்கம், நிர்வாகி'],
    'admin.brightPicture': ['Here’s the bright picture.', 'இதோ ஒளிமயமான நிலவரம்.'],
    'admin.headingCopy': ['Keep the catalogue fresh, stock ready, and every order moving.', 'பட்டியலைப் புதுப்பித்து, கையிருப்பைத் தயாராக வைத்து, ஒவ்வொரு ஆர்டரையும் நகர்த்துங்கள்.'],
    'admin.readyStorefront': ['Ready on the storefront', 'ஸ்டோரில் தயாராக உள்ளது'],
    'admin.findSparkle': ['Ways to find the sparkle', 'ஒளியைத் தேடும் வழிகள்'],
    'admin.lowOrOut': ['Low or out of stock', 'குறைவு அல்லது கையிருப்பில் இல்லை'],
    'admin.waitingConfirmation': ['Waiting for confirmation', 'உறுதிப்படுத்தலுக்காக காத்திருக்கிறது'],
    'admin.addProduct': ['Add a product', 'தயாரிப்பைச் சேர்க்கவும்'],
    'admin.activeProducts': ['Active products', 'செயலில் உள்ள தயாரிப்புகள்'],
    'admin.categoryCount': ['Categories', 'வகைகள்'],
    'admin.needAttention': ['Need attention', 'கவனம் தேவை'],
    'admin.newOrders': ['New orders', 'புதிய ஆர்டர்கள்'],
    'admin.latestActivity': ['LATEST ACTIVITY', 'சமீபத்திய செயல்பாடு'],
    'admin.recentOrders': ['Recent orders', 'சமீபத்திய ஆர்டர்கள்'],
    'admin.viewAll': ['View all', 'அனைத்தையும் பார்க்கவும்'],
    'admin.inventoryWatch': ['INVENTORY WATCH', 'கையிருப்பு கண்காணிப்பு'],
    'admin.runningLow': ['Running low', 'குறைந்து வருகிறது'],
    'admin.manage': ['Manage', 'நிர்வகிக்கவும்'],
    'admin.quickActions': ['QUICK ACTIONS', 'விரைவு செயல்கள்'],
    'admin.nextMove': ['Make the next move.', 'அடுத்த செயலைச் செய்யுங்கள்.'],
    'admin.updateStock': ['Update stock', 'கையிருப்பைப் புதுப்பிக்கவும்'],
    'admin.newCategory': ['New category', 'புதிய வகை'],
    'admin.processOrders': ['Process orders', 'ஆர்டர்களைச் செயல்படுத்தவும்'],
    'admin.catalogue': ['CATALOGUE', 'பட்டியல்'],
    'admin.catalogueHeading': ['Products that spark joy.', 'மகிழ்ச்சியைப் பொறிக்கும் தயாரிப்புகள்.'],
    'admin.catalogueCopy': ['Add, edit, feature, or archive items from the storefront.', 'ஸ்டோரில் தயாரிப்புகளைச் சேர்க்கவும், திருத்தவும், சிறப்பாக்கவும் அல்லது காப்பகப்படுத்தவும்.'],
    'admin.stockControl': ['STOCK CONTROL', 'கையிருப்பு கட்டுப்பாடு'],
    'admin.stockHeading': ['Keep the shelves bright.', 'அலமாரிகளை ஒளிமயமாக வைத்திருங்கள்.'],
    'admin.collections': ['COLLECTIONS', 'தொகுப்புகள்'],
    'admin.collectionsHeading': ['Organise the magic.', 'கொண்டாட்டத்தை ஒழுங்குபடுத்துங்கள்.'],
    'admin.orderDesk': ['ORDER DESK', 'ஆர்டர் மேசை'],
    'admin.orderHeading': ['Keep every order moving.', 'ஒவ்வொரு ஆர்டரையும் நகர்த்துங்கள்.'],
    'admin.productName': ['Product name', 'தயாரிப்பு பெயர்'],
    'admin.tamilName': ['Tamil name', 'தமிழ் பெயர்'],
    'admin.category': ['Category', 'வகை'],
    'admin.image': ['Image', 'படம்'],
    'admin.salePrice': ['Sale price', 'விற்பனை விலை'],
    'admin.mrp': ['MRP', 'அசல் விலை'],
    'admin.unit': ['Unit / pack size', 'அலகு / பேக் அளவு'],
    'admin.badge': ['Badge / tag', 'பேட்ஜ் / குறிச்சொல்'],
    'admin.stockQuantity': ['Stock quantity', 'கையிருப்பு அளவு'],
    'admin.lowThreshold': ['Low stock alert at', 'குறைந்த கையிருப்பு எச்சரிக்கை'],
    'admin.featured': ['Feature this product', 'இந்த தயாரிப்பை சிறப்பாக்கவும்'],
    'admin.visible': ['Visible on storefront', 'ஸ்டோரில் காட்டவும்'],
    'admin.cancel': ['Cancel', 'ரத்து செய்'],
    'admin.saveProduct': ['Save product', 'தயாரிப்பைச் சேமிக்கவும்'],
    'admin.categoryName': ['Category name', 'வகையின் பெயர்'],
    'admin.description': ['Description', 'விளக்கம்'],
    'admin.saveCategory': ['Save category', 'வகையைச் சேமிக்கவும்'],
    'admin.allItems': ['ALL ITEMS', 'அனைத்து பொருட்கள்'],
    'admin.productCatalogue': ['Product catalogue', 'தயாரிப்பு பட்டியல்'],
    'admin.liveInventory': ['LIVE INVENTORY', 'நேரடி கையிருப்பு'],
    'admin.stockLevels': ['Stock levels', 'கையிருப்பு நிலைகள்'],
    'admin.allOrders': ['ALL ORDERS', 'அனைத்து ஆர்டர்கள்'],
    'admin.orderQueue': ['Order queue', 'ஆர்டர் வரிசை'],
  };

  let language = 'en';
  try { language = localStorage.getItem('udaya-language') === 'ta' ? 'ta' : 'en'; } catch {}

  function t(key) {
    const entry = translations[key];
    return entry ? entry[language === 'ta' ? 1 : 0] : key;
  }

  function apply() {
    document.documentElement.lang = language === 'ta' ? 'ta' : 'en';
    document.documentElement.dataset.language = language;
    document.querySelectorAll('[data-i18n]').forEach((element) => {
      element.textContent = t(element.dataset.i18n);
    });
    document.querySelectorAll('[data-i18n-placeholder]').forEach((element) => {
      element.placeholder = t(element.dataset.i18nPlaceholder);
    });
    document.querySelectorAll('[data-lang-en]').forEach((element) => {
      element.textContent = language === 'ta' ? element.dataset.langTa : element.dataset.langEn;
    });
    document.querySelectorAll('[data-language-toggle]').forEach((button) => {
      button.textContent = language === 'ta' ? 'English' : 'தமிழ்';
      button.setAttribute('aria-label', language === 'ta' ? 'Switch to English' : 'தமிழில் பார்க்கவும்');
    });
    document.dispatchEvent(new CustomEvent('udaya:language', { detail: { language } }));
  }

  function setLanguage(nextLanguage) {
    language = nextLanguage === 'ta' ? 'ta' : 'en';
    try { localStorage.setItem('udaya-language', language); } catch {}
    apply();
  }

  window.UdayaI18n = { apply, setLanguage, t, getLanguage: () => language };
  document.addEventListener('DOMContentLoaded', apply);
})();