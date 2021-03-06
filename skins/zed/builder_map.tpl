        <div class="grid_7 alpha">
{$xyz = explode(',', substr($CurrentPerso->location_local, 1, -1))}
{$x = $xyz[0]}{$y = $xyz[1]}{$z = $xyz[2]}
        Sector C<span id="sector">{GeoOctocube::getSector($x, $y, $z)}</span>
    </div>
    <div class="grid_2" style="text-align: center;" id="info_area">
        Zone <span id="area">{abs($x)}-{abs($y)}</span>
    </div>
    <div class="grid_7 omega" style="text-align: right; margin-bottom: 1em;">
        Niveau <span id="level">{abs($z)}</span>
    </div>

    <div class="clear"></div>

    <!-- Map -->
{if $zones}
    <div id="map"></div>
    <style>
        .zone-build img {
            opacity: 0.10;
        }
    </style>
    <script type="text/javascript" src="/js/builder/map.js"></script>
    <script>
    var zones = [
{foreach from=$zones item=zone name=zones}
        ['{$zone->location_local}', {$zone->id}, '{$zone->type}', '{$zone->params}']{if !$smarty.foreach.zones.last},{/if}

{/foreach}
    ];
    dojo.ready(function() {
        map.init('map', zones);
    });
    </script>
    <noscript>
        <p>You've zones at:</p>
        <ul>
{foreach from=$zones item=zone}
            <li>{$zone->location_local}</li>
{/foreach}
        </ul>
        <p>A patch for a pure HTML renderer to print a map without javascript is welcome.</p>
    </noscript>
{else}
    <div class="grid_16 alpha omega">
        <div class="notify">This area is empty.</div>
    </div>
    <div class="clear"></div>
{/if}
