#!/bin/bash

#Script para poner participa a funcionar

cd ..
bundle exec unicorn -E production -c config/unicorn/production.rb
