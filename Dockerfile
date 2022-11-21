FROM prestashop/prestashop:1.7.7.8

COPY ssl/ /etc/apache2/sites-available

RUN a2enmod ssl
RUN a2enconf ssl-params
RUN a2ensite default-ssl
RUN service apache2 restart

EXPOSE 80
EXPOSE 443

