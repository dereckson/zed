			<!-- Tags -->
			<div class="profile_separator_light"></div>
			<div class="profile_tags">
				<dl>
{foreach from=$tags key=class item=tags_class}
					<dt>{$class}</dt>
					<dd>{foreach from=$tags_class item=tag name=tags}<nobr>{$tag}</nobr>{if !$smarty.foreach.tags.last}, {/if}{/foreach}</dd>
{/foreach}
				</dl>
			</div>