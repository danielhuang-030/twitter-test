@servers(['web' => 'ubuntu@34.214.215.230'])

@setup
    $repository = 'git@github.com:danielhuang-030/twitter-test.git';
    $releases_dir = '/var/www/tt/releases';
    $app_dir = '/var/www/tt';
    $release = date('YmdHis');
    $new_release_dir = $releases_dir .'/'. $release;
@endsetup

@story('deploy')
    clone_repository
    run_composer
    update_symlinks
    clear_cache
    run_migration
    clean_old_releases
@endstory

@task('clone_repository')
    echo 'Cloning repository'
    [ -d {{ $releases_dir }} ] || mkdir {{ $releases_dir }}
    git clone --depth 1 {{ $repository }} {{ $new_release_dir }}
    cd {{ $new_release_dir }}
    git reset --hard {{ $commit }}
@endtask

@task('run_composer')
    echo "Starting deployment ({{ $release }})"
    cd {{ $new_release_dir }}
    composer install --prefer-dist --no-scripts -q -o
@endtask

@task('update_symlinks')
    echo "Linking storage directory"
    rm -rf {{ $new_release_dir }}/storage
    ln -nfs {{ $app_dir }}/storage {{ $new_release_dir }}/storage

    echo 'Linking .env file'
    ln -nfs {{ $app_dir }}/.env {{ $new_release_dir }}/.env

    echo 'Linking current release'
    ln -nfs {{ $new_release_dir }} {{ $app_dir }}/current
@endtask

@task('clear_cache')
    echo "Clear cache"
    cd {{ $new_release_dir }}
    php artisan cache:clear
    php artisan config:cache
    php artisan route:cache
@endtask

@task('run_migration')
    echo "Migration"
    cd {{ $new_release_dir }}
    php artisan migrate --force
@endtask

@task('clean_old_releases')
    echo "Cleaning old releases (keep latest 5)"
    cd {{ $releases_dir }}
    ls -dt {{ $releases_dir }}/* | tail -n +6 | xargs -d "\n" rm -rf;
@endtask
