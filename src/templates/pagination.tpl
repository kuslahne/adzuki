<nav aria-label="Page navigation">
  <ul class="pagination pagination-centered">
	{{#if pagination.prev}}
	  <li class="page-item">
		  <a href="{{pagination.prev.url}}" class="page-link">Prev</a>
	  </li>
	{{/if}}
	
	{{#if pagination.pages}}
		{{#each pagination.pages}}
			<li {{#if current}}class="page-item active"{{/if}}>
				{{#if current}}
					<div class="page-link">{{name}}</div>
				{{else}}
					<a href="{{url}}" class="page-link">{{name}}</a>
				{{/if}}				
			</li>
		{{/each}}
	{{/if}}
	
	{{#if pagination.next}}
	  <li class="page-item">
		  <a href="{{pagination.next.url}}" class="page-link">Next</a>
	  </li>
	{{/if}}
  </ul>
</nav>
