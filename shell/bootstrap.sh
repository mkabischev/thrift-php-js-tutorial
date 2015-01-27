#!/usr/bin/env bash

install_puppet_module ()
{
    local MODULE_NAME=$1
    local MODULE_VERSION=$2
    puppet module list --modulepath /etc/puppet/modules | grep " $MODULE_NAME "
    if [ $? -eq 0 ]; then
        echo $MODULE_NAME already installed
    else
        INSTALL_ARGS=
        if [ ! -z $MODULE_VERSION ]; then
            INSTALL_ARGS="--version $MODULE_VERSION"
        fi
        puppet module install $INSTALL_ARGS $MODULE_NAME
    fi
}

install_gem ()
{
    local MODULE_NAME=$1
    gem list $MODULE_NAME | grep -v $MODULE_NAME && gem install --no-rdoc --no-ri $MODULE_NAME
}

install_gem deep_merge

install_puppet_module puppetlabs-apt
install_puppet_module puppetlabs-git
install_puppet_module puppetlabs-vcsrepo

exit 0
