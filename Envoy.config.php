<?php

$servers = [
    'staging' => [
        'servers' => ['staging' => 'user@ip -p 22007'],
        'directory' => '/path/to/deployment/directory',
    ],
    'production' => [
        'servers' => ['production' => 'user@ip -p 22007'],
        'directory' => '/path/to/deployment/directory',
    ],
];

$repository = 'git@gitlab.com:chrismou/envoy-craft3';

$maxDeploymentsToKeep = 10;

$litespeed = false;
$projectConfig = false;
