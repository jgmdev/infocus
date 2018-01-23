<form method="get" action="<?=$this->url($uri)?>">
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

<?php $global_type = $type; ?>

<div id="applications" class="list">
    <?php foreach($activities as $activity){ ?>
    <div class="item">
        <div class="icon">
            <img src="<?=$this->url("image/".$activity->binary)?>" />
        </div>
        <div class="info">
            <a name="<?=$activity->id?>"></a>
            <h3 class="title"><?=$activity->name?> (<?=$activity->binary?>)</h3>
            <div class="description">
                <form method="post" action="<?=$this->url($uri)?>#<?=$activity->id?>">
                    <input type="hidden" name="day" value="<?=$day?>" />
                    <input type="hidden" name="month" value="<?=$month?>" />
                    <input type="hidden" name="year" value="<?=$year?>" />
                    <input type="hidden" name="type" value="<?=$global_type?>" />

                    <input
                        type="hidden"
                        name="activity"
                        value="<?=$activity->id?>"
                    />

                    <input
                        type="text"
                        name="description"
                        value="<?=$this->escape($activity->description)?>"
                        placeholder="description"
                    />

                    <?php $types = new \InFocus\Lists\Types() ?>
                    <select name="type_value">
                    <?php foreach($types->getAll() as $type){ ?>
                        <?php
                            $selected = $activity->type == $type->id ?
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
                <div class="amount" style="width: <?=$activity->usage_percent?>%"></div>
            </div>
            <div class="usage">
                <?=Utils\Date::getHumanTime($activity->total_time)?>
            </div>
        </div>
        <div class="actions">
            <a
                class="button"
                href="<?=$this->url("activities", array("activity"=>$activity->binary, "day"=>$day, "month"=>$month, "year"=>$year))?>"
            >
                Activities
            </a>
        </div>
    </div>
    <?php } ?>
</div>