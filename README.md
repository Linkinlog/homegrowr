# homegrowr

Simple interface to manage garden stats

Clone to /var/www/homegrowr

Add the following to /etc/apache2/sites-available/homegrowr.conf

<VirtualHost *:80>
    ServerName homegrowr.io
    ServerAlias www.homegrowr.io
    ServerAdmin webmaster@localhost
    DocumentRoot /var/www/homegrowr/public
    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
<Directory /var/www/homegrowr/public>
    AllowOverride All
</Directory>
