FROM mysql:5

MAINTAINER Emanuela Davelli <emanuela.davelli@sottosviluppo.com>

RUN echo "sql_mode = \"STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION\"" >> /etc/mysql/mysql.conf.d/mysqld.cnf
RUN echo "max_allowed_packet=32M" >> /etc/mysql/mysql.conf.d/mysqld.cnf

EXPOSE 3306

