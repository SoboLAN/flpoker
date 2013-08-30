<?php

interface CacheInterface
{
	public function contains($key, $lifetime);
	
	public function getContent($key);
	
	public function save($key, $value);
	
	public function flush($key);
}