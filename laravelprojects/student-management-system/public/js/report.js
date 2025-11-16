document.addEventListener('DOMContentLoaded', function(){
  const printCv = document.getElementById('printCv');

  function getRows(){ return document.querySelectorAll('tbody tr'); }

  function buildPrintableHTML() {
    let rowsHtml = '';
    getRows().forEach(row => {
      const dept = row.querySelector('.department-cell')?.textContent.trim() || '';
      const studentCount = row.querySelector('.student-count-cell')?.textContent.trim() || '0';
      const teacherCount = row.querySelector('.teacher-count-cell')?.textContent.trim() || '0';
      const subjectCount = row.querySelector('.subject-count-cell')?.textContent.trim() || '0';
      rowsHtml += `<tr>
        <td style="padding:6px;border:1px solid #ccc;text-align:center">${dept}</td>
        <td style="padding:6px;border:1px solid #ccc;text-align:center">${studentCount}</td>
        <td style="padding:6px;border:1px solid #ccc;text-align:center">${teacherCount}</td>
        <td style="padding:6px;border:1px solid #ccc;text-align:center">${subjectCount}</td>
      </tr>`;
    });
    return `<!doctype html>
      <html>
        <head>
          <meta charset="utf-8">
          <title>Department Reports</title>
          <style>
            body{font-family:Arial,sans-serif;color:#222;padding:15px;font-size:12px;}
            table{border-collapse:collapse;width:100%;font-size:11px;}
            th{background:#f4f4f4;padding:8px;border:1px solid #ccc;text-align:center;font-weight:bold;}
            td{padding:6px;border:1px solid #ccc;}
            h2{text-align:center;margin-bottom:15px;font-size:16px;}
          </style>
        </head>
        <body>
          <h2>Report</h2>
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
});