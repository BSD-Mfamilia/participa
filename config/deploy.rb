# config valid only for Capistrano 3.4.0
lock '3.10.2'

set :application, 'participa.masmadrid.org'
set :repo_url, 'git@github.com:insomniaproyectos/participa.git'
set :linked_files, %w{config/database.yml config/secrets.yml}
set :linked_dirs, %w{log tmp/pids tmp/cache tmp/sockets vendor/bundle public/system non-public/system db/masmadrid}
