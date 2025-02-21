<?php

/**
 * @author Darren Edale
 * @version 0.9.2
 */

use Equit\View;
use Equit\WebApplication;

require_once __DIR__ . "/../../../src/autoload.php";

$app = new WebApplication(__DIR__ . "/..");
$app->setPluginsDirectory("plugins");
View::inject("pageTitle", "Bead test page");

$app->exec();
