#!/bin/bash

#Script para construir el proyecto

cd ..
bundle install
cp config/database.yml.example config/database.yml
cp config/secrets.yml.example config/secrets.yml
rake db:migrate
bundle update rails
