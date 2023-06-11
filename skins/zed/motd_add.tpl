<!-- MOTD preview code -->
<script>
    function updateWall () {
        const wallTextValue = document.getElementById("WallText").value;
        document.getElementById("wall_message").innerHTML = wallTextValue ? wallTextValue : "{#DummyPlaceholder#}";
    }
</script>

<!-- Add something on the MOTD -->
<h1>{#PushMessage#}</h1>
<form method="post">
    <label for="WallText">{#TextToAdd#}{#_t#}</label><br />
    <input type="text" maxlength="90" size="100" id="WallText" name="text" onblur="updateWall();" onkeyup="updateWall();" onchange="updateWall();" />
    <input type="submit" value="{#Push#}" />
</form>
<em>{#TextToAddWarning#}</em>

<!-- Preview -->
<h2>{#Rendering#}</h2>
<div class="wall">
    <span id="wall_message">{#DummyPlaceholder#}</span>
    <br /><span class="wall_info">-- <a href="{get_url('who', $CurrentPerso->nickname)}">{$CurrentPerso->name}</a></span>
    <div class="clear"></div>
</div>
<div class="clear"></div>
