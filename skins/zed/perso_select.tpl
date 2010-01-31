{include file="perso_header.tpl"}
<div class="grid_16">
    <h2>{#PickPerso#}</h2>
{foreach from=$PERSOS item=perso}
        <!-- {$perso->name} -->
        <div class="avatar">
            <a href="/?action=perso.select&perso_id={$perso->id}">
{if $perso->avatar}
                <img src="/users/_avatars/{$perso->avatar}" />
{else}
                <img src="/img/misc/NoAvatar.png" alt="{#NoAvatar#}" />
{/if}
            </a>
            <span class="avatar_name"><a href="/?action=perso.select&perso_id={$perso->id}">{$perso->name}</a></span>
        </div>
        
{/foreach}
{include file="perso_footer.tpl"}