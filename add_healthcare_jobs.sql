-- Add new category for Healthcare & Medical
INSERT INTO kategori (nama_kategori) VALUES ('Healthcare & Medical');

-- Get the new category id (assuming auto_increment, it will be the last id)
-- For safety, you may want to check the id after insertion

-- Add keywords related to healthcare and midwifery
INSERT INTO keywords (keyword, kategori_id, bobot) VALUES
('midwife', (SELECT id FROM kategori WHERE nama_kategori = 'Healthcare & Medical'), 1.5),
('bidan', (SELECT id FROM kategori WHERE nama_kategori = 'Healthcare & Medical'), 1.5),
('nurse', (SELECT id FROM kategori WHERE nama_kategori = 'Healthcare & Medical'), 1.5),
('healthcare', (SELECT id FROM kategori WHERE nama_kategori = 'Healthcare & Medical'), 1.3),
('patient care', (SELECT id FROM kategori WHERE nama_kategori = 'Healthcare & Medical'), 1.4),
('medical', (SELECT id FROM kategori WHERE nama_kategori = 'Healthcare & Medical'), 1.3),
('obstetrics', (SELECT id FROM kategori WHERE nama_kategori = 'Healthcare & Medical'), 1.4),
('maternal health', (SELECT id FROM kategori WHERE nama_kategori = 'Healthcare & Medical'), 1.4),
('prenatal care', (SELECT id FROM kategori WHERE nama_kategori = 'Healthcare & Medical'), 1.4),
('postnatal care', (SELECT id FROM kategori WHERE nama_kategori = 'Healthcare & Medical'), 1.4);

-- Add sample job listings for midwife and healthcare roles
INSERT INTO lowongan (judul, perusahaan, deskripsi, persyaratan, lokasi, kategori_id, sumber, tanggal_posting) VALUES
('Midwife', 'RS Sehat Sentosa',
'RS Sehat Sentosa mencari Midwife yang berpengalaman untuk memberikan perawatan prenatal, persalinan, dan postnatal kepada pasien. Kandidat harus memiliki sertifikasi bidan dan pengalaman minimal 3 tahun.',
'- Sertifikasi bidan yang valid
- Pengalaman minimal 3 tahun sebagai midwife
- Pengetahuan tentang perawatan prenatal dan postnatal
- Kemampuan komunikasi yang baik dengan pasien
- Mampu bekerja dalam tim medis',
'Jakarta', (SELECT id FROM kategori WHERE nama_kategori = 'Healthcare & Medical'), 'Jobstreet', CURDATE()),

('Nurse', 'Klinik Medika',
'Klinik Medika membutuhkan Nurse yang berdedikasi untuk memberikan perawatan medis dan dukungan kepada pasien. Kandidat harus memiliki lisensi keperawatan dan pengalaman di bidang kesehatan.',
'- Lisensi keperawatan yang valid
- Pengalaman minimal 2 tahun di bidang keperawatan
- Kemampuan melakukan tindakan medis dasar
- Komunikatif dan empati terhadap pasien
- Mampu bekerja shift',
'Bandung', (SELECT id FROM kategori WHERE nama_kategori = 'Healthcare & Medical'), 'LinkedIn', CURDATE());
