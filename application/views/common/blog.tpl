<!--{foreach $blogs as $blog}-->
<!--{include file="$lang/content/blog/$blog.tpl"}-->
<!--{/foreach}-->
<nav>
<!--{if isset($prev)}-->
<a href="<!--{$url_lang}-->blog/<!--{$prev}-->">&lt;==</a>
<!--{/if}-->
<!--{if isset($next)}-->
<a href="<!--{$url_lang}-->blog/<!--{$next}-->">==&gt;</a>
<!--{/if}-->
</nav>