<?php

if (file_exists(dirname(__DIR__).'/var/cache/prod/App_KernelProdContainer.preload.php')) {
    opcache_compile_file(dirname(__DIR__) . '/var/cache/prod/App_KernelProdContainer.preload.php');
}
