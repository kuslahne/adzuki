<?php $this->layout('admin::admin-layout', ['title' => 'Admin - Categories', 'page' => $page]) ?>

<h2>Categories <form action="/admin/categories" method="POST" style="float:right">
<button type="submit" class="btn btn-primary"><i class="bi bi-person-plus"></i> Add category</button></form></h2>
<table class="table table-hover">
  <thead>
    <tr>
      <th scope="col">#</th>
      <th scope="col">Name</th>
      <th scope="col">Description</th>
      <th scope="col"></th>
    </tr>
  </thead>
  <tbody>
    <?php 
      $i = $start;
      $zero = strlen($total);
    ?>
    <?php foreach($categories as $category): ?>
    <tr>
      <td><?= sprintf("%0{$zero}d", ++$i)?></td>
      <td><?= $this->e($category->name)?></td>
      <td><?= $this->e($category->description)?></td>
      <td>
      
        <a href="/admin/categories/<?= $this->e($category->id) ?>" class="no-underline">
          <i class="bi bi-pencil-square"></i> Edit
        </a> - 
        <a href="#" data-bs-toggle="modal" data-bs-target="#confirmModal" class="no-underline" data-title="<?= $this->e($category->name)?>" data-id="<?= $this->e($category->id)?>">
          <i class="bi bi-trash"></i> Delete
        </a>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<div class="row">
  <p>Total number of categories: <strong><?= $this->e($total)?></strong></p>
</div>
<nav aria-label="Page navigation">
  <ul class="pagination justify-content-center">
    <?php $this->insert('admin::pagination', [ 
      'start'    => $start, 
      'size'     => $size, 
      'total'    => $total,
      'url'      => '/admin/categories',
      'numItems' => 5
    ]); ?>
  </ul>
</nav>
<!-- Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmModalLabel">Confirm delete</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure to delete category `<span id="name"></span>`?<br />
        Please note, this action cannot be undone.
      </div>
      <div class="modal-footer">
        <div id="deleteError"></div>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-danger" id="confirmDelete" data-id="">Confirm</button>
      </div>
    </div>
  </div>
</div>

<?php $this->push('js') ?>
<script type="text/javascript">

  var confirmModal = document.getElementById('confirmModal')
  confirmModal.addEventListener('show.bs.modal', function (e) {
    document.getElementById('deleteError').style.visibility = "hidden"
    document.getElementById('confirmDelete').disabled = false
    document.getElementById('name').innerHTML = e.relatedTarget.attributes['data-name'].value;
    document.getElementById('confirmDelete').setAttribute('data-id', e.relatedTarget.attributes['data-id'].value)
  })

  var confirmDelete = document.getElementById('confirmDelete')
  confirmDelete.addEventListener('click', function(e) {
    var xhr = new XMLHttpRequest()
    xhr.open('DELETE', '/admin/categories/' + e.target.attributes['data-id'].value)
    xhr.onload = function () {
      if (xhr.readyState == 4 && xhr.status == "200") {
        location.reload()
      } else {
        var response = JSON.parse(xhr.responseText)
        document.getElementById('confirmDelete').disabled = true
        document.getElementById('deleteError').innerHTML = response.error
        document.getElementById('deleteError').style.visibility = "visible"
      }
    }
    xhr.send()
  })

</script>
<?php $this->end() ?>
