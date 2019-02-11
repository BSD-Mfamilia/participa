#!/bin/bash

#Script para instalar el nginx y poner la

#https://www.digitalocean.com/community/tutorials/how-to-deploy-a-rails-app-with-unicorn-and-nginx-on-ubuntu-14-04

#https://www.hugeserver.com/kb/how-serve-ruby-rails-app-nginx-passenger-ubuntu16/





apt-get install libcurl4-openssl-dev
apt-get install libpcre3 libpcre3-dev

cd ..

gem install passenger

nvmsudo passenger-install-nginx-module




#PS1='${debian_chroot:+($debian_chroot)}\[\033[01;95m\]\u@\h\[\033[00m\]:\[\033[01;34m\]\w\[\033[00m\]\$ '
#PS1='${debian_chroot:+($debian_chroot)}\[\033[01;91m\]\u@\h\[\033[00m\]:\[\033[01;34m\]\w\[\033[00m\]\$ '



http {
      ...
      passenger_root /home/fernando/.rvm/gems/ruby-2.6.0/gems/passenger-6.0.1;
      passenger_ruby /home/fernando/.rvm/gems/ruby-2.6.0/wrappers/ruby;
      ...
  }
