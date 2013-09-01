<?php
class Config
{
	private static $__instance;
	
	private $siteConfig;
	
	public static function getConfig()
	{
		if (is_null(self::$__instance))
		{
			self::$__instance = new Config();
		}
		
		return self::$__instance;
	}
	
	private function __construct()
	{
		$this->siteConfig = array (
			'online' => true,
			'online_eta' => 'at 9 PM on Tuesday',
			
			'default_lang' => 'ro',
			'enable_google_analytics' => false,
			'enable_cache' => true,
			'cache_type' => 'db',
			
			'lang_cookie_name' => 'lang',
			'lang_cookie_duration' => 30 * 24 * 3600,
			
			'recaptcha_public_key' => '6LeaOOYSAAAAAKC2CLhZa1jXiSPm2gIc2k8M1qzq',
			'recaptcha_private_key' => '6LeaOOYSAAAAAAV0ARQJxLiL4_rPiLGXfsmNP_gh',
			'contact_email' => 'contact@flpoker.javafling.org',
			
			'cache_lifetime_players' => 24 * 3600,
			'cache_lifetime_player_general' => 12 * 3600,
			'cache_lifetime_players_alltime' => 24 * 3600,
			'cache_lifetime_players_mostactive' => 24 * 3600,
			'cache_lifetime_tournament_graph' => 24 * 3600,
			'cache_lifetime_general_stats' => 24 * 3600,
			'cache_lifetime_players_6months' => 24 * 3600,
			
			'cache_key_players' => 'players',
			'cache_key_player_general' => 'player_general_',
			'cache_key_players_alltime' => 'players_alltime',
			'cache_key_players_mostactive' => 'players_most_active',
			'cache_key_tournament_graph' => 'tournament_graph',
			'cache_key_general_stats' => 'general_stats',
			'cache_key_players_6months' => 'players_6months'
		);
	}
	
	public function getValue($key)
	{
		return isset($this->siteConfig[$key]) ? $this->siteConfig[$key] : null;		
	}	
}