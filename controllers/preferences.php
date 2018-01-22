<?php
/**
 * @author Jefferson Gonzalez <jgmdev@gmail.com>
 * @license https://opensource.org/licenses/GPL-3.0
 * @link http://github.com/jgmdev/infocus Source code.
 */

$controller = new InFocus\Web\Controller($request, $response);

$controller->onGetRequest = function(InFocus\Web\Controller $self){
    $request = $self->request;
    $response = $self->response;

    $self->view->setTitle("Preferences");

    $settings = new InFocus\Settings("InFocus");

    $timezone = $settings->get("timezone");

    $log_service = 0;

    $systemd_disabled = exec("systemctl --user status infocus | grep disabled");

    if(!$systemd_disabled)
    {
        $log_service = 1;
    }

    $self->view->setContent(
        $self->view->render(
            "content/preferences",
            array(
                "timezone" => $timezone,
                "log_service" => $log_service
            )
        )
    );

    $response->getBody()->write($self->view->render());

    return $response;
};

$controller->onPostRequest = function(InFocus\Web\Controller $self){
    $request = $self->request;

    $log_service = intval($request->getParam("log_service", "0"));
    $timezone = strval($request->getParam("timezone", "UTC"));

    $systemd_disabled = exec("systemctl --user status infocus | grep disabled");
    $systemd_inactive = exec("systemctl --user status infocus | grep inactive");

    // In order to be able to properly save the changes
    if(!$systemd_inactive)
    {
        exec("systemctl --user stop infocus");
    }

    $settings = new InFocus\Settings("InFocus");
    $settings->set("timezone", $timezone);

    $setup = $settings->get("setup");

    if($log_service && $systemd_disabled)
    {
        exec("systemctl --user enable infocus");
        $systemd_disabled = false;
    }
    elseif(!$log_service && !$systemd_disabled)
    {
        exec("systemctl --user disable infocus");
        $systemd_disabled = true;
    }

    if(!$systemd_disabled)
    {
        exec("systemctl --user start infocus");
    }
    else
    {
        exec("systemctl --user stop infocus");
    }

    if(!$setup)
    {
        return $self->response->withRedirect("/");
    }

    return ($self->onGetRequest)($self);
};