FROM mysql:8.4.3

COPY ./conf/my.cnf /etc/mysql/my.cnf

RUN chmod 0444 /etc/mysql/my.cnf

RUN mkdir -p /var/run/mysqld && \
    chown mysql:mysql /var/run/mysqld && \
    chmod 700 /var/run/mysqld


