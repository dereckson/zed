    <!-- DIJIT -->
    <script type="text/javascript">
        dojo.require("dijit.form.Form");
        dojo.require("dijit.form.TextBox");
        dojo.require("dijit.form.ValidationTextBox");
        dojo.require("dijit.form.Button");
        dojo.require("dijit.form.FilteringSelect");
        dojo.require("dijit.form.CheckBox");
    </script>

    <!-- Settings - #{$page->id} -->
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
        <label for="{$setting->key}" class="firstLabel ">{#$setting->key#}</label>
        <input dojoType="dijit.form.TextBox" id="{$setting->key}" name="{$setting->key}" type="password" value="{$setting->get()}" class="long" />
    </div>
    <div class="row">
        <label for="{$setting->key}_confirm" class="firstLabel">{#$setting->key#} (confirm it)</label>
        <input dojoType="dijit.form.TextBox" id="{$setting->key}_confirm" name="{$setting->key}_confirm" type="password" value="{$setting->get()}" class="long" />
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