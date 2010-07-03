{include file="perso_header.tpl"}

<div class="grid_16">
    <img src="{#StaticContentURL#}/img/login/invite.png" title="{#CreateAccountImageTitle#}"  alt="{#CreateAccountImageAlt#}" align="right" />
    <h2>{#CreateAccountTitle#}</h2>
    <p><em>{#CreateAccountIntro#}</em></p>
    <!-- Edit Perso form -->
    <form dojoType="dijit.form.Form" id="AccountForm" method="post" execute="document.getElementById('AccountForm').submit()">
        <input type="hidden" name="form" value="account.create" />
        <div class="row">
            <label class="firstLabel" for="username">{#YourLogin#}</label>
            <input type="text" id="username" name="username" maxlength="11"
                   value="{$username}" dojoType="dijit.form.ValidationTextBox"
                   required="true" trim="true" lowercase="true" class="medium"
                   promptMessage="{#EnterUsernamePromptMessage#}"
            />
        </div>
        <div class="row">
            <label class="firstLabel" for="invite_code">{#InviteCode#}</label>
            <input type="text" id="invite_code" name="invite_code" maxlength="6"
                   value="{$invite_code}"  class="small"
                   dojoType="dijit.form.ValidationTextBox" uppercase="true"
                   regExp="{literal}[a-zA-Z]{3}[0-9]{3}{/literal}"
                   promptMessage="{#EnterInviteCodePromptMessage#}"
                   invalidMessage="{#IncorrectInviteCode#}"
            />
        </div>
        <div class="row">
            <label class="firstLabel" for="email">{#YourEmail#}</label>
            <input type="text" id="email" name="email" maxlength="63"
                   value="{$email}" class="long" required="true" trim="true"
                   regExpGen="dojox.validate.regexp.emailAddress"
                   dojoType="dijit.form.ValidationTextBox"
                   promptMessage="{#EnterEmailPromptMessage#}"
            />
        </div>
        <div class="row" dojoType="dojox.form.PasswordValidator" name="passwd">
            <label class="firstLabel">{#Password#}</label>
            <input type="password" pwType="new" />
            <br />
            <label class="firstLabel">{#Password#}</label>
            <input type="password" pwType="verify" />
        </div>
        <div class="row">
            <button dojoType="dijit.form.Button" type="submit" value="Save"
                    iconClass="dijitEditorIcon dijitEditorIconSave"
            />{#Save#}</button>
        </div>
    </form>
</div>
{include file="perso_footer.tpl"}