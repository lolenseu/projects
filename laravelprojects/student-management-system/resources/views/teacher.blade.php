@extends('layout')
@section('title', 'Teacher')

@section('content')
<div class="teacher-page">
  <div class="teacher-header">
    <div style="display:flex;align-items:center;gap:12px;">
      <h2>Teacher List</h2>
      <a id="printCv" class="print-cv-link">Print CV</a>
    </div>
    <div class="header-actions">
      <label class="filter-label">Filter:</label>
      <select id="statusFilter" class="filter-select">
        <option value="all">All</option>
        <option value="GS">GS</option>
        <option value="CTE">CTE</option>
        <option value="CAS">CAS</option>
        <option value="CBME">CBME</option>
      </select>
      <label class="filter-label" for="searchInput">Search:</label>
      <input id="searchInput" class="search-input" type="text" placeholder="Search Teacher ID or Name">
      <button class="add-btn" id="openModalBtn">Add New</button>
    </div>
  </div>

  <div id="addModal" class="modal">
    <div class="modal-content">
      <span class="close-btn" id="closeModalBtn">&times;</span>
      <h3>Add New Teacher</h3>
      <form method="POST" action="{{ route('teacher.store') }}">
        @csrf
        <label class="modal-label">Details:</label>
        <input type="text" name="teacher_id" placeholder="Teacher ID" required>
        <input type="text" name="name" placeholder="Name" required>
        <input type="date" name="birthday" placeholder="B-Day">
        <input type="text" name="address" placeholder="Address">
        <input type="text" name="phone" placeholder="Phone">
        <input type="email" name="email" placeholder="Email">
        <label class="modal-label">Subjects:</label>
        <select class="teacher-select-subject" name="subject_id">
          <option value="">Select Subject (Optional)</option>
          @foreach($subjects as $subject)
            <option value="{{ $subject->id }}">{{ $subject->subject_name }} ({{ $subject->subject_code }})</option>
          @endforeach
        </select>
        <label class="modal-label">Department:</label>
        <select class="modal-department" name="department" required>
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
      <h3>Teacher Details</h3>
      <p><strong>Teacher ID:</strong> <span id="viewTeacherId"></span></p>
      <p><strong>Name:</strong> <span id="viewName"></span></p>
      <p><strong>B-Day:</strong> <span id="viewBirthday"></span></p>
      <p><strong>Address:</strong> <span id="viewAddress"></span></p>
      <p><strong>Phone:</strong> <span id="viewPhone"></span></p>
      <p><strong>Email:</strong> <span id="viewEmail"></span></p>
      <p><strong>Department:</strong> <span id="viewDepartment"></span></p>
      <p><strong>Assigned Subject:</strong> <span id="viewSubject"></span></p>
    </div>
  </div>

  <div id="editModal" class="modal">
    <div class="modal-content">
      <span class="close-btn" id="closeEditBtn">&times;</span>
      <h3>Edit Teacher</h3>
      <form id="editForm" method="POST">
        @csrf
        <label class="modal-label">Details:</label>
        <input type="text" name="teacher_id" id="editTeacherId" placeholder="Teacher ID" required>
        <input type="text" name="name" id="editName" placeholder="Name" required>
        <input type="date" name="birthday" id="editBirthday" placeholder="B-Day">
        <input type="text" name="address" id="editAddress" placeholder="Address">
        <input type="text" name="phone" id="editPhone" placeholder="Phone">
        <input type="email" name="email" id="editEmail" placeholder="Email">
        <label class="modal-label">Subjects:</label>
        <select class="teacher-select-subject" name="subject_id" id="editSubject">
          <option value="">Select Subject (Optional)</option>
          @foreach($subjects as $subject)
            <option value="{{ $subject->id }}">{{ $subject->subject_name }} ({{ $subject->subject_code }})</option>
          @endforeach
        </select>
        <label class="modal-label">Department:</label>
        <select class="modal-department" name="department" id="editDepartment" required>
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
      <p>Are you sure you want to delete this teacher details?</p>
      <form id="deleteForm" method="POST">
        @csrf
        <div class="delete-actions">
          <button type="submit" class="delete-confirm-btn">Delete</button>
          <button type="button" class="cancel-confirm-btn" id="cancelDeleteBtn">Cancel</button>
        </div>
      </form>
    </div>
  </div>

  <div class="teacher-container">
    <table>
      <thead>
        <tr>
          <th>Department</th>
          <th>Teacher ID</th>
          <th>Name</th>
          <th>Assigned Subject</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        @foreach($teachers as $teacher)
        <tr>
          <td class="department-cell">{{ $teacher->department }}</td>
          <td class="teacher-id-cell">{{ $teacher->teacher_id }}</td>
          <td class="name-cell">{{ $teacher->name }}</td>
          <td class="subject-cell">{{ $teacher->subject ? $teacher->subject->subject_name : 'Not assigned' }}</td>
          <td>
            <button type="button" class="view-btn" 
                data-teacher-id="{{ $teacher->teacher_id }}" 
                data-name="{{ $teacher->name }}" 
                data-birthday="{{ $teacher->birthday ?? '' }}" 
                data-address="{{ $teacher->address ?? '' }}" 
                data-phone="{{ $teacher->phone ?? '' }}" 
                data-email="{{ $teacher->email ?? '' }}" 
                data-subject="{{ $teacher->subject ? $teacher->subject->subject_name : 'Not assigned' }}" 
                data-department="{{ $teacher->department }}">View</button>
            <button type="button" class="edit-btn" 
                data-id="{{ $teacher->id }}"
                data-teacher-id="{{ $teacher->teacher_id }}" 
                data-name="{{ $teacher->name }}" 
                data-birthday="{{ $teacher->birthday ?? '' }}" 
                data-address="{{ $teacher->address ?? '' }}" 
                data-phone="{{ $teacher->phone ?? '' }}" 
                data-email="{{ $teacher->email ?? '' }}" 
                data-subject-id="{{ $teacher->subject_id }}" 
                data-department="{{ $teacher->department }}">Edit</button>
            <button type="button" class="delete-btn" data-id="{{ $teacher->id }}">Delete</button>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>

    @if($teachers->isEmpty())
    <div class="no-teachers">
      <p>No Teachers Found</p>
    </div>
    @endif
  </div>
</div>

<button id="backToTop" class="back-to-top">Back to Top</button>

<script src="{{ asset('js/teacher.js') }}"></script>
@endsection