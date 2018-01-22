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
    <select name="idle">
        <option value="">
            All Types
        </option>
    <?php foreach(array("System off"=>"0", "Idle"=>"1") as $name=>$value){ ?>
        <?php
            $selected = $value === $idle ?
                "selected=\"selected\"" : ""
            ;
        ?>
        <option <?=$selected?> value="<?=$value?>">
            <?=$name?>
        </option>
    <?php } ?>
    </select>

    <input type="submit" value="Filter" />

    <input type="submit" name="today" value="Today" />
</form>

<div style="padding: 10px; text-align: right; font-size: 28px;">
<strong style="font-size: 28px;">Total time Off:</strong>
<?=Utils\Date::getHumanTime($inactivity_total["system"])?>
&nbsp;&nbsp;&nbsp;&nbsp;
<strong style="font-size: 28px;">Total time Idle:</strong>
<?=Utils\Date::getHumanTime($inactivity_total["idle"])?>
</div>

<div id="applications" class="list">
    <?php foreach($inactivities as $inactivity){ ?>
    <div class="item">
        <div class="info">
            <a name="<?=$inactivity->id?>"></a>
            <h3 class="title">
                <?php if($inactivity->idle){ ?>
                Idle (no mouse or keyboard activity)
                <?php } else{ ?>
                System Off (computer off or application disabled)
                <?php } ?>
            </h3>
            <div class="description">
                <?=date("j/n/Y g:i:s A", $inactivity->from_timestamp)?>
                -
                <?=date("j/n/Y g:i:s A", $inactivity->to_timestamp)?>
            </div>
        </div>
        <div class="progress">
            <div class="line">
                <div class="amount" style="width: <?=$inactivity->usage_percent?>%"></div>
            </div>
            <div class="usage">
                <?=Utils\Date::getHumanTime($inactivity->seconds)?>
            </div>
        </div>
    </div>
    <?php } ?>
</div>