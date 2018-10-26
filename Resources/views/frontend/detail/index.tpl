{extends file='parent:frontend/detail/index.tpl'}

{* Itempropn NewCondition ist zur zeit nur für das Merchant-Center Interesannt und gibt an ob es Neu oder Gebrauchtware ist *}
{block name="frontend_detail_rich_snippets_release_date"}
    {$smarty.block.parent}
    {if !empty($sArticle.avh_google_itemconditon)}
        <link itemprop="itemCondition" content="https://schema.org/{$sArticle.avh_google_itemconditon}"/>
    {else}
        {* <link itemprop="itemCondition" content="https://schema.org/NewCondition"/> *}
    {/if}
{/block}
