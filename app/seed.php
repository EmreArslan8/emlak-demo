<?php
declare(strict_types=1);

/** Demo veritabanını örnek (mock) verilerle doldurur. Görseller Unsplash stok fotoğraflarıdır. */
function seed(PDO $pdo): void
{
    $u = fn(string $id) => 'https://images.unsplash.com/' . $id;

    $ins = function (string $table, array $row) use ($pdo): int {
        $cols = array_keys($row);
        $st = $pdo->prepare(sprintf('INSERT INTO %s (%s) VALUES (%s)', $table, implode(',', $cols), implode(',', array_fill(0, count($cols), '?'))));
        $st->execute(array_values($row));
        return (int) $pdo->lastInsertId();
    };

    $pdo->beginTransaction();

    $ins('users', ['username' => 'admin', 'password_hash' => password_hash('admin123', PASSWORD_DEFAULT), 'name' => 'Ofis Yöneticisi']);

    $settings = [
        'site_name' => 'Vista Gayrimenkul',
        'tagline' => 'İstanbul\'da doğru adres, doğru yatırım.',
        'phone' => '+90 212 000 00 00',
        'whatsapp' => '+90 500 000 00 00',
        'email' => 'info@vistagayrimenkul.com',
        'address' => 'Bağdat Caddesi No: 214, Caddebostan, Kadıköy / İstanbul',
        'office_lat' => '40.9645',
        'office_lng' => '29.0645',
        'working_hours' => 'Hafta içi 09:00 – 19:00 · Cumartesi 10:00 – 17:00',
        'hero_title' => 'İstanbul\'un en seçkin adresleri',
        'hero_subtitle' => 'Satış, kiralama ve yatırımda 20 yılı aşkın deneyim. Her adımda kişiye özel, gizlilik esaslı danışmanlık.',
        'instagram' => 'https://instagram.com/',
        'linkedin' => 'https://linkedin.com/',
        'meta_description' => 'Vista Gayrimenkul — İstanbul\'da satılık ve kiralık daire, villa, rezidans ve ticari gayrimenkul ilanları. Uzman danışman kadrosu ile güvenilir emlak hizmeti.',
    ];
    foreach ($settings as $k => $v) {
        $ins('settings', ['key' => $k, 'value' => $v]);
    }

    $agents = [
        ['Elif Aydın', 'Kurucu Ortak & Kıdemli Danışman', '+90 500 000 00 01', 'photo-1494790108377-be9c29b29330', 18, 'Türkçe, İngilizce, Almanca', 'Lüks konut, Boğaz hattı yalı ve villalar',
            "Elif, 18 yıllık sektör tecrübesiyle İstanbul'un Boğaz hattındaki lüks konut piyasasının en tanınan isimlerinden biri. Yatırımcı ve yabancı müşteri portföyü ile özellikle Bebek, Arnavutköy ve Sarıyer bölgelerinde uzmanlaşmıştır.\n\nMüşterileriyle uzun vadeli güven ilişkisi kurmayı önceliği olarak görür; her işlemi hukuki ve finansal açıdan uçtan uca takip eder."],
        ['Mert Kaya', 'Ticari Gayrimenkul Uzmanı', '+90 500 000 00 02', 'photo-1507003211169-0a1dd7228f2d', 12, 'Türkçe, İngilizce', 'Ofis, dükkan, plaza katı ve yatırım amaçlı ticari mülkler',
            "Mert, Maslak–Levent iş aksında ofis ve plaza katı kiralama/satış süreçlerinde 12 yıllık deneyime sahiptir. Kurumsal firmaların yer seçimi, kira müzakeresi ve yatırım getirisi analizlerinde danışmanlık verir."],
        ['Zeynep Demir', 'Konut Danışmanı', '+90 500 000 00 03', 'photo-1438761681033-6461ffad8d80', 7, 'Türkçe, İngilizce, Rusça', 'Anadolu Yakası daire ve rezidans projeleri',
            "Zeynep, Kadıköy, Ataşehir ve Üsküdar bölgelerinde ilk evini alacak aileler ve genç profesyonellerle çalışır. Kredi süreçleri ve proje karşılaştırmalarında detaycı yaklaşımıyla bilinir."],
        ['Can Öztürk', 'Arsa & Proje Danışmanı', '+90 500 000 00 04', 'photo-1500648767791-00dcc994a43e', 10, 'Türkçe, Arapça', 'Arsa, kat karşılığı ve kentsel dönüşüm projeleri',
            "Can, imar durumu analizi, kat karşılığı anlaşmalar ve kentsel dönüşüm süreçlerinde mal sahipleri ile müteahhitler arasında köprü görevi görür. Avrupa Yakası batı aksı ve Çekmeköy bölgesinde aktif portföy yönetir."],
    ];
    $agentIds = [];
    foreach ($agents as $i => [$name, $title, $phone, $photo, $exp, $langs, $spec, $bio]) {
        $agentIds[] = $ins('agents', [
            'slug' => slugify($name), 'name' => $name, 'title' => $title, 'phone' => $phone,
            'email' => slugify(explode(' ', $name)[0]) . '@vistagayrimenkul.com', 'photo' => $u($photo),
            'bio' => $bio, 'experience_years' => $exp, 'languages' => $langs, 'specialties' => $spec, 'sort' => $i,
        ]);
    }

    $interiors = ['photo-1502672260266-1c1ef2d93688', 'photo-1522708323590-d24dbb6b0267', 'photo-1493809842364-78817add7ffb',
        'photo-1560448204-e02f11c3d0e2', 'photo-1484154218962-a197022b5858', 'photo-1600607687939-ce8a6c25118c',
        'photo-1600566753190-17f0baa2a6c3', 'photo-1586023492125-27b2c045efd7', 'photo-1600210492486-724fe5c67fb0',
        'photo-1554995207-c18c203602cb'];
    $gallery = function (string $cover, int $offset) use ($interiors, $u): string {
        $imgs = [$u($cover)];
        for ($i = 0; $i < 4; $i++) {
            $imgs[] = $u($interiors[($offset + $i) % count($interiors)]);
        }
        return json_encode($imgs);
    };

    // [başlık, tip, kategori, fiyat, ilçe, mahalle, lat, lng, oda, brüt, net, banyo, kat, toplam kat, yaş, ısınma, özellikler, kapak, danışman, öne çıkan, durum]
    $listings = [
        ['Moda\'da Deniz Manzaralı Yenilenmiş 3+1 Daire', 'satilik', 'daire', 14750000, 'Kadıköy', 'Moda', 40.9840, 29.0260, '3+1', 145, 125, 2, '4', 5, 25, 'Kombi (Doğalgaz)',
            ['Asansör', 'Balkon', 'Deniz Manzarası', 'Ankastre Mutfak', 'Ebeveyn Banyosu', 'Metroya Yakın', 'Krediye Uygun'], 'photo-1545324418-cc1a3fa10c00', 2, 1, 'aktif'],
        ['Bebek\'te Boğaz Manzaralı Müstakil Villa', 'satilik', 'villa', 98500000, 'Beşiktaş', 'Bebek', 41.0770, 29.0430, '6+2', 480, 410, 5, 'Bahçe', 3, 8, 'Yerden Isıtma',
            ['Boğaz Manzarası', 'Bahçe', 'Teras', 'Havuz', 'Kapalı Otopark', 'Güvenlik', 'Akıllı Ev', 'Şömine', 'Giyinme Odası', 'Jeneratör'], 'photo-1613490493576-7fde63acd811', 0, 1, 'aktif'],
        ['Zekeriyaköy\'de Havuzlu Bahçeli Villa', 'satilik', 'villa', 42000000, 'Sarıyer', 'Zekeriyaköy', 41.1960, 29.0300, '5+1', 380, 320, 4, 'Bahçe', 3, 5, 'Yerden Isıtma',
            ['Havuz', 'Bahçe', 'Site İçerisinde', 'Güvenlik', 'Otopark', 'Şömine', 'Ebeveyn Banyosu', 'Giyinme Odası'], 'photo-1512917774080-9991f1c4c750', 3, 1, 'aktif'],
        ['Ataşehir\'de Rezidans Katında Eşyalı 1+1', 'kiralik', 'rezidans', 48000, 'Ataşehir', 'Barbaros', 40.9923, 29.1244, '1+1', 78, 62, 1, '18', 32, 4, 'Merkezi',
            ['Asansör', 'Kapalı Otopark', 'Güvenlik', 'Havuz', 'Spor Salonu', 'Eşyalı', 'Metroya Yakın'], 'photo-1522708323590-d24dbb6b0267', 5, 1, 'aktif'],
        ['Kuzguncuk\'ta Tarihi Dokuda Restore Edilmiş Ev', 'satilik', 'mustakil', 23500000, 'Üsküdar', 'Kuzguncuk', 41.0360, 29.0310, '4+1', 210, 180, 2, 'Bahçe', 3, 90, 'Kombi (Doğalgaz)',
            ['Bahçe', 'Teras', 'Boğaz Manzarası', 'Şömine', 'Ankastre Mutfak'], 'photo-1564013799919-ab600027ffc6', 7, 0, 'aktif'],
        ['Yeşilköy\'de Sahile Yürüme Mesafesinde 2+1', 'kiralik', 'daire', 55000, 'Bakırköy', 'Yeşilköy', 40.9620, 28.8260, '2+1', 110, 95, 1, '2', 4, 15, 'Kombi (Doğalgaz)',
            ['Balkon', 'Asansör', 'Otopark', 'Metroya Yakın'], 'photo-1493809842364-78817add7ffb', 2, 0, 'aktif'],
        ['Beylikdüzü\'nde Site İçinde Yatırımlık 2+1', 'satilik', 'daire', 5450000, 'Beylikdüzü', 'Adnan Kahveci', 40.9900, 28.6400, '2+1', 105, 88, 1, '7', 14, 6, 'Kombi (Doğalgaz)',
            ['Site İçerisinde', 'Havuz', 'Spor Salonu', 'Kapalı Otopark', 'Güvenlik', 'Krediye Uygun'], 'photo-1460317442991-0ec209397118', 4, 0, 'aktif'],
        ['Nişantaşı\'nda Yüksek Tavanlı Klasik 4+1', 'satilik', 'daire', 36000000, 'Şişli', 'Teşvikiye', 41.0520, 28.9940, '4+1', 230, 200, 2, '3', 6, 60, 'Merkezi',
            ['Asansör', 'Balkon', 'Ebeveyn Banyosu', 'Şömine', 'Metroya Yakın'], 'photo-1600585154340-be6161a56a0c', 6, 1, 'aktif'],
        ['Fenerbahçe\'de Marina Manzaralı Lüks 3+1', 'kiralik', 'daire', 125000, 'Kadıköy', 'Fenerbahçe', 40.9720, 29.0400, '3+1', 170, 150, 2, '6', 8, 3, 'Yerden Isıtma',
            ['Deniz Manzarası', 'Kapalı Otopark', 'Asansör', 'Güvenlik', 'Akıllı Ev', 'Ebeveyn Banyosu', 'Balkon'], 'photo-1600596542815-ffad4c1539a9', 8, 1, 'aktif'],
        ['Cihangir\'de Teraslı Eşyalı Stüdyo', 'kiralik', 'daire', 38000, 'Beyoğlu', 'Cihangir', 41.0320, 28.9830, '1+0', 55, 45, 1, 'Çatı', 5, 40, 'Klima',
            ['Teras', 'Boğaz Manzarası', 'Eşyalı', 'Metroya Yakın'], 'photo-1502672260266-1c1ef2d93688', 9, 0, 'aktif'],
        ['Maslak\'ta A+ Plazada 420 m² Ofis Katı', 'kiralik', 'ofis', 390000, 'Sarıyer', 'Maslak', 41.1100, 29.0200, '-', 420, 380, 4, '21', 42, 6, 'Merkezi',
            ['Asansör', 'Kapalı Otopark', 'Güvenlik', 'Jeneratör', 'Metroya Yakın'], 'photo-1497366216548-37526070297c', 1, 1, 'aktif'],
        ['Levent\'te Cadde Üzeri Köşe Dükkan', 'satilik', 'dukkan', 27500000, 'Beşiktaş', 'Levent', 41.0820, 29.0110, '-', 160, 145, 1, 'Zemin', 6, 12, 'Klima',
            ['Metroya Yakın', 'Jeneratör'], 'photo-1441986300917-64674bd600d8', 3, 0, 'aktif'],
        ['Çekmeköy\'de Villa İmarlı 1.250 m² Arsa', 'satilik', 'arsa', 18750000, 'Çekmeköy', 'Taşdelen', 41.0330, 29.1830, '-', 1250, 1250, 0, '-', 0, 0, 'Yok',
            ['Krediye Uygun'], 'photo-1500382017468-9049fed747ef', 0, 0, 'aktif'],
        ['Kartal\'da Deniz Gören Yeni Proje 3+1', 'satilik', 'rezidans', 9850000, 'Kartal', 'Kordonboyu', 40.8900, 29.1900, '3+1', 150, 128, 2, '12', 25, 1, 'Yerden Isıtma',
            ['Deniz Manzarası', 'Site İçerisinde', 'Havuz', 'Spor Salonu', 'Kapalı Otopark', 'Güvenlik', 'Akıllı Ev', 'Krediye Uygun'], 'photo-1580587771525-78b9dba3b914', 1, 0, 'satildi'],
        ['Arnavutköy\'de Boğaz Hattında Tarihi Yalı Dairesi', 'satilik', 'daire', 64000000, 'Beşiktaş', 'Arnavutköy', 41.0670, 29.0430, '4+1', 260, 225, 3, '1', 3, 110, 'Yerden Isıtma',
            ['Boğaz Manzarası', 'Teras', 'Şömine', 'Ebeveyn Banyosu', 'Ankastre Mutfak'], 'photo-1600566753190-17f0baa2a6c3', 4, 0, 'satildi'],
        ['Etiler\'de Bahçeli Tripleks Konak', 'satilik', 'villa', 72500000, 'Beşiktaş', 'Etiler', 41.0810, 29.0330, '6+2', 520, 450, 5, 'Bahçe', 3, 12, 'Yerden Isıtma',
            ['Bahçe', 'Havuz', 'Kapalı Otopark', 'Güvenlik', 'Akıllı Ev', 'Giyinme Odası', 'Şömine', 'Jeneratör'], 'photo-1600585154340-be6161a56a0c', 6, 0, 'satildi'],
    ];

    $agentFor = [0 => 2, 1 => 0, 2 => 0, 3 => 2, 4 => 2, 5 => 3, 6 => 3, 7 => 0, 8 => 2, 9 => 0, 10 => 1, 11 => 1, 12 => 3, 13 => 2, 14 => 0, 15 => 0];
    foreach ($listings as $i => $l) {
        [$title, $type, $cat, $price, $district, $hood, $lat, $lng, $rooms, $gross, $net, $bath, $floor, $tfloors, $age, $heat, $feat, $cover, $off, $featured, $status] = $l;
        $created = date('Y-m-d H:i:s', strtotime('-' . ($i * 3 + 1) . ' days'));
        $ins('listings', [
            'slug' => slugify($title), 'title' => $title, 'type' => $type, 'category' => $cat, 'price' => $price,
            'district' => $district, 'neighborhood' => $hood, 'lat' => $lat, 'lng' => $lng, 'rooms' => $rooms,
            'gross_m2' => $gross, 'net_m2' => $net, 'bathrooms' => $bath, 'floor' => $floor, 'total_floors' => $tfloors,
            'building_age' => $age, 'heating' => $heat, 'features' => json_encode($feat, JSON_UNESCAPED_UNICODE),
            'images' => in_array($cat, ['arsa', 'ofis', 'dukkan'], true) ? json_encode([$u($cover)]) : $gallery($cover, $off),
            'description' => listing_description($title, $hood, $district, $cat, $type),
            'agent_id' => $agentIds[$agentFor[$i]], 'featured' => $featured, 'status' => $status,
            'views' => random_int(40, 900), 'created_at' => $created, 'updated_at' => $created,
        ]);
    }

    $posts = [
        ['2026\'da Konut Kredisi Kullanırken Dikkat Edilmesi Gerekenler', 'Rehber', 'photo-1560518883-ce09059eeffa', 3,
            "Konut kredisi, ev sahibi olmanın en yaygın yolu. Ancak faiz oranları, vade seçimi ve masraf kalemleri doğru planlanmadığında toplam maliyet ciddi oranda artabilir.\n\n## Kredi notunuzu önceden kontrol edin\n\nBankaya başvurmadan önce Findeks üzerinden kredi notunuzu kontrol etmeniz, alacağınız teklifleri doğrudan etkiler. Kredi kartı borçlarınızı düzenli ödemek kısa sürede notunuzu yükseltebilir.\n\n## Ekspertiz değeri satış fiyatından farklı olabilir\n\nBankalar kredi tutarını, satış fiyatına değil ekspertiz raporundaki değere göre belirler. Bu nedenle peşinat planınızı ekspertiz sonrası netleştirmeniz önemlidir.\n\n## Masraf kalemlerini unutmayın\n\n- Tapu harcı (alıcı ve satıcı için ayrı ayrı %2)\n- Ekspertiz ücreti\n- DASK ve konut sigortası\n- Döner sermaye bedeli\n\nDanışmanlarımız, kredi sürecinin her adımında size ücretsiz destek sağlar."],
        ['İstanbul\'da Kira Getirisi En Yüksek 5 Semt', 'Yatırım', 'photo-1524231757912-21f4fe3a7200', 10,
            "Yatırım amaçlı konut alırken amortisman süresi ve bölgenin kiracı profili en kritik iki göstergedir. Ofisimizin 2026 verilerine göre öne çıkan semtleri derledik.\n\n## 1. Ataşehir\n\nFinans merkezine yakınlığı ve rezidans stoğu sayesinde kurumsal kiracı talebi yüksek.\n\n## 2. Beylikdüzü\n\nGiriş fiyatlarının görece uygun olması nedeniyle amortisman süresi İstanbul ortalamasının altında.\n\n## 3. Kadıköy\n\nÜniversite ve genç profesyonel nüfusu sayesinde boş kalma riski en düşük bölgelerden biri.\n\n## 4. Maslak – Sarıyer\n\nPlaza çalışanlarına yönelik 1+1 ve stüdyo daireler yıl boyunca talep görüyor.\n\n## 5. Kartal\n\nYeni projeler ve sahil düzenlemesiyle değer artışı potansiyeli yüksek."],
        ['Evinizi Satmadan Önce Yapmanız Gereken 7 Küçük Dokunuş', 'İpuçları', 'photo-1600210492486-724fe5c67fb0', 18,
            "İlk izlenim, satış süresini ve fiyatı doğrudan etkiler. Büyük tadilatlara girmeden evinizin algılanan değerini artırabilirsiniz.\n\n- Duvarları nötr renklere boyayın\n- Kişisel eşyaları ve fazla mobilyayı kaldırın\n- Aydınlatmayı güçlendirin, perdeleri açık tutun\n- Küçük tamirleri (musluk, kapı kolu, derz) tamamlayın\n- Balkon ve girişi düzenleyin\n- Profesyonel fotoğraf çektirin\n- Tapu ve iskân belgelerinizi hazır bulundurun\n\nVista Gayrimenkul olarak portföyümüze aldığımız her mülk için profesyonel fotoğraf çekimi ücretsizdir."],
        ['Kentsel Dönüşümde Hak Sahiplerini Bekleyen Süreç', 'Mevzuat', 'photo-1486406146926-c627a92ad1ab', 26,
            "Riskli yapı tespiti ile başlayan kentsel dönüşüm süreci, doğru yönetildiğinde hak sahipleri için önemli bir değer artışı fırsatıdır.\n\n## Riskli yapı tespiti\n\nTespit, Çevre ve Şehircilik Bakanlığı tarafından lisanslandırılmış kuruluşlarca yapılır ve tapuya şerh düşülür.\n\n## Müteahhit seçimi\n\nKat karşılığı sözleşmesinde teslim süresi, gecikme cezası ve kira yardımı maddeleri mutlaka net yazılmalıdır.\n\n## Kira yardımı\n\nHak sahipleri, belirli şartlarla devletten kira yardımı alabilir. Başvuru süreçlerinde ekibimiz size rehberlik eder."],
    ];
    foreach ($posts as [$title, $cat, $cover, $days, $content]) {
        $ins('posts', ['slug' => slugify($title), 'title' => $title, 'category' => $cat, 'cover' => $u($cover),
            'excerpt' => excerpt(preg_replace('/^## .*$/m', '', $content), 160), 'content' => $content,
            'author' => 'Vista Gayrimenkul', 'published_at' => date('Y-m-d H:i:s', strtotime("-$days days"))]);
    }

    $ins('messages', ['name' => 'Ayşe Yılmaz', 'email' => 'ayse@example.com', 'phone' => '0500 000 00 11',
        'subject' => 'İlan hakkında bilgi', 'message' => 'Merhaba, Moda\'daki 3+1 daire için hafta sonu görüntüleme randevusu almak istiyorum.',
        'listing_id' => 1, 'created_at' => date('Y-m-d H:i:s', strtotime('-3 hours'))]);
    $ins('messages', ['name' => 'Burak Şen', 'email' => 'burak@example.com', 'phone' => '0500 000 00 12',
        'subject' => 'Evimi satmak istiyorum', 'message' => 'Ataşehir\'deki 2+1 dairem için değerleme hizmeti almak istiyorum. Uygun olduğunuzda arayabilir misiniz?',
        'is_read' => 1, 'created_at' => date('Y-m-d H:i:s', strtotime('-2 days'))]);

    $pdo->commit();
}

function listing_description(string $title, string $hood, string $district, string $cat, string $type): string
{
    $action = $type === 'kiralik' ? 'kiralık' : 'satılık';
    if ($cat === 'arsa') {
        return "$district, $hood bölgesinde $action, villa imarlı arsa. Ana yola cepheli, altyapısı hazır, tapusu müstakil parseldir.\n\nKonut projesi veya kendi villanızı inşa etmek için ideal konumda; okul, market ve TEM bağlantısına dakikalar mesafede.\n\nİmar durumu belgesi ve aplikasyon krokisi ofisimizde incelemeye hazırdır.";
    }
    if (in_array($cat, ['ofis', 'dukkan'], true)) {
        return "$district / $hood'da prestijli konumda $action ticari alan. Yüksek yaya ve araç trafiği, toplu taşımaya yürüme mesafesi ile markanız için güçlü bir adres.\n\nBina 7/24 güvenlik, jeneratör ve kapalı otopark hizmetlerine sahiptir. Teslim tarihi ve kira artış koşulları için danışmanımızla iletişime geçebilirsiniz.";
    }
    return "$hood'un en sevilen sokaklarından birinde konumlanan bu $action mülk, ferah yaşam alanları ve gün boyu ışık alan cephesiyle öne çıkıyor.\n\nGeniş salon, açık plan ankastre mutfak ve özenle seçilmiş malzemelerle yenilenmiş ıslak hacimler sunar. Ebeveyn yatak odası, giyinme alanı ve ayrı banyo ile konforlu bir yaşam için tasarlanmıştır.\n\nUlaşım: Toplu taşıma duraklarına yürüme mesafesinde; okul, market, kafe ve sahil gibi sosyal olanaklar hemen yakınınızda.\n\nTapu: Kat mülkiyetli, iskânlı. Görüntüleme için danışmanımızdan randevu alabilirsiniz.";
}
