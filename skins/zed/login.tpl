<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="{#Lang#}">
<head>
    <title>{#SiteTitle#}</title>
    <link rel="Stylesheet" href="{#StaticContentURL#}/css/zed/login.css" type="text/css" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <script type="text/javascript" src="js/login.js"></script>
    <script type="text/javascript" src="js/misc.js"></script>
    <script type="text/javascript" src="js/prototype.js"></script>
    <script type="text/javascript" src="js/effects.js"></script>
</head>
<body>

<!-- Login form -->
<div id="LoginBox">
    <form method="post" action="{get_url()}">
        <div class="row">
            <label for="username">{#Login#}{#_t#}</label>
            <input type="text" id="username" name="username" value="{$username}" />
        </div>
        <div class="row">
            <label for="password">{#Password#}{#_t#}</label>
            <input type="password" id="password" name="password" />
        </div>
        <div class="row">
            <label for="openid">{#OpenID#}{#_t#}</label>
            <input type="text" id="openid" name="openid" value="{$OpenID}" />
        </div>
        <div class="row">
            <input type="submit" id="submit" name="LogIn" value="{#OK#}" onclick="return OnLoginSubmit(this);" />
        </div>
    </form>
{if $LoginError}
        <!-- Ooops, something wrong -->
        <div class=row>
            <p id="error" class="error">&nbsp;&nbsp;&nbsp;&nbsp;{$LoginError}</p>
        </div>
{/if}
</div>

<!-- Links -->
<div id="link_tour"><a href="/tour.html"></a></div>
<div id="link_blog"><a href="http://planet.nasqueron.org/zed/"></a></div>

<!--

            XXXXXXX             XX
            X    X               X
                X                X               Invitation code:
                X    XXXXX   XXXXX               {$code}
               X    X     X X    X
              X     XXXXXXX X    X
              X     X       X    X
             X    X X     X X    X
            XXXXXXX  XXXXX   XXXXXX

Welcome to the Zed beta. We're happy you're reading the source :)

If you want to know what we're building, check http://zed.dereckson.be/tour.html

If you wish an access, send a mail to zedinvite (alt+64) dereckson.be
and specify the following code: {$code}

                                    * * * *

Bienvenue dans la version bêta de Zed. Heureux que vous consultiez la source.

Un petit aperçu de ce que l'on crée est sur http://zed.dereckson.be/tour.html

Pour obtenir un accès, envoyez un mail à zedinvite (alt+64) dereckson.be
en spécifiant le code suivant : {$code}
-->
<script type="text/javascript">
    slide.initialize('LoginBox');
</script>
</body>
</html>
