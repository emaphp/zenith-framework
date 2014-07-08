<?php
return [
	'path' => function ($app) {
		return $app->path('logs', $app->getEnvironment() . '_' . date('Y-m-d') . '.log');
	}
];