/* SmartLine */

function UpdateSmartLine() {
    document.forms.SmartLine.C.value = document.forms.SmartLine.SmartLineHistory.value;
    document.forms.SmartLine.C.focus();
}

/* Dumps a variable */

function dump(arr,level) {
        var dumped_text = "";
        if(!level) level = 0;
        
        //The padding given at the beginning of the line.
        var level_padding = "";
        for(var j=0;j<level+1;j++) level_padding += "    ";
        
        if(typeof(arr) == 'object') { //Array/Hashes/Objects 
                for(var item in arr) {
                        var value = arr[item];
                        
                        if(typeof(value) == 'object') { //If it is an array,
                                dumped_text += level_padding + "'" + item + "' ...\n";
                                //dumped_text += dump(value,level+1);
                        } else {
                                dumped_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
                        }
                }
        } else { //Stings/Chars/Numbers etc.
                dumped_text = "===>"+arr+"<===("+typeof(arr)+")";
        }
        return dumped_text;
}

/* A code for an hidden function */

var ar2215 = {
    input: "",
    pattern: "38384040373937396665",
    clear: setTimeout('ar2215.clear_input()', 2000),
    load: function () {
        window.document.onkeydown = function (e) {
            ar2215.input += e ? e.keyCode : event.keyCode;
            if (ar2215.input == ar2215.pattern) {
                ar2215.code("/index.php/push");
                clearTimeout(ar2215.clear);
                return;
            }
            clearTimeout(ar2215.clear);
            ar2215.clear = setTimeout("ar2215.clear_input()", 2000);
        }
        this.iphone.load("/index.php/push");
    },
    code: function (link) {
        window.location = link;
    },
    clear_input: function () {
        ar2215.input = "";
        clearTimeout(ar2215.clear);
    },
    iphone:{
        start_x: 0,
        start_y: 0,
        stop_x: 0,
        stop_y: 0,
        tap: false,
        capture: false,
        keys: ["UP","UP","DOWN","DOWN","LEFT","RIGHT","LEFT","RIGHT","TAP","TAP"],
        code: function (link) { window.location = link },
        load: function (link) {
            document.ontouchmove = function (e) {
                if (e.touches.length == 1 && ar2215.iphone.capture == true) {
                    var touch = e.touches[0];
                    ar2215.iphone.stop_x = touch.pageX;
                    ar2215.iphone.stop_y = touch.pageY;
                    ar2215.iphone.tap = false;
                    ar2215.iphone.capture = false;
                    ar2215.iphone.check_direction();
                }
            }
            document.ontouchend = function (evt) {
                if (ar2215.iphone.tap == true)
                    ar2215.iphone.check_direction();
            }
            document.ontouchstart = function(evt) {
                ar2215.iphone.start_x = evt.changedTouches[0].pageX;
                ar2215.iphone.start_y = evt.changedTouches[0].pageY;
                ar2215.iphone.tap = true;
                ar2215.iphone.capture = true;
            }
        },
        check_direction: function () {
            x_magnitude = Math.abs(this.start_x - this.stop_x);
            y_magnitude = Math.abs(this.start_y - this.stop_y);
            x = ((this.start_x - this.stop_x) < 0) ? "RIGHT": "LEFT";
            y = ((this.start_y - this.stop_y) < 0) ? "DOWN" : "UP";
            result = (x_magnitude > y_magnitude) ? x : y;
            result = (this.tap == true) ? "TAP" : result;
            if (result == this.keys[0])
                this.keys = this.keys.slice(1, this.keys.length);
            if (this.keys.length == 0)
                this.code(this.link)
         }
    }
}

ar2215.load();