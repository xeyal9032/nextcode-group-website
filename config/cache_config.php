<?php
// Cache konfigürasyonu
return [
    "enabled" => true,
    "default_ttl" => 3600, // 1 saat
    "portfolio_ttl" => 1800, // 30 dakika
    "blog_ttl" => 3600, // 1 saat
    "settings_ttl" => 7200, // 2 saat
    "cache_dir" => __DIR__ . "/cache/",
    "compress" => true
];