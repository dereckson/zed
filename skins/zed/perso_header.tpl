<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>{#SiteTitle#}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="{#StaticContentURL#}/css/960.css" media="screen" />
    <link rel="stylesheet" href="{#StaticContentURL#}/css/zed/theme.css" />
    
    <!-- Calls dojo -->
    <script src="/{#StaticContentURL#}js/dojo/dojo/dojo.js" type="text/javascript"
            djConfig="isDebug: false, parseOnLoad: true"></script>
    <link rel="stylesheet" href="{#StaticContentURL#}/css/zed/forms.css" />
    <script type="text/javascript">
        dojo.require("dijit.form.Form");
        
        dojo.require("dijit.form.ValidationTextBox");
        dojo.require("dijit.form.TextBox");
        dojo.require("dijit.form.FilteringSelect");
        dojo.require("dijit.form.Button");
    
        dojo.require("dojox.validate.regexp");
        dojo.require("dojox.form.PasswordValidator");
    
        dojo.require("dojo.parser");
    </script>
</head>
<body class="tundra">
<!-- Header -->
<div id="header">
    <div id="header_content">
        <div class="container_16">
            <div class="grid_4 alpha omega suffix_8">
                <a href="{get_url()}"><img src="{#StaticContentURL#}/img/zed/logo.png" src="Zed logo" border=0 /></a>
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
{/if}
{if $NOTIFY}
    
    <!-- Notify -->
    <div class="grid_16 alpha omega">
        <div class="notify">{$NOTIFY}</div>
    </div>
{/if}
