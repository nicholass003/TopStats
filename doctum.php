<?php

use Doctum\Doctum;
use Symfony\Component\Finder\Finder;

require __DIR__ . '/vendor/autoload.php';

$iterator = Finder::create()
    ->files()
    ->in(__DIR__ . '/src');

return new Doctum($iterator, [
    'title' => 'TopStats API Documentation',
    'build_dir' => __DIR__ . '/build/api',
    'cache_dir' => __DIR__ . '/build/cache',
]);