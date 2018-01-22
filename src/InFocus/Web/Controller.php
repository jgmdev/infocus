<?php
/**
 * @author Jefferson Gonzalez <jgmdev@gmail.com>
 * @license https://opensource.org/licenses/GPL-3.0
 * @link http://github.com/jgmdev/infocus Source code.
 */

namespace InFocus\Web;

use \Slim\Http\Request;
use \Slim\Http\Response;

class Controller
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
     * @var \InFocus\Web\View
     */
    public $view;

    /**
     * @var string
     */
    public $content_view;

    /**
     * Signature of callback: function(Controller):Response
     * @var callable
     */
    public $onGetRequest;

    /**
     * Signature of callback: function(Controller):Response
     * @var callable
     */
    public $onPostRequest;

    /**
     * Signature of callback: function(Controller):Response
     * @var callable
     */
    public $onDeleteRequest;

    /**
     * Constructor.
     * @param \Slim\Http\Request $request
     * @param \Slim\Http\Response $response
     */
    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;

        $this->view = new View($request, "views");

        $this->content_view = "";

        $this->view->addMenu(
            "primary",
            array(
                "Overview" => "",
                "Applications" => "applications",
                "Activities" => "activities",
                "inactivity" => "inactivity",
                "Preferences" => "preferences"
            )
        );

        $this->view->setApplicationName("InFocus");

        $this->onGetRequest = null;
        $this->onPostRequest = null;
        $this->onDeleteRequest = null;

        return $this;
    }

    /**
     * Process the response using the various user defined callbacks.
     * on onProcessResponse().
     * @return \Slim\Http\Response
     */
    public function processResponse():Response
    {
        if($this->request->isGet())
        {
            if($this->onGetRequest)
            {
                return ($this->onGetRequest)($this);
            }
        }
        elseif($this->request->isPost())
        {
            if($this->onPostRequest)
            {
                return ($this->onPostRequest)($this);
            }
        }
        elseif($this->request->isDelete())
        {
            if($this->onDeleteRequest)
            {
                return ($this->onDeleteRequest)($this);
            }
        }

        return $this->response;
    }
}