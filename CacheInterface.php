<?php

interface CacheInterface
{
	public function contains($key);
	
	public function getContent($key);
	
	public function save($key);
	
	public function flush($key);
}