<?php

$servers = [
    'staging' => [
        'servers' => ['staging' => 'user@ip -p 22007'],
        'directory' => '/path/to/deployment/directory',
        'url' => 'https://xxx',
    ],
    'production' => [
        'servers' => ['production' => 'user@ip -p 22007'],
        'directory' => '/path/to/deployment/directory',
        'url' => 'https://xxx',
    ],
];

$repository = 'git@gitlab.com:chrismou/envoy-craft3';

$maxDeploymentsToKeep = 10;

$opCache = true;
$litespeed = false;
$projectConfig = false;
