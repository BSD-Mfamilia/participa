role :app, %w{capistrano@betaparticipa.masmadrid.org}
role :web, %w{capistrano@betaparticipa.masmadrid.org}
role :db,  %w{capistrano@betaparticipa.masmadrid.org}

set :rvm_ruby_version, '2.4.2'
set :repo_url, 'git@github.com:insomniaproyectos/participa.git'
set :branch, :staging
set :deploy_to, '/var/www/betaparticipa.masmadrid.org'

after 'deploy:publishing', 'deploy:restart'
namespace :deploy do
  task :start do
    on roles(:app) do
      execute "/etc/init.d/unicorn_staging start"
      execute "sudo /etc/init.d/god start"
    end
  end
  task :stop do
    on roles(:app) do
      execute "/etc/init.d/unicorn_staging stop"
      execute "sudo /etc/init.d/god stop"
    end
  end
  task :restart do
    on roles(:app) do
      execute "/etc/init.d/unicorn_staging restart"
      execute "sudo /etc/init.d/god restart"
    end
  end
end
