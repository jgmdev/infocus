<form method="get" action="<?=$this->url($uri)?>">
    <?php $activities = new \InFocus\Lists\Activities() ?>
    <select name="activity">
        <option value="">
            Select Application
        </option>
    <?php foreach($activities->getAll() as $activity_data){ ?>
        <?php
            $selected = $activity_data->binary == $activity ?
                "selected=\"selected\"" : ""
            ;
        ?>
        <option <?=$selected?> value="<?=$activity_data->binary?>">
            <?=$activity_data->name?>
        </option>
    <?php } ?>
    </select>

    <select name="day">
        <option value="">
            All Days
        </option>
    <?php foreach(\Utils\Date::getDays() as $label=>$value){ ?>
        <?php
            $selected = $value == $day ?
                "selected=\"selected\"" : ""
            ;
        ?>
        <option <?=$selected?> value="<?=$value?>">
            <?=$label?>
        </option>
    <?php } ?>
    </select>

    <select name="month">
        <option value="">
            All Months
        </option>
    <?php foreach(\Utils\Date::getMonths() as $label=>$value){ ?>
        <?php
            $selected = $value == $month ?
                "selected=\"selected\"" : ""
            ;
        ?>
        <option <?=$selected?> value="<?=$value?>">
            <?=$label?>
        </option>
    <?php } ?>
    </select>

    <select name="year">
        <option value="">
            All Years
        </option>
    <?php foreach(\Utils\Date::getYears() as $label=>$value){ ?>
        <?php
            $selected = $value == $year ?
                "selected=\"selected\"" : ""
            ;
        ?>
        <option <?=$selected?> value="<?=$value?>">
            <?=$label?>
        </option>
    <?php } ?>
    </select>

    <?php $types = new \InFocus\Lists\Types() ?>
    <select name="type">
        <option value="">
            All Types
        </option>
    <?php foreach($types->getAll() as $type_data){ ?>
        <?php
            $selected = $type_data->id == $type ?
                "selected=\"selected\"" : ""
            ;
        ?>
        <option <?=$selected?> value="<?=$type_data->id?>">
            <?=$type_data->name?>
        </option>
    <?php } ?>
    </select>

    <input type="submit" value="Filter" />

    <input type="submit" name="today" value="Today" />
</form>

<?php if($activity){ ?>
<?php
    $activity_data = new InFocus\Element\Activity();
    $activity_data->loadFromBinaryName($activity);
?>
<div id="application-info">
    <div class="icon">
        <img src="<?=$this->url("image/".$activity)?>" />
    </div>
    <div class="info">
        <h3><?=$activity_data->name?> (<?=$activity_data->binary?>)</h3>
        <div><?=$activity_data->description?></div>
    </div>
</div>
<?php } ?>
<div id="applications" class="list">
    <?php foreach($subactivities as $subactivity){ ?>
    <div class="item">
        <?php if(!$activity){ ?>
        <div class="icon">
            <img src="<?=$this->url("image/".$subactivity->application_name)?>" />
        </div>
        <?php } ?>
        <div class="info">
            <a name="<?=$subactivity->id?>"></a>
            <h3 class="title"><?=$subactivity->window_title?> (<?=$subactivity->application_name?>)</h3>
            <div class="description">
                <form method="post" action="<?=$this->url($uri)?>#<?=$subactivity->id?>">
                    <input
                        type="hidden"
                        name="activity"
                        value="<?=$activity?>"
                    />

                    <input
                        type="hidden"
                        name="subactivity"
                        value="<?=$subactivity->id?>"
                    />

                    <?php $types = new \InFocus\Lists\Types() ?>
                    <select name="type">
                    <?php foreach($types->getAll() as $type){ ?>
                        <?php
                            $selected = $subactivity->type == $type->id ?
                                "selected=\"selected\"" : ""
                            ;
                        ?>
                        <option <?=$selected?> value="<?=$type->id?>">
                            <?=$type->name?>
                        </option>
                    <?php } ?>
                    </select>

                    <input type="submit" value="Save" />
                </form>
            </div>
        </div>
        <div class="progress">
            <div class="line">
                <div class="amount" style="width: <?=$subactivity->usage_percent?>%"></div>
            </div>
            <div class="usage">
                <?=Utils\Date::getHumanTime($subactivity->seconds)?>
            </div>
        </div>
    </div>
    <?php } ?>
</div>