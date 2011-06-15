<!DOCTYPE html>
<html>
	<head>
        <title>Dummy page</title>

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
            var v = function() {
                if (document.getElementById("id_div_trace").style.display == 'none')
                    document.getElementById("id_div_trace").style.display = 'block';
                else
                    document.getElementById("id_div_trace").style.display = 'none'
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
        <a href="#" id="id_link_trace" onclick="v(); return false;">Trace</a>
        <div id="id_div_trace" style="display:none;">
            {gv ext=GvDebugExt act=getTrace}
        </div>
        <br/>
	</body>
</html>