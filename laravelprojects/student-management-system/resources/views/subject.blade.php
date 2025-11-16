@extends('layout')
@section('title', 'Subject')

@section('content')
<div class="subject-page">
  <div class="subject-header">
    <div style="display:flex;align-items:center;gap:12px;">
      <h2>Subject</h2>
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
      <input id="searchInput" class="search-input" type="text" placeholder="Subject Code or Name">
      <button class="add-btn" id="openModalBtn">Add New</button>
    </div>
  </div>

  <div id="addModal" class="modal">
    <div class="modal-content">
      <span class="close-btn" id="closeModalBtn">&times;</span>
      <h3>Add New Subject</h3>
      <form method="POST" action="{{ route('subject.store') }}">
        @csrf
        <label class="modal-label">Details:</label>
        <input type="text" name="subject_code" placeholder="Subject Code" required>
        <input type="text" name="subject_name" placeholder="Subject Name" required>
        <input type="text" name="description" placeholder="Description">
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
      <h3>Subject Details</h3>
      <p><strong>Subject Code:</strong> <span id="viewSubjectCode"></span></p>
      <p><strong>Subject Name:</strong> <span id="viewSubjectName"></span></p>
      <p><strong>Description:</strong> <span id="viewDescription"></span></p>
      <p><strong>Department:</strong> <span id="viewDepartment"></span></p>
      <p><strong>Assigned Teacher:</strong> <span id="viewTeacher"></span></p>
    </div>
  </div>

  <div id="editModal" class="modal">
    <div class="modal-content">
      <span class="close-btn" id="closeEditBtn">&times;</span>
      <h3>Edit Subject</h3>
      <form id="editForm" method="POST">
        @csrf
        <label class="modal-label">Details:</label>
        <input type="text" name="subject_code" id="editSubjectCode" placeholder="Subject Code" required>
        <input type="text" name="subject_name" id="editSubjectName" placeholder="Subject Name" required>
        <input type="text" name="description" id="editDescription" placeholder="Description">
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
      <p>Are you sure you want to delete this subject?</p>
      <form id="deleteForm" method="POST">
        @csrf
        <div class="delete-actions">
          <button type="submit" class="delete-confirm-btn">Delete</button>
          <button type="button" class="cancel-confirm-btn" id="cancelDeleteBtn">Cancel</button>
        </div>
      </form>
    </div>
  </div>

  <div class="subject-container">
    <table>
      <thead>
        <tr>
          <th>Department</th>
          <th>Subject Code</th>
          <th>Subject Name</th>
          <th>Assigned Teacher</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        @foreach($subjects as $subject)
        <tr>
          <td class="department-cell">{{ $subject->department }}</td>
          <td class="subject-code-cell">{{ $subject->subject_code }}</td>
          <td class="subject-name-cell">{{ $subject->subject_name }}</td>
          <td class="teacher-cell">{{ $subject->teacher ? $subject->teacher->name : 'Not assigned' }}</td>
          <td>
            <button type="button" class="view-btn" data-subject-code="{{ $subject->subject_code }}" data-subject-name="{{ $subject->subject_name }}" data-description="{{ $subject->description ?? '' }}" data-department="{{ $subject->department }}" data-teacher="{{ $subject->teacher ? $subject->teacher->name : 'Not assigned' }}">View</button>
            <button type="button" class="edit-btn" data-id="{{ $subject->id }}" data-subject-code="{{ $subject->subject_code }}" data-subject-name="{{ $subject->subject_name }}" data-description="{{ $subject->description ?? '' }}" data-department="{{ $subject->department }}">Edit</button>
            <button type="button" class="delete-btn" data-id="{{ $subject->id }}">Delete</button>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>

    @if($subjects->isEmpty())
    <div class="no-subjects">
      <p>No Subjects Found</p>
    </div>
    @endif
  </div>
</div>

<button id="backToTop" class="back-to-top">Back to Top</button>

<script src="{{ asset('js/subject.js') }}"></script>
@endsection