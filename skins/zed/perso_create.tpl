{include file="perso_header.tpl"}
<div class="grid_16">
    <h2>{if $perso->nickname}{sprintf(#EditCharacter#, $perso->nickname)}{else}{#CreateCharacter#}{/if}</h2>
    <!-- Edit Perso form -->
    <form dojoType="dijit.form.Form" id="PersoForm" method="post" execute="document.getElementById('PersoForm').submit()">
        <input type="hidden" name="form" value="perso.create" />
{if 0}
        <input type="hidden" name="id" value="{$perso->id}" />
{/if}
        <div class="row">
            <label class="firstLabel" for="name">{#FullName#}</label>
            <input type="text" id="name" name="name" maxlength="255" value="{$perso->name}" dojoType="dijit.form.TextBox" class="long" />
        </div>
        <div class="row">
            <label class="firstLabel" for="nickname">{#Nickname#}</label>
            <input type="text" id="nickname" name="nickname" maxlength="31" value="{$perso->nickname}" dojoType="dijit.form.TextBox" class="medium" />
        </div>
        <div class="row">
            <label class="firstLabel" for="race">{#Race#}</label>
            <input type="text" id="race" name="race" maxlength="31" value="{if $perso->race}{$perso->race}{else}humanoid{/if}" dojoType="dijit.form.TextBox" class="medium" />
        </div>
        <div class="row">
            <label class="firstLabel" for="sex">{#Sex#}</label>
            <select id="sex" name="sex" dojoType="dijit.form.FilteringSelect" class="medium">
                <option value="M">{#Male#}</option>
                <option value="F">{#Female#}</option>
                <option value="N">{#Neutral#}</option>
                <option value="2">{#Hermaphrodit#}</option>
            </select>
        </div>
        <div class="row">
            <button dojoType="dijit.form.Button"
                    iconClass="dijitEditorIcon dijitEditorIconSave"
                    type="submit" value="Save">
                {#Save#}
            </button>
        </div>
    </form>
</div>
{include file="perso_footer.tpl"}