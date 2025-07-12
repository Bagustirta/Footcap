import pandas as pd
import random

# Baca file yang sudah Anda miliki sebelumnya
df = pd.read_excel("Data Penerimaan Siswa Baru - Data Visual(Revisi).xlsx")

data_dummy = []
counter = 1

def generate_nama(jk):
    nama_depan = ['Adi', 'Bagus', 'Citra', 'Dewi', 'Eka', 'Farhan', 'Gita', 'Hendra', 'Intan', 'Joko', 'Kiki', 'Lestari']
    nama_belakang = ['Wijaya', 'Putra', 'Sari', 'Utami', 'Saputra', 'Anggraini', 'Permana']
    return f"{random.choice(nama_depan)} {random.choice(nama_belakang)}"

def generate_nomer(tahun, index):
    return f"{tahun}REG{str(index).zfill(4)}"

tahun_lahir = [2005, 2006, 2007, 2008, 2009]

for _, row in df.iterrows():
    jumlah = row['Jumlah Siswa']
    for _ in range(jumlah):
        nama = generate_nama(row['Jenis Kelamin'])
        no_pendaftaran = generate_nomer(row['Tahun Pendaftaran'], counter)
        nilai_rapor = round(random.uniform(65, 95), 2)
        tahun_lhr = random.choice(tahun_lahir)
        kip = 'Ya' if (row['Jenis Sekolah Asal'] == 'Negeri' or row['Jenis Pendaftaran'] == 'Afirmasi') and random.random() < 0.2 else 'Tidak'
        difabel = 'Ya' if random.random() < 0.03 else 'Tidak'

        data_dummy.append({
            'No': counter,
            'Nama': nama,
            'Nomor Pendaftaran': no_pendaftaran,
            'Umur (Tahun Lahir)': tahun_lhr,
            'Jenis Kelamin': row['Jenis Kelamin'],
            'Tahun Pendaftaran': row['Tahun Pendaftaran'],
            'Asal Sekolah': row['Asal Sekolah'],
            'Jenis Sekolah Asal': row['Jenis Sekolah Asal'],
            'Wilayah Domisili': row['Wilayah Domisili'],
            'Jenis Pendaftaran': row['Jenis Pendaftaran'],
            'Pilihan Jurusan': row['Pilihan Jurusan'],
            'Status Penerimaan': row['Status Penerimaan'],
            'Kepemilikan KIP': kip,
            'Nilai Rata-rata Rapor': nilai_rapor,
            'Status Difabel': difabel
        })
        counter += 1

# Simpan ke Excel
df_dummy = pd.DataFrame(data_dummy)
df_dummy.to_excel("D:/data_dummy_siswa_1844.xlsx", index=False)

print("Data dummy sebanyak 1844 siswa berhasil dibuat.")
