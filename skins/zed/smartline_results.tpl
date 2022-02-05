        <!-- SmartLine output -->
        <div id="SmartLineResults" class="black">
{if isset($SmartLine_STDOUT)}
            <p>{$SmartLine_STDOUT}</p>
{/if}
{if isset($SmartLine_STDERR)}
            <p class="error">{$SmartLine_STDERR}</p>
{/if}

        </div>

        <p class="clear"></p>
