(function () {
    'use strict';
    var $ = function (s, c) { return (c || document).querySelector(s); };
    var $$ = function (s, c) { return Array.prototype.slice.call((c || document).querySelectorAll(s)); };
    var body = document.body;

    // Header gölgesi
    var header = $('#header');
    var onScroll = function () { header && header.classList.toggle('is-scrolled', window.scrollY > 10); };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();

    // Mobil menü
    var burger = $('#burger');
    if (burger) {
        burger.addEventListener('click', function () {
            var open = body.classList.toggle('nav-open');
            burger.setAttribute('aria-expanded', open);
        });
    }

    // Mobil filtre paneli
    $$('[data-open-filters]').forEach(function (b) { b.addEventListener('click', function () { body.classList.add('filters-open'); }); });
    $$('[data-close-filters]').forEach(function (b) { b.addEventListener('click', function () { body.classList.remove('filters-open'); }); });
    var filters = $('#filters');
    if (filters) filters.addEventListener('click', function (e) { if (e.target === filters) body.classList.remove('filters-open'); });

    // Fiyat alanlarında binlik ayırıcı (gönderimde temizlenir)
    $$('[data-money]').forEach(function (input) {
        var fmt = function () {
            var d = input.value.replace(/\D/g, '');
            input.value = d ? Number(d).toLocaleString('tr-TR') : '';
        };
        input.addEventListener('input', fmt);
        fmt();
    });
    $$('form').forEach(function (f) {
        f.addEventListener('submit', function () {
            $$('[data-money]', f).forEach(function (i) { i.value = i.value.replace(/\D/g, ''); });
            // Boş GET parametrelerini URL'den çıkar (temiz, SEO dostu adresler)
            if ((f.method || 'get').toLowerCase() === 'get') {
                $$('input[name], select[name]', f).forEach(function (el) {
                    if (el.value === '') el.disabled = true;
                });
            }
        });
    });

    // Geri tuşuyla dönüldüğünde (bfcache) devre dışı kalan alanları geri aç
    window.addEventListener('pageshow', function () { $$('form [disabled]').forEach(function (el) { el.disabled = false; }); });

    // Paylaş
    $$('[data-share]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var data = { title: btn.dataset.share, url: location.href };
            if (navigator.share) { navigator.share(data).catch(function () {}); return; }
            navigator.clipboard && navigator.clipboard.writeText(location.href).then(function () {
                var t = btn.innerHTML; btn.textContent = 'Bağlantı kopyalandı ✓';
                setTimeout(function () { btn.innerHTML = t; }, 1800);
            });
        });
    });

    // Galeri lightbox
    var gallery = $('[data-gallery]');
    var lb = $('#lightbox');
    if (gallery && lb) {
        var srcs = JSON.parse($('template[data-sources]', gallery).content.textContent);
        var img = $('img', lb), count = $('.lightbox__count', lb), idx = 0;
        var show = function (i) {
            idx = (i + srcs.length) % srcs.length;
            img.src = srcs[idx];
            count.textContent = (idx + 1) + ' / ' + srcs.length;
        };
        var open = function (i) { show(i); lb.hidden = false; body.style.overflow = 'hidden'; };
        var close = function () { lb.hidden = true; body.style.overflow = ''; };
        $$('.gallery__item', gallery).forEach(function (b) { b.addEventListener('click', function () { open(+b.dataset.index); }); });
        $('[data-lb-close]', lb).addEventListener('click', close);
        $('[data-lb-prev]', lb).addEventListener('click', function () { show(idx - 1); });
        $('[data-lb-next]', lb).addEventListener('click', function () { show(idx + 1); });
        lb.addEventListener('click', function (e) { if (e.target === lb) close(); });
        document.addEventListener('keydown', function (e) {
            if (lb.hidden) return;
            if (e.key === 'Escape') close();
            if (e.key === 'ArrowLeft') show(idx - 1);
            if (e.key === 'ArrowRight') show(idx + 1);
        });
        var sx = 0;
        lb.addEventListener('touchstart', function (e) { sx = e.touches[0].clientX; }, { passive: true });
        lb.addEventListener('touchend', function (e) {
            var dx = e.changedTouches[0].clientX - sx;
            if (Math.abs(dx) > 40) show(idx + (dx < 0 ? 1 : -1));
        });
    }

    // Kaydırınca beliren öğeler
    if ('IntersectionObserver' in window) {
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (en) { if (en.isIntersecting) { en.target.classList.add('is-in'); io.unobserve(en.target); } });
        }, { rootMargin: '0px 0px -60px 0px' });
        $$('.card, .agent, .post-card, .feature-box, .cat, .steps li, .agent-row').forEach(function (el) {
            el.classList.add('reveal'); io.observe(el);
        });
    }

    // ---- Haritalar (Leaflet + CARTO açık altlık) ----
    var tiles = function (map) {
        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; OpenStreetMap katkıcıları &copy; CARTO', subdomains: 'abcd', maxZoom: 19
        }).addTo(map);
    };
    var pinIcon = function () {
        return L.divIcon({
            className: '', iconSize: [36, 46], iconAnchor: [18, 46], popupAnchor: [0, -40],
            html: '<svg width="36" height="46" viewBox="0 0 36 46"><path d="M18 0C8 0 0 8 0 18c0 13 18 28 18 28s18-15 18-28C36 8 28 0 18 0z" fill="#0f3d3e"/><circle cx="18" cy="18" r="7" fill="#e0a458"/></svg>'
        });
    };

    var initMaps = function () {
        if (!window.L) return;
        $$('[data-map]').forEach(function (el) {
            var lat = parseFloat(el.dataset.lat), lng = parseFloat(el.dataset.lng);
            if (isNaN(lat)) return;
            var map = L.map(el, { scrollWheelZoom: false }).setView([lat, lng], +el.dataset.zoom || 14);
            tiles(map);
            if (el.dataset.circle) {
                L.circle([lat, lng], { radius: 250, color: '#0f3d3e', fillColor: '#e0a458', fillOpacity: .25, weight: 2 }).addTo(map);
            } else {
                var m = L.marker([lat, lng], { icon: pinIcon() }).addTo(map);
                if (el.dataset.popup) m.bindPopup('<strong>' + el.dataset.popup + '</strong>').openPopup();
            }
        });

        var rm = $('#results-map');
        if (rm) {
            var map = null;
            var build = function () {
                if (map) { map.invalidateSize(); return; }
                var items = JSON.parse(rm.dataset.markers || '[]');
                map = L.map(rm, { scrollWheelZoom: true }).setView([41.03, 29.0], 11);
                tiles(map);
                var bounds = [];
                items.forEach(function (it) {
                    var icon = L.divIcon({ className: '', html: '<span class="price-pin">' + it.price + '</span>', iconSize: [0, 0] });
                    var div = document.createElement('div');
                    div.className = 'map-pop';
                    div.innerHTML = '<img alt=""><strong></strong><a></a>';
                    div.querySelector('img').src = it.img;
                    div.querySelector('strong').textContent = it.price;
                    var a = div.querySelector('a'); a.href = it.url; a.textContent = it.title;
                    L.marker([it.lat, it.lng], { icon: icon }).addTo(map).bindPopup(div);
                    bounds.push([it.lat, it.lng]);
                });
                if (bounds.length) map.fitBounds(bounds, { padding: [50, 50], maxZoom: 14 });
            };
            $$('[data-view]').forEach(function (b) {
                b.addEventListener('click', function () {
                    $$('[data-view]').forEach(function (x) { x.classList.toggle('is-active', x === b); });
                    var isMap = b.dataset.view === 'map';
                    rm.hidden = !isMap;
                    try { localStorage.setItem('listingView', b.dataset.view); } catch (e) {}
                    if (isMap) build();
                });
            });
            var saved = null;
            try { saved = localStorage.getItem('listingView'); } catch (e) {}
            if (saved === 'map') $('[data-view="map"]').click();
        }
    };
    if (window.L) initMaps(); else window.addEventListener('load', initMaps);
})();
