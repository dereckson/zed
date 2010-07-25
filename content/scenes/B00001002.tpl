{$xyz = explode(',', substr($CurrentPerso->location_local, 1 -1))}
{$x = $xyz[0]}{$y = $xyz[1]}{$z = $xyz[2]}
<p>
    Secteur C<span id="sector">{GeoOctocube::get_sector($x, $y, $z)}</span><br />
    Niveau <span id="level">{$z}</span><br />
    Zone <span id="area">{abs($x)}, {abs($y)}</span>
</p>