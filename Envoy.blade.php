{{-- craft3 deployment --}}

<?php
// Include the config
include 'Envoy.config.php';

// Check a couple of required params have been passed
if (!isset($commit) || !$commit) {
    throw new Exception('Commit hash has not been provided');
}
if (!isset($site) || !isset($servers[$site])) {
    throw new Exception('Config for site ' . $site . ' not found');
}

$server = $servers[$site]['servers'];
$appDir = $servers[$site]['directory'];

?>

{{-- Set the servers for this deployment (specified in your Envoy.config.php --}}
@servers($server)

{{-- Set the paths the deployment will use --}}
@setup
    $releasesDir = $appDir . '/releases';
    $sharedDir = $appDir . '/shared';
    $deploymentDirectory = $releasesDir .'/'. $commit;
@endsetup

{{-- Specify which tasks will run --}}
@story('deploy')
    clone_repository
    update_symlinks
    post_deployment
@endstory

{{-- Clone the repository and set up required packages --}}
@task('clone_repository')
    echo "Checking app directory exists for this site ({{ $appDir }})"
    mkdir -p {{ $appDir }}

    echo "Cloning repository"
    mkdir -p {{ $releasesDir }}
    git clone {{ $repository }} {{ $deploymentDirectory }}

    echo 'Reset to deployed commit: {{ $commit }}'
    cd {{ $deploymentDirectory }}
    git reset --hard {{ $commit }}

    echo 'Installing packages'
    cd {{ $deploymentDirectory }}
    composer install
@endtask

{{-- Link shared directories and switch the symlinks --}}
@task('update_symlinks')
    echo "Checking shared directory exists"
    mkdir -p {{ $sharedDir }}

    echo "Linking storage directory"
    mkdir -p  {{ $sharedDir }}/storage
    rm -rf {{ $deploymentDirectory }}/storage
    ln -nfs {{ $sharedDir }}/storage {{ $deploymentDirectory }}/storage

    echo "Linking uploads directory"
    mkdir -p  {{ $sharedDir }}/uploads
    rm -rf {{ $deploymentDirectory }}/web/uploads
    ln -nfs {{ $sharedDir }}/uploads {{ $deploymentDirectory }}/web/uploads

    echo "Linking .htaccess file"
    touch {{ $sharedDir }}/.htaccess
    ln -nfs {{ $sharedDir }}/.htaccess {{ $deploymentDirectory }}/web/.htaccess

    echo "Linking .env file"
    touch {{ $sharedDir }}/.env
    ln -nfs {{ $sharedDir }}/.env {{ $deploymentDirectory }}/.env

    echo "Switch the site to the new release"
    ln -nfs {{ $deploymentDirectory }} {{ $appDir }}/current
@endtask

{{-- Any post deployment tasks. Add more or remove whatever you don't need --}}
@task('post_deployment')
    echo "Run craft migrations, in case of a version jump"
    {{ $deploymentDirectory }}/craft migrate/all

    @if (isset($projectConfig) and $projectConfig)
        echo "Sync project.yml"
        {{ $deploymentDirectory }}/craft project-config/sync
    @endif

    @if (isset($litespeed) and $litespeed)
        echo "Clear litespeed cache"
        rm -rf {{ $appDir }}/../.lscache/*
    @endif

    @if (isset($maxDeploymentsToKeep))
        echo "Delete all but the most recent {{ $maxDeploymentsToKeep }} deployments"
        cd {{ $releasesDir  }} && rm -rf `ls -t | awk 'NR>{{ $maxDeploymentsToKeep }}'`
    @endif
@endtask