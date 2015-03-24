# -*- mode: ruby -*-
# vi: set ft=ruby :

VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|

    config.vm.box = "ubuntu/trusty64"

    config.vm.provider "virtualbox" do |vb|
        vb.customize ["modifyvm", :id, "--memory", "1280"]
    end

    config.vm.provision :file, :source => "puppet/hiera", :destination => "/tmp"

    config.vm.provision :shell, :path => "shell/bootstrap.sh"

    config.vm.provision "vagrant", type: "puppet" do |puppet|
        puppet.manifests_path = "puppet/manifests"
        puppet.module_path = "puppet/modules"
        puppet.hiera_config_path = "puppet/hiera.yaml"
    end

    config.vm.define "thrift", primary: true do |thrift|
        thrift.vm.network "private_network", ip: "10.0.1.2"
        thrift.vm.provision "vagrant", type: "puppet" do |puppet|
            puppet.manifest_file = "thrift.pp"
            puppet.options = "--environment dev"
        end
    end

end
