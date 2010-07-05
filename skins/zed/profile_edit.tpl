    <!-- Calls dojo -->
    <script src="/js/dojo/dojo/dojo.js" type="text/javascript"
            djConfig="isDebug: false, parseOnLoad: true"></script>
    <script type="text/javascript">
        dojo.require("dijit.form.Form");
        dojo.require("dijit.form.CheckBox");
        dojo.require("dijit.form.Button");
        
        function SetWidgetFont(id, font) {
            //TODO: document.getElementById(id).style.font = font;
        }
    </script>

    <!-- Edit profile -->
    <div class="grid_11 alpha profile">
        <div class="profile_id clearfix">
            <h1 class="profile_nick" id="UserLongname">{$USERNAME}</h1>
        </div>
        <div class="profile_separator"></div>
        <div class="profile_text">
            <form action="" method="post">
                <input type="hidden" name="EditProfile" value="1" />
				<h2>{#ProfileTextTitle#}</h2>
                <textarea style="font-family: Calibri" id="ProfileText" rows="16" cols="72" name="text" class="text">{$PROFILE_TEXT}</textarea><br />
                <div class="row" style="background-color: white; color: black;">
                    <span>{#ProfileFont#}{#_t#}</span>
                    <input type="radio" name="fixedwidth" id="fixedwidthNo" value="0" dojoType="dijit.form.RadioButton" {if !$PROFILE_FIXEDWIDTH}checked{/if} onclick="SetWidgetFont('ProfileText', 'Calibri')" />
                    <label for="fixedwidthNo"><span style="font-family: Calibri, Arial; font-weight: 100; font-size: 1.25em; color: black;">{#Calibri#}</span></label>
                    <input type="radio" name="fixedwidth" id="fixedwidthYes" value="1" dojoType="dijit.form.RadioButton" {if $PROFILE_FIXEDWIDTH}checked={/if} onclick="SetWidgetFont('ProfileText', 'FixedSys')" />
                    <label for="fixedwidthYes"><span style='font-family: "Fixedsys Excelsior 3.01", Fixedsys, Fixed; font-weight: 100; color: black;'>{#FixedSys#}</span></label>
				</div>
                <div class="row">
                    <button dojoType="dijit.form.Button" iconClass="dijitEditorIcon dijitEditorIconSave" type=submit onclick="document.forms[0].submit()">
                            {#SaveProfile#}
                    </button>
                    <noscript>
                        <input type="submit" value="{#SaveProfile#} {#JavaScriptSafeMessage#}" />
                    </noscript>
                </div>
            </form>
        </div>
    </div>
    
    <!--  Faerie content -->
    <div class="grid_5 omega">
        <div class="sidebar_border"></div>
        <div id="sidebar">
            <div class="border_top"></div>		
				<div class="sidebar_content">
                    <h2>{#EditMyPage#}</h2>
                    <ul>
                        <li>{#EditProfile#}</li>
                        <li><a href="{get_url('settings','perso')}">{#EditAccount#}</a></li>
                        <li><a href="{get_url('who')}/edit/photos">{if $PICS}{#ManagePhotos#}{else}{#AddPhoto#}{/if}</a></li>
                    </ul>
            	</div>
            <div class="border_bottom"></div>
        </div>
    </div>