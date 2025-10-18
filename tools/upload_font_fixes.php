<?php
/**
 * Font düzeltmelerini FTP'ye yükle
 */

$ftpConfig = [
    'host' => 'gtorg.ftp.tools',
    'username' => 'gtorg_nextcode',
    'password' => 'JDH6h9T2zb8UC47t@rn56@',
    'port' => 21
];

echo "Font düzeltmeleri yükleniyor...\n";

$connection = ftp_connect($ftpConfig['host'], $ftpConfig['port']);

if (!$connection) {
    echo "FTP bağlantı hatası!\n";
    exit(1);
}

if (!ftp_login($connection, $ftpConfig['username'], $ftpConfig['password'])) {
    echo "FTP giriş hatası!\n";
    ftp_close($connection);
    exit(1);
}

ftp_pasv($connection, true);

$filesToUpload = [
    'js/font-loader.js' => '/js/font-loader.js',
    'css/inter-font.css' => '/css/inter-font.css'
];

foreach ($filesToUpload as $localFile => $remoteFile) {
    if (ftp_put($connection, $remoteFile, $localFile, FTP_BINARY)) {
        echo "✅ $localFile başarıyla yüklendi!\n";
    } else {
        echo "❌ $localFile yükleme hatası!\n";
    }
}

ftp_close($connection);
echo "İşlem tamamlandı.\n";
?>














































