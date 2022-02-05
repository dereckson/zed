

    <div class="clear"></div>
{if $SmartLinePrint}
{include file="smartline.tpl"}
{/if}

    <!-- Footer -->
    <hr />
    <div id="footer">
        <div class="grid_8 alpha">
            <p>[ {#Product#} / {$CurrentPerso->location_global} {$CurrentPerso->location_local} / {if isset($screen)}{$screen}{else}Untitled screen{/if} ]</p>
        </div>
        <div class="grid_8 omega" style="float: right">
            <p style="text-align: right">[ {if $MultiPerso}<a href="{get_url()}?action=perso.logout">{sprintf(#SwapPerso#, $CurrentPerso->name)}</a> | {/if}<a href="{get_url()}?action=user.logout">{#Logout#}</a> ]</p>
        </div>
    </div>
</div>
</body>
</html>
