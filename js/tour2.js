/*  -------------------------------------------------------------
    Zed
    - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    Author:         Dereckson
    Tags:           animation jquery l10n
    Filename:       tour.js
    Version:        1.0
    Created:        2010-01-25
    Updated:        2010-02-03
    Licence:        Dual licensed: BSD and Creative Commons BY 3.0.
    Dependencies:   jQuery (for dom elements selection and dimensions.js)
                    dimensions.js
    -------------------------------------------------------------    */

var tour = {           
    //Default language
    lang: "en",
    
    //Translated in
    langs: "en,fr",
    
    //Current highlight showed
    current: -1,
    
    //File extension
    extension: "png",
    
    //Highlights files and position
    //File: /img/tour/{filename}.{extension}
    highlights: [
                 ["create", 13, 18],
                 ["lounge", 339, 107],
                 ["play", 22, 345],
                 ["explore", 325, 373]
                ],
    
    //The center x, y coordinate
    //It's used to determinate what highlight to print
    center: [368, 390],
    
    //Gets the highlight index, from position
    where: function(x, y) {
        if (x < this.center[0]) {
            //We're at left from center point
            return (y < this.center[1]) ? 0 : 2;
        } else {
            //We're at right from center point
            return (y < this.center[1]) ? 1 : 3;
        }
    },
    
    //Determines if we're inside the #Tour id
    isInside: function (pageX, pageY) {
        var tourOffset = $("#Tour").offset();
        return pageX >= tourOffset.left && pageY >= tourOffset.top
        && pageX <= tourOffset.left + $("#Tour").width()
        && pageY <= tourOffset.top + $("#Tour").height();
    },
    
    //Shows the highlight at specified the page position
    showAt: function (pageX, pageY) {
        var tourOffset = $("#Tour").offset();
        this.show(
            this.where(pageX - tourOffset.left , pageY - tourOffset.top)
        );
    },
    
    //Shows the specified highlight
    show: function (i) {
        if (this.current != i) {
            var filename = this.highlights[i][0] + "_" + this.lang + "." + this.extension;
            var code = '<img src="http://zed.espace-win.org.nyud.net/img/tour/' +  filename + '" alt="' + this.highlights[i][0] + '" />';
            $('#TourHighlight').empty().html(code);
            var o = document.getElementById("TourHighlight");
            o.style.left = this.highlights[i][1] + "px";
            o.style.top = this.highlights[i][2] + "px";
            this.current = i;
        }
    },
    
    //Hides highlight
    hideall: function () {
        if (this.current > -1) {
            this.current = -1;
            $('#TourHighlight').empty();
        }
    },
    
    //Runs the animation
    run: function (delay) {
        //Highlight order
        //[0, 1, 3, 2] is a counterwise move
        var order = [0, 1, 3, 2];
                        
        //Prints first hightlight
        this.show(order[0]);
        
        //Prints next highlights
        n = this.highlights.length;
        for (i = 1 ; i < n ; i++) {
            setTimeout('tour.show(' + order[i] + ')', delay * i);
        }
        
        //Prints back the first, and enables rollover
        setTimeout('tour.show(' + order[0] + ')', delay * n);
        setTimeout('tour.enableRollover()', delay * n);
    },
               
    //Enables rollovers
    enableRollover: function () {
        //Enables panel on click
        $('#Tour').bind("mousemove mouseout", function(e) {
            if (tour.isInside(e.pageX, e.pageY)) {
                tour.showAt(e.pageX, e.pageY);
            } else {
                tour.hideall();
            }             
        });
    },            
    
    //Gets client language (Firefox) or preferences content language (IE)
    getLanguage: function () {
        var lang = navigator.language;
        if (lang == undefined) lang = navigator.userLanguage;
        if (lang == undefined) return "";
        
        //fr-be -> fr
        var pos = lang.indexOf('-');
        if (pos > -1) lang = lang.substring(0, pos);
        
        return lang.toLowerCase();
    },
    
    //Initializes tour
    init: function () {
        //Tries to localize
        var lang = this.getLanguage();
        if (this.langs.indexOf(lang) > -1) this.lang = lang;
        
        //Runs tour animation
        //The rollover will be enabled at anim end
        this.run(900);
    }
}
        
$(document).ready(function() {
  tour.init();          
});