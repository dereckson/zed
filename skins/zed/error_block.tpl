    <div class="clear clearfix"></div>
    <div class="grid_16 alpha omega">
        <h1>{$TITLE}</h1>
        <p>{$ERROR_TEXT}</p>
        <p><a href="{get_url()}">{#BackToHome#}</a></p>
    </div>
    <div class="clear"></div>
    <hr />
    <div class="grid_12 alpha">
        <p>[ {#Product#} / {#FatalErrorInterrupt#} ]</p>
    </div>
    <div class="grid_4 omega">
        <p style="text-align: right">[ <a href="{get_url()}?action=user.logout">{#Logout#}</a> ]</p>
    </div>
</div>
</body>
</html>