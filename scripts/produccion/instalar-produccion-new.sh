#!/bin/bash

#Script para instalar el nginx y poner la
#https://www.hugeserver.com/kb/how-serve-ruby-rails-app-nginx-passenger-ubuntu16/



sudo apt-key adv --keyserver keyserver.ubuntu.com --recv-keys 561F9B9CAC40B2F7

sudo touch /etc/apt/sources.list.d/passenger.list

sudo echo "deb https://oss-binaries.phusionpassenger.com/apt/passenger xenial main" > /etc/apt/sources.list.d/passenger.list

sudo apt-get update && apt-get install passenger nginx-extras nginx

systemctl start nginx

systemctl enable nginx


#Set up Nginx and Passenger
