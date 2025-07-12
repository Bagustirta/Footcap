import pandas as pd
import random
from itertools import product
from collections import defaultdict

# Variabel dasar
tahun_pendaftaran = [2020, 2021, 2022, 2023, 2024]
jenis_kelamin = ['Laki-laki', 'Perempuan']
asal_sekolah = ['SMPN 1 DPS, SMPN 1 KUTA', 'SMP SANTO YOSEPH', 'SMPN 7 DPS', 'SMP JEMBATAN BUDAYA']
wilayah_domisili = ['Badung', 'Denpasar']
jenis_pendaftaran = ['Afirmasi', 'Prestasi']
status_difabel = ['Ya', 'Tidak']
pilihan_jurusan = ['RPL', 'TKJ', 'MM']
status_penerimaan = ['Diterima', 'Ditolak']
jenis_sekolah_asal = ['Negeri', 'Swasta']

# Buat semua kombinasi kategori (selain tahun)
kategori_kombinasi = list(product(
    jenis_kelamin,
    asal_sekolah,
    wilayah_domisili,
    jenis_pendaftaran,
    status_difabel,
    pilihan_jurusan,
    status_penerimaan,
    jenis_sekolah_asal
))

data = []
diterima_counter = defaultdict(int)  # Hitung total siswa diterima per (tahun, jurusan, gender)

# Tahap 1: Hitung jumlah siswa diterima
for tahun in tahun_pendaftaran:
    for combo in kategori_kombinasi:
        jk, sekolah, domisili, jenis_daftar, difabel, jurusan, status, jenis_sekolah = combo
        jumlah = 0  # default 0

        if status == 'Diterima':
            # TKJ: 85% laki-laki, 15% perempuan
            if jurusan == 'TKJ':
                if jk == 'Laki-laki' and random.random() < 0.85:
                    jumlah = random.randint(2, 4)
                elif jk == 'Perempuan' and random.random() < 0.15:
                    jumlah = random.randint(1, 2)

            # RPL: 75% laki-laki, 25% perempuan
            elif jurusan == 'RPL':
                if jk == 'Laki-laki' and random.random() < 0.75:
                    jumlah = random.randint(1, 3)
                elif jk == 'Perempuan' and random.random() < 0.25:
                    jumlah = random.randint(1, 3)

            # MM: 73% laki-laki, 27% perempuan
            elif jurusan == 'MM':
                if jk == 'Laki-laki' and random.random() < 0.73:
                    jumlah = random.randint(1, 2)
                elif jk == 'Perempuan' and random.random() < 0.27:
                    jumlah = random.randint(2, 4)

            # Hitung total diterima
            diterima_counter[(tahun, jurusan, jk)] += jumlah

        # Simpan baris data
        data.append({
            'Tahun Pendaftaran': tahun,
            'Jenis Kelamin': jk,
            'Asal Sekolah': sekolah,
            'Wilayah Domisili': domisili,
            'Jenis Pendaftaran': jenis_daftar,
            'Status Difabel': difabel,
            'Pilihan Jurusan': jurusan,
            'Status Penerimaan': status,
            'Jenis Sekolah Asal': jenis_sekolah,
            'Jumlah Siswa': jumlah,
            'KIP': 'Tidak'  # default
        })

# Tahap 2: Tambahkan data 'Ditolak' (10–15% dari total diterima)
target_ditolak = {
    key: int(val * random.uniform(0.10, 0.15))
    for key, val in diterima_counter.items()
}

for row in data:
    if row['Status Penerimaan'] != 'Ditolak':
        continue

    key = (row['Tahun Pendaftaran'], row['Pilihan Jurusan'], row['Jenis Kelamin'])

    if target_ditolak.get(key, 0) > 0:
        jml = random.randint(1, 2)
        if jml > target_ditolak[key]:
            jml = target_ditolak[key]
        row['Jumlah Siswa'] = jml
        target_ditolak[key] -= jml
    else:
        row['Jumlah Siswa'] = 0

# Tahap 3: Tambahkan status KIP (1–5% dari siswa negeri per tahun)
for tahun in tahun_pendaftaran:
    siswa_negeri = [i for i, row in enumerate(data)
                    if row['Tahun Pendaftaran'] == tahun and
                       row['Jenis Sekolah Asal'] == 'Negeri' and
                       row['Jumlah Siswa'] > 0]

    n_kip = int(len(siswa_negeri) * random.uniform(0.01, 0.05))
    penerima_kip_idx = random.sample(siswa_negeri, n_kip)

    for i in penerima_kip_idx:
        data[i]['KIP'] = 'Ya'

# Simpan ke file Excel
df = pd.DataFrame(data)
excel_path = "D:/data_visual_2880_komplit1.xlsx"
df.to_excel(excel_path, index=False)

print("File berhasil disimpan di:", excel_path)
