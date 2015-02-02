class thrift (
    $version = $thrift::params::version,
    $pkgs = $thrift::params::pkgs
) inherits thrift::params {

    validate_re($version, '^\d+\.\d+\.\d+$')
    validate_array($pkgs)

    $src_dir = "/usr/local/src/thrift-${version}"

    package { $pkgs:
        ensure => installed,
        before => Anchor['thrift-compile-start']
    }

    vcsrepo { 'clone-thrift':
        path => $src_dir,
        ensure => present,
        provider => git,
        source => 'https://github.com/apache/thrift.git',
        revision => $version,
        before => Anchor['thrift-compile-start']
    }

    anchor { 'thrift-compile-start': }
    anchor { 'thrift-compile-end': }

    Exec {
        cwd => $src_dir,
        path => '/usr/local/sbin:/usr/local/bin:/sbin:/bin:/usr/sbin:/usr/bin',
        provider => shell,
        unless => 'test -x /usr/local/bin/thrift',
        timeout => 1200
    }

    exec {
        'thrift-bootstrap': command => './bootstrap.sh';
        'thrift-configure': command => './configure --without-python --disable-tests';
        'thrift-make': command => 'make';
        'thrift-install': command => 'make install && make clean';
    }

    Anchor['thrift-compile-start']->
        Exec['thrift-bootstrap']->
        Exec['thrift-configure']->
        Exec['thrift-make']->
        Exec['thrift-install']->
    Anchor['thrift-compile-end']
}
