    <!-- Story -->
    <div class="story">
        <h1>{$PAGE_TITLE}</h1>
        <h2>{$section->title}</h2>
        <p>{$section->description|trim|text2html}</p>
{if $section->choices}
        <ul>
{foreach from=$section->choices item=choice}
            <li><a href="{get_url('explore', $choice->guid)}">{$choice->text}</a></li>
{/foreach}
        </ul>
{/if}
    </div>