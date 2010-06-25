    <!-- Calls dojo -->
    <script src="/js/dojo/dojo/dojo.js" type="text/javascript"
            djConfig="isDebug: false, parseOnLoad: true"></script>
    <script type="text/javascript">
        dojo.require("dijit.form.Form");
        dojo.require("dijit.form.ValidationTextBox");
        dojo.require("dijit.form.CheckBox");
        dojo.require("dijit.form.Button");

        function updateMail (mail) {
            document.getElementById('UserEmail').innerHTML =
                '<a href="mailto:' + mail + '">' + mail + '</a>';
        }
    </script>
    
    <!-- Edit user account form -->
    <div class="grid_11 alpha profile">
        <div class="profile_id clearfix">
            <h1 class="profile_nick" id="UserLongname">{$user->longname}</h1>
            <div class="profile_info">
                <br />
                <img src="/skins/VacuumCleanerBridge/images/mail.png" title="{#Mail#}" alt="{#MailAlt#}" align="top" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span id="UserEmail">{mailto address=$user->email}</span>&nbsp;
            </div>
        </div>
        <div class="profile_separator"></div>
        <div class="profile_text">
            <br />
            <form dojoType="dijit.form.Form" name="UserAccount" method="POST">    
                <input type="hidden" name="UserAccount" value="1" />
                <div class="row">
                    <span class="firstLabel">{#Login#}</span>
                    {$user->username}
                </div>
                <div class="row">
                    <label class="firstLabel" for="longname">{#LongName#}</label>
                    <input type="text" id="longname" name="longname" class="long"
                            value="{$user->longname}"
                            dojoType="dijit.form.ValidationTextBox"
                            required="false"
                            onChange="document.getElementById('UserLongname').innerHTML = document.getElementById('longname').value;";
                    />
                </div> 
                <div class="row">
                    <label class="firstLabel" for="realname">{#RealName#}</label>
                    <input type="text" id="realname" name="realname" class="long"
                            value="{$user->realname}"
                            dojoType="dijit.form.ValidationTextBox"
                            required="false" 
                    />
		    <span class="dojotooltip" dojoType="dijit.Tooltip" connectId="realname">{#RealNameToolTip#}</span>
                </div>
                <div class="row">
                    <label class="firstLabel" for="email">{#Mail#}</label>
                    <input type="text" id="email" name="email" class="long"
                            value="{$user->email}"
                            dojoType="dijit.form.ValidationTextBox"
                            required="false" 
                            onChange="javascript:updateMail(arguments[0]);"
                    />
                </div>
                <div class="row">
                    <button dojoType="dijit.form.Button" iconClass="dijitEditorIcon dijitEditorIconSave" type="submit" onclick="document.forms[0].submit()">
                            {#UpdateAccountInfo#}
                    </button>
                    <noscript>
                        <input type="submit" value="{#UpdateAccountInfo#} {#JavaScriptSafeMessage#}" />
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
		    <li><a href="{get_url('who')}/edit/profile">{#EditProfile#}</a></li>
		    <li>{#EditAccount#}</li>
		    <li><a href="{get_url('who')}/edit/photos">{if $PICS}{#ManagePhotos#}{else}{#AddPhoto#}{/if}</a></li>
		</ul>
	    </div>
            <div class="border_bottom"></div>
        </div>
    </div>