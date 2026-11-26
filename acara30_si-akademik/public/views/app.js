function escapeHTML(teks) {
    const div = document.createElement('div');
    div.textContent = teks == null ? '' : String(teks);
    return div.innerHTML;
}

async function fetchWithRetry(url, options = {}, retries = 1) {
    let lastError;

    for (let attempt = 0; attempt <= retries; attempt += 1) {
        try {
            return await fetch(url, options);
        } catch (error) {
            lastError = error;
            if (attempt < retries) {
                await new Promise((resolve) => window.setTimeout(resolve, 500));
            }
        }
    }

    throw lastError;
}

async function muatDataMahasiswa() {
    const status = document.querySelector('#status');
    const totalMahasiswa = document.querySelector('#total-mahasiswa');
    const hasilDitampilkan = document.querySelector('#hasil-ditampilkan');
    const jumlahMahasiswa = document.querySelector('#jumlah-mahasiswa');
    const filterInput = document.querySelector('#filter-mahasiswa');
    const tbody = document.querySelector('#tabel-mahasiswa tbody');

    if (!status || !tbody) {
        return;
    }

    let dataMahasiswa = [];

    const renderRows = () => {
        const query = filterInput ? filterInput.value.trim().toLocaleLowerCase() : '';
        const filtered = dataMahasiswa.filter((mahasiswa) => {
            const searchable = `${mahasiswa.nim ?? ''} ${mahasiswa.nama ?? ''} ${mahasiswa.email ?? ''}`;
            return searchable.toLocaleLowerCase().includes(query);
        });

        tbody.replaceChildren();
        if (hasilDitampilkan) {
            hasilDitampilkan.textContent = String(filtered.length);
        }
        if (jumlahMahasiswa) {
            jumlahMahasiswa.textContent = `Menampilkan ${filtered.length} dari ${dataMahasiswa.length} mahasiswa`;
        }

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
            nimCell.textContent = escapeHTML(mahasiswa.nim ?? '');
            nameWrapper.className = 'd-flex align-items-center gap-2';
            avatar.className = 'student-avatar';
            avatar.setAttribute('aria-hidden', 'true');
            avatar.textContent = escapeHTML((mahasiswa.nama ?? '?').trim().slice(0, 1).toLocaleUpperCase());
            name.textContent = escapeHTML(mahasiswa.nama ?? '');
            nameWrapper.append(avatar, name);
            nameCell.appendChild(nameWrapper);
            emailCell.className = 'text-secondary';
            emailCell.textContent = escapeHTML(mahasiswa.email ?? '');

            row.append(numberCell, nimCell, nameCell, emailCell);
            tbody.appendChild(row);
        });
    };

    const loadData = async () => {
        status.className = 'alert alert-info';
        status.textContent = 'Memuat data mahasiswa...';

        try {
            const apiUrl = window.APP_API_URL || '../../api/mahasiswa.php';
            const response = await fetchWithRetry(apiUrl, {
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
            if (totalMahasiswa) {
                totalMahasiswa.textContent = String(dataMahasiswa.length);
            }
            status.className = 'visually-hidden';
            status.textContent = 'Data mahasiswa berhasil dimuat.';
            renderRows();
        } catch (error) {
            console.error('Gagal memuat data mahasiswa:', error);
            dataMahasiswa = [];
            if (totalMahasiswa) {
                totalMahasiswa.textContent = '0';
            }
            if (hasilDitampilkan) {
                hasilDitampilkan.textContent = '0';
            }
            if (jumlahMahasiswa) {
                jumlahMahasiswa.textContent = 'Data gagal dimuat';
            }
            status.className = 'alert alert-danger';
            status.textContent = `Gagal memuat data mahasiswa: ${error.message || 'Periksa koneksi API dan database.'}`;
            tbody.replaceChildren();
        }
    };

    if (filterInput) {
        filterInput.addEventListener('input', renderRows);
    }
    await loadData();
}

function pasangUmpanBalikForm() {
    document.querySelectorAll('form').forEach((form) => {
        const method = (form.getAttribute('method') || 'GET').toUpperCase();
        if (method !== 'POST') {
            return;
        }

        let feedback = form.querySelector('[data-form-feedback]');
        if (!feedback) {
            feedback = document.createElement('div');
            feedback.setAttribute('data-form-feedback', '');
            feedback.setAttribute('role', 'alert');
            feedback.className = 'alert d-none mt-3';
            form.appendChild(feedback);
        }

        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            const submitButton = form.querySelector('[type="submit"]');
            const originalText = submitButton ? submitButton.textContent : '';

            feedback.className = 'alert alert-info mt-3';
            feedback.textContent = 'Memproses permintaan...';
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.textContent = 'Memproses...';
            }

            try {
                const response = await fetchWithRetry(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: { Accept: 'text/html' },
                    credentials: 'same-origin'
                });

                if (!response.ok) {
                    throw new Error(`Server merespons dengan status ${response.status}.`);
                }

                if (response.redirected || response.url !== window.location.href) {
                    window.location.assign(response.url);
                    return;
                }

                feedback.className = 'alert alert-success mt-3';
                feedback.textContent = 'Permintaan berhasil diproses.';
            } catch (error) {
                console.error('Gagal mengirim form:', error);
                feedback.className = 'alert alert-danger mt-3';
                feedback.textContent = `Gagal memproses permintaan: ${error.message || 'Periksa koneksi jaringan.'}`;
            } finally {
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.textContent = originalText;
                }
            }
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    muatDataMahasiswa();
    pasangUmpanBalikForm();
});
