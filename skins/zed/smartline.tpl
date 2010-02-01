
    <!-- SmartLine -->
    <div class="grid_16 alpha omega" id="SmartLine">
        <form method="post" name="SmartLine" action="{get_page_url()}">
{if $SmartLineHistory}
            <!-- SmartLine history -->
            <div class="grid_4 left alpha">
                <select name="SmartLineHistory" id="SmartLineHistory" class="black" onChange=UpdateSmartLine()>
                    <option value="">[ {#SmartLineHistory#} ]</option>
{foreach from=$SmartLineHistory item=command}
                    <option value="{$command->text|escape}">{$command->time} | {$command->text|escape}</option>
{/foreach}
                </select>
            </div>
            <!-- SmartLine line -->
            <div class="grid_12 right omega">
{else}
            <!-- SmartLine line -->
            <div class="grid_16 alpha omega left" style="width: 100.2%">
{/if}
                <input name="C" type="text" id="SmartLineBar" maxlength=255 class="black" style="text-align: left;">
            </div>
        </form>
    </div>
    
    <div class="clear"></div>
