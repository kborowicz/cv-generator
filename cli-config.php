<?php
use Doctrine\ORM\Tools\Console\ConsoleRunner;

return ConsoleRunner::createHelperSet(require __DIR__ . '/app/config/database.inc.php');