@extends('layout')
@section('title', 'Report')

@section('content')
<div class="report-page">
  <div class="report-header">
    <div style="display:flex;align-items:center;gap:12px;">
      <h2>Report</h2>
      <a id="printCv" class="print-cv-link">Print CV</a>
    </div>
  </div>

  <div class="report-container">
    <table>
      <thead>
        <tr>
          <th>Department</th>
          <th>Total Students</th>
          <th>Total Teachers</th>
          <th>Total Subjects</th>
        </tr>
      </thead>
      <tbody>
        @foreach($reports as $report)
        <tr>
          <td class="department-cell">{{ $report['department'] }}</td>
          <td class="student-count-cell">{{ $report['student_count'] }}</td>
          <td class="teacher-count-cell">{{ $report['teacher_count'] }}</td>
          <td class="subject-count-cell">{{ $report['subject_count'] }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>

    @if(empty($reports))
    <div class="no-reports">
      <p>No Reports Found</p>
    </div>
    @endif
  </div>
</div>

<button id="backToTop" class="back-to-top">Back to Top</button>

<script src="{{ asset('js/report.js') }}"></script>
@endsection