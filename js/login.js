/*  -------------------------------------------------------------
    Zed
    - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    Author:         Dereckson
    Tags:           animation
    Filename:       login.js
    Version:        1.0
    Created:        2010-02-08
    Updated:        2010-02-08
    Licence:        Dual licensed: BSD and Creative Commons BY 3.0.
    Dependencies:   Prototype (for .setStyle method)
                    Scriptaculous + effects (not required for slide)
    -------------------------------------------------------------    */

/*
 * Slides an item from top to bottom
 */
var slide = {
    //The element to slide
    element:        null,

    //The slider height
    height:         120,

    //Let's track where we are
    currentHeight:  null,

    //Down step pixels each iteration
    step:           5,

    //Time in ms between 2 iteration
    interval:       1,

    //Time before to start
    startDelay:     250,

    //Moves the element at current iteration
    move:           function () {
        //Get current slider height
        if (this.currentHeight > 0) {
            //- step px (but we don't down after 0)
            this.currentHeight -= this.step;
            if (this.currentHeight < 0) {
                this.currentHeight = 0;
            }
        }

        //Sets the margin
        $(this.element).setStyle("margin-top: -" + this.currentHeight + "px;");

        //Next move
        if (this.currentHeight > 0) {
            setTimeout('slide.move()', this.interval);
        } else {
            slide.end();
        }

    },

    //When we've finished the move, what to do?
    end:            function () {
        //Er... nothing for now.
        var errorElement = document.getElementById("error");
        if (errorElement != null)
            Effect.Pulsate(errorElement);
    },

    //Start position, timer setup
    initialize:     function (elementId) {
        this.element = document.getElementById(elementId);
        if (this.element != null) {
            this.currentHeight = this.height;
            $(this.element).setStyle("margin-top: -" + this.height + "px;");
            setTimeout('slide.move()', this.startDelay);
        } else {
            throw "null element"
        }
    }
}

/*
 * Checks if fields are filled.
 * If so, hides error and ok button (useful as OpenID takes some time)
 * If not, highlight the missing fields
 */
function OnLoginSubmit (submitButton) {
    //Checks all fields are completed
    if (document.getElementById("openid").value != "") {
        //OpenID is preferred login way, so we're okay.
    } else {
        haveUsername = document.getElementById("username").value != "";
        havePassword = document.getElementById("password").value != "";
        haveBoth = haveUsername && havePassword;
        if (!haveBoth) {
            if (!haveUsername) {
                Effect.Pulsate(document.getElementById("username"));
            }
            if (!havePassword) {
                Effect.Pulsate(document.getElementById("password"));
            }
            return false;
        }
        //If both are filled, we're okay and can proceed.
    }

    //Hides error and ok item
    var errorElement = document.getElementById('error');
    if (errorElement != null) {
        Effect.Puff(errorElement);
    }
    Effect.Puff(submitButton);

    //We can submit
    return true;
}