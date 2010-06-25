    <!-- Add a photo -->
    <div class="grid_11 alpha profile">
        <div class="profile_id clearfix">
            <h1 class="profile_nick" id="UserLongname">{$USERNAME}</h1>
        </div>
        <div class="profile_separator"></div>
        <div class="profile_text">
            <h2>{#AddPhotoToProfile#}</h2>
            <form name="PhotoUpload" method="post" enctype="multipart/form-data">
                <p>{#AddPhotoExplanations#}</p>
                <p><label>Photo{#_t#}</label> <INPUT type="file" name="photo" /></p>
                <p><label>{#ShortDescription#}{#_t#}</label> <input type="text" maxlength="63" size="32" name="description"></p>
                <p><INPUT type="checkbox" name="SafeForWork" id="SafeForWork" value="0" /> <label for="SafeForWork">{#SafeForWorkLabel#}</label></p>
                <p><input type="submit" value="{#Upload#}" /></p>
            </form>
        </div>
    </div>
    
    <div class="grid_5 omega">
        <div class="sidebar_border"></div>
        <div id="sidebar" style="min-height: inherit;">
            <div class="border_top"></div>		
            <div class="sidebar_content">
		<h2>{#EditMyPage#}</h2>
		<ul>
                    <li><a href="{get_url('who')}/edit/profile">{#EditProfile#}</a></li>
                    <li><a href="{get_url('who')}/edit/account">{#EditAccount#}</a></li>
                    <li>{if $PICS}{#ManagePhotos#}{else}{#AddPhoto#}{/if}</a></li>
                </ul>
            </div>
            <div class="border_bottom"></div>
        </div>
    </div>
    
{if $PICS}
<!-- Manage current photos -->
<div class="grid_16 alpha omega profile_comments">
    <h2>{#ManageCurrentPhotos#}</h2>
    <div class="photos">
{foreach from=$PICS item=photo}
    <div class="photo" style="float: left">
        <a rel="lightbox" href="{$URL_PICS}/{$photo->name}" title="{$photo->description}"><img src="{$URL_PICS}/tn/{$photo->name}" alt="{$photo->description}" /></a>
        <br />
        <a href="{get_url('who')}/edit/photos/edit/{$photo->id}" title="{#EditPictureProperties#}"><img src="/skins/VacuumCleanerBridge/images/open.gif" alt="{#PictureProperties#}"></a>
        <a href="{get_url('who')}/edit/photos/delete/{$photo->id}" title="{#DeleteThisPicture#}"><img src="/skins/VacuumCleanerBridge/images/del.gif" alt="{#Delete#}"></a>
    </div>
{/foreach}
    </div>
{/if}
</div>