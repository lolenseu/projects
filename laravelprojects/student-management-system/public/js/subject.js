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
      const codeCell = row.querySelector('.subject-code-cell');
      const nameCell = row.querySelector('.subject-name-cell');
      if (!deptCell || !codeCell || !nameCell) return;
      const dept = deptCell.textContent.trim();
      const code = codeCell.textContent.trim().toLowerCase();
      const name = nameCell.textContent.trim().toLowerCase();
      const matchesFilter = (selected === 'all' || dept === selected);
      const matchesSearch = q === '' || code.includes(q) || name.includes(q);
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
      const code = row.querySelector('.subject-code-cell')?.textContent.trim() || '';
      const name = row.querySelector('.subject-name-cell')?.textContent.trim() || '';
      const dept = row.querySelector('.department-cell')?.textContent.trim() || '';
      const credits = row.querySelector('.credits-cell')?.textContent.trim() || '';
      rowsHtml += `<tr><td style="padding:8px;border:1px solid #ccc;">${code}</td><td style="padding:8px;border:1px solid #ccc;">${name}</td><td style="padding:8px;border:1px solid #ccc;">${dept}</td><td style="padding:8px;border:1px solid #ccc;">${credits}</td></tr>`;
    });
    return `<!doctype html>
      <html>
        <head>
          <meta charset="utf-8">
          <title>Subject List</title>
          <style>
            body{font-family:Arial,sans-serif;color:#222;padding:20px;}
            table{border-collapse:collapse;width:100%;}
            th{background:#f4f4f4;padding:10px;border:1px solid #ccc;text-align:left;}
          </style>
        </head>
        <body>
          <h2>Subject List</h2>
          <table>
            <thead>
              <tr>
                <th style="padding:10px;border:1px solid #ccc;">Subject Code</th>
                <th style="padding:10px;border:1px solid #ccc;">Subject Name</th>
                <th style="padding:10px;border:1px solid #ccc;">Department</th>
                <th style="padding:10px;border:1px solid #ccc;">Credits</th>
              </tr>
            </thead>
            <tbody>
              ${rowsHtml || '<tr><td colspan="4" style="padding:8px;border:1px solid #ccc;">No records</td></tr>'}
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
      const editSubjectCode = document.getElementById('editSubjectCode');
      const editSubjectName = document.getElementById('editSubjectName');
      const editDescription = document.getElementById('editDescription');
      const editCredits = document.getElementById('editCredits');
      const editDepartment = document.getElementById('editDepartment');
      if (editForm) editForm.action = `/subject/update/${id}`;
      if (editSubjectCode) editSubjectCode.value = btn.getAttribute('data-subject-code') || '';
      if (editSubjectName) editSubjectName.value = btn.getAttribute('data-subject-name') || '';
      if (editDescription) editDescription.value = btn.getAttribute('data-description') || '';
      if (editCredits) editCredits.value = btn.getAttribute('data-credits') || '';
      if (editDepartment) editDepartment.value = btn.getAttribute('data-department') || 'GS';
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
      const viewSubjectCode = document.getElementById('viewSubjectCode');
      const viewSubjectName = document.getElementById('viewSubjectName');
      const viewDescription = document.getElementById('viewDescription');
      const viewCredits = document.getElementById('viewCredits');
      const viewDepartment = document.getElementById('viewDepartment');
      if (viewSubjectCode) viewSubjectCode.textContent = btn.getAttribute('data-subject-code') || '';
      if (viewSubjectName) viewSubjectName.textContent = btn.getAttribute('data-subject-name') || '';
      if (viewDescription) viewDescription.textContent = btn.getAttribute('data-description') || '';
      if (viewCredits) viewCredits.textContent = btn.getAttribute('data-credits') || '';
      if (viewDepartment) viewDepartment.textContent = btn.getAttribute('data-department') || '';
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
      if (deleteForm) deleteForm.action = `/subject/delete/${id}`;
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