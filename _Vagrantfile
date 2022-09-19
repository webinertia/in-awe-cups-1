# -*- mode: ruby -*-
# vi: set ft=ruby :

VAGRANTFILE_API_VERSION = '2'

$install_software = <<SCRIPT
export DEBIAN_FRONTEND=noninteractive
apt-get -yq update
# INSTALL MySQL
debconf-set-selections <<< "mysql-server mysql-server/root_password password Password123"
debconf-set-selections <<< "mysql-server mysql-server/root_password_again password Password123"
apt-get -yq install mysql-server
# Allow external connections to MySQL as root (with password Password123)
sed -i 's/127\.0\.0\.1/0\.0\.0\.0/g' /etc/mysql/mysql.conf.d/mysqld.cnf
mysql -u root -pPassword123 -e 'USE mysql; UPDATE `user` SET `Host`="%" WHERE `User`="root" AND `Host`="localhost"; DELETE FROM `user` WHERE `Host` != "%" AND `User`="root"; FLUSH PRIVILEGES;'
mysql -h localhost -u root -pPassword123 -e "ALTER USER 'root'@'%' IDENTIFIED WITH mysql_native_password BY 'Password123';"
service mysql restart
SCRIPT

$setup_vagrant_user_environment = <<SCRIPT
if ! grep "cd /vagrant" /home/vagrant/.profile > /dev/null; then
  echo "cd /vagrant" >> /home/vagrant/.profile
fi
SCRIPT

Vagrant.configure(2) do |config|
  config.vm.box = 'bento/ubuntu-20.04'
  config.vm.provider "virtualbox" do |v|
    v.memory = 4096
    v.cpus = 2
  end

  config.vm.network "private_network", ip: "192.168.20.20"

  config.vm.provision 'shell', inline: $install_software
  config.vm.provision 'shell', privileged: false, inline: '/vagrant/.ci/mysql_fixtures.sh'
  config.vm.provision 'shell', inline: $setup_vagrant_user_environment
end
