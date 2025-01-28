<!DOCTYPE html>
<html>
	<head>
		<title>Admin - {{title}}</title>
		<link href="/css/admin.css" rel="stylesheet">
		<link href="/css/main.css" rel="stylesheet">
		<link rel="stylesheet" href="/css/sakura.css" type="text/css">
		<script src="https://uicdn.toast.com/editor/latest/toastui-editor-all.min.js"></script>
        <link rel="stylesheet" href="https://uicdn.toast.com/editor/latest/toastui-editor.min.css" />
	</head>
	<body>		
		{{! The page header }}
		{{> admin_header}}
		<div class="main">
		{{> admin_sidebar}}
		{{! The main content of the page }}
		{{#>content}}
		Content is not overridden
		{{/content}}
		</div>
	</body>
</html>
