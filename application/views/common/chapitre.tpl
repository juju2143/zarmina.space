<article>
<!--{include file="content/chapitres/$id.tpl"}-->
<nav>
<a href="<!--{$url_base}--><!--{$prev}-->">&lt;==</a>
<!--{$id}-->
<a href="<!--{$url_base}--><!--{$next}-->">==&gt;</a>
</nav>
<!--{if !isset($nocomment)}-->
<section>
<!--{include file="common/comments.tpl"}-->
</section>
<!--{/if}-->
</article>