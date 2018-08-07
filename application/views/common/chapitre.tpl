<article>
<!--{include file="$lang/content/chapitres/$id.tpl"}-->
<nav>
<!--{if isset($prev)}-->
<a href="<!--{$prev}-->">&lt;==</a>
<!--{/if}-->
<!--{if isset($next)}-->
<a href="<!--{$next}-->">==&gt;</a>
<!--{/if}-->
</nav>
<!--{if !isset($nocomment)}-->
<section>
<!--{include file="common/comments.tpl"}-->
</section>
<!--{/if}-->
</article>