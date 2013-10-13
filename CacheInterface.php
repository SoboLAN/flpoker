<?php

interface CacheInterface
{
	public function contains($key);
	
	public function getContent($key);
	
	public function save($key, $value, $lifetime);
	
	public function flush($key);
}