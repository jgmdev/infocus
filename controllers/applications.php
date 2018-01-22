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

    $self->view->setTitle("Applications");

    $activities = new InFocus\Lists\Activities();

    $day = intval($request->getParam("day"));
    $month = intval($request->getParam("month"));
    $year = intval($request->getParam("year"));
    $type = intval($request->getParam("type"));

    if($request->getParam("today"))
    {
        $day = intval(date("j"));
        $month = intval(date("n"));
        $year = intval(date("Y"));
    }

    $activities_list = $activities->getWithTime($day, $month, $year, $type);

    $self->view->setContent(
        $self->view->render(
            "content/applications",
            array(
                "activities" => $activities_list,
                "longest_activity" => $activities_list[0]->total_time,
                "day" => $day,
                "month" => $month,
                "year" => $year,
                "type" => $type
            )
        )
    );

    $response->getBody()->write($self->view->render());

    return $response;
};

$controller->onPostRequest = function(InFocus\Web\Controller $self){
    $request = $self->request;

    $activity_id = intval($request->getParam("activity", "0"));
    $description = strval($request->getParam("description", ""));
    $type = intval($request->getParam("type", "1"));

    if($activity_id)
    {
        $activity = new InFocus\Element\Activity();

        $activity->loadFromId($activity_id);

        $activity->description = $description;
        $activity->type = $type;

        $activity->update();
    }

    return ($self->onGetRequest)($self);
};