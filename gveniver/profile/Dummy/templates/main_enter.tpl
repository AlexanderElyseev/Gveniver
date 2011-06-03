<!DOCTYPE html>
<html>
	<head>
        <title>Dummy page</title>
        <link href="gveniver/profile/Dummy/styles/style.css" rel="stylesheet" />
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.5/jquery-ui.min.js"></script>
        <script type="text/javascript">
            {literal}
            $(function() {
                $('#id_link_trace').click(function(event) {
                    $('#id_div_trace').toggle();
                    event.preventDefault();
                });
            });
            {/literal}
        </script>
	</head>
	<body>
        This is dummy profile and its main page.<br/>
        <a href="main_enter.tpl#" id="id_link_trace">Trace</a>
        <div id="id_div_trace">
            {gv ext=GvDebugExt act=getTrace}
        </div>
        <br/>
	</body>
</html>