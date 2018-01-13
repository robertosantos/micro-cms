<?php
use Doctrine\ORM\Tools\Console\ConsoleRunner;
require_once 'doctrine-bootstrap.php';
return ConsoleRunner::createHelperSet($entityManager);
