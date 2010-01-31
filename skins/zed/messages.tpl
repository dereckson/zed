

    <!-- Messages -->
    <div class="grid_16 alpha omega">
{foreach from=$MESSAGES item=message}
{$who = get_name($message->from)}
{$url = get_url('user', $who)}
        <div class="message {cycle values="light,dark"}">
            <div class="message_info"><a href="{$url}">{$who}</a> | {get_hypership_time($message->date)} | <a href="{$url}#Message">{#Reply#}</a> | <a href="?action=msg_delete&id={$message->id}" title="{#DeleteThisMessage#}">X</a></div>
            <div class="message_text">{$message->text|text2html}</div>
        </div>
{/foreach}
    </div>