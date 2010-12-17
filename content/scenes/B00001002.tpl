    <!--
    Global location: B00001002
    -->

    <div class="grid_7 alpha">
{$xyz = explode(',', substr($CurrentPerso->location_local, 1, -1))}
{$x = $xyz[0]}{$y = $xyz[1]}{$z = $xyz[2]}
        Sector C<span id="sector">{GeoOctocube::get_sector($x, $y, $z)}</span>
    </div>
    <div class="grid_2" style="text-align: center;">
        Zone <span id="area">{abs($x)}-{abs($y)}</span>
    </div>
    <div class="grid_7 omega" style="text-align: right; margin-bottom: 1em;">
        Niveau <span id="level">{abs($z)}</span>
    </div>
    
    <div class="clear"></div>

    <div class="content_wrapper">
        <h1>HyperShip builder</h1>
        <div class="grid_9 suffix_1 content alpha">
            <h2>This area is buildable.</h2>
            <p>You can take over this part of the ship, transform it and design it as you want.</p>
            <p>To start, you can:</p>
            <ul>
                <li>Create a 960px wide picture with your scene background.</li>
                <li>Define which parts of your picture are exits to another zone or level.</li>
            </ul>
            <p>If you want, you will also able to:</p>
            <ul>
                <li>Add descriptions to part of the area (like in the 90s adventure games)</li>
                <li>Create a textual scenario (like in 80s choose your own adventure books)</li>
                <li>Enhance the area with javascript or HTML5 (like in 2010)</li>
            </ul>
        </div>
        <div class="grid_6 omega">
            <p><a href="{get_url('builder')}">Start to build</a></p>
            
            <p><a href="{get_url('do.php')}/local_move/0,1,0?redirectTo=/">Go north</a><br />
            <a href="{get_url('do.php')}/local_move/1,0,0?redirectTo=/">Go east</a><br />
            <a href="{get_url('do.php')}/local_move/0,-1,0?redirectTo=/">Go south</a><br />
            <a href="{get_url('do.php')}/local_move/-1,0,0?redirectTo=/">Go west</a></p>
            
            <p><a href="{get_url('do.php')}/local_move/0,0,1?redirectTo=/">Climb up</a><br />
            <a href="{get_url('do.php')}/local_move/0,0,-1?redirectTo=/">Climb down</a></p>
        </div>
        <div class="clear fixclear"></div>
    </div>

