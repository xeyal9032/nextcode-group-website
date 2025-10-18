<?php
// Force refresh - Cache bypass
header('Content-Type: text/html; charset=utf-8');
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: Thu, 01 Jan 1970 00:00:00 GMT");

echo "<!DOCTYPE html>";
echo "<html><head><meta charset='utf-8'><title>Force Refresh</title></head><body>";
echo "<h1>Cache Bypass - Force Refresh</h1>";
echo "<p>Bu sayfa cache'i bypass eder ve her zaman güncel içeriği gösterir.</p>";
echo "<p>Son güncelleme: " . date('Y-m-d H:i:s') . "</p>";

echo "<h2>Blog Makaleleri (Güncel):</h2>";
echo "<ul>";
echo "<li><a href='blog-post.php?id=1&debug=1&t=" . time() . "'>SEO Optimizasiyası (12 dk)</a></li>";
echo "<li><a href='blog-post.php?id=2&debug=1&t=" . time() . "'>Modern Web Development Trends (15 dk)</a></li>";
echo "<li><a href='blog-post.php?id=3&debug=1&t=" . time() . "'>E-commerce Development (18 dk)</a></li>";
echo "<li><a href='blog-post.php?id=4&debug=1&t=" . time() . "'>Cybersecurity Guide (20 dk)</a></li>";
echo "</ul>";

echo "<h2>Cache Temizleme:</h2>";
echo "<p><a href='clear-cache.php'>Cache Temizle</a></p>";
echo "<p><a href='test-blog.php'>Test Sayfası</a></p>";

echo "<h2>Browser Cache Temizleme:</h2>";
echo "<p>Tarayıcınızda Ctrl+F5 veya Ctrl+Shift+R tuşlarına basın</p>";
echo "<p>Veya Developer Tools (F12) açıp Network sekmesinde 'Disable cache' işaretleyin</p>";

echo "</body></html>";
?>


