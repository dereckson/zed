    <div class="clear"></div>
{include file="smartline.tpl"}
    <hr />
    <div id="footer">
        <div class="grid_12 alpha">
            <p>[ {#Product#} / {$CurrentPerso->location_global} {$CurrentPerso->location_local} / {if $screen}{$screen}{else}Untitled screen{/if} ]</p>
        </div>
        <div class="grid_4 omega" style="float: right">
            <p style="text-align: right">[ <a href="/?action=user.logout">{#Logout#}</a> ]</p>
        </div>
    </div>
</div>
</body>
</html>