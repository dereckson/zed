<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>{$PAGE_TITLE} - {#SiteTitle#}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="/css/960.css" media="screen" />
    <link rel="stylesheet" href="/css/zed/theme.css" />
    <script src="/js/misc.js"></script>
</head>
<body>
<div class="container_16">
    <!-- Header -->
    <div class="grid_16">
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
{/if}
{if $NOTIFY}
    
    <!-- Notify -->
    <div class="grid_16 alpha omega">
        <div class="notify">{$NOTIFY}</div>
    </div>
{/if}
{if $SmartLine_STDOUT || $SmartLine_STDERR}
{include file="smartline_results.tpl"}
{/if}