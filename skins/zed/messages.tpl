

    <!-- Messages -->
    <div class="grid_16 alpha omega">
{foreach from=$MESSAGES item=message}
{if $message->source == Zed\Models\MessageSource::System}
        <div class="message red">
            <div class="message_info">{#SystemNotice#} | {get_hypership_time($message->date)} | <a href="?action=msg_delete&id={$message->id}" title="{#DeleteThisMessage#}">X</a></div>
            <div class="message_text">{$message->text|text2html}</div>
        </div>
{else}
{$who = $message->perso->name}
{$url = get_url('who', $message->perso->nickname)}
        <div class="message {cycle values="light,dark"}">
            <div class="message_info"><a href="{$url}">{$who}</a> | {get_hypership_time($message->date)} | <a href="{$url}#Message">{#Reply#}</a> | <a href="?action=msg_delete&id={$message->id}" title="{#DeleteThisMessage#}">X</a></div>
            <div class="message_text">{$message->text|text2html}</div>
        </div>
{/if}
{/foreach}
    </div>