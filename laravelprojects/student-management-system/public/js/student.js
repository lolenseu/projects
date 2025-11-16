document.addEventListener('DOMContentLoaded', function(){
  const statusFilter = document.getElementById('statusFilter');
  const searchInput = document.getElementById('searchInput');
  const printCv = document.getElementById('printCv');

  function getRows(){ return document.querySelectorAll('tbody tr'); }

  function filterRows() {
    const selected = statusFilter ? statusFilter.value : 'all';
    const q = (searchInput ? searchInput.value : '').trim().toLowerCase();
    getRows().forEach(row => {
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

  if (statusFilter) statusFilter.addEventListener('change', filterRows);
  if (searchInput) {
    searchInput.addEventListener('input', filterRows);
    searchInput.addEventListener('keyup', (e) => { if (e.key === 'Enter') filterRows(); });
  }

  function buildPrintableHTML() {
    let rowsHtml = '';
    getRows().forEach(row => {
      if (window.getComputedStyle(row).display === 'none') return;
      const dept = row.querySelector('.department-cell')?.textContent.trim() || '';
      const sid = row.querySelector('.student-id-cell')?.textContent.trim() || '';
      const name = row.querySelector('.name-cell')?.textContent.trim() || '';
      const subjects = row.querySelector('.subjects-cell')?.textContent.trim() || '';
      rowsHtml += `<tr>
        <td style="padding:6px;border:1px solid #ccc;text-align:center">${dept}</td>
        <td style="padding:6px;border:1px solid #ccc;text-align:left">${sid}</td>
        <td style="padding:6px;border:1px solid #ccc;text-align:left">${name}</td>
        <td style="padding:6px;border:1px solid #ccc;text-align:left">${subjects.replace(/, /g, ',\n')}</td>
      </tr>`;
    });
    return `<!doctype html>
      <html>
        <head>
          <meta charset="utf-8">
          <title>Student List</title>
          <style>
            body{font-family:Arial,sans-serif;color:#222;padding:15px;font-size:12px;}
            table{border-collapse:collapse;width:100%;font-size:11px;}
            th{background:#f4f4f4;padding:8px;border:1px solid #ccc;text-align:center;font-weight:bold;}
            td{padding:6px;border:1px solid #ccc;white-space:pre-line;}
            h2{text-align:center;margin-bottom:15px;font-size:16px;}
          </style>
        </head>
        <body>
          <h2>Student List</h2>
          <table>
            <thead>
              <tr>
                <th>Department</th>
                <th>Student ID</th>
                <th>Name</th>
                <th>Enrolled Subjects</th>
              </tr>
            </thead>
            <tbody>
              ${rowsHtml || '<tr><td colspan="4" style="padding:8px;border:1px solid #ccc;text-align:center">No records found</td></tr>'}
            </tbody>
          </table>
        </body>
      </html>`;
  }

  if (printCv) {
    printCv.addEventListener('click', function(){
      const html = buildPrintableHTML();
      const w = window.open('', '_blank');
      w.document.write(html);
      w.document.close();
      w.focus();
      setTimeout(() => { w.print(); w.close(); }, 300);
    });
  }

  const addModal = document.getElementById('addModal');
  const openBtn = document.getElementById('openModalBtn');
  const closeBtn = document.getElementById('closeModalBtn');
  if (openBtn) openBtn.addEventListener('click', () => addModal && (addModal.style.display = 'block'));
  if (closeBtn) closeBtn.addEventListener('click', () => addModal && (addModal.style.display = 'none'));

  const editForm = document.getElementById('editForm');
  document.querySelectorAll('.edit-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.getAttribute('data-id');
      const editStudentId = document.getElementById('editStudentId');
      const editName = document.getElementById('editName');
      const editBirthday = document.getElementById('editBirthday');
      const editAddress = document.getElementById('editAddress');
      const editPhone = document.getElementById('editPhone');
      const editEmail = document.getElementById('editEmail');
      const editDepartment = document.getElementById('editDepartment');
      const editSubjects = document.getElementById('editSubjects');
      
      if (editForm) editForm.action = `/student/update/${id}`;
      if (editStudentId) editStudentId.value = btn.getAttribute('data-student-id') || '';
      if (editName) editName.value = btn.getAttribute('data-name') || '';
      if (editBirthday) editBirthday.value = btn.getAttribute('data-birthday') || '';
      if (editAddress) editAddress.value = btn.getAttribute('data-address') || '';
      if (editPhone) editPhone.value = btn.getAttribute('data-phone') || '';
      if (editEmail) editEmail.value = btn.getAttribute('data-email') || '';
      if (editDepartment) editDepartment.value = btn.getAttribute('data-department') || 'GS';
      
      if (editSubjects) {
        const subjectsData = btn.getAttribute('data-subjects');
        if (subjectsData) {
          const subjectIds = JSON.parse(subjectsData);
          Array.from(editSubjects.options).forEach(option => {
            option.selected = subjectIds.includes(option.value);
          });
        }
      }
      
      const editModal = document.getElementById('editModal');
      if (editModal) editModal.style.display = 'block';
    });
  });

  const closeEditBtn = document.getElementById('closeEditBtn');
  if (closeEditBtn) closeEditBtn.addEventListener('click', () => {
    const editModal = document.getElementById('editModal');
    if (editModal) editModal.style.display = 'none';
  });

  document.querySelectorAll('.view-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const viewStudentId = document.getElementById('viewStudentId');
      const viewName = document.getElementById('viewName');
      const viewBirthday = document.getElementById('viewBirthday');
      const viewAddress = document.getElementById('viewAddress');
      const viewPhone = document.getElementById('viewPhone');
      const viewEmail = document.getElementById('viewEmail');
      const viewDepartment = document.getElementById('viewDepartment');
      const viewSubjects = document.getElementById('viewSubjects');
      
      if (viewStudentId) viewStudentId.textContent = btn.getAttribute('data-student-id') || '';
      if (viewName) viewName.textContent = btn.getAttribute('data-name') || '';
      if (viewBirthday) viewBirthday.textContent = btn.getAttribute('data-birthday') || '';
      if (viewAddress) viewAddress.textContent = btn.getAttribute('data-address') || '';
      if (viewPhone) viewPhone.textContent = btn.getAttribute('data-phone') || '';
      if (viewEmail) viewEmail.textContent = btn.getAttribute('data-email') || '';
      if (viewDepartment) viewDepartment.textContent = btn.getAttribute('data-department') || '';
      
      if (viewSubjects) {
        const subjectsData = btn.getAttribute('data-subjects');
        if (subjectsData && subjectsData !== '') {
          try {
            const subjectIds = JSON.parse(subjectsData);
            const subjectNames = [];
            subjectIds.forEach(id => {
              const subjectOption = document.querySelector(`#editSubjects option[value="${id}"]`);
              if (subjectOption) {
                subjectNames.push(subjectOption.textContent);
              }
            });
            viewSubjects.innerHTML = subjectNames.map(subject => `<div>${subject}</div>`).join('');
          } catch (e) {
            viewSubjects.textContent = 'Error loading subjects';
          }
        } else {
          viewSubjects.textContent = 'No subjects enrolled';
        }
      }
      
      const viewModal = document.getElementById('viewModal');
      if (viewModal) viewModal.style.display = 'block';
    });
  });

  const closeViewBtn = document.getElementById('closeViewBtn');
  if (closeViewBtn) closeViewBtn.addEventListener('click', () => {
    const viewModal = document.getElementById('viewModal');
    if (viewModal) viewModal.style.display = 'none';
  });

  document.querySelectorAll('.delete-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.getAttribute('data-id');
      const deleteForm = document.getElementById('deleteForm');
      if (deleteForm) deleteForm.action = `/student/delete/${id}`;
      const deleteModal = document.getElementById('deleteModal');
      if (deleteModal) deleteModal.style.display = 'block';
    });
  });

  const closeDeleteBtn = document.getElementById('closeDeleteBtn');
  const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
  if (closeDeleteBtn) closeDeleteBtn.addEventListener('click', () => { const m = document.getElementById('deleteModal'); if (m) m.style.display = 'none'; });
  if (cancelDeleteBtn) cancelDeleteBtn.addEventListener('click', () => { const m = document.getElementById('deleteModal'); if (m) m.style.display = 'none'; });

  window.addEventListener('click', (e) => {
    const targets = ['addModal','viewModal','editModal','deleteModal'];
    targets.forEach(id => { const el = document.getElementById(id); if (el && e.target === el) el.style.display = 'none'; });
  });

  const backToTop = document.getElementById("backToTop");
  if (backToTop) {
    window.addEventListener("scroll", () => { backToTop.style.display = window.scrollY > 200 ? "block" : "none"; });
    backToTop.addEventListener("click", () => { window.scrollTo({ top: 0, behavior: "smooth" }); });
  }

  filterRows();
});