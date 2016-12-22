DELETE FROM mysql.user WHERE user='api_user';
grant all on api_server.* to api_user@'%' identified by 'api_user';
grant all on api_server.* to api_user@localhost identified by 'api_user';

flush privileges;
