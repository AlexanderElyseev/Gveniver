<!DOCTYPE html>
<html>
	<head>
        <title>{gv ext=ProfileExt act=getTitle} - {gv ext=ProfileExt act=getSubTitle}</title>
        {gv ext=ProfileExt act=getScripts}
        {gv ext=ProfileExt act=getStyles}
{*        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>*}
{*        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.5/jquery-ui.min.js"></script>*}
        <script type="text/javascript">
            {literal}
            $(function() {
                $('#id_div_trace').hide();
                $('#id_link_trace').click(function(event) {
                    $('#id_div_trace').toggle();
                    event.preventDefault();
                });
            });
            {/literal}
        </script>
        <meta http-equiv="Content-Type" content="{gv ext=ProfileExt act=GetContentType}" />
		<meta name="keywords" content="{gv ext=ProfileExt act=GetKeywords}" />
		<meta name="robots" content="{gv ext=ProfileExt act=GetRobots}" />
		<meta name="author" content="{gv ext=ProfileExt act=GetAuthor}" />
	</head>
	<body>
        This is dummy profile and its main page.<br/>
        INDEX!<br/>
        <a href="#" id="id_link_trace">Trace</a>
        <div id="id_div_trace">
            {gv ext=DebugExt act=getTrace}
        </div>
        <br/>
	</body>
</html>