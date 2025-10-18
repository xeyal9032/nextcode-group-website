<?php
/**
 * API dosyasını FTP'ye yükle
 */

$ftpConfig = [
    'host' => 'gtorg.ftp.tools',
    'username' => 'gtorg_nextcode',
    'password' => 'JDH6h9T2zb8UC47t@rn56@',
    'port' => 21
];

echo "API dosyası yükleniyor...\n";

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

$localFile = 'api/contact-info.php';
$remoteFile = '/api/contact-info.php';

if (ftp_put($connection, $remoteFile, $localFile, FTP_BINARY)) {
    echo "✅ contact-info.php başarıyla yüklendi!\n";
} else {
    echo "❌ contact-info.php yükleme hatası!\n";
}

ftp_close($connection);
echo "İşlem tamamlandı.\n";
?>














































