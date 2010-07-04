    <!-- DIJIT -->
    <script type="text/javascript">
        dojo.require("dijit.form.Form");
        dojo.require("dijit.form.TextBox");
        dojo.require("dijit.form.ValidationTextBox");
        dojo.require("dijit.form.Button");
        dojo.require("dijit.form.FilteringSelect");
        dojo.require("dijit.form.CheckBox");
        
        dojo.require("dojox.form.PasswordValidator");
    </script>

    <!-- Settings - #{$page->id} -->
    <div class="grid_12 alpha">
    <h2>{#$page->title#}</h2>
    <form method="post">
    <input type="hidden" name="settings.page" value="{$page->id}" />
{foreach from=$page->settings item=setting}
    <div class="row">
{if $setting->field == "validationtext"}
        <label for="{$setting->key}" class="firstLabel ">{#$setting->key#}</label>
        <input dojoType="dijit.form.ValidationTextBox" regExp="{$setting->regExp}" id="{$setting->key}" name="{$setting->key}" type="text" value="{$setting->get()}" class="long" />
{elseif $setting->field == "text"}
        <label for="{$setting->key}" class="firstLabel ">{#$setting->key#}</label>
        <input dojoType="dijit.form.TextBox" id="{$setting->key}" name="{$setting->key}" type="text" value="{$setting->get()}" class="long" />
{elseif $setting->field == "password"}
        <div dojoType="dojox.form.PasswordValidator" name="{$setting->key}">
            <label for="{$setting->key}" class="firstLabel ">{#$setting->key#}</label>
            <input type="password" pwType="new" id="{$setting->key}" name="{$setting->key}" value="{$setting->get()}" class="long" />
            <br />
            <label for="{$setting->key}_confirm" class="firstLabel">{#$setting->key#} {#PasswordConfirm#}</label>
            <input type="password" pwType="verify" id="{$setting->key}_confirm" name="{$setting->key}_confirm" value="{$setting->get()}" class="long" />
        </div>
{elseif $setting->field == "filteredlist"}
        <label for="{$setting->key}" class="firstLabel ">{#$setting->key#}</label>
        <select id="{$setting->key}" name="{$setting->key}" dojoType="dijit.form.FilteringSelect" class="long">
{foreach from=$setting->choices item=value key=key}
            <option value="{$value}">{#$key#}</option>
{/foreach}
        </select>
{elseif $setting->field == "checkbox"}
        <input type="checkbox" dojoType="dijit.form.CheckBox" id="{$setting->key}" name="{$setting->key}" value="1" {if $setting->get()}checked="true" {/if}/> <label for="{$setting->key}">{#$setting->key#}</label>
{else}{dprint_r($setting)}
{/if}
    </div>
{/foreach}
    <div class="row">
        <button dojoType="dijit.form.Button" iconClass="dijitEditorIcon dijitEditorIconSave" type="submit" value="Save" />{#SaveSettings#}</button>
    </div>
    </form>
    </div>
    
    <div class="grid_4 omega">
        <h2>Settings</h2>
        <ul style="list-style-type: cjk-ideographic; line-height: 2em;">
{foreach from=$pages item=value key=key}
{if $key == $page->id}
            <li>{$value}</li>
{else}
            <li><a href="{get_url('settings', $key)}">{$value}</a></li>
{/if}
{/foreach}
            <li><a href="{get_url('who', 'edit', 'profile')}">{#EditProfile#}</a></li>
        </ul>
    </div>
    
    <div class="clear"></div>