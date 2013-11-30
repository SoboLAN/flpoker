<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
    <title>{html_title}</title>
    {css_files}
    <link rel="shortcut icon" href="favicon.ico" />
	{js_files}
</head>
<body>
	{google_analytics_script}
	<div id="container">
        <div id="logo"><img src="images/logofl.png" /></div>
            <ul class="claybricks">
				<li><a href="index.php" {selected_menu_home}>{menu_home}</a></li>
				<li><a href="players.php" {selected_menu_players}>{menu_players}</a></li>
				<li><a href="tournaments.php" {selected_menu_tournaments}>{menu_tournaments}</a></li>
				<li><a href="rankings.php" {selected_menu_rankings}>{menu_rankings}</a></li>
				<li><a href="statistics.php" {selected_menu_statistics}>{menu_statistics}</a></li>
				<li><a href="players.month.php" {selected_menu_players_of_the_month}>{menu_players_of_the_month}</a></li>
			</ul>
        <div id="language_panel">
			{language_panel_content}
        </div>
		<div id="title">{title}</div>
		<div id="{content_type_id}">
			{page_content}
		</div>
		<div id="footer">
			FileList Poker v{version} (currently in feature freeze).
			<br />
			Copyright &copy; 2013 Radu Murzea.
			<br />
			Project Author and Site Design: Radu Murzea.
		</div>
	</div>
	{bottom_page_scripts}
</body>
</html>