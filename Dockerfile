FROM prestashop/prestashop:1.7.7.8


COPY ssl/ /etc/apache2/sites-available

RUN a2enmod ssl
RUN service apache2 restart

EXPOSE 443

