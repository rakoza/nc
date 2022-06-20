
# 1. Droplet za mysql bazu i redis

> Na droplet se spajam `root` korisnikom sa javnim kljucem

## Dodajmo admin korisnika
```
adduser rakoza
adduser rakoza sudo
```
SSH public key predajem administratoru
```
mv /root/.ssh/ /home/rakoza/
chown -R rakoza.rakoza /home/rakoza/.ssh
```
Ne dozvoljavamo `root` korisniku logovanje preko ssh
```
vi /etc/ssh/sshd_config
PermitRootLogin no
systemctl restart sshd
```

## Azuriramo Ubuntu
`sudo apt update && sudo apt -y upgrade`

## Instaliramo mysql 5.7
> follow instructions https://www.vultr.com/docs/how-to-install-mysql-5-7-on-ubuntu-20-04/

## Instaliramo netstat
`sudo apt install net-tools`

## Bindujemo database servis na privatnu mrezu
`sudo vi /etc/mysql/mysql.conf.d/mysqld.cnf`

```
bind-address        = 10.133.0.4
mysqlx-bind-address = 10.133.0.4
```

`systemctl restart mysql`

```
sudo -uroot mysql
ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password by 'secure_password';
mysql_secure_installation
```

## Kreiramo mysql bazu i korisnika za nopusPro aplikaciju
`sudo mysql -uroot -p mysql`

```
CREATE DATABASE nopus_pro;
CREATE USER IF NOT EXISTS 'nopusproapp'@'10.133.0.0/255.255.0.0' IDENTIFIED BY 'nopus.pro.67';
GRANT ALL ON *.* TO 'nopusproapp'@'10.133.0.0/255.255.0.0' WITH GRANT OPTION;
FLUSH PRIVILEGES;
```

> Napomena: Ovo nece valjati
> `GRANT CREATE,CREATE USER,GRANT OPTION ON *.* TO 'nopusproapp'@'10.133.0.0/255.255.0.0';`
> Mysql user treba kreirati mysql bazu i korisnika za tenanta i potom sve privilegije dati tenant user-u na njegovoj bazi, a to ne moze uraditi ako i sam nema sve privilegije na tenant bazi.

## Instaliramo redis
`sudo apt install redis-server`

## Bindujemo Redis na privatnu adresu
`sudo vi /etc/redis/redis.conf`

Dodamo u konfiguraciju
`bind 10.133.0.4 127.0.0.1 ::1`

I restartujemo redis
`sudo systemctl restart redis`


# 2. Droplet za aplikaciju i docker

> Na droplet se spajam `root` korisnikom sa javnim kljucem

## Dodajmo admin korisnika
```
adduser rakoza
adduser rakoza sudo
```
SSH public key predajem administratoru
```
mv /root/.ssh/ /home/rakoza/
chown -R rakoza.rakoza /home/rakoza/.ssh
```
Ne dozvoljavamo `root` korisniku logovanje preko ssh
```
vi /etc/ssh/sshd_config
PermitRootLogin no
systemctl restart sshd
```

## Dodajmo admin korisniku posebne prvilegije za Linux komande `adduser` i `chown`

`sudo vi /etc/sudoers.d/rakoza`

Add line: `rakoza       ALL = NOPASSWD: /usr/sbin/useradd, /usr/bin/chown`
> Poslije rakoza ide tab, a ne space. Bez ovoga, konzolna komanda za kreiranje docker container-a nece se izvrsiti, jer sadrzi poziv `sudo` komande koji ocekuje unos admin lozinke.

## Azuriramo Ubuntu
`sudo apt update && sudo apt -y upgrade`

## Instaliramo netstat
`sudo apt install net-tools`

## Instaliramo apache2 i php8

https://computingforgeeks.com/how-to-install-php-on-ubuntu-linux-system/

```
sudo apt update && sudo apt -y upgrade
sudo apt update
sudo apt install -y apache2
sudo apt install lsb-release ca-certificates apt-transport-https software-properties-common -y
sudo add-apt-repository ppa:ondrej/php
sudo apt update
sudo apt install php8.1
php --version
```

## Instalacija extenzija

> Primjer: `sudo apt install php8.1-<extension>`
```
sudo apt install php8.1-{bcmath,xml,fpm,mysql,zip,intl,ldap,gd,cli,bz2,curl,mbstring,pgsql,opcache,soap,cgi}
```

## Podesavamo apache nalog

> Podesavamo apache da se izvrsava pod admin nalogom zato sto ce morati izvrsavati neke admin komande, laravel console commands

`sudo vi /etc/apache2/envvars`
```
#export APACHE_RUN_USER=www-data
#export APACHE_RUN_GROUP=www-data
export APACHE_RUN_USER=rakoza
export APACHE_RUN_GROUP=rakoza
```
`sudo chown -R rakoza.rakoza /var/www`

## Omoguci mod_rewrite modul
`sudo a2enmod rewrite`

## Install php composer
```
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('sha384', 'composer-setup.php') === '55ce33d7678c5a611085589f1f3ddf8b3c52d662cd01d4ba75c0ee0459970c2200a51f492d557530c71c15d8dba01eae') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php
php -r "unlink('composer-setup.php');"
```
`sudo mv composer.phar /usr/local/bin/composer`

## Install git
Git dolazi preinstaliran na Ubuntu dropletu

## Generisi par ssh kljuceva za GitHub
```
ssh-keygen -t ed25519 -C "predrag.rakonjac@gmail.com"
ssh-add ~/.ssh/id_ed25519
```

## Kopiraj pub key u GitHub deply keys i kloniraj repozitorij
```
cat ~/.ssh/id_ed25519.pub
git clone https://github.com/rakoza/nc.git
```

## Instaliraj Laravel biblioteke
```
mv ./nc ./nopus.pro
cd nopus.pro
composer install
cp ./.env.example ./.env
php artisan key:generate
```

Azuriraj .env konfiguracionim podacima

## Podesimo apache da slusa na portu 8888

> Zakomentarisemo port 80 jer ce smetati nginx-proxy docker instanci

`sudo vi /etc/apache2/ports.conf`
`Listen 8888`

## Dodajemo virtual host
`sudo vi /etc/apache2/sites-available/nopus.pro.conf`
```
<VirtualHost *:8888>
        # The ServerName directive sets the request scheme, hostname and port that
        # the server uses to identify itself. This is used when creating
        # redirection URLs. In the context of virtual hosts, the ServerName
        # specifies what hostname must appear in the request's Host: header to
        # match this virtual host. For the default virtual host (this file) this
        # value is not decisive as it is used as a last resort host regardless.
        # However, you must set it for any further virtual host explicitly.
        ServerName nopus.pro
        ServerAlias www.nopus.pro

        ServerAdmin predrag.rakonjac@gmail.com

        DocumentRoot /var/www/nopus.pro/public

        # In Apache 2.4, Order Allow,Deny has been replaced by Require all granted.
        <Directory /var/www/nopus.pro/public>
                Options Indexes FollowSymLinks MultiViews
                AllowOverride All
                Require all granted
        </Directory>

        # Available loglevels: trace8, ..., trace1, debug, info, notice, warn,
        # error, crit, alert, emerg.
        # It is also possible to configure the loglevel for particular
        # modules, e.g.
        #LogLevel info ssl:warn

        ErrorLog ${APACHE_LOG_DIR}/error.log
        CustomLog ${APACHE_LOG_DIR}/access.log combined

        # For most configuration files from conf-available/, which are
        # enabled or disabled at a global level, it is possible to
        # include a line for only one particular virtual host. For example the
        # following line enables the CGI configuration for this host only
        # after it has been globally disabled with "a2disconf".
        #Include conf-available/serve-cgi-bin.conf
</VirtualHost>
```

## Aktiviramo virtual host
`sudo a2ensite nopus.pro`

## Pokrenemo migracije i seeder-e
`php artisan migrate --seed`

# Pripremamo okruzenje za docker
```
cd /mnt/volume_ams3_02
sudo git clone https://github.com/rakoza/docker-laravel.git
sudo chown -R rakoza.rakoza ./docker-laravel
```

## Dodamo putanju u Laravel .env file
`/mnt/volume_ams3_02/docker-laravel`

## Dodamo aplikaciju u docker-laravel/apps direktorijum
```
cd /mnt/volume_ams3_02/docker-laravel
mkdir apps
cd apps
git clone git@bitbucket.org:mirko_igumnovic/hr.git
composer install --ignore-platform-reqs
```

## Instaliramo docker servis
https://docs.docker.com/engine/install/ubuntu/#install-using-the-convenience-script
```
cd ~
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh
sudo apt-get install docker-compose
```

## To run the Docker daemon as a fully privileged service
https://docs.docker.com/engine/install/linux-postinstall/#manage-docker-as-a-non-root-user
```
sudo groupadd docker
sudo usermod -aG docker $USER
```

## Pull nginx-proxy:alpine
```
docker pull nginxproxy/nginx-proxy:alpine
docker network create nginx-proxy-net
cd /mnt/volume_ams3_02/docker-laravel/nginx-proxy
docker-compose up
```

## Configure Docker to start on boot
```
sudo systemctl enable docker.service
sudo systemctl enable containerd.service
```
