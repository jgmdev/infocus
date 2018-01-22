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

    $self->view->setTitle("Activities");

    $day = intval($request->getParam("day"));
    $month = intval($request->getParam("month"));
    $year = intval($request->getParam("year"));
    $activity = strval($request->getParam("activity"));
    $type = intval($request->getParam("type"));

    if($request->getParam("today"))
    {
        $day = intval(date("j"));
        $month = intval(date("n"));
        $year = intval(date("Y"));
    }

    $subactivities = new \InFocus\Lists\SubActivities();

    $subactivities_list = $subactivities->getWithTime(
        $day, $month, $year, $activity, $type
    );

    $self->view->setContent(
        $self->view->render(
            "content/activities",
            array(
                "subactivities" => $subactivities_list,
                "day" => $day,
                "month" => $month,
                "year" => $year,
                "activity" => $activity,
                "type" => $type
            )
        )
    );

    $response->getBody()->write($self->view->render());

    return $response;
};

$controller->onPostRequest = function(InFocus\Web\Controller $self){
    $request = $self->request;

    $activity_id = intval($request->getParam("subactivity", "0"));
    $type = intval($request->getParam("type", "1"));

    if($activity_id)
    {
        $activity = new InFocus\Element\SubActivity();

        $activity->loadFromId($activity_id);

        $activity->type = $type;

        $activity->update();
    }

    return ($self->onGetRequest)($self);
};