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
			'lang_cookie_name' => 'lang',
			'lang_cookie_duration' => 30 * 24 * 3600,
			'cache_lifetime_classic_view' => 12 * 3600,
			'cache_lifetime_top_six_months' => 12 * 3600,
			'cache_lifetime_top_alltime' => 12 * 3600,
			'cache_lifetime_top_bonus' => 24 * 3600,
			'cache_lifetime_five' => 12 * 3600,
		);
	}
	
	public function getValue($key)
	{
		return $this->siteConfig[$key];
	}	
}