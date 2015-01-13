    <!--
    Global location: B00001002
    Local location: (0, 0, 0)
    -->
    <div class="grid_7 alpha">
        {#Sector#} C<span id="sector">0</span>
    </div>
    <div class="grid_9 omega" style="text-align: right; margin-bottom: 1em;">
        {#Core#}
    </div>
    
    <div class="clear"></div>
    
    <div class="content_wrapper">
    <h1>{#Core#}</h1>

    <div class="grid_9 suffix_1 content alpha">
        <h2>{#YouAreInCore#}</h2>
        {#CoreInfo#}
        <h2>{#ShipNavigationControl#}</h2>
        <p><strong>{#CurrentLocation#}{#_t#}</strong> {$location}</p>
    </div>
    <div class="grid_6 omega">
        <p><a href="{get_url('do.php')}/local_move/-1,1,-1?redirectTo=/">C1</a><br>
        <a href="{get_url('do.php')}/local_move/1,1,-1?redirectTo=/">C2</a><br>
        <a href="{get_url('do.php')}/local_move/-1,-1,-1?redirectTo=/">C3</a><br>
        <a href="{get_url('do.php')}/local_move/1,-1,-1?redirectTo=/">C4</a><br></p>
        <p><a href="{get_url('do.php')}/local_move/-1,1,1?redirectTo=/">C5</a><br>
        <a href="{get_url('do.php')}/local_move/1,1,1?redirectTo=/">C6</a><br>
        <a href="{get_url('do.php')}/local_move/-1,-1,1?redirectTo=/">C7</a><br>
        <a href="{get_url('do.php')}/local_move/1,-1,1?redirectTo=/">C8</a><br></p>        
    </div>
    <div class="clear fixclear"></div>
</div>
