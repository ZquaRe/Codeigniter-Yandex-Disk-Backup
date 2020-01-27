<?php
/*
 _______   __   __  ___  __  ___      ___   .___________.                                                                                                                                                                                                                                                                                                                                                                                            
|       \ |  | |  |/  / |  |/  /     /   \  |           |                                                                                                                                                                                                                                                                                                                                                                                            
|  .--.  ||  | |  '  /  |  '  /     /  ^  \ `---|  |----`                                                                                                                                                                                                                                                                                                                                                                                            
|  |  |  ||  | |    <   |    <     /  /_\  \    |  |                                                                                                                                                                                                                                                                                                                                                                                                 
|  '--'  ||  | |  .  \  |  .  \   /  _____  \   |  |                                                                                                                                                                                                                                                                                                                                                                                                 
|_______/ |__| |__|\__\ |__|\__\ /__/     \__\  |__|                                                                                                                                                                                                                                                                                                                                                                                                 
Öncelikle Videoyu İzliyerek API Key Oluşturunuz.
https://www.youtube.com/watch?v=t20Ye0A1dRY&t=51s
https://tech.yandex.com/disk/webdav/oauth.yandex.ru/authorize?response_type=token&client_id=TOKENID
Tayfun Erbilen'in BackuPhp kütüphanesi kullanılmıştır,
Vendor içerisinde Backup Klasöründedir. Autload.php'e ek olarak required edilerek eklenmiştir.
Eğer kendi Vendor dosyanız var ise ayrıca BackuPhp kütüphanesini projenize dahil ediniz.
Bu php dosyasının bulunduğu yere Backup adında bir dosya oluşturunuz.
Localde Çalışıyor, SSL Lazım Sadece.
*/


defined('BASEPATH') OR exit('No direct script access allowed');
use Yandex\Disk\DiskClient;
class yandex {
    private $config = [];
    public function __construct() {

        ini_set('memory_limit', '-1');
        $this->CI = $CI = & get_instance();
        $this->CI->load->database();
    }
    public function YandexBackup() {
        $Config = array(
        'BackupName'    => 'Backup',
        'YandexKey'     => 'ACCESS_TOKEN',
        'dbHost'        => $this->CI->db->hostname, 
        'dbUserName'    => $this->CI->db->username, 
        'dbPassword'    => $this->CI->db->password, 
        'dbDatabase'    => $this->CI->db->database
        );

        $aylar = array('Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık');
        $ay = $aylar[date('m') - 1];
        $diskClient = new DiskClient($Config['YandexKey']);
        $diskClient->setServiceScheme(DiskClient::HTTPS_SCHEME);
        //Hangi kullanıcı adı ile giriş yapıldığını görüntülüyebilirsiniz.
        $login = $diskClient->getLogin();
        //echo $login . '<br>';

        //Yandex hesabının kullanıcı ID'si
        $YandexUserID = explode('login:', $login);
        //Yandex hesabının kullanıcı adı
        $YandexUserName = explode('fio:', $YandexUserID[1]);
        //echo $YandexUserID[0];
        //echo '<br>';
        //echo $YandexUserName[0];


        //Eğer Backup klasörü içerisinde bugün tarihli klasör yok ise aç.
        $dirContent = $diskClient->directoryContents('/');
        foreach ($dirContent as $dirItem) {
            if ($dirItem['resourceType'] === 'dir') {
                if ($dirItem['displayName'] == $Config['BackupName']) {
                    $BackupContent = $diskClient->directoryContents('/' . $Config['BackupName'] . '/');
                    foreach ($BackupContent as $Okay) {
                        if ($Okay['resourceType'] === 'dir') {
                            if ($Okay['displayName'] == date('j ') . $ay . date(' Y')) {
                                $YandexBackupFileControl = true;
                            } else {
                                $YandexBackupFileControl = false;
                            }
                        }
                    }
                }
            }
        }

        //Hataya düşmemesi için.
        if ($YandexBackupFileControl == false) {
            $diskClient->createDirectory('/' . $Config['BackupName'] . '/' . date('j ') . $ay . date(' Y'));
        }

        //Bugün tarihli klasörün içine aktarma işlemi
        $uniqid = APPPATH . '/Backup/' . uniqid(uniqid(uniqid(strtotime('now')))) . '.sql';
        $backup = new Backup();
        $BackupName = date(' H:i:s') . ' - ' . date('j ') . $ay . date(' Y') . '.sql';

        // Mysql yedeği almak için
        $mysqlBackup = $backup->mysql(['host' => $Config['dbHost'], 'user' => $Config['dbUserName'], 'pass' => $Config['dbPassword'], 'dbname' => $Config['dbDatabase'], 'file' => $uniqid]);
        if ($mysqlBackup) {
            $diskClient->uploadFile('/' . $Config['BackupName'] . '/' . date('j ') . $ay . date(' Y') . '/', array('path' => $uniqid, 'size' => filesize($uniqid), 'name' => $BackupName));
            if ($diskClient) {
                unlink($uniqid);
            }
        }
    }
}
?> 