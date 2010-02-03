{$code = substr($CurrentPerso->location_global, 0, 6)}
    <!-- {$code} -->
    <img class="SpaceScene" src="{$SCENE_URL}/{$code}.png" alt="{sprintf(#SpaceAround#, $CurrentPerso->location->body->name)}" border="0" usemap="#SceneMap" />
    <map name="SceneMap" id="SceneMap">
        <area shape="circle" coords="155,144,93" href="{get_url('explore')}" alt="{$CurrentPerso->location->body->name}" />
    </map>