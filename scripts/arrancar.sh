#!/bin/bash

#Script para poner participa a funcionar

cd ..
#mailcatcher # para probar en desarrollo
rails server --binding=0.0.0.0 -p 8080
