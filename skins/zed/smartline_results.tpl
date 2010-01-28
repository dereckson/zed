        <!-- SmartLine output -->
        <div id="SmartLineResults" class="black">
{if $SmartLine_STDOUT}
            <p>{$SmartLine_STDOUT}</p>
{/if}
{if $SmartLine_STDERR}
            <p class="error">{$SmartLine_STDERR}</p>
{/if}
        </div>
        
        <div class="clear"></div>