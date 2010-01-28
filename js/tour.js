        var tour = {
            //Lang
            lang: "en",
            
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
                    var code = '<img src="/img/tour/' +  filename + '" alt="' + this.highlights[i][0] + '" />';
                    $('#TourHighlight').empty().html(code);
                    var o = document.getElementById("TourHighlight");
                    o.style.left = this.highlights[i][1] + "px";
                    o.style.top = this.highlights[i][2] + "px";
                    this.current = i;
                }
            },
            
            hideall: function () {
                if (this.current > -1) {
                    this.current = -1;
                    $('#TourHighlight').empty();
                }
            },
            
            //Runs the animation
            run: function (delay) {
                this.show(0);
                
                n = this.highlights.length;
                order = [0, 1, 3, 2];
                for (i = 1 ; i < n ; i++) {
                    setTimeout('tour.show(' + order[i] + ')', delay * i);
                }
                setTimeout('tour.show(0)', delay * n);
                setTimeout('tour.enableRollover()', delay * n);
            },
                       
            //Enable rollovers
            enableRollover: function () {
                //Enables panel on click
                $('#Tour').bind("mousemove", function(e) {
                    if (tour.isInside(e.pageX, e.pageY)) {
                        tour.showAt(e.pageX, e.pageY);
                    } else {
                        tour.hideall();
                    }             
                });
            },            
            
            //Initializes tour
            init: function () {
                //this.enableRollover();
                this.run(900);
            }
        }
                
        $(document).ready(function() {
          tour.lang = 'fr';
          tour.init();
        });