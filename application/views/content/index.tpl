<h1>Zarmina</h1>
<h2>par J. P. Savard</h2>
<section id="intro">
Zarmina est une histoire alliant comédie, science-fiction et fantasy qui prend place sur la planète du même nom. Découvrez les aventures de Miyuki et ses amis!
</section>

<h2>Chapitres</h2>
<section>
<!--{foreach $chapitres as $chapitre}-->
<a href="<!--{$url_base}-->chapitre/<!--{$chapitre}-->"><!--{$chapitre}--></a>
<!--{/foreach}-->
</section>

<h2>Nouvelles</h2>
<section id="news">
<!--{foreach $blogs as $blog}-->
<!--{include file="content/blog/$blog"}-->
<!--{/foreach}-->
</section>