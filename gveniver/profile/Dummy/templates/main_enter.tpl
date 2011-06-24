<!DOCTYPE html>
<html>
	<head>
        <title>{gv ext=GvProfileExt act=getTitle} - {gv ext=GvProfileExt act=getSubTitle}</title>

        {gv ext=GvProfileExt act=getScripts var=scriptList}
        {foreach from=$scriptList item=script}
            <script type="text/javascript" src="{$script.FileName}"></script>
        {/foreach}

        {gv ext=GvProfileExt act=getStyles var=styleList}
        {foreach from=$styleList item=style}
            {if $style.Condition}
                <!--[if {$style.Condition}]><link rel="stylesheet" type="text/css" href="{$style.FileName}" /><![endif]-->
            {else}
                <link rel="stylesheet" type="text/css" href="{$style.FileName}" />
            {/if}
        {/foreach}

        <script type="text/javascript">
            {literal}
            var v = function(sId) {
                if (document.getElementById(sId).style.display == 'none')
                    document.getElementById(sId).style.display = 'block';
                else
                    document.getElementById(sId).style.display = 'none'
            };
            {/literal}
        </script>
        <meta http-equiv="Content-Type" content="{gv ext=GvProfileExt act=GetContentType}" />
		<meta name="keywords" content="{gv ext=GvProfileExt act=GetKeywords}" />
		<meta name="robots" content="{gv ext=GvProfileExt act=GetRobots}" />
		<meta name="author" content="{gv ext=GvProfileExt act=GetAuthor}" />
	</head>
	<body>
        This is dummy profile and its main page.<br/>
        <a href="/gv/">Enter</a> | <a href="/gv/action/v/">V</a> | <a href="/gv/section/index/">Index</a>
        <br/><br/>

        <div>
            <a href="#" onclick="v('id_div_trace'); return false;">Trace</a>
            <div id="id_div_trace" style="display:none;">
                {gv ext=GvDebugExt act=getTrace}
            </div>
        </div>

        <div>
            <a href="#" onclick="v('id_div_content'); return false;">Content</a>
            <div id="id_div_content" style="display:none;">
                {gv ext=GvProfileExt act=parseActTemplate}
            </div>
        </div>
	</body>
</html>