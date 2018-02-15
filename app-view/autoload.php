<?php
$base = __DIR__ . '/../app-view/';

$folders = [
	'Controller',
	'Model'
];

foreach($folders as $f)
{
	foreach (glob($base . "$f/*.php") as $k => $filename)
	{
		require $filename;
	}
}
