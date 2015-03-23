    <!--
    Global location: B00001002
    -->
    <div class="grid_7 alpha">
{$xyz = explode(',', substr($CurrentPerso->location_local, 1, -1))}
{$x = $xyz[0]}{$y = $xyz[1]}{$z = $xyz[2]}
        {#Sector#} C<span id="sector">{GeoOctocube::getSector($x, $y, $z)}</span>
    </div>
    <div class="grid_2" style="text-align: center;">
        {#Zone#} <span id="area">{abs($x)}-{abs($y)}</span>
    </div>
    <div class="grid_7 omega" style="text-align: right; margin-bottom: 1em;">
        {#Level#} <span id="level">{abs($z)}</span>
    </div>

    <div class="clear"></div>
{if $zone}
{if $zone->type == "hotglue"}
    <!-- Content iframe -->
    <script type="text/javascript">
    function hijacklinks(iframe){
      var as = iframe.contentDocument.getElementsByTagName('a');
      for(i=0;i<as.length;i++){
        as[i].setAttribute('target','_parent');
      }
    }
    </script>
    <iframe src="{$HOTGLUE}?zone_{$zone->id}" width="960" height="600" id="content_iframe" frameborder="0" scrolling="no" style="margin-bottom: 1em" onload="hijacklinks(this)"></iframe>
{else}
<p>{#CantRender#}</p>
{dprint_r($zone)}
{/if}
{else}
    <div class="content_wrapper">
        <h1>{#Builder#}</h1>
        <div class="grid_9 suffix_1 content alpha">
            {#BuildInfo#}
        </div>
        <div class="grid_6 omega">
            <p><a href="{get_url('builder')}">{#StartBuild#}</a></p>

            <p><a href="{get_url('do.php')}/local_move/0,1,0?redirectTo=/">{#GoNorth#}</a><br />
            <a href="{get_url('do.php')}/local_move/1,0,0?redirectTo=/">{#GoEast#}</a><br />
            <a href="{get_url('do.php')}/local_move/0,-1,0?redirectTo=/">{#GoSouth#}</a><br />
            <a href="{get_url('do.php')}/local_move/-1,0,0?redirectTo=/">{#GoWest#}</a></p>

            <p><a href="{get_url('do.php')}/local_move/0,0,1?redirectTo=/">{#GoUp#}</a><br />
            <a href="{get_url('do.php')}/local_move/0,0,-1?redirectTo=/">{#GoDown#}</a></p>

            <p><a href="{get_url('builder', 'map')}">{#ViewMap#}</a></p>

            <p><a href="{get_xhr_hashed_url('global_move', 'B00001001', 'T2C3')}?redirectTo={get_url()}">{#GoTower#}</a></p>
        </div>
        <div class="clear fixclear"></div>
    </div>
{/if}
