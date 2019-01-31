{extends file='parent:frontend/detail/index.tpl'}

{* Itempropn NewCondition ist zur zeit nur für das Merchant-Center Interesannt und gibt an ob es Neu oder Gebrauchtware ist *}
{block name="frontend_detail_rich_snippets_release_date"}
    {$smarty.block.parent}
    {function name=GetSubcategorieById}
        {foreach from=$sCategorie key='k' item='i'}
            {if $k eq 'id'}
                {if $i eq $id}
                    {$avh_google_itemconditon=$sCategorie.attribute.avh_google_itemconditon}
                    {$sCategorie.attribute.avh_google_itemconditon}
                {/if}
            {/if}
        {/foreach}
        {if $sCategorie.subcategories|count > 1}
            {foreach from=$sCategorie.subcategories key='key' item='val'}
                {GetSubcategorieById sCategorie=$val id=$id}
            {/foreach}
        {/if}
    {/function}

    {if !empty($sArticle.avh_google_itemconditon)}
        {*Erst Article Prüfen*}
        <link itemprop="itemCondition" content="https://schema.org/{$sArticle.avh_google_itemconditon}"/>
    {else}
        {*Kategorie Prüfen*}
        {foreach $sCategories as $sCategorie}
            {$itemcondition = {GetSubcategorieById sCategorie=$sCategorie id=$sArticle.categoryID}}
            {if $itemcondition|strip|count_characters > 2}
                {$avh_google_itemconditon=$itemcondition|escape:'quotes'|strip|replace:" ":""}
            {/if}
        {/foreach}
        {if !empty($avh_google_itemconditon)}
            <link itemprop="itemCondition" content="https://schema.org/{$avh_google_itemconditon}"/>
        {/if}
    {/if}
{/block}
