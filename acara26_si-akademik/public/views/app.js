async function muatDataMahasiswa() {
    const status = document.querySelector('#status');
    const totalMahasiswa = document.querySelector('#total-mahasiswa');
    const hasilDitampilkan = document.querySelector('#hasil-ditampilkan');
    const jumlahMahasiswa = document.querySelector('#jumlah-mahasiswa');
    const filterInput = document.querySelector('#filter-mahasiswa');
    const tbody = document.querySelector('#tabel-mahasiswa tbody');
    let dataMahasiswa = [];

    const renderRows = () => {
        const query = filterInput.value.trim().toLocaleLowerCase();
        const filtered = dataMahasiswa.filter((mahasiswa) => {
            const searchable = `${mahasiswa.nim ?? ''} ${mahasiswa.nama ?? ''} ${mahasiswa.email ?? ''}`;
            return searchable.toLocaleLowerCase().includes(query);
        });

        tbody.replaceChildren();
        hasilDitampilkan.textContent = String(filtered.length);
        jumlahMahasiswa.textContent = `Menampilkan ${filtered.length} dari ${dataMahasiswa.length} mahasiswa`;

        if (filtered.length === 0) {
            const row = document.createElement('tr');
            const cell = document.createElement('td');
            cell.colSpan = 4;
            cell.className = 'text-center text-secondary py-5';
            cell.textContent = query ? 'Tidak ada mahasiswa yang cocok dengan pencarian.' : 'Belum ada data mahasiswa.';
            row.appendChild(cell);
            tbody.appendChild(row);
            return;
        }

        filtered.forEach((mahasiswa, index) => {
            const row = document.createElement('tr');
            const numberCell = document.createElement('td');
            const nimCell = document.createElement('td');
            const nameCell = document.createElement('td');
            const emailCell = document.createElement('td');
            const nameWrapper = document.createElement('div');
            const avatar = document.createElement('span');
            const name = document.createElement('span');

            numberCell.className = 'ps-4 text-secondary';
            numberCell.textContent = String(index + 1);
            nimCell.className = 'text-nowrap fw-semibold';
            nimCell.textContent = mahasiswa.nim ?? '';
            nameWrapper.className = 'd-flex align-items-center gap-2';
            avatar.className = 'student-avatar';
            avatar.setAttribute('aria-hidden', 'true');
            avatar.textContent = (mahasiswa.nama ?? '?').trim().slice(0, 1).toLocaleUpperCase();
            name.textContent = mahasiswa.nama ?? '';
            nameWrapper.append(avatar, name);
            nameCell.appendChild(nameWrapper);
            emailCell.className = 'text-secondary';
            emailCell.textContent = mahasiswa.email ?? '';

            row.append(numberCell, nimCell, nameCell, emailCell);
            tbody.appendChild(row);
        });
    };

    const loadData = async () => {
        status.className = 'alert alert-info';
        status.textContent = 'Memuat data mahasiswa...';

        try {
            const response = await fetch('../../api/mahasiswa.php', {
                headers: { Accept: 'application/json' }
            });
            if (!response.ok) {
                throw new Error(`Permintaan API gagal dengan status ${response.status}.`);
            }

            const hasil = await response.json();
            if (!hasil.success || !Array.isArray(hasil.data)) {
                throw new Error(hasil.message || 'Format data mahasiswa tidak valid.');
            }

            dataMahasiswa = hasil.data;
            totalMahasiswa.textContent = String(dataMahasiswa.length);
            status.className = 'visually-hidden';
            status.textContent = 'Data mahasiswa berhasil dimuat.';
            renderRows();
        } catch (error) {
            console.error('Gagal memuat data mahasiswa:', error);
            dataMahasiswa = [];
            totalMahasiswa.textContent = '0';
            hasilDitampilkan.textContent = '0';
            jumlahMahasiswa.textContent = 'Data gagal dimuat';
            status.className = 'alert alert-danger';
            status.textContent = 'Gagal memuat data mahasiswa. Periksa koneksi API dan database.';
            tbody.replaceChildren();
        }
    };

    filterInput.addEventListener('input', renderRows);
    await loadData();
}

document.addEventListener('DOMContentLoaded', muatDataMahasiswa);
