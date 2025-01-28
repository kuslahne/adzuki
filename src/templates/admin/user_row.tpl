    <tr>
      <td>{{ counter }}</td>
      <td>{{ username }}</td>
      <td>{{#if is_active}}<span class="humbleicons--check"></span>{{else}}<span class="humbleicons--times"></span>{{/if}}</td>      
      <td>{{ last_login }}</td>
      <td>
      
        <a href="/admin/users/{{id}}" class="no-underline">
          <span class="humbleicons--pencil"></span> Edit
        </a> - 
        <a href="#" class="del-row"  data-bs-toggle="modal" data-bs-target="#confirmModal" class="no-underline" data-modal="{{username}}" data-id="{{id}}">
          <span class="mdi-light--delete"></span> Delete
        </a>
      </td>
    </tr>
