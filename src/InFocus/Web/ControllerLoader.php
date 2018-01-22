<?php
/**
 * @author Jefferson Gonzalez <jgmdev@gmail.com>
 * @license https://opensource.org/licenses/GPL-3.0
 * @link http://github.com/jgmdev/infocus Source code.
 */

namespace InFocus\Web;

use \Slim\Http\Request;
use \Slim\Http\Response;

class ControllerLoader
{
    /**
     * @var \Slim\Http\Request
     */
    public $request;

    /**
     * @var \Slim\Http\Response
     */
    public $response;

    /**
     * Default controller used when accessing the basePath.
     * @var string
     */
    public $default_controller;

    /**
     * Controller loaded when no page was found.
     * @var string
     */
    public $not_found_controller;

    /**
     * Path where the controllers are stored.
     * @var string
     */
    public $controllers_path;

    /**
     * Constructor.
     * @param string $url The url of currently viewed page.
     * @param string $templates_path Path where template files reside.
     */
    public function __construct(
        Request $request,
        Response $response,
        string $controllers_path="controllers"
    )
    {
        $this->request = $request;
        $this->response = $response;

        if(!is_dir($controllers_path))
            throw new \ErrorException(
                "The controllers directory does not exists."
            );

        $this->controllers_path = rtrim($controllers_path, "/");

        $this->default_controller = "default";

        $this->not_found_controller = "not-found";

        return $this;
    }

    /**
     * Gets the response of the controller that matched the requested uri.
     * @return \Slim\Http\Response
     */
    public function getControllerResponse():\Slim\Http\Response
    {
        $path = $this->request->getServerParams()["PATH_INFO"];

        $settings = new \InFocus\Settings("InFocus");
        if(!$settings->get("timezone") && $path != "/preferences")
        {
            return $this->response->withRedirect("/preferences");
        }
        elseif(
            $settings->get("timezone") &&
            !$settings->get("setup") &&
            $path != "/overview"
        )
        {
            return $this->response->withRedirect("/overview");
        }

        $request = $this->request;
        $response = $this->response;

        if($path == "" || $path == "/")
        {
            include $this->controllers_path
                . "/"
                . $this->default_controller
                . ".php"
            ;

            /* @var $controller \InFocus\Web\Controller */
            return $controller->processResponse();
        }
        elseif(
            file_exists(
                $this->controllers_path
                . "/"
                . $path
                . ".php"
            )
        )
        {
            include $this->controllers_path
                . "/"
                . $path
                . ".php"
            ;

            /* @var $controller \InFocus\Web\Controller */
            return $controller->processResponse();
        }

        if($this->not_found_controller)
        {
            if(
                file_exists(
                    $this->controllers_path
                    . "/"
                    . $this->not_found_controller
                    . ".php"
                )
            )
            {
                include $this->controllers_path
                    . "/"
                    . $this->not_found_controller
                    . ".php"
                ;

                /* @var $controller \InFocus\Web\Controller */
                return $controller->processResponse();
            }
        }

        $not_found = new \Slim\Handlers\NotFound;

        return $not_found($this->request, $this->response);
    }

}