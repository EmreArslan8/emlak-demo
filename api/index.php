<?php
// Vercel giriş noktası: dosya sistemi salt-okunur olduğundan veritabanı ve oturumlar /tmp'de tutulur.
// Not: /tmp geçicidir; sunucu yeniden başladığında demo verileri sıfırlanır.
putenv('EMLAK_DB=/tmp/emlak.sqlite');
putenv('EMLAK_BASE=');
ini_set('session.save_path', '/tmp');

require __DIR__ . '/../public/index.php';
