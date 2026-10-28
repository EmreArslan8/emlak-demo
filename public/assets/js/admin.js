(function () {
    'use strict';
    var $ = function (s, c) { return (c || document).querySelector(s); };
    var $$ = function (s, c) { return Array.prototype.slice.call((c || document).querySelectorAll(s)); };

    // Fiyat alanı biçimlendirme
    $$('[data-money]').forEach(function (input) {
        var fmt = function () { var d = input.value.replace(/\D/g, ''); input.value = d ? Number(d).toLocaleString('tr-TR') : ''; };
        input.addEventListener('input', fmt);
        fmt();
        input.form && input.form.addEventListener('submit', function () { input.value = input.value.replace(/\D/g, ''); });
    });

    // Görsel önizleme: textarea'daki her satır bir küçük resim olur; sil / kapak yap
    var ta = $('[data-images]'), thumbs = $('[data-thumbs]');
    var base = document.querySelector('link[rel=icon]').getAttribute('href').split('/assets/')[0];
    var src = function (u) {
        if (u.indexOf('https://images.unsplash.com/') === 0 && u.indexOf('?') < 0) return u + '?auto=format&fit=crop&w=300&q=60';
        if (u.indexOf('/uploads/') === 0) return base + u;
        return u;
    };
    var render = function () {
        if (!ta || !thumbs) return;
        var lines = ta.value.split(/\n/).map(function (s) { return s.trim(); }).filter(Boolean);
        thumbs.innerHTML = '';
        lines.forEach(function (u, i) {
            var d = document.createElement('div');
            d.className = 'thumb';
            var img = document.createElement('img'); img.src = src(u); img.alt = '';
            var del = document.createElement('button'); del.type = 'button'; del.textContent = '×'; del.title = 'Kaldır';
            del.onclick = function () { lines.splice(i, 1); ta.value = lines.join('\n'); render(); };
            d.appendChild(img); d.appendChild(del);
            if (i > 0) {
                var first = document.createElement('button'); first.type = 'button'; first.className = 'thumb__first'; first.textContent = 'Kapak yap';
                first.onclick = function () { lines.unshift(lines.splice(i, 1)[0]); ta.value = lines.join('\n'); render(); };
                d.appendChild(first);
            }
            thumbs.appendChild(d);
        });
    };
    if (ta) { ta.addEventListener('input', render); render(); }

    // Yükleme alanında seçilen dosya sayısı
    $$('.upload input[type=file]').forEach(function (inp) {
        inp.addEventListener('change', function () {
            var label = inp.parentNode, span = $('span', label);
            if (inp.files.length) {
                label.classList.add('has-files');
                span.textContent = inp.files.length + ' dosya seçildi — kaydedince yüklenecek';
            }
        });
    });

    // Haritadan konum seçici
    var initPicker = function () {
        var el = $('[data-map-picker]');
        if (!el || !window.L) return;
        var latIn = $('[data-lat]'), lngIn = $('[data-lng]');
        var lat = parseFloat(el.dataset.lat) || 41.015, lng = parseFloat(el.dataset.lng) || 28.98;
        var map = L.map(el).setView([lat, lng], latIn.value ? 15 : 11);
        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', { attribution: '&copy; OpenStreetMap &copy; CARTO', subdomains: 'abcd', maxZoom: 19 }).addTo(map);
        var marker = L.marker([lat, lng], { draggable: true }).addTo(map);
        var set = function (ll) {
            marker.setLatLng(ll);
            latIn.value = ll.lat.toFixed(6);
            lngIn.value = ll.lng.toFixed(6);
        };
        map.on('click', function (e) { set(e.latlng); });
        marker.on('dragend', function () { set(marker.getLatLng()); });
        [latIn, lngIn].forEach(function (i) {
            i.addEventListener('change', function () {
                var a = parseFloat(latIn.value), b = parseFloat(lngIn.value);
                if (!isNaN(a) && !isNaN(b)) { marker.setLatLng([a, b]); map.setView([a, b]); }
            });
        });
    };
    if (window.L) initPicker(); else window.addEventListener('load', initPicker);
})();
