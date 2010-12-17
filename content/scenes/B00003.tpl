    <!--
    Global location: B00003
    -->
    <img class="SpaceScene" src="{$SCENE_URL}/B00003.jpg" alt="{sprintf(#SpaceAround#, $CurrentPerso->location->body->name)}" border="0" usemap="#SceneMap" />
    <map name="SceneMap" id="SceneMap">
        <area shape="circle" coords="732,207,129" href="{get_url('explore')}" alt="{$CurrentPerso->location->body->name}" />
    </map>