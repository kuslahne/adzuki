<!DOCTYPE html>
<html>
	<head>
		<title>{{title}}</title>
		<link href="/css/home.css" rel="stylesheet">
		<link rel="stylesheet" href="/css/sakura.css" type="text/css">
	</head>
	<body>
		{{! The page header }}
		{{> header}}
		<div class="header">
			{{>nav}}
		</div>
		{{! The main content of the page }}
		{{#>content}}
		Content is not overridden
		{{/content}}
		{{! The page footer }}
		{{> footer}}
	</body>
</html>
