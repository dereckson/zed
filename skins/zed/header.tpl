<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>{$PAGE_TITLE} - {#SiteTitle#}</title>
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
{if $DIJIT}
    <link rel="stylesheet" type="text/css" href="{#StaticContentURL#}/css/zed/forms.css">
{/if}
{/if}
</head>
<body{if $DIJIT} class="tundra"{/if}>
<!-- Header -->
<div id="header">
    <div id="header_content">
        <div class="container_16">
            <div class="grid_4 alpha">
                <a href="{get_url()}"><img src="{#StaticContentURL#}/img/zed/logo.png" src="Zed logo" border=0 /></a>
            </div>
            <div class="grid_12 omega">
                <div class="wall">
                    <p>
                        {$WALL_TEXT}
                        <br /><span class="wall_info">-- <a href="{$WALL_USER_URL}">{$WALL_USER}</a></span>
                    </p>
                </div>
            </div>
            <div class="clear"></div>
        </div>
    </div>
</div>
<div class="clear"></div>

<!-- Content -->
<div class="container_16">
{if $WAP}
    
    <!-- WAP -->
    <div class="grid_16 alpha omega">
        <div class="wap">{$WAP}</div>
    </div>
    <div class="clear"></div>
{/if}
{if $NOTIFY}
    
    <!-- Notify -->
    <div class="grid_16 alpha omega">
        <div class="notify">{$NOTIFY}</div>
    </div>
    <div class="clear"></div>
{/if}

    <!-- Where? When? -->
    <div class="info">
        <div class="info_left">
            <strong>Current location</strong> {$CurrentPerso->where()}
        </div>
        <div class="info_right">
            <span id="HypershipTime">{get_hypership_time()}</span>
        </div>
    </div>
{if $SmartLine_STDOUT || $SmartLine_STDERR}
{include file="smartline_results.tpl"}
{/if}

