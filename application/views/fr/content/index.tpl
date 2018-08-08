<h1>Zarmina<sup style="font-size:12pt;">v2.0</sup></h1>
<h2>par J. P. Savard</h2>
<section id="intro">
Zarmina est une histoire alliant comédie, science-fiction et fantasy qui prend place sur la planète du même nom. Découvrez les aventures de Miyuki et ses amis alors qu'ils rencontrent des choses et des situations qu'ils n'ont jamais vu avant et utilisent leur magie afin de trouver une solution viable!
</section>

<h2>Chapitres</h2>
<section>
<!--{foreach $chapitres as $chapitre}-->
<a href="<!--{$url_lang}-->chapitre/<!--{$chapitre}-->"><!--{$chapitre}--></a>
<!--{/foreach}-->
</section>

<h2>Nouvelles</h2>
<section id="news">
<!--{foreach $blogs as $blog}-->
<!--{include file="$lang/content/blog/$blog.tpl"}-->
<!--{/foreach}-->
</section>