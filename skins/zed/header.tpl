<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>{if isset($PAGE_TITLE)}{$PAGE_TITLE} - {/if}{#SiteTitle#}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="{#StaticContentURL#}/css/960.css" media="screen" />
    <link rel="stylesheet" href="{#StaticContentURL#}/css/zed/theme.css" />
    <script type="text/javascript" src="{#StaticContentURL#}/js/misc.js"></script>
{foreach from=$PAGE_CSS item=css}
    <link rel="stylesheet" href="{#StaticContentURL#}/css/{$css}" />
{/foreach}
{foreach from=$PAGE_JS item=js}
    <script src="{#StaticContentURL#}/js/{$js}"></script>
{/foreach}
{if $DOJO}

    <!-- DOJO -->
    <script type="text/javascript" src="{#StaticContentURL#}/js/dojo/dojo/dojo.js" djConfig="isDebug:false, parseOnLoad: true" ></script>
{if isset($DIJIT)}
    <link rel="stylesheet" type="text/css" href="{#StaticContentURL#}/css/zed/forms.css" />
{/if}
{/if}
</head>
<body{if isset($DIJIT)} class="tundra"{/if}>
<!-- Header -->
<div id="header">
    <div id="header_content">
        <div class="container_16">
            <div class="grid_9">
                <div class="wall" id="header_wall">
                    {$WALL_TEXT}
                    <br />
                    <span class="wall_info">- - <a href="{$WALL_USER_URL}">{$WALL_USER}</a></span>
                </div>
                <div class="clear"></div>
                <div id="HypershipTime">{get_hypership_time()}</div>
            </div>
            <div class="grid_7">
                <a href="{get_url()}"><img src="{#StaticContentURL#}/img/zed/logo.png" alt="Zed logo" border="0" /></a>
            </div>
            <div class="clear"></div>
        </div>
    </div>
</div>
<div class="clear"></div>

<!-- Content -->
<div class="container_16">
{if isset($WAP)}

    <!-- WAP -->
    <div class="grid_16 alpha omega">
        <div class="wap">{$WAP}</div>
    </div>
    <div class="clear"></div>
{/if}
{if isset($NOTIFY)}

    <!-- Notify -->
    <div class="grid_16 alpha omega">
        <div class="notify">{$NOTIFY}</div>
    </div>
    <div class="clear"></div>
{/if}

{if isset($SmartLine_STDOUT) || isset($SmartLine_STDERR)}
{include file="smartline_results.tpl"}
{/if}
