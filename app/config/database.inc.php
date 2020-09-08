<?php
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

$entitiesPaths = [dirname(__DIR__) . '/Model/Entity'];
$dbConfig = [
    'driver'   => 'pdo_mysql',
    'user'     => 'root',
    'password' => '',
    'dbname'   => 'cv_generator',
];

//TODO dla false wywali error o klasach proxy -> trzeba ogarnąć
$devMode = true;

$config = Setup::createAnnotationMetadataConfiguration($entitiesPaths, $devMode);
return EntityManager::create($dbConfig, $config);