{$description = $section->description|trim}
{foreach from=$section->hooks item=hook}
{$hook->update_description($description)}
{/foreach}
    <!-- Story -->
    <div class="content_wrapper">
        <h1>{$PAGE_TITLE}</h1>
        <div class="content">
            <h2>{$section->title}</h2>
            <p>{$description|text2html}</p>
            <ul>
{if $section->choices}
{foreach from=$section->choices item=choice}
                <li><a href="{get_url('explore', $choice->guid)}">{$choice->text}</a></li>{/foreach}{/if}
{$links = array()}
{foreach from=$section->hooks item=hook}
{$hook->get_choices_links($links)}
{/foreach}
{foreach from=$links item=link}
                <li><a href="{$link[1]}">{$link[0]}</a></li>{/foreach}
            </ul>
{foreach from=$section->hooks item=hook}
{$hook->add_content()}
{/foreach}
        </div>
    </div>
    
{foreach from=$section->hooks item=hook}
{$hook->add_html()}
{/foreach}