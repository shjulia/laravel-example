<?php
namespace Deployer;

require 'recipe/laravel.php';

// Project name
set('application', 'Boon');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true); 

// Shared files/dirs between deploys 
add('shared_files', []);

add('shared_dirs', [
    'public/build',
    'public/.well-known',
    'safari.push',
]);

// Hosts
host('nextondeck.doingboon.com')
    ->stage('dev')
    ->set('deploy_path', '/var/www/dev')
    ->set('repository', 'git@github.com:RyanVet/boon-dev.git')
    ->set('branch', 'dev')
    ->user('root');

host('app.doingboon.com')
    ->stage('live')
    ->set('deploy_path', '/var/www/html')
    ->set('repository', 'git@github.com:RyanVet/boon-live.git')
    ->set('branch', 'master')
    ->user('root');
    
// Tasks
desc('Execute npm install');
task('npm:install', function () {
    run('cd {{release_path}} &&  npm install');
});

desc('Execute npm run prod');
task('npm:prod', function () {
    run('cd {{release_path}} && npm run prod');
});

desc('Execute artisan config:clear');
task('artisan:config:cache', function () {
    run('{{bin/php}} {{release_path}}/artisan config:clear');
});

task('restart:queue-worker', function () {
    run('supervisorctl restart laravel-queue-worker:*');
});

task('restart:cron-worker', function () {
    run('supervisorctl restart cron-worker:*');
});

task('restart:redis', function () {
    run('/etc/init.d/redis-server restart');
});

task('phpmyadmin:link', function () {
    run('sudo ln -s /usr/share/phpmyadmin {{deploy_path}}/current/public');
});

desc('Deploy your project');
task('deploy', [
    'deploy:info',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:vendors',
    'deploy:writable',
    'deploy:symlink',
    'phpmyadmin:link',
    'artisan:migrate',
    'npm:install',
    'npm:prod',
    'artisan:cache:clear',
    'artisan:config:cache',
    'artisan:queue:restart',
    'restart:queue-worker',
    'restart:cron-worker',
    'restart:redis',
    'deploy:unlock',
    'cleanup',
    'success'
]);

desc('Deploy your project without building assets');
task('deploy-no-assets', [
    'deploy:info',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:vendors',
    'deploy:writable',
    'deploy:symlink',
    'phpmyadmin:link',
    'artisan:migrate',
    'artisan:cache:clear',
    'artisan:config:cache',
    'artisan:queue:restart',
    'restart:queue-worker',
    'restart:cron-worker',
    'restart:redis',
    'deploy:unlock',
    'cleanup',
    'success'
]);

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

