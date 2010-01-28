<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>{#SiteTitle#}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="/css/960.css" media="screen" />
    <link rel="stylesheet" href="/css/zed/theme.css" />
</head>
<body>
<div class="container_16">
{if $WAP}
    
    <!-- WAP -->
    <div class="grid_16 alpha omega">
        <div class="wap">{$WAP}</div>
    </div>
{/if}
{if $NOTIFY}
    
    <!-- Notify -->
    <div class="grid_16 alpha omega">
        <div class="notify">{$NOTIFY}</div>
    </div>
{/if}

    <div class="grid_16 alpha omega">
        <h1>{$TITLE}</h1>
        <p>{$ERROR_TEXT}</p>
        <p><a href="/">{#BackToHome#}</a></p>
    </div>
    <div class="clear"></div>
    <hr />
    <div class="grid_12 alpha">
        <p>[ {#Product#} / {#FatalErrorScreen#} ]</p>
    </div>
    <div class="grid_4 omega">
        <p style="text-align: right">[ <a href="/?action=user.logout">{#Logout#}</a> ]</p>
    </div>
</div>
</body>
</html>