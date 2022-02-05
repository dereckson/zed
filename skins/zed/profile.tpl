    <!--  Profile -->
	<div class="clear">&nbsp;</div>
	<div class="grid_11 alpha">
		<!-- Profile header -->
		<div class="profile_id clearfix">
			<h1 class="profile_nick">{$perso->name}</h1>
			<div class="profile_info">
				{$perso->location}&nbsp;<br />
				{if $perso->is_online()}{#Online#}{/if}
			</div>
		</div>
		<div class="clear">&nbsp;</div>
		<div class="profile">
{if isset($PICS)}
			<!-- Photos -->
			<div class="profile_photos">
{foreach from=$PICS item=photo}
				<a rel="lightbox" href="{$URL_PICS}/{$photo->name}" title="{$photo->description}"><img src="{$URL_PICS}/tn/{$photo->name}" alt="{$photo->description}" /></a>
{/foreach}
			</div>
{/if}
			<!-- Text -->
			<div class="profile_text{if $PROFILE_FIXEDWIDTH} fixedwidth{/if}">{if $PROFILE_TEXT != ""}{if $PROFILE_FIXEDWIDTH}{$PROFILE_TEXT}{else}{$PROFILE_TEXT|nl2br}{/if}{else}{if $PROFILE_SELF}<a href="{get_url('who')}/edit/profile">{/if}<img src="{#StaticContentURL#}/img/zed/empty_profile.png" width="642" height="392" alt="Be creative ! Fill this space with your best words." />{if $PROFILE_SELF}</a>{/if}{/if}</div>

{$PROFILE_TAGS}

			<!-- Leave a message -->
			<div class="profile_separator_light"></div>
			<div class="profile_message">
				<h2 id="Message">{#DropMessage#}</h2>
				<form method="post" action="{get_url('who')}/{$perso->nickname}">
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
	</div>

    <!-- User content -->
    <div class="grid_5 omega">
        <div class="sidebar_border"></div>
        <div id="sidebar">
            <div class="border_top"></div>
	    <div class="sidebar_content">

{if $PROFILE_SELF}
		<!-- {{counter name=section}|romanize}. edit profile, account, photos  -->
		<h2>{#EditMyPage#}</h2>
		<ul>
		    <li><a href="{get_url('who','edit','profile')}">{#EditProfile#}</a></li>
		    <li><a href="{get_url('settings','perso')}">{#EditAccount#}</a></li>
		    <li><a href="{get_url('who','edit','photos')}">{if isset($PICS)}{#ManagePhotos#}{else}{#AddPhoto#}{/if}</a></li>
		</ul>
{/if}
		<!-- {{counter name=section}|romanize}. sidebar placeholder/explanation -->
		<h2>Sidebar</h2>
		<p>Here will be published new art submission, request/offers post it, external content imported, etc.</p>

            </div>
            <div class="border_bottom"></div>
        </div>
    </div>

{if $PROFILE_COMMENTS}
    <!-- Profile comments -->
    <div class="grid_16 alpha omega profile_comments" id="comments" style="margin-bottom: 1em;">
{foreach from=$PROFILE_COMMENTS item=comment}
        <div class="comment black">
            <div class="profile_comments_text"><p>{$comment->text|nl2br}</p></div>
            <div class="profile_comments_info">-- <a href="{get_url('who')}/{$comment->author}">{$comment->authorname}</a>, {get_hypership_time($comment->date)}</div>
        </div>
{/foreach}
    </div>
{/if}
