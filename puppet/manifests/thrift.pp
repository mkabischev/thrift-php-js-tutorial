
include apt
include git
include thrift

Class[apt] -> Class[git] -> Class[thrift]
