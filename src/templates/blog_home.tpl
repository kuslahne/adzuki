{{#>layout}}
	{{#*inline "content"}}
		This very new blog has been overriden

		<ul>
			{{#each Data}}
			<li>{{.}}</li>
			{{/each}}
		</ul>

	{{/inline}}
{{/layout}}
