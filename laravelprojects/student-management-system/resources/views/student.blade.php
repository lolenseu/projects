@extends('layout')
@section('title', 'Student')

@section('content')
<div class="student-page">
  <div class="student-header">
    <div style="display:flex;align-items:center;gap:12px;">
      <h2>Student</h2>
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
      <input id="searchInput" class="search-input" type="text" placeholder="Student ID or Name">
      <button class="add-btn" id="openModalBtn">Add New</button>
    </div>
  </div>

  <div id="addModal" class="modal">
    <div class="modal-content">
      <span class="close-btn" id="closeModalBtn">&times;</span>
      <h3>Add New Student</h3>
      <form method="POST" action="{{ route('student.store') }}">
        @csrf
        <label class="modal-label">Details:</label>
        <input type="text" name="student_id" placeholder="Student ID" required>
        <input type="text" name="name" placeholder="Name" required>
        <input type="date" name="birthday" placeholder="B-Day">
        <input type="text" name="address" placeholder="Address">
        <input type="text" name="phone" placeholder="Phone">
        <input type="email" name="email" placeholder="Email">
        <label class="modal-label">Subjects:</label>
        <select class="student-select-subject" name="subjects[]" multiple>
          <option value="">Select Subjects (Optional)</option>
          @foreach($subjects as $subject)
            <option value="{{ $subject->id }}">{{ $subject->subject_code }} - {{ $subject->subject_name }}</option>
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
      <h3>Student Details</h3>
      <p><strong>Student ID:</strong> <span id="viewStudentId"></span></p>
      <p><strong>Name:</strong> <span id="viewName"></span></p>
      <p><strong>B-Day:</strong> <span id="viewBirthday"></span></p>
      <p><strong>Address:</strong> <span id="viewAddress"></span></p>
      <p><strong>Phone:</strong> <span id="viewPhone"></span></p>
      <p><strong>Email:</strong> <span id="viewEmail"></span></p>
      <p><strong>Department:</strong> <span id="viewDepartment"></span></p>
      <p><strong>Enrolled Subjects:</strong></p>
      <div id="viewSubjects" style="margin-left: 20px;"></div>
    </div>
  </div>

  <div id="editModal" class="modal">
    <div class="modal-content">
      <span class="close-btn" id="closeEditBtn">&times;</span>
      <h3>Edit Student</h3>
      <form id="editForm" method="POST">
        @csrf
        <label class="modal-label">Details:</label>
        <input type="text" name="student_id" id="editStudentId" placeholder="Student ID" required>
        <input type="text" name="name" id="editName" placeholder="Name" required>
        <input type="date" name="birthday" id="editBirthday" placeholder="B-Day">
        <input type="text" name="address" id="editAddress" placeholder="Address">
        <input type="text" name="phone" id="editPhone" placeholder="Phone">
        <input type="email" name="email" id="editEmail" placeholder="Email">
        <label class="modal-label">Subjects:</label>
        <select class="student-select-subject" name="subjects[]" id="editSubjects" multiple>
          <option value="">Select Subjects (Optional)</option>
          @foreach($subjects as $subject)
            <option value="{{ $subject->id }}">{{ $subject->subject_code }} - {{ $subject->subject_name }}</option>
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

  <div class="student-container">
    <table>
      <thead>
        <tr>
          <th>Department</th>
          <th>Student ID</th>
          <th>Name</th>
          <th>Enrolled Subjects</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        @foreach($students as $student)
        <tr>
          <td class="department-cell">{{ $student->department }}</td>
          <td class="student-id-cell">{{ $student->student_id }}</td>
          <td class="name-cell">{{ $student->name }}</td>
          <td class="subjects-cell">
            @if($student->subjects)
              @php
                $subjectIds = json_decode($student->subjects, true) ?: [];
                $enrolledSubjects = DB::table('subjects')
                  ->whereIn('id', $subjectIds)
                  ->get();
                $subjectList = [];
                foreach($enrolledSubjects as $subj) {
                  $subjectList[] = $subj->subject_code . ' - ' . $subj->subject_name;
                }
              @endphp
              {{ implode(', ', $subjectList) }}
            @else
              No subjects enrolled
            @endif
          </td>
          <td>
            <button type="button" class="view-btn" 
                data-student-id="{{ $student->student_id }}" 
                data-name="{{ $student->name }}" 
                data-birthday="{{ $student->birthday ?? '' }}" 
                data-address="{{ $student->address ?? '' }}" 
                data-phone="{{ $student->phone ?? '' }}" 
                data-email="{{ $student->email ?? '' }}" 
                data-subjects="{{ $student->subjects ?? '' }}" 
                data-department="{{ $student->department }}">View</button>
            <button type="button" class="edit-btn" 
                data-id="{{ $student->id }}"
                data-student-id="{{ $student->student_id }}" 
                data-name="{{ $student->name }}" 
                data-birthday="{{ $student->birthday ?? '' }}" 
                data-address="{{ $student->address ?? '' }}" 
                data-phone="{{ $student->phone ?? '' }}" 
                data-email="{{ $student->email ?? '' }}" 
                data-subjects="{{ $student->subjects ?? '' }}" 
                data-department="{{ $student->department }}">Edit</button>
            <button type="button" class="delete-btn" data-id="{{ $student->id }}">Delete</button>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>

    @if($students->isEmpty())
    <div class="no-students">
      <p>No Students Found</p>
    </div>
    @endif
  </div>
</div>

<button id="backToTop" class="back-to-top">Back to Top</button>

<script src="{{ asset('js/student.js') }}"></script>
@endsection