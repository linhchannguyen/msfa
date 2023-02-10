<?php

namespace App\Repositories\Home;

use App\Repositories\Home\HomeRepository;
use Illuminate\Cache\CacheManager;

class HomeCacheDecorator {
    private $cache;
	private $repo;
	const TTL = 3600; // Time to life: second

	public function __construct(CacheManager $cache, HomeRepository $repo) {
		$this->cache = $cache;
		$this->repo = $repo;
	}

    public function get($page){
		$cacheKey = 'key_cache'; // unique name
		return $this->cache->remember($cacheKey, self::TTL, function () use ($page) {
			return $this->repo->getList($page);
		});
	}
}