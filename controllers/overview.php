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

    $self->view->setTitle("Overview");
    $self->view->setApplicationName("InFocus");

    $types = new InFocus\Lists\Types();

    $day = intval($request->getParam("day"));
    $month = intval($request->getParam("month"));
    $year = intval($request->getParam("year"));

    $settings = new InFocus\Settings("InFocus");

    if($request->getParam("ready"))
    {
        $settings->set("setup", "true");
    }

    $setup = $settings->get("setup");

    if($request->getParam("today"))
    {
        $day = intval(date("j"));
        $month = intval(date("n"));
        $year = intval(date("Y"));
    }

    $types_list = $types->getWithTime($day, $month, $year);

    $self->view->setContent(
        $self->view->render(
            "content/overview",
            array(
                "types" => $types_list,
                "longest_type" => $types_list[0]->total_time,
                "day" => $day,
                "month" => $month,
                "year" => $year,
                "setup" => $setup
            )
        )
    );

    $response->getBody()->write($self->view->render());

    return $response;
};

$controller->onPostRequest = function(InFocus\Web\Controller $self){
    $request = $self->request;

    $type_id = intval($request->getParam("type", "0"));
    $name = strval($request->getParam("name"));
    $tags = strval($request->getParam("tags", ""));
    $description = intval($request->getParam("description"));

    $add_type = $request->getParam("add");

    if($type_id)
    {
        $type = new InFocus\Element\Type();

        $type->loadFromId($type_id);

        $type->name = $name;
        $type->tags = $tags;

        $type->update();
    }
    elseif($add_type)
    {
        $type = new InFocus\Element\Type();
        $type->name = $name;
        $type->tags = $tags;
        $type->description = $description;


        $types = new InFocus\Lists\Types;
        $types->add($type);
    }

    return ($self->onGetRequest)($self);
};