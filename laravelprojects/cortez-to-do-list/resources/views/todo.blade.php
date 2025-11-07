@extends('layout')
@section('title', 'ToDo List')

@section('content')
<div class="todo-page">
  <div class="todo-header">
    <h2>ToDo List</h2>
    <div class="header-actions">
      <span class="filter-label">Filter:</span>
      <select id="statusFilter" class="filter-select">
        <option value="all">All Tasks</option>
        <option value="Pending">Pending</option>
        <option value="Completed">Completed</option>
      </select>
      <button class="add-btn" id="openModalBtn">Add New</button>
    </div>
  </div>

  <!-- Add Modal -->
  <div id="addModal" class="modal">
    <div class="modal-content">
      <span class="close-btn" id="closeModalBtn">&times;</span>
      <h3>Add New Task</h3>
      <form method="POST" action="{{ route('todo.store') }}">
        @csrf
        <input type="text" name="task" placeholder="Task" required>
        <textarea name="description" placeholder="Description"></textarea>
        <button type="submit" class="submit-btn">Submit</button>
      </form>
    </div>
  </div>

  <!-- View Modal -->
  <div id="viewModal" class="modal">
    <div class="modal-content">
      <span class="close-btn" id="closeViewBtn">&times;</span>
      <h3>Task Details</h3>
      <p><strong>Task:</strong> <span id="viewTask"></span></p>
      <p><strong>Description:</strong></p>
      <p><span id="viewDescription"></span></p>
      <p><strong>Status:</strong> <span id="viewStatus"></span></p>
    </div>
  </div>

  <!-- Edit Modal -->
  <div id="editModal" class="modal">
    <div class="modal-content">
      <span class="close-btn" id="closeEditBtn">&times;</span>
      <h3>Edit Task</h3>
      <form id="editForm" method="POST">
        @csrf
        <input type="text" name="task" id="editTask" placeholder="Task" required>
        <textarea name="description" id="editDescription" placeholder="Description"></textarea>
        <select name="status" id="editStatus" required>
          <option value="Pending">Pending</option>
          <option value="Completed">Completed</option>
        </select>
        <button type="submit" class="submit-btn">Save Changes</button>
      </form>
    </div>
  </div>

  <!-- Delete Modal -->
  <div id="deleteModal" class="modal">
    <div class="modal-content">
      <span class="close-btn" id="closeDeleteBtn">&times;</span>
      <h3>Confirm Delete</h3>
      <p>Are you sure you want to delete this task?</p>
      <form id="deleteForm" method="POST">
        @csrf
        <div class="delete-actions">
          <button type="submit" class="delete-confirm-btn">Delete</button>
          <button type="button" class="cancel-confirm-btn" id="cancelDeleteBtn">Cancel</button>
        </div>
      </form>
    </div>
  </div>

  <div class="todo-container">
    <table>
      <thead>
        <tr>
          <th>Status</th>
          <th>Task</th>
          <th>Description</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        @foreach($todos as $todo)
        <tr>
          <td class="status-cell {{ strtolower($todo->status) }}">{{ $todo->status }}</td>
          <td>{{ $todo->task }}</td>
          <td class="description-cell" title="{{ $todo->description }}">
            {{ $todo->description }}
          </td>
          <td>
            <button 
              type="button" 
              class="view-btn"
              data-task="{{ $todo->task }}"
              data-description="{{ $todo->description }}"
              data-status="{{ $todo->status }}">
              View
            </button>

            <button 
              type="button" 
              class="edit-btn"
              data-id="{{ $todo->id }}"
              data-task="{{ $todo->task }}"
              data-description="{{ $todo->description }}"
              data-status="{{ $todo->status }}">
              Edit
            </button>

            <button 
              type="button" 
              class="delete-btn"
              data-id="{{ $todo->id }}">
              Delete
            </button>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>

    @if($todos->isEmpty())
    <div class="no-tasks">
      <p>No Tasks Found</p>
    </div>
    @endif
  </div>
</div>

<button id="backToTop" class="back-to-top">Back to Top</button>

<script>
  // Filter Tasks by Status
  const statusFilter = document.getElementById('statusFilter');
  const tableRows = document.querySelectorAll('tbody tr');

  statusFilter.addEventListener('change', () => {
    const selected = statusFilter.value;
    tableRows.forEach(row => {
      const statusCell = row.querySelector('td:first-child');
      if (!statusCell) return;

      const status = statusCell.textContent.trim();
      if (selected === 'all' || status === selected) {
        row.style.display = '';
      } else {
        row.style.display = 'none';
      }
    });
  });

  // Add Modal
  const addModal = document.getElementById('addModal');
  const openBtn = document.getElementById('openModalBtn');
  const closeBtn = document.getElementById('closeModalBtn');
  openBtn.onclick = () => addModal.style.display = 'block';
  closeBtn.onclick = () => addModal.style.display = 'none';

  // Edit Modal
  const editModal = document.getElementById('editModal');
  const closeEditBtn = document.getElementById('closeEditBtn');
  const editTask = document.getElementById('editTask');
  const editDescription = document.getElementById('editDescription');
  const editStatus = document.getElementById('editStatus');
  const editForm = document.getElementById('editForm');

  document.querySelectorAll('.edit-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.getAttribute('data-id');
      editForm.action = `/todo/update/${id}`;
      editTask.value = btn.getAttribute('data-task');
      editDescription.value = btn.getAttribute('data-description');
      editStatus.value = btn.getAttribute('data-status');
      editModal.style.display = 'block';
    });
  });

  closeEditBtn.onclick = () => editModal.style.display = 'none';

  // View Modal
  const viewModal = document.getElementById('viewModal');
  const closeViewBtn = document.getElementById('closeViewBtn');
  const viewTask = document.getElementById('viewTask');
  const viewDescription = document.getElementById('viewDescription');
  const viewStatus = document.getElementById('viewStatus');

  document.querySelectorAll('.view-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      viewTask.textContent = btn.getAttribute('data-task');
      viewDescription.textContent = btn.getAttribute('data-description');
      viewStatus.textContent = btn.getAttribute('data-status');
      viewModal.style.display = 'block';
    });
  });

  closeViewBtn.onclick = () => viewModal.style.display = 'none';

  // Delete Modal
  const deleteModal = document.getElementById('deleteModal');
  const deleteForm = document.getElementById('deleteForm');
  const closeDeleteBtn = document.getElementById('closeDeleteBtn');
  const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');

  document.querySelectorAll('.delete-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.getAttribute('data-id');
      deleteForm.action = `/todo/delete/${id}`;
      deleteModal.style.display = 'block';
    });
  });

  closeDeleteBtn.onclick = () => deleteModal.style.display = 'none';
  cancelDeleteBtn.onclick = () => deleteModal.style.display = 'none';

  // Close Modals When Clicking Outside
  window.onclick = (e) => {
    if (e.target == addModal) addModal.style.display = 'none';
    if (e.target == viewModal) viewModal.style.display = 'none';
    if (e.target == editModal) editModal.style.display = 'none';
    if (e.target == deleteModal) deleteModal.style.display = 'none';
  };

  // Back to Top Button Logic
  const backToTop = document.getElementById("backToTop");
  window.addEventListener("scroll", () => {
    if (window.scrollY > 200) {
      backToTop.style.display = "block";
    } else {
      backToTop.style.display = "none";
    }
  });
  backToTop.addEventListener("click", () => {
    window.scrollTo({ top: 0, behavior: "smooth" });
  });
</script>
@endsection
