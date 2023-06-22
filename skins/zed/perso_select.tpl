{include file="perso_header.tpl"}
<div class="grid_16">
    <h2>{#PickPerso#}</h2>
{foreach from=$PERSOS item=perso}
        <!-- {$perso->name} -->
        <div class="avatar">
            <a href="{get_url()}?action=perso.select&perso_id={$perso->id}">
{if $perso->avatar}
                <img src="{#StaticContentURL#}/content/users/_avatars/{$perso->avatar}" alt="Avatar of {$perso->name}" />
{else}
                <img src="{#StaticContentURL#}/img/misc/NoAvatar.png" alt="{#NoAvatar#}" />
{/if}
            </a>
            <span class="avatar_name"><a href="{get_url()}?action=perso.select&perso_id={$perso->id}">{$perso->name}</a></span>
        </div>

{/foreach}
{include file="perso_footer.tpl"}