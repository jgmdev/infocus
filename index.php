<?php
/**
 * @author Jefferson Gonzalez <jgmdev@gmail.com>
 * @license https://opensource.org/licenses/GPL-3.0
 * @link http://github.com/jgmdev/infocus Source code.
 */

use Slim\Http\Request;
use Slim\Http\Response;

chdir(__DIR__);

// Register application autoloader
require "src/Autoloader.php";
InFocus\Autoloader::register();

// Register composer autoloader
$autoload = 'vendor/autoload.php';
if(!file_exists($autoload))
{
    die('Please install composer from https://getcomposer.org and run composer install');
}
require $autoload;

// Set timezone
$settings = new InFocus\Settings("InFocus");
$timezone = $settings->get("timezone");
if($timezone == "") // Dont query date command if not needed
{
    date_default_timezone_set(exec("date +%Z"));
}
else
{
    date_default_timezone_set($settings->get("timezone"));
}

// Main starting point
$config = [
    'settings' => [
        'displayErrorDetails' => true
    ]
];

$app = new Slim\App($config);

// Routes catch all
$app->add(
    function(Request $request, Response $response, $next)
    {
        $url_parts = parse_url($request->getUri());

        if(preg_match("/^\/image\//", $url_parts["path"]))
        {
            $response = $next($request, $response);
            return $response;
        }

        $controller_loader = new InFocus\Web\ControllerLoader(
            $request, $response
        );

        $controller_loader->default_controller = "overview";

        return $controller_loader->getControllerResponse();
    }
);

// Application image route
$app->get(
    "/image/{name}",
    function(Request $request, Response $response, $args)
    {
        $image_handler = new InFocus\ImageHandler();

        return $image_handler->getResponse($request, $response, $args);
    }
);

// Process request with slim
$app->run();