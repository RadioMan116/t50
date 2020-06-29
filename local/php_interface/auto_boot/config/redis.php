<?php

if( ENV == "DEV" )
	return array(
		"scheme" => "tcp",
		"host" => "127.0.0.1",
		"port" => 6379
	);

return array(
	"scheme" => "unix",
	"path" => "/var/run/redis/redis.sock",
	"host" => "127.0.0.1",
	"port" => 6379
);