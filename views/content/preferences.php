<h1>Preferences</h1>

<?php if(!$timezone){ ?>
<div id="message">
    Please set these preferences for the correct functioning of the application.
</div>
<?php } ?>

<form method="post" action="<?=$this->url("preferences")?>">
    <div id="preferences" class="form">
        <div class="item">
            <div class="label">
                Timezone
                <div class="description">
                    Used to properly calculate and display date data.
                </div>
            </div>
            <div class="field">
                <?php $zones = Utils\Timezones::getList(); ?>
                <select name="timezone">
                <?php foreach($zones as $zone){ ?>
                    <?php
                        $selected = $zone == $timezone ?
                            "selected=\"selected\"" : ""
                        ;
                    ?>
                    <option <?=$selected?> value="<?=$zone?>">
                        <?=$zone?>
                    </option>
                <?php } ?>
                </select>
            </div>
        </div>

        <div class="item">
            <div class="label">
                Activity Log Service
                <div class="description">
                    Enable or disable the systemd infocus logging service.
                </div>
            </div>
            <div class="field">
                <?php
                    $enabled_checked = "";
                    $disabled_checked = "";
                    if($log_service)
                    {
                        $enabled_checked = 'checked="checked"';
                    }
                    else
                    {
                        $disabled_checked = 'checked="checked"';
                    }
                ?>
                <input
                    id="log_enabled"
                    type="radio"
                    <?=$enabled_checked?>
                    name="log_service"
                    value="1"
                /> <label for="log_enabled">Enabled</label>
                <input
                    id="log_disabled"
                    type="radio"
                    <?=$disabled_checked?>
                    name="log_service"
                    value="0"
                /> <label for="log_disabled">Disabled</label>
            </div>
        </div>

        <div class="item">
            <div class="label"></div>
            <div class="field">
                <input type="submit" value="Save" />
            </div>
        </div>
    </div>
</form>