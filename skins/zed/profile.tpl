    <!--  Faerie profile -->
	<div class="grid_11 alpha profile" style="background-color: black">
        <div class="profile_id clearfix">
            <h1 class="profile_nick">{$NAME}</h1>
            <div class="profile_info">
{if $PHONE}
                <img src="/skins/VacuumCleanerBridge/images/tel.png" title="{#PhoneNumber#}" alt="{#PhoneNumberAlt#}" align="top" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {$PHONE}
{/if}
                <br />
{if $MAIL}
                <img src="/skins/VacuumCleanerBridge/images/mail.png" title="{#Mail#}" alt="{#MailAlt#}" align="top" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <a href="mailto:{$MAIL}">{$MAIL}</a>&nbsp;
{/if}
            </div>
        </div>
        <div class="profile_separator"></div>
{if $PICS}
        <div class="profile_photos">
{foreach from=$PICS item=photo}
            <a rel="lightbox" href="{$URL_PICS}/{$photo->name}" title="{$photo->description}"><img src="{$URL_PICS}/tn/{$photo->name}" alt="{$photo->description}" /></a>
{/foreach}
            <div class="photos_item"></div>
        </div>
{/if}
    <div class="profile_text{if $PROFILE_FIXEDWIDTH} fixedwidth{/if}">{if $PROFILE_TEXT != ""}{if $PROFILE_FIXEDWIDTH}{$PROFILE_TEXT}{else}{$PROFILE_TEXT|nl2br}{/if}{else}{if $PROFILE_SELF}<a href="{$URL_USER}/edit/profile">{/if}<img src="/skins/VacuumCleanerBridge/images/empty_profile.png" width="642" height="392" alt="Be creative ! Fill this space with your best words." />{if $PROFILE_SELF}</a>{/if}{/if}</div>
        <div class="profile_separator_light"></div>
        <div class="profile_message">
            <h2 id="Message">{#DropMessage#}</h2>
            <form method="post" action="{$URL_USER}/{$USERNAME}">
                    <div class="grid_4 alpha">
                        <input type="radio" name="message_type" value="private_message" checked onclick="document.getElementById('MessageSubmit').value = '{#Send#}';">{sprintf(#SendMessage#, $NAME)}
                    </div>
                    <div class="grid_6 omega">
                        <input type="radio" name="message_type" value="profile_comment" onclick="document.getElementById('MessageSubmit').value = '{#Publish#}';">{sprintf(#AddComment#, $NAME)}
                    </div>
                    <p><textarea rows="7" cols="64" name="message"></textarea></p>
                    <p><input id="MessageSubmit" type="submit" name="MessageSubmit" value="{#Send#}" /></p>
                
            </form>
        </div>
    </div>
    
    <!--  Faerie content -->
    <div class="grid_5 omega">
        <div class="sidebar_border"></div>
        <div id="sidebar">
            <div class="border_top"></div>		
	    <div class="sidebar_content">
		
{if $PROFILE_SELF}
		<!-- {{counter name=section}|romanize}. edit profile, account, photos  -->
		<h2>{#EditMyPage#}</h2>
		<ul>
		    <li><a href="{$URL_USER}/edit/profile">{#EditProfile#}</a></li>
		    <li><a href="{$URL_USER}/edit/account">{#EditAccount#}</a></li>
		    <li><a href="{$URL_USER}/edit/photos">{if $PICS}{#ManagePhotos#}{else}{#AddPhoto#}{/if}</a></li>
		</ul>
{/if}
{if $SIDEBAR_LASTPICS}

		<!-- {{counter name=section}|romanize}. content from http://photos.folleterre.org -->
		<h2>{#LastSharedPhotos#}</h2>
		<div style="text-align: center">
{foreach from=$SIDEBAR_LASTPICS item=pic}
		    <a href="{$pic->link}"><img src="{$pic->url}" /></a>
{/foreach}
		    <br />
		    <a href="{$SIDEBAR_LASTPICS_URL}">{#ViewRecentUploads#}</a>
		</div>
{/if}

		<!-- {{counter name=section}|romanize}. sidebar placeholder/explanation -->
		<h2>Sidebar</h2>
		<p>Here will be published new art submission, request/offers post it, external content imported, etc.</p>
{if $lift}

		<!-- {{counter name=section}|romanize}. lift request  -->
		<div class="postit_cyan lift">
		    <h2>{$NAME} needs a pickup for {$lift->event->name}</h2>
		    <p>{#IdealDate#}{#_t#} {$lift->date}<br />{#From#}{#_t#} {$lift->from}</p>
		    <p><a href="{$URL_EVENT}/{$lift->event->id}/travel?LiftID={$lift->id}">{#OfferLift#}</a></p>
		</div>
{/if}

            </div>
            <div class="border_bottom"></div>
        </div>
    </div>
    
{if $PROFILE_COMMENTS}    
    <!-- Profile comments -->
    <div class="grid_16 alpha omega profile_comments" id="comments">
{foreach from=$PROFILE_COMMENTS item=comment}
        <div class="comment">
            <div class="profile_comments_text"><p>{$comment->text|nl2br}</p></div>
            <div class="profile_comments_info">-- <a href="{$URL_USER}/{$comment->author}">{$comment->authorname}</a>, {$comment->date|date_format:"%Y-%m-%d %H:%M:%S"}.</div>
        </div>
{/foreach}
    </div>
{/if}
