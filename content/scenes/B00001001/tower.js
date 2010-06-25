/*  -------------------------------------------------------------
    Zed hypership tower javascript code
    - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    Authors:        Dereckson (tower, passage and gallery classes)
                    G. Fernandez (tv, Library classes)
    Tags:           animation
    Filename:       login.js
    Version:        1.0
    Created:        2010-01-31
    Updated:        2010-02-23
    Licences:       Dereckson code is dual licensed:
                    BSD and Creative Commons BY 3.0.
                    G. Fernandez code is licensed under CC-BY-NC 2.0.
    Dependencies:   dojo   (xhr calls, dialog ui)
    -------------------------------------------------------------    */

/*  -------------------------------------------------------------
    Tower class
    Prints a tower map, with hl on current corridor  
    - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -    */
    
var tower = {
    //The source to highlight picture
    hl: 'hl.png',
    
    //The highlighted corridor (1-6). 0 = no hl
    i: 0,
    
    //The corridor 4 (top) hl starts at:
    hlStartPosition: [163, 93],
    
    //Gets CurrentPerso html code
    getHighlightCode: function () {
        return '<img id="tower_hl" src="' + this.hl + '" alt="Corridor ' + this.i + '" />';
    },
    
    highlight: function (i) {
        //If already there, nothing to do
        if (this.i == i) return;
        
        //Puts hl
        this.i = i;
        var tower = document.getElementById("tower");
        if (tower != null) {
            tower.innerHTML = this.getHighlightCode();
            var towerHl = document.getElementById("tower_hl");
            if (towerHl != null) {
                towerHl.style.left = this.hlStartPosition[0] + "px";
                towerHl.style.top = this.hlStartPosition[1] + "px";
                
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
    }
}

/*  -------------------------------------------------------------
    Passage class
    Prints relevant bay
    - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -    */

//Corridors are like art galleries
var passage = {
    //Parameters
    shipGlobalLocation: null,
    persoLocalLocation: null,
    bayPath: '',
    id: 'passage',
    
    //gets hyperspace bay
    //randomly selected from 23 choices
    getHyperspaceBay: function () {
        var n = Math.floor(Math.random() * 23 + 1);
        return 'hyper/' + ((n.toString().length == 1) ? "0" + n : n) + '.png';
    },
    
    getBay: function () {
        //The bay depends of ship location
        if (this.shipGlobalLocation[0] == 'B') {
            //TODO: handle orientation of the ship and objects
            //{$bay = substr($location->global, 0, 6)}
            return this.shipGlobalLocation.substring(0, 6) + '.png';
        } else {
            //TODO: check if we're really in hyperspace.
            //If not, we should fallback on default.png
            return this.getHyperspaceBay();
        }
        
        return 'default.png'; //will be reachable, cf. upper
    },
    
    updateBay: function () {
        var bgImage = 'url("' + this.bayPath + this.getBay() + '")';
        document.getElementById(this.id).style.backgroundImage = bgImage;
    },
    
    getLocalLocation: function () {
        //Splits TtCc expression at C
        //    '0' => "T2"
        //    '1' => "1"
        var location = this.persoLocalLocation.split('C');
        
        if (location.length == 2 && location[0][0] == 'T') {
            //Current t, c coordinates
            var t = location[0].substring(1);
            var c = location[1];
        }
        
        return [t, c];
    },
    
    //TODO: t and c are strings, parse them into numbers
    
    goLeft: function () {
        //TxC1 -> TxC6 -> TxC5 -> TxC4 -> TxC3 -> TxC2 -> TxC1
        tc = this.getLocalLocation();
        t = tc[0];
        c = tc[1];

        //New c coordinates
        var nc = (c == 1) ? 6 : c - 1;
        
        this.moveTo('T' + t + 'C' + nc);
    },
    
    goRight: function () {
        //TxC1 -> TxC2 -> TxC3 -> TxC4 -> TxC5 -> TxC6 -> TxC1
        tc = this.getLocalLocation();
        t = tc[0];
        c = tc[1];

        //New c coordinates
        var nc = (c == 6) ? 1 : eval(c) + 1;
        
        this.moveTo('T' + t + 'C' + nc);
    },
    
    moveTo: function (local_location) {
        //New local location
        passage.persoLocalLocation = local_location;
        document.getElementById('location_local').value = local_location;

        //Updates bays
        //passage.updateBay();
        
        //TODO: make url parameter compatible to any URL scheme
        //TODO: check in dojo doc if the local_location have to be encoded
        dojo.xhrGet({
            handleAs:       "json",
            url:            "/do.php/set_local_location/" + local_location,
            preventCache:   true,
            handle:         function (response, ioArgs) {
                //Loads new gallery content
                if (gallery.initialized) {
                    gallery.loadPics();
                }
            }
        });
    },
    
    onGalleryInitialized: function () {
        //Adds left and right arrows
        this.addLeftArrow();
        this.addRightArrow();
    },
       
    addLeftArrow: function () {
        //Adds left arrow
        var element = document.getElementById(this.id);
        var left = element.offsetLeft +  28;
        var top  = element.offsetTop  + 173;
        
        element.innerHTML += '<div id="passage_left" onClick="passage.goLeft()" style="display: none; position: absolute; top: ' + top + 'px; left: ' + left + 'px"></div>';
    },
    
    addRightArrow: function () {
        //Adds right arrow
        var element = document.getElementById(this.id);
        var left = element.offsetLeft + 898;
        var top  = element.offsetTop  + 173;
        
        element.innerHTML += '<div id="passage_right" onClick="passage.goRight()" style="display: none; position: absolute; top: ' + top + 'px; left: ' + left + 'px"></div>';
    },
    
    displayArrows: function (state) {
        //Displays left and right arrows?
        var display = [false, false];
        
        switch (state) {
            case "left":
                display[0] = true;
            break;
                
            case "right":
                display[1] = true;
            break;
        }
        
        document.getElementById("passage_left").style.display =  display[0] ? 'block' : 'none';
        document.getElementById("passage_right").style.display = display[1] ? 'block' : 'none';
    },
    
    onmousemove: function () {
        var element = document.getElementById(this.id);
        var left = element.offsetLeft;
        var top  = element.offsetTop;
        
        //alert(element.offsetWidth);
        var inPassage = (
            mouse.x >= left && mouse.y >= top &&
            mouse.x <= left + element.offsetWidth &&
            mouse.y <= top  + element.offsetHeight
        )
        
        if (inPassage) {
            var x = mouse.x - left;
            var y = mouse.y - top;
            if (x < 130) {
                this.displayArrows('left');
            } else if (x > 830) {
                this.displayArrows('right');
            } else {
                this.displayArrows('none');
            }
        }
    },
    
    initialize: function (shipGlobalLocation, persoLocalLocation) {
        this.shipGlobalLocation = shipGlobalLocation;
        this.persoLocalLocation = persoLocalLocation;
        if (persoLocalLocation == "") {
            this.moveTo('T2C1');
            
            //Notify (this code requires prototype.js (or jquery, but in this case, simplify it with a insertAfter call))
            //TODO: ensure this code have dojo as only dependency
            $("header").replace('<div id="header">' + $("header").innerHTML + '</div><div class="container_16"><div class="grid_16 alpha omega"><div class="notify">As you were losing yourself in the hypership tower, your paths guide you to the second floor.</div></div></div>');
        }
        this.updateBay();
    }
}

/*  -------------------------------------------------------------
    Gallery class
    Prints the gallery and calls artwork script
    - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -    */

var gallery = {
    pics: [],
    
    initialized: false,
    
    artworkDisplayMode: false,
    
    currentPic: -1,
    
    path: '',
    
    loadXhrUrl: null,
    
    getSquare: function (url) {
        //Gets extension position (last . position)
        var pos = url.lastIndexOf('.');
        
        return url.substring(0, pos) + 'Square' + url.substring(pos);
    },
    
    showGallery: function () {
        var html = '<ul>';
        for (var i = 0 ; i < this.pics.length ; i++) {
            if (this.pics[i] != null) {
                //Shows artwork
                html += '\n\t<li><img src="' + this.getSquare(this.pics[i]) + '" onClick="gallery.show(' + i +  ')" /></li>';
            } else {
                //Shows placeholder
                html += '\n\t<li onClick="gallery.uploadDialog(' + i + ');"></li>';
            }
        }
        html += '\n</ul>';
        document.getElementById('passage_gallery').innerHTML = html;
        passage.onGalleryInitialized();
    },
    
    hideGallery: function () {
        var elem = document.getElementById('passage_gallery');
        Effect.Puff(elem);
    },
    
    loadPics: function () {
        dojo.xhrGet({
            handleAs:       'json',
            url:            this.loadXhrUrl + '?location_local=' + passage.persoLocalLocation,
            preventCache:   true,
            handle:         function (response, ioArgs) {
                //TODO: in the future, we should print some metadata,
                //      and the objects will be useful.
                //      Meanwhile, we transform it to an array line.
                
                var pics = [null, null, null];
                
                //Builds pics array
                for (var i = 0 ; i < response.length ; i++) {
                    if (response[i]['location_k'] > -1 && response[i]['location_k'] < 3) {
                        pics[response[i]['location_k']] = gallery.path + '/' + response[i]['path'];
                    }
                }
                
                //alert(dump(response));
                gallery.loadPicsCallback(pics);

            }
        });
    },
    
    loadPicsCallback: function (pics) {
        //Sets pics array
        this.pics = pics;
        
        //Show gallery
        this.showGallery();
        
        //Init done
        if (!this.initialized) {
            this.initialized = true;
        }
    },
    
    show: function (i) {
        //Hides gallery
        this.hideGallery();
        
        //Shows image
        setTimeout('gallery.showImage(' + i + ')', 800);
    },
    
    showImage: function (i) {
        //Sets image information
        this.currentPic = i; //currently not used
        this.artworkDisplayMode = true; //to handle properly ESC key down.
        
        //New HTML passage code
        document.getElementById('passage').innerHTML = '<div id="screen"></div><div id="bankImages"><img src="' + this.pics[i] + '" /></div>';
        
        //Calls Photo3D script
        onresize = tv.resize;
        tv.init();
    },
    
    backToGallery: function () {
        //Fades current picture
        Effect.Fade(document.getElementById('screen'));
        
        //Rebuilds gallery (in 800 ms, to let time to fading effect)
        setTimeout("document.getElementById('passage').innerHTML = '<div id=\"passage_gallery\"></div>'; gallery.showGallery()", 800);
        
        //Sets image information
        this.currentPic = -1; //currently not used
        this.artworkDisplayMode = false; //to handle properly ESC key down.
    },
    
    uploadDialog: function (i) {
        document.getElementById('i').value = i;
        dijit.byId('uploadDialog').show();
    },
    
    initialize: function (path, loadXhrUrl) {
        //Sets load xhr url
        this.path = path;
        this.loadXhrUrl = loadXhrUrl;
        
        //Loads pics
        this.loadPics();
        
        //Listens ESC key
        document.onkeydown = function (e) {
            if (e.keyCode == 27) {
                if (gallery.artworkDisplayMode) {
                    gallery.backToGallery();
                } else {
                    gallery.hideGallery();
                }
            }
        }
    }
}

/*  -------------------------------------------------------------
    Photo3D G. Fernandez script
    http://www.dhteumeuleu.com/runscript.php?scr=photo3D.html
        
    TODO: simplify code to handle one picture, and not an array
    - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -    */

var gridsize = 1;

var Library = {};
Library.ease = function() {
    this.target = 0;
    
    this.position = 0;
    
    this.move = function(_1, _2) {
        this.position += (_1 - this.position) * _2;
    };
};

var tv = {
    O:  [], 
    
    screen: {},
    
    grid: {
        size: gridsize,
        borderSize: 6,
        zoomed: false
    },
    
    angle: {
        x: new Library.ease(),
        y: new Library.ease()
    },
    
    camera: {
        x: new Library.ease(),
        y: new Library.ease(),
        zoom: new Library.ease(),
        focalLength: 750
    },
    
    init: function() {
        this.screen.obj = document.getElementById("screen");
        var images = document.getElementById("bankImages").getElementsByTagName("img");
        
        this.screen.obj.onselectstart = function () {
            return false;
        };
        this.screen.obj.ondrag = function () {
            return false;
        };
        var ni = 0;
        var n = (tv.grid.size / 2) - 0.5;
        for (var y = - n ; y <= n ; y++) {
            for (var x = - n ; x <= n; x++) {
                var o = document.createElement("img");
                var i = images[(ni++) % images.length];
                o.className = "tvout";
                o.src = i.src;
                tv.screen.obj.appendChild(o);
                o.point3D = {
                    x: x,
                    y: y,
                    z: new Library.ease()
                };
                o.point2D = {};
                o.ratioImage = 1;
                tv.O.push(o);
                
                o.onmouseover = function () {
                    if (!tv.grid.zoomed) {
                        if (tv.o) {
                            tv.o.point3D.z.target = 0;
                            tv.o.className = "tvout";
                        }
                        this.className = "tvover";
                        this.point3D.z.target =  - 0.5;
                        tv.o = this;
                    }
                };
                
                o.onclick = function () {
                    if (!tv.grid.zoomed) {
                        tv.camera.x.target = this.point3D.x;
                        tv.camera.y.target = this.point3D.y;
                        tv.camera.zoom.target = tv.screen.w * 1.25;
                        tv.grid.zoomed = this;
                    } else {
                        if (this == tv.grid.zoomed) {
                            tv.camera.x.target = 0;
                            tv.camera.y.target = 0;
                            tv.camera.zoom.target = tv.screen.w / (tv.grid.size + 0.1);
                            tv.grid.zoomed = false;
                        }
                    }
                };
            
                o.calc = function () {
                    this.point3D.z.move(this.point3D.z.target, 0.5);
                    var x = (this.point3D.x - tv.camera.x.position) * tv.camera.zoom.position;
                    var y = (this.point3D.y - tv.camera.y.position) * tv.camera.zoom.position;
                    var z = this.point3D.z.position * tv.camera.zoom.position;
                    var xy = tv.angle.cx * y - tv.angle.sx * z;
                    var xz = tv.angle.sx * y+tv.angle.cx * z;
                    var yz = tv.angle.cy * xz - tv.angle.sy * x;
                    var yx = tv.angle.sy * xz+tv.angle.cy * x;
                    this.point2D.scale = tv.camera.focalLength / (tv.camera.focalLength+yz);
                    this.point2D.x = yx * this.point2D.scale;
                    this.point2D.y = xy * this.point2D.scale;
                    this.point2D.w = Math.round(Math.max(0, this.point2D.scale * tv.camera.zoom.position * 0.8));
                    if (this.ratioImage > 1) {
                        this.point2D.h = Math.round(this.point2D.w / this.ratioImage);
                    } else {
                        this.point2D.h = this.point2D.w;this.point2D.w = Math.round(this.point2D.h * this.ratioImage);
                    }
                };
                
                o.draw = function () {
                    if (this.complete) {
                        if (!this.loaded) {
                            if (!this.img) {
                                this.img = new Image();
                                this.img.src = this.src;
                            }
                            if (this.img.complete) {
                                this.style.visibility = "visible";
                                this.ratioImage = this.img.width / this.img.height;
                                this.loaded = true;this.img = false;
                            }
                        }
                        this.style.left = Math.round(this.point2D.x * this.point2D.scale + tv.screen.w - this.point2D.w * 0.5) + "px";
                        this.style.top  = Math.round(this.point2D.y * this.point2D.scale + tv.screen.h - this.point2D.h * 0.5) + "px";
                        this.style.width  = this.point2D.w + "px";
                        this.style.height = this.point2D.h + "px";
                        this.style.borderWidth = Math.round(Math.max(this.point2D.w, this.point2D.h) * tv.grid.borderSize * 0.01) + "px";
                        this.style.zIndex = Math.floor(this.point2D.scale * 100);
                    }
                };
            }
        }
        tv.resize();
        mouse.y = tv.screen.y+tv.screen.h;
        mouse.x = tv.screen.x+tv.screen.w;
        tv.run();
    },
    
    resize: function() {
        var o = tv.screen.obj;
        tv.screen.w = o.offsetWidth / 2;
        tv.screen.h = o.offsetHeight / 2;
        tv.camera.zoom.target = tv.screen.w / (tv.grid.size + 0.1);
        for (tv.screen.x = 0, tv.screen.y = 0 ; o != null ; o = o.offsetParent) {
            tv.screen.x += o.offsetLeft;
            tv.screen.y  = o.offsetTop;
        }
    },
    
    run: function () {
        tv.angle.x.move(-(mouse.y - tv.screen.h - tv.screen.y) * 0.0025, 0.1);
        tv.angle.y.move((mouse.x - tv.screen.w - tv.screen.x) * 0.0025, 0.1);
        tv.camera.x.move(tv.camera.x.target, tv.grid.zoomed ? 0.25: 0.025);
        tv.camera.y.move(tv.camera.y.target, tv.grid.zoomed ? 0.25: 0.025);
        tv.camera.zoom.move(tv.camera.zoom.target, 0.05);
        tv.angle.cx = Math.cos(tv.angle.x.position);
        tv.angle.sx = Math.sin(tv.angle.x.position);
        tv.angle.cy = Math.cos(tv.angle.y.position);
        tv.angle.sy = Math.sin(tv.angle.y.position);
        for (var i = 0, o ; o = tv.O[i] ; i++) {
            o.calc();
            o.draw();
        }
        setTimeout(tv.run, 32);
    }
};

var mouse = {x: 0, y: 0};

document.onmousemove = function (e) {
    if (window.event) {
        e = window.event;
    }
    mouse.x = e.clientX;
    mouse.y = e.clientY;
    passage.onmousemove();
    return false;
};