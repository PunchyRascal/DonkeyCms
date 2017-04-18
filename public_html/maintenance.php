<?php

header('HTTP/1.1 503 Service Unavailable');
header('Retry-After: 300'); // 5 minutes in seconds

?>
<!doctype html>
<meta charset="utf-8">
<meta name="robots" content="noindex">
<meta name="generator" content="Nette Framework">

<style>
	body { color: #333; background: white; width: 500px; margin: 100px auto }
	h1 { font: bold 47px/1.5 sans-serif; margin: .6em 0 }
	p { font: 21px/1.5 Georgia,serif; margin: 1.5em 0 }
</style>

<title>Na webu právě probíhá technická údržba</title>

<h1>Omlouváme se</h1>

<p>
	Na našem webu právě probíhá technická údržba.
	Zkuste to prosím znovu za několik minut.
</p>

<?php

exit;
