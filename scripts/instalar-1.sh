#!/bin/bash

#Script para instalar los requerimientos de participa
#https://github.com/insomniaproyectos/participa


#Actualización del sistema:
sudo apt-get update
sudo apt-get upgrade -y


#Instalación de dependencias:
sudo apt-get install dirmngr -y
sudo apt-get install curl -y
sudo apt-get install libicu52 -y                       # para manejar cadenas Unicode correctamente
sudo apt-get install postgres -y                       # para la base de datos, o mysql-server si lo prefieres
sudo apt-get install imagemagick -y                    # para la generación del captcha
sudo apt-get install redis-server -y                   # para la gestión de las colas de trabajo (resque)
sudo apt-get install libpq-dev -y                      # para la gema pg
sudo apt-get install qt5-default libqt5webkit5-dev -y  # para capybara (tests)
sudo apt-get install wkhtmltopdf -y                    # para generación de PDFs (microcreditos)


#Instalación de rvm para control de gemas:
#https://www.digitalocean.com/community/tutorials/how-to-install-ruby-on-rails-with-rvm-on-ubuntu-18-04
sudo apt install gnupg2
gpg2 --keyserver hkp://keys.gnupg.net --recv-keys 409B6B1796C275462A1703113804BB82D39DC0E3 7D2BAF1CF37B13E2069D6956105BD0E739499BDB
cd /tmp
curl -sSL https://get.rvm.io -o rvm.sh
cat /tmp/rvm.sh | bash -s stable --rails
echo "export PATH=\"$PATH:$HOME/.rvm/bin\"" >> ~/.bashrc
echo "source $HOME/.rvm/scripts/rvm" >> ~/.bashrc


#Reiniciamos el bashrc para que pille los cambios.
cd
. ./.bashrc
#Pero parece que desde el script no funciona




