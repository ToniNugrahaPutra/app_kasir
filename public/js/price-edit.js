// Menambahkan baris baru pada tabel grosir
function addRow(tableId) {
    const tableBody = document.querySelector(`#${tableId} tbody`);
    const newRow = document.createElement('tr');
    const rowCount = tableBody.rows.length;

    newRow.innerHTML = `
        <td><input type="number" class="min-quantity form-control" name="${tableId}[${rowCount}][min_quantity]" required></td>
        <td><input type="text" class="price form-control" name="${tableId}[${rowCount}][price]" required></td>
        <td>
            <button type="button" class="btn" onclick="deleteRow(this)"><i class="fa-solid fa-trash"></i></button>
        </td>
    `;

    tableBody.appendChild(newRow);
}

// Menghapus baris dari tabel grosir
function deleteRow(button) {
    const row = button.closest('tr');
    row.remove();
}

// Event listener untuk tombol tambah grosir umum dan member
document.getElementById('add-grosir-umum').addEventListener('click', () => addRow('grosir-umum'));
document.getElementById('add-grosir-member').addEventListener('click', () => addRow('grosir-member'));

// Format angka ke dalam format Rupiah secara dinamis
function formatToRupiah(input) {
    $(input).on('keyup', (e) => {
        let value = e.target.value.replace(/[^0-9]/g, '');
        e.target.value = new Intl.NumberFormat('id-ID').format(value);
    });
}

document.querySelectorAll('.price').forEach(formatToRupiah);
