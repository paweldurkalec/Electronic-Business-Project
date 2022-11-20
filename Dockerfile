FROM prestashop/prestashop:1.7.7.8

RUN rm -rf *

COPY webshop/ ./

RUN chown -R www-data:root ./

RUN service apache2 restart

EXPOSE 80
