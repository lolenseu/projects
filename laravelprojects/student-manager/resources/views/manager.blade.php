@extends('layout')
@section('title', 'Student Manager')

@section('content')
<div class="manager-page">
  <div class="manager-header">
    <h2>Student List</h2>
    <div class="header-actions">
      <label class="filter-label" for="searchInput">Search:</label>
      <input id="searchInput" class="search-input" type="text" placeholder="Search Student ID or Name">

      <label class="filter-label">Filter:</label>
      <select id="statusFilter" class="filter-select">
        <option value="all">All</option>
        <option value="GS">GS</option>
        <option value="CTE">CTE</option>
        <option value="CAS">CAS</option>
        <option value="CBME">CBME</option>
      </select>
      <button class="add-btn" id="openModalBtn">Add New</button>
    </div>
  </div>

  <div id="addModal" class="modal">
    <div class="modal-content">
      <span class="close-btn" id="closeModalBtn">&times;</span>
      <h3>Add New Student</h3>
      <form method="POST" action="{{ route('manager.store') }}">
        @csrf
        <input type="text" name="student_id" placeholder="Student ID" required>
        <input type="text" name="name" placeholder="Name" required>
        <input type="date" name="birthday" placeholder="B-Day" required>
        <input type="text" name="address" placeholder="Address" required>
        <input type="text" name="phone" placeholder="Phone" required>
        <input type="email" name="email" placeholder="Email" required>
        <select name="department" required>
          <option value="GS">GS</option>
          <option value="CTE">CTE</option>
          <option value="CAS">CAS</option>
          <option value="CBME">CBME</option>
        </select>
        <button type="submit" class="submit-btn">Submit</button>
      </form>
    </div>
  </div>

  <div id="viewModal" class="modal">
    <div class="modal-content">
      <span class="close-btn" id="closeViewBtn">&times;</span>
      <h3>Student Details</h3>
      <p><strong>Student ID:</strong> <span id="viewStudentId"></span></p>
      <p><strong>Name:</strong> <span id="viewName"></span></p>
      <p><strong>B-Day:</strong> <span id="viewBirthday"></span></p>
      <p><strong>Address:</strong> <span id="viewAddress"></span></p>
      <p><strong>Phone:</strong> <span id="viewPhone"></span></p>
      <p><strong>Email:</strong> <span id="viewEmail"></span></p>
      <p><strong>Department:</strong> <span id="viewDepartment"></span></p>
    </div>
  </div>

  <div id="editModal" class="modal">
    <div class="modal-content">
      <span class="close-btn" id="closeEditBtn">&times;</span>
      <h3>Edit Student</h3>
      <form id="editForm" method="POST">
        @csrf
        <input type="text" name="student_id" id="editStudentId" placeholder="Student ID" required>
        <input type="text" name="name" id="editName" placeholder="Name" required>
        <input type="date" name="birthday" id="editBirthday" placeholder="B-Day" required>
        <input type="text" name="address" id="editAddress" placeholder="Address" required>
        <input type="text" name="phone" id="editPhone" placeholder="Phone" required>
        <input type="email" name="email" id="editEmail" placeholder="Email" required>
        <select name="department" id="editDepartment" required>
          <option value="GS">GS</option>
          <option value="CTE">CTE</option>
          <option value="CAS">CAS</option>
          <option value="CBME">CBME</option>
        </select>
        <button type="submit" class="submit-btn">Save Changes</button>
      </form>
    </div>
  </div>

  <div id="deleteModal" class="modal">
    <div class="modal-content">
      <span class="close-btn" id="closeDeleteBtn">&times;</span>
      <h3>Confirm Delete</h3>
      <p>Are you sure you want to delete this student details?</p>
      <form id="deleteForm" method="POST">
        @csrf
        <div class="delete-actions">
          <button type="submit" class="delete-confirm-btn">Delete</button>
          <button type="button" class="cancel-confirm-btn" id="cancelDeleteBtn">Cancel</button>
        </div>
      </form>
    </div>
  </div>

  <div class="manager-container">
    <table>
      <thead>
        <tr>
          <th>Student ID</th>
          <th>Name</th>
          <th>Department</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        @foreach($managers as $manager)
        <tr>
          <td class="student-id-cell">{{ $manager->student_id }}</td>
          <td class="name-cell">{{ $manager->name }}</td>
          <td class="department-cell">{{ $manager->department }}</td>
          <td>
            <button 
              type="button" 
              class="view-btn"
              data-student-id="{{ $manager->student_id }}"
              data-name="{{ $manager->name }}"
              data-birthday="{{ $manager->birthday ?? '' }}"
              data-address="{{ $manager->address ?? '' }}"
              data-phone="{{ $manager->phone ?? '' }}"
              data-email="{{ $manager->email ?? '' }}"
              data-department="{{ $manager->department }}">
              View
            </button>

            <button 
              type="button" 
              class="edit-btn"
              data-id="{{ $manager->id }}"
              data-student-id="{{ $manager->student_id }}"
              data-name="{{ $manager->name }}"
              data-birthday="{{ $manager->birthday ?? '' }}"
              data-address="{{ $manager->address ?? '' }}"
              data-phone="{{ $manager->phone ?? '' }}"
              data-email="{{ $manager->email ?? '' }}"
              data-department="{{ $manager->department }}">
              Edit
            </button>

            <button 
              type="button" 
              class="delete-btn"
              data-id="{{ $manager->id }}">
              Delete
            </button>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>

    @if($managers->isEmpty())
    <div class="no-managers">
      <p>No Students Found</p>
    </div>
    @endif
  </div>
</div>

<button id="backToTop" class="back-to-top">Back to Top</button>

<script>
  const statusFilter = document.getElementById('statusFilter');
  const searchInput = document.getElementById('searchInput');
  const tableRows = document.querySelectorAll('tbody tr');

  function filterRows() {
    const selected = statusFilter.value;
    const q = (searchInput.value || '').trim().toLowerCase();
    tableRows.forEach(row => {
      const deptCell = row.querySelector('.department-cell');
      const idCell = row.querySelector('.student-id-cell');
      const nameCell = row.querySelector('.name-cell');
      if (!deptCell || !idCell || !nameCell) return;
      const dept = deptCell.textContent.trim();
      const sid = idCell.textContent.trim().toLowerCase();
      const name = nameCell.textContent.trim().toLowerCase();
      const matchesFilter = (selected === 'all' || dept === selected);
      const matchesSearch = q === '' || sid.includes(q) || name.includes(q);
      row.style.display = (matchesFilter && matchesSearch) ? '' : 'none';
    });
  }

  statusFilter.addEventListener('change', filterRows);
  searchInput.addEventListener('input', filterRows);
  searchInput.addEventListener('keyup', (e) => { if (e.key === 'Enter') filterRows(); });

  const addModal = document.getElementById('addModal');
  const openBtn = document.getElementById('openModalBtn');
  const closeBtn = document.getElementById('closeModalBtn');
  openBtn.onclick = () => addModal.style.display = 'block';
  closeBtn.onclick = () => addModal.style.display = 'none';

  const editModal = document.getElementById('editModal');
  const closeEditBtn = document.getElementById('closeEditBtn');
  const editStudentId = document.getElementById('editStudentId');
  const editName = document.getElementById('editName');
  const editBirthday = document.getElementById('editBirthday');
  const editAddress = document.getElementById('editAddress');
  const editPhone = document.getElementById('editPhone');
  const editEmail = document.getElementById('editEmail');
  const editDepartment = document.getElementById('editDepartment');
  const editForm = document.getElementById('editForm');

  document.querySelectorAll('.edit-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.getAttribute('data-id');
      editForm.action = `/manager/update/${id}`;
      editStudentId.value = btn.getAttribute('data-student-id') || '';
      editName.value = btn.getAttribute('data-name') || '';
      editBirthday.value = btn.getAttribute('data-birthday') || '';
      editAddress.value = btn.getAttribute('data-address') || '';
      editPhone.value = btn.getAttribute('data-phone') || '';
      editEmail.value = btn.getAttribute('data-email') || '';
      editDepartment.value = btn.getAttribute('data-department') || 'GS';
      editModal.style.display = 'block';
    });
  });

  closeEditBtn.onclick = () => editModal.style.display = 'none';

  const viewModal = document.getElementById('viewModal');
  const closeViewBtn = document.getElementById('closeViewBtn');
  const viewStudentId = document.getElementById('viewStudentId');
  const viewName = document.getElementById('viewName');
  const viewBirthday = document.getElementById('viewBirthday');
  const viewAddress = document.getElementById('viewAddress');
  const viewPhone = document.getElementById('viewPhone');
  const viewEmail = document.getElementById('viewEmail');
  const viewDepartment = document.getElementById('viewDepartment');

  document.querySelectorAll('.view-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      viewStudentId.textContent = btn.getAttribute('data-student-id') || '';
      viewName.textContent = btn.getAttribute('data-name') || '';
      viewBirthday.textContent = btn.getAttribute('data-birthday') || '';
      viewAddress.textContent = btn.getAttribute('data-address') || '';
      viewPhone.textContent = btn.getAttribute('data-phone') || '';
      viewEmail.textContent = btn.getAttribute('data-email') || '';
      viewDepartment.textContent = btn.getAttribute('data-department') || '';
      viewModal.style.display = 'block';
    });
  });

  closeViewBtn.onclick = () => viewModal.style.display = 'none';

  const deleteModal = document.getElementById('deleteModal');
  const deleteForm = document.getElementById('deleteForm');
  const closeDeleteBtn = document.getElementById('closeDeleteBtn');
  const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');

  document.querySelectorAll('.delete-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.getAttribute('data-id');
      deleteForm.action = `/manager/delete/${id}`;
      deleteModal.style.display = 'block';
    });
  });

  closeDeleteBtn.onclick = () => deleteModal.style.display = 'none';
  cancelDeleteBtn.onclick = () => deleteModal.style.display = 'none';

  window.onclick = (e) => {
    if (e.target == addModal) addModal.style.display = 'none';
    if (e.target == viewModal) viewModal.style.display = 'none';
    if (e.target == editModal) editModal.style.display = 'none';
    if (e.target == deleteModal) deleteModal.style.display = 'none';
  };

  const backToTop = document.getElementById("backToTop");
  window.addEventListener("scroll", () => {
    backToTop.style.display = window.scrollY > 200 ? "block" : "none";
  });
  backToTop.addEventListener("click", () => {
    window.scrollTo({ top: 0, behavior: "smooth" });
  });
</script>
@endsection