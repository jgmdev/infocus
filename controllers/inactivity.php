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

    $self->view->setTitle("Inactivity");

    $day = intval($request->getParam("day"));
    $month = intval($request->getParam("month"));
    $year = intval($request->getParam("year"));
    $idle = strval($request->getParam("idle"));

    if($request->getParam("today"))
    {
        $day = intval(date("j"));
        $month = intval(date("n"));
        $year = intval(date("Y"));
    }

    $inactivities = new \InFocus\Lists\Inactivities();

    $inactivities_list = $inactivities->getWithTime(
        $day, $month, $year, $idle
    );

    $inactivity_total = $inactivities->getTotalTime(
        $day, $month, $year
    );

    $self->view->setContent(
        $self->view->render(
            "content/inactivity",
            array(
                "inactivities" => $inactivities_list,
                "inactivity_total" => $inactivity_total,
                "day" => $day,
                "month" => $month,
                "year" => $year,
                "idle" => $idle
            )
        )
    );

    $response->getBody()->write($self->view->render());

    return $response;
};