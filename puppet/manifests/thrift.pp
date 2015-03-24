
include apt
include git
include thrift
include mysql::server

Class[apt] -> Class[git] -> Class[thrift]

mysql::db { 'test':
    user => 'root',
    password => hiera('mysql::server::root_password'),
    host => '%'
}
