    <tr>
      <td>{{ counter }}</td>
      <td>{{ name }}</td>
      <td>{{ description }}</td>      
      <td>
      
        <a href="/admin/{{../page}}/{{id}}" class="no-underline">
          <span class="humbleicons--pencil"></span> Edit
        </a> - 
        <a href="#" class="del-row"  data-bs-toggle="modal" data-bs-target="#confirmModal" class="no-underline" data-modal="{{name}}" data-id="{{id}}">
          <span class="mdi-light--delete"></span> Delete
        </a>
      </td>
    </tr>
