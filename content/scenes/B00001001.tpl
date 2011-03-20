    <!--
    Global location: B00001001
    -->

    <!-- Tower navigation and passage CSS -->
    <style>
    body {
        overflow: hidden;
    }
    
    /* Tower map */
    
    #tower {
        background-image: url({$SCENE_URL}/{$CurrentPerso->location_global}/all.png);
        background-position: left;
        background-repeat: no-repeat;
        height: 442px;
    }
    
    #tower_hl {
        position: relative;
    }
    
    /* Passage */

    #passage {
        width: 960px;
        height: 401px;
        
        background-image: url({$SCENE_URL}/{$CurrentPerso->location_global}/couloir/bay/void.png);
        background-position: top left;
        background-repeat: no-repeat;
    }
    
    #passage_left {
        position: relative;
        background-image: url({$SCENE_URL}/{$CurrentPerso->location_global}/couloir/GoLeft.png);
        width:  38px;
        height: 38px;
    }
    
    #passage_right {
        position: relative;
        background-image: url({$SCENE_URL}/{$CurrentPerso->location_global}/couloir/GoRight.png);
        width:  38px;
        height: 38px;
    }
    
    #passage_gallery ul {
        position: relative;
        top: 145px;
        left: 120px;
        
        margin-top: inherit;
        margin-bottom: inherit;
    }
    
    #passage_gallery li {
        display: block;
        float: left;
        margin-right: 60px;
        
        width: 170px;
        height: 170px;
        
        padding: 4px 4px;
        
        background-image: url({$SCENE_URL}/{$CurrentPerso->location_global}/couloir/frame.png);
        background-repeat: no-repeat;
        background-position: top left;
    }
       
    #screen {
        position: absolute;
        left: 10%;
        top: 10%;
        width: 80%;
        height: 80%;
    }
    
    #screen img {
        position: absolute;
        cursor: pointer;
        visibility: hidden;
        width: 0px;
        height: 0px;
    }
    
    #screen .tvover {
        border: solid #343434;
        opacity: 1;
        filter: alpha(opacity=100);
    }
    
    #screen .tvout {
        border: solid #fff;
        opacity: 0.7;
    }
    
    #bankImages {
        display: none;
    }
    
    #uploadDialog label {
        color: black;
    }

    /* Couloir info */
    #couloir_info {
        background-image: url({$SCENE_URL}/{$CurrentPerso->location_global}/couloir/info.png);
        height: 42px;
        padding-top: 20px;
        color: black;
    }

    #couloir_info a {
        color: white;
    }

    #couloir_info a:hover {
        font-weight: 900;
    }

    #domain_name {
        margin-left: 1em;
    }
    </style>
    
    <!-- Javascript bits: dojo -->
    <script>
        dojo.require("dojo.parser");
        dojo.require("dijit.Dialog");
        dojo.require("dijit.form.Button");
        dojo.require("dijit.form.TextBox");
    </script>
    
    <!-- Tower -->
    <!--<div id="tower"></div>-->
    
    <!-- Navigation -->
    {$loc = explode('C', $CurrentPerso->location_local)}{$loc[0] = substr($loc[0], 1)}
    <div class="grid_7 alpha">
        {#Sector#} T<span id="TowerFloor">{$loc[0]}</span>
    </div>
    <div class="grid_2" style="text-align: center">
        {#Couloir#} <span id="TowerCouloir">{$loc[1]}</span>
    </div>
    <div class="grid_7 omega" style="text-align: right">
        <a href="{if $loc[0] == 1}#{else}/do.php/set_local_location/T{$loc[0] - 1}C{$loc[1]}?redirectTo={get_url()}{/if}" onClick="passage.goUp(); return false;">{#GoUp#}</a> | <a href="/do.php/set_local_location/T{$loc[0] + 1}C{$loc[1]}?redirectTo={get_url()}" onClick="passage.goDown(); return false;">{#GoDown#}</a>
    </div>

    <!-- Passage  -->
    <div class="grid_16 alpha omega">
        <div id="passage">
            <div id="passage_gallery"></div>
            <!-- Navigation -->
        </div>
    </div>
    <div class="clear"></div>


    <!-- Info -->
    <div id="couloir_info">
        <div class="grid_6 prefix_2 alpha">
            <span id="domain_name">{#FreeDomain#}</span>
        </div>
        <div class="grid_6 suffix_2 omega" style="text-align: right">
            <span id="domain_owner">{sprintf(#ClaimDomain#, '#')}</span>
        </div>
        <div class="clear"></div>
    </div>

    <div class="clear"></div>
    
    <!-- Upload dialog -->
    <div dojoType="dijit.Dialog" id="uploadDialog" style="display: none;" title="{#UploadNewArtwork#}">
        <p>{#UploadNewArtworkDescription#}</p>
        <form method="post" id="test" action="{get_xhr_hashed_url('upload_content', {$CurrentPerso->location_global})}?redirectTo={get_url()}" enctype="multipart/form-data">
            <input type="hidden" id="location_local" name="location_local" value='{$CurrentPerso->location_local}' />
            <input type="hidden" id="i" name="location_k" value="-1">
            <div class="row">
                <label for="artwork" class="firstLabel">{#UploadNewArtworkPicture#} (max. {ini_get('upload_max_filesize')})</label>
                <input type="file" name="artwork" id="artwork" class="long" />
            </div>
            <div class="row">
                <label for="title" class="firstLabel">{#UploadNewArtworkTitle#}</label>
                <input dojoType="dijit.form.TextBox" name="title" id="title" type="text" class="long" />
            </div>
            <div class="row center">
                <button dojoType="dijit.form.Button" iconClass="dijitEditorIcon dijitEditorIconSave" type="submit" value="Save" />OK</button>
            </div>
        </form>
    </div>
    
    <!-- Javascript bits: script.aculo.us -->
    <script src="{#StaticContentURL#}/js/prototype.js"></script>
    <script src="{#StaticContentURL#}/js/effects.js"></script>

    <!-- Javascript bits: tower and couloir/passage/corridor/gallery/whatNameYouGiveToIt -->
    <script src="{$SCENE_URL}/{$CurrentPerso->location_global}/tower.js"></script>
    <script>
        //Initializes tower map
        //tower.hl = '{$SCENE_URL}/{$CurrentPerso->location_global}/hl.png';
        //tower.highlight(1);
        
        //Initializes passage view
        passage.bayPath = '{$SCENE_URL}/{$CurrentPerso->location_global}/couloir/bay/';
        passage.initialize('{$location->global}', '{$CurrentPerso->location_local}');
        
        //Initializes gallery
        gallery.initialize('{#StaticContentURL#}', '{get_xhr_hashed_url('get_content', {$CurrentPerso->location_global})}');
    </script>
