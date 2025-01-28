    <tr>
      <td>{{ counter }}</td>
      <td>{{ title }}</td>
      <td>{{#if is_published }}<span class="humbleicons--check"></span>{{else}}<span class="humbleicons--times"></span>{{/if}}</td>      
      <td>
      
        <a href="/admin/posts/{{id}}" class="no-underline">
          <span class="humbleicons--pencil"></span> Edit
        </a> - 
        <a href="#" class="del-row"  data-bs-toggle="modal" data-bs-target="#confirmModal" class="no-underline" data-modal="{{title}}" data-id="{{id}}">
          <span class="mdi-light--delete"></span> Delete
        </a>
      </td>
    </tr>
