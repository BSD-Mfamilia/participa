#!/bin/bash

#Script para instalar los requerimientos de participa (Parte 2)
#https://github.com/insomniaproyectos/participa

#Instalamos el Ruby on Rails:
rvm install 2.4.2
rvm use 2.4.2
rvm use 2.4.2
gem search '^rails$' --all
gem install rails -v 5.2.2
gem install mailcatcher # para probar en desarrollo
