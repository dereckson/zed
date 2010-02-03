<!-- MOTD preview code -->
<script>
    function updateWall () {
        wallTextValue = document.getElementById("WallText").value;
        wallText = wallTextValue ? wallTextValue : "{#DummyPlaceholder#}";
        document.getElementById("wall_message").innerHTML = wallText;
    }
</script>

<!-- Add something on the MOTD -->

<h1>{#PushMessage#}</h1>
<form method="post">
    <label for="WallAddText">{#TextToAdd#}{#_t#}</label><br />
    <input type="text" maxlength="80" size="80" id="WallText" name="text" onblur="updateWall();" onkeyup="updateWall();" onchange="updateWall();" />
    <input type="submit" value="{#Push#}" />
</form>
<em>{#TextToAddWarning#}</em>

<h2>{#Rendering#}</h2>
<div class="grid_4 alpha">
    <p><em>{#RenderingWhere#}</em></p>
</div>
<div class="grid_12 omega">
    <div class="wall">
        <p>
            <span id="wall_message">{#DummyPlaceholder#}</span>
            <br /><span class="wall_info">-- <a href="{get_url('who', $CurrentPerso->nickname)}">{$CurrentPerso->name}</a></span>
        </p>
        <div class="clear"></div>
    </div>
    <div class="clear"></div>
</div>