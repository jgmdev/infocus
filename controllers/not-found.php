<?php
/**
 * @author Jefferson Gonzalez <jgmdev@gmail.com>
 * @license https://opensource.org/licenses/GPL-3.0
 * @link http://github.com/jgmdev/infocus Source code.
 */

$controller = new InFocus\Web\Controller($request, $response);

$controller->onGetRequest = function(InFocus\Web\Controller $self){
    $response = $self->response;

    $self->view->setTitle("Page Not Found");

    $self->view->setContent(
        "<h4>The section that you tried to access does not exists.</h4>"
    );

    $response = $response->withStatus(404);

    $response->getBody()->write($self->view->render());

    return $response;
};