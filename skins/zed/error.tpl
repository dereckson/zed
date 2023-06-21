<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>{#SiteTitle#}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="{#StaticContentURL#}/css/960.css" media="screen" />
    <link rel="stylesheet" href="{#StaticContentURL#}/css/zed/theme.css" />
</head>
<body>
<!-- Header -->
<div id="header">
    <div id="header_content">
        <div class="container_16">
            <div class="grid_9">
                <div id="HypershipTime">{get_hypership_time()}</div>
            </div>
            <div class="grid_7">
                <a href="{get_url()}"><img src="{#StaticContentURL#}/img/zed/logo.png" alt="Zed logo" border="0" /></a>
            </div>
        </div>
    </div>
</div>
<div class="clear"></div>
<div class="container_16">
{if isset($WAP)}

    <!-- WAP -->
    <div class="grid_16 alpha omega">
        <div class="wap">{$WAP}</div>
    </div>
{/if}
{if isset($NOTIFY)}

    <!-- Notify -->
    <div class="grid_16 alpha omega">
        <div class="notify">{$NOTIFY}</div>
    </div>
{/if}

    <!-- Error -->
    <div class="content_wrapper">
        <h1>{$TITLE}</h1>
        <div class="content">
            <p>{$ERROR_TEXT}</p>
            <p><a href="{get_url()}">{#BackToHome#}</a></p>
        </div>
    </div>
    <div class="clear"></div>
    <hr />
    <div id="footer">
        <div class="grid_12 alpha">
            <p>[ {#Product#} / {#FatalErrorScreen#} ]</p>
        </div>
        <div class="grid_4 omega">
            <p style="text-align: right">[ <a href="{get_url()}?action=user.logout">{#Logout#}</a> ]</p>
        </div>
    </div>
</div>
</body>
</html>
