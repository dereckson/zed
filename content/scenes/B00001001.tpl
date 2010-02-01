    <!-- JQuery -->
    <script src="{#StaticContentURL#}/js/jquery-1.3.2.min.js"></script>
    
    <!-- Tower navigation CSS -->
    <style>
    #tower {
        background-image: url({$SCENE_URL}/{$CurrentPerso->location_global}/all.png);
        background-position: left;
        background-repeat: no-repeat;
        height: 442px;
    }
    
    #tower_hl {
        position: relative;
    }
    </style>
    
    <div id="tower"></div>
    
    <!-- Tower navigation JQuery code -->
    <script>
    var tower = {
        //The source to highlight picture
        hl: '{$SCENE_URL}/{$CurrentPerso->location_global}/hl.png',
        
        //The highlighted corridor (1-6). 0 = no hl
        i: 0,
        
        //The corridor 4 (top) hl starts at:
        hlStartPosition: [163, 93],
        
        //Gets CurrentPerso html code
        getHighlightCode: function () {
            return "<img id='tower_hl' src='" + this.hl + "' alt='Corridor " + this.i + "' />";
        },
        
        highlight: function (i) {
            //If already there, nothing to do
            if (this.i == i) return;
            
            //Puts hl
            this.i = i;
            $('#tower').html(this.getHighlightCode());
            var o = document.getElementById("tower_hl");
            o.style.left = this.hlStartPosition[0] + "px";
            o.style.top = this.hlStartPosition[1] + "px";
            
            //The 4 is okay
            if (i == 4) return;
            
            //Gets rotation angle
            if (i > 4) {
                angle = 60 * (i - 4);
            } else {
                angle = 180 + (i -1) * 60;
            }
        }
    }
    
    $(document).ready(function() {
      tower.highlight(3);
    });
    </script>