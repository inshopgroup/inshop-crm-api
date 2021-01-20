<?php

use Symfony\Component\Dotenv\Dotenv;

echo "Resetting test database...";
passthru(
    sprintf(
        'php "%s/../bin/console" doctrine:database:drop --env=test --force',
        __DIR__
    )
);
passthru(
    sprintf(
        'php "%s/../bin/console" doctrine:database:create --env=test',
        __DIR__
    )
);
passthru(
    sprintf(
        'php "%s/../bin/console" doctrine:schema:update --env=test --force',
        __DIR__
    )
);
passthru(
    sprintf(
        'php "%s/../bin/console" doctrine:migrations:execute "App\Migrations\Version20180624121015" --up --env=test --no-interaction',
        __DIR__
    )
);
passthru(
    sprintf(
        'php "%s/../bin/console" doctrine:migrations:execute "App\Migrations\Version20180719203044" --up --env=test --no-interaction',
        __DIR__
    )
);
passthru(
    sprintf(
        'php "%s/../bin/console" doctrine:migrations:execute "App\Migrations\Version20180729170556" --up --env=test --no-interaction',
        __DIR__
    )
);
passthru(
    sprintf(
        'php "%s/../bin/console" doctrine:migrations:execute "App\Migrations\Version20180826150207" --up --env=test --no-interaction',
        __DIR__
    )
);
passthru(
    sprintf(
        'php "%s/../bin/console" doctrine:fixtures:load --env=test --append',
        __DIR__
    )
);
echo " Done" . PHP_EOL . PHP_EOL;

require dirname(__DIR__).'/vendor/autoload.php';

if (file_exists(dirname(__DIR__).'/config/bootstrap.php')) {
    require dirname(__DIR__).'/config/bootstrap.php';
} elseif (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}
