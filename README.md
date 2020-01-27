# Codeigniter Yandex Disk Backup

# Kurulum

Öncelikle Videoyu İzleyerek API Key Oluşturunuz. <br>
https://www.youtube.com/watch?v=t20Ye0A1dRY&t=51s <br>
Videoda bulunan AccesToken kodunuzu almaya yarayan link. <br>
https://tech.yandex.com/disk/webdav/oauth.yandex.ru/authorize?response_type=token&client_id=TOKENID
<br>
Aşağıdaki resimdeki gibi bir **Access_Token** kodu verilecektir bu kodu **libraries** klasörü içerisindeki **$Config** arrayi içerisinde bulunan **YandexKey** içerisine yazınız.
<br>
![alt text](https://i.hizliresim.com/dLQX0L.png)
<br>

+ **application/config/config.php** dosyasından composer_autoload alanını aşağıdaki kod ile değiştiriniz.
```php
$config['composer_autoload'] = APPPATH . 'vendor/autoload.php'; 
```
**Not: Dosya içerisinde config.php mevcuttur.**


+ **application/config/database.php** dosyasından veritabanı ayarlarınızı yapınız.

+ **Backup** klasörünü application dosyasının içerisine atınız.
+ **vendor** klasörünü application dosyasının içerisine atınız. **Aksi takdirde çalışmayacaktır.**
+ **libraries** klasörü içerisinde **yandex.php** dosyası mevcuttur, **libraries** klasörünün içerisine atınız.

# Saat Farkı
Eğer Yandex'e yüklenen dosya veya klasörlerin saatleri farklı ise aşağıda bulunan kodu **libraries** içerisinde yandex.php dosyasının **__construct** içerisine yazınız.
```php
date_default_timezone_set('Europe/Istanbul');
```


# Kullanım

```php
//Kütüphaneyi yüklüyoruz.
$this->load->library('yandex');
//Herhangi bir hata ekrana yansımadıysa dosya başarılı bir biçimde yüklenmiştir.
$this->yandex->YandexBackup();
```


