<?php if(!$setup){ ?>
<div id="message">
    Please set the activity types and when ready click
    <a href="<?=$this->url("overview", array("ready"=>1))?>">I'm Ready!</a>.
</div>
<br />
<?php } ?>

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

    <input type="submit" value="Filter" />

    <input type="submit" name="today" value="Today" />
</form>

<div id="overview" class="list">
    <?php foreach($types as $type){ ?>
    <form method="post" action="<?=$this->url($uri)?>#<?=$type->id?>">
    <div class="item">
        <div class="icon">
            <img src="" />
        </div>
        <div class="info">
            <a name="<?=$type->id?>"></a>
            <input
                type="hidden"
                name="type"
                value="<?=$type->id?>"
            />
            <?php
                $readonly = "";
                if(in_array($type->id, array(1, 2, 3, 4)))
                    $readonly = "readonly=\"readonly\"";
            ?>
            <input
                type="text" <?=$readonly?>
                name="name"
                value="<?=$this->escape($type->name)?>"
                placeholder="name of type"
            />
            <div class="tags">
                <textarea
                    name="tags"
                    placeholder="tags are used to automatically associate a type to an activity based on its title."
                /><?=$this->escape($type->tags)?></textarea>

                <input type="submit" value="Save" />
            </div>
        </div>
        <div class="progress">
            <div class="line">
                <div class="amount" style="width: <?=$type->usage_percent?>%"></div>
            </div>
            <div class="usage">
                <?=Utils\Date::getHumanTime($type->total_time)?>
            </div>
        </div>
        <div class="actions">
            <a
                class="button"
                href="<?=$this->url("applications", array("type"=>$type->id, "day"=>$day, "month"=>$month, "year"=>$year))?>"
            >
                Applications
            </a>
        </div>
    </div>
    </form>
    <?php } ?>
</div>

<div id="add-type">
<h3>Add New Type</h3>
<form method="post" action="<?=$this->url($uri)?>#<?=$type->id?>">
    <input
        type="text"
        name="name"
        placeholder="name of type"
    />
    <input
        type="text"
        name="description"
        placeholder="description of type"
    />
    <textarea
        name="tags"
        placeholder="tags are used to automatically associate a type to an activity based on its title."
    /></textarea>

    <input type="submit" name="add" value="Add" />
</form>
</div>