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
        <header>
            <div id="logo"><img src="images/logofl.png" alt="Logo" /></div>
                <nav id="navigation-menu">
                    <a href="index.php" {selected_menu_home}>{menu_home}</a>
                    <a href="status.php" {selected_menu_status}>{menu_status}</a>
                    <a href="players.php" {selected_menu_players}>{menu_players}</a>
                    <a href="tournaments.php" {selected_menu_tournaments}>{menu_tournaments}</a>
                    <a href="rankings.php" {selected_menu_rankings}>{menu_rankings}</a>
                    <a href="statistics.php" {selected_menu_statistics}>{menu_statistics}</a>
                    <a href="players.month.php" {selected_menu_players_of_the_month}>{menu_players_of_the_month}</a>
                </nav>
            <div id="language_panel">
                {language_panel_content}
            </div>
            <div id="title">{title}</div>
        </header>
        <div id="{content_type_id}">
            {page_content}
        </div>
        <footer>
            FileList Poker v{version}
            <br />
            Project Author and Site Design: Radu Murzea.
            <br />
            <a href="https://github.com/SoboLAN/flpoker">GitHub Repository</a>
        </footer>
    </div>
    {bottom_page_scripts}
</body>
</html>