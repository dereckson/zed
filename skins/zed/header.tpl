<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>{$PAGE_TITLE} - {#SiteTitle#}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="/css/960.css" media="screen" />
    <link rel="stylesheet" href="/css/zed/theme.css" />
    <script type="text/javascript" src="/js/misc.js"></script>
{foreach from=$PAGE_CSS item=css}
    <link rel="stylesheet" href="/css/{$css}" />
{/foreach}
{foreach from=$PAGE_JS item=js}
    <script src="/js/{$js}"></script>
{/foreach}
    {if $DOJO}
    <script type="text/javascript" src="/js/dojo/dojo/dojo.js" djConfig="isDebug:false, parseOnLoad: true" ></script>
    {/if}
</head>
<body{if $DIJIT} class="tundra"{/if}>
<div class="container_16">
    <!-- Header -->
    <div class="grid_4 alpha">
        <a href="/"><img src="/img/zed/logo.png" src="Zed logo" /></a>
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
    <p class="info">
        <strong>Current location</strong> {$CurrentPerso->where()}<br />
        <strong>Hypership time</strong> <span id="HypershipTime">{get_hypership_time()}</span>
    </p>
{if $SmartLine_STDOUT || $SmartLine_STDERR}
{include file="smartline_results.tpl"}
{/if}

