<!DOCTYPE html>
<html lang="<!--{$lang}-->">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Zarmina</title>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.2/css/all.css" integrity="sha384-/rXc/GQVaYpyDdyxK+ecHPVYJSN9bmVFBvjA/9eOB+pb3F2w2N6fc5qB9Ew5yIns" crossorigin="anonymous">
    <!--{if isset($a_CSS) && is_array($a_CSS)}-->
        <!--{foreach from=$a_CSS item=css}-->
            <!--{if is_array($css)}-->
                <link rel="<!--{$css.rel}-->" type="text/css" href="<!--{$css.file}-->.<!--{$css.extension}-->?v=<!--{$css.date}-->" media="all" />
            <!--{else}-->
                <link rel="stylesheet" type="text/css" href="<!--{$css}-->.css" media="all" />
            <!--{/if}-->
        <!--{/foreach}-->
    <!--{/if}-->
    <!--{if isset($a_embedded_javascript) && is_array($a_embedded_javascript)}-->
        <!--{foreach from=$a_embedded_javascript item=embeddedJS}-->
            <script type="text/javascript">
    <!--{$embeddedJS}-->
            </script>
        <!--{/foreach}-->
    <!--{/if}-->
</head>
<body>
<header>
<nav>
<!--{include file="$lang/common/nav.tpl"}-->
</nav>
</header>
<main>
<!--{include file=$template}-->
</main>
<footer>
<!--{include file="$lang/common/footer.tpl"}-->
</footer>
<!--{if isset($a_script) && is_array($a_script)}-->
    <!--{foreach from=$a_script item=script}-->
		<!--{if is_array($script)}-->
        	<script type="text/javascript" src="<!--{$script.file}-->.js?v=<!--{$script.date}-->"></script>
        <!--{else}-->
        	<script type="text/javascript" src="<!--{$script}-->.js"></script>
        <!--{/if}-->
    <!--{/foreach}-->
<!--{/if}-->
</body>
</html>