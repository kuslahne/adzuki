<!DOCTYPE html>
<html>
	<head>
		<title>{{title}}</title>
		<link href="/css/home.css" rel="stylesheet">
		<link rel="stylesheet" href="/css/sakura.css" type="text/css">
	</head>
	<body>
		<div class="main">
		{{! The page header }}
		{{> header}}
		{{> sidebar}}
		{{! The main content of the page }}
		{{#>content}}
		Content is not overridden
		{{/content}}
		</div>
	</body>
</html>
