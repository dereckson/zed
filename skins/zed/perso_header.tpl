<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>{#SiteTitle#}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="/css/960.css" media="screen" />
    <link rel="stylesheet" href="/css/zed/theme.css" />
    
    <!-- Calls dojo -->
    <script src="/js/dojo/dojo/dojo.js" type="text/javascript"
            djConfig="isDebug: false, parseOnLoad: true"></script>
    <link rel="stylesheet" href="/css/zed/forms.css" />
    <script type="text/javascript">
        dojo.require("dijit.form.Form");
        
        dojo.require("dijit.form.ValidationTextBox");
        dojo.require("dijit.form.TextBox");
        dojo.require("dijit.form.FilteringSelect");
        dojo.require("dijit.form.Button");
    
        dojo.require("dojo.parser");
    </script>
</head>
<body class="tundra">
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
