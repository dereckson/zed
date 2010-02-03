    <div class="grid_11 alpha">
        <h1>{#EditPhoto#}</h1>
        <a rel="lightbox" href="{$URL_PICS}/{$photo->name}" title="{$photo->description}"><img src="{$URL_PICS}/tn/{$photo->name}" alt="{$photo->description}" /></a>
        <h2>{#PhotoInfo#}</h2>
        <form method=post><input type="hidden" name="id" value="{$photo->id}" />
        <blockquote>
        <table>
            <tr><td><strong><label for="description">{#Description#}</label></strong></td><td><input type='text' id='description' name='description' maxlength=63 value="{$photo->description}" /></td></tr>
            <tr><td><strong><label for="safe">{#SafeForWork#}</label></strong></td><td><input type='checkbox' id='safe' name='safe' maxlength=3 size=5 value=1{if $photo->safe} checked{/if} /></td></tr>
            <tr><td><strong><label for="avatar">{#UseAsAvatar#}</label></strong></td><td><input type='checkbox' id='avatar' name='avatar' maxlength=3 size=5 value=1{if $photo->avatar} checked{/if} /></td></tr>
            <tr><td>&nbsp;</td><td><input type="submit" value="{#Save#}" /></td></tr>
        </table>
        </blockquote>
        </form>
        <h2>{#OtherActions#}</h2>
        <ul>
            <li><a href="{$URL_USER}/edit/photos/delete/{$photo->id}" title="{#DeletePicture#}">{#DeletePicture#}</a></li>
            <li><a href="{$URL_USER}/edit/photos">{#BackToPhotoManager#}</a></li>
        </ul>
    </div>
    
    <div class="grid_5 omega">
        <div class="sidebar_border"></div>
        <div id="sidebar" style="min-height: inherit;">
            <div class="border_top"></div>		
            <div class="sidebar_content">
                <h2>{#EditMyPage#}</h2>
                <ul>
                    <li><a href="{$URL_USER}/edit/profile">{#EditProfile#}</a></li>
                    <li><a href="{$URL_USER}/edit/account">{#EditAccount#}</a></li>
                    <li>{if $PICS}{#ManagePhotos#}{else}{#AddPhoto#}{/if}</a></li>
                </ul>
            </div>
            <div class="border_bottom"></div>
        </div>
    </div>