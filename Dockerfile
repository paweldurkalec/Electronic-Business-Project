FROM mariadb:10.8.2

ADD db/be_181841_dump.sql /docker-entrypoint-initdb.d
