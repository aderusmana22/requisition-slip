Alur Proses
1.	Kategori SAMPLE
•	Sample Finished Goods:
•	RnD (5302) / QA (5302): Permintaan diajukan oleh Requester, disetujui oleh Atasan Requester, lalu oleh Business Controller, dan akhirnya oleh Outward WH Supervisor (dengan pelacakan).
•	SnM Normal (5300): Requester → Atasan Requester → Business Controller → Outward WH Supervisor → Transportation.
•	SnM Special Order (5300): Requester mengisi form ukuran khusus, lalu persetujuan oleh Atasan Requester, Business Controller, dan Quality Assurance (dengan pelacakan dan dia mengisi form sisal alu submit).
•	Sample Packaging:
•	SnM (5300): Requester → Atasan Requester → Business Controller. Kemudian muncul dialog "Print Batch Number?". Jika Ya: → Inward WH Supervisor → Material Support Supervisor → Inward WH Supervisor → Transportation. Jika Tidak: → Inward WH Supervisor → Transportation.
•	RnD (5302): Requester → Atasan Requester → Business Controller → Inward WH Supervisor (dengan pelacakan).

2.	Kategori PACKAGING REPLACEMENT (4914)
•	Logika Persetujuan: Alur dimulai dengan Requester ke QA. Jika permintaan Ditolak, pemohon harus mengunggah bukti pembayaran. Kemudian, alur dilanjutkan ke Atasan Requester dan Business Controller.
•	Logika Pengiriman: Setelah persetujuan, alur ini mengikuti jalur yang sama dengan Sample Packaging SnM dengan dialog "Print Batch Number?".
3.	Kategori FREE GOODS
•	Jika dari Department SnM: Requester → SnM Manager (yang juga atasan requester) → Business Controller → Outward WH Supervisor (dengan pelacakan).
•	Jika dari Department Selain SnM: Requester → HCD Dept. Head → Business Controller → Outward WH Supervisor (dengan pelacakan).
Semua category dan sub category setelah selesai tracking dan approval akan mengembalikan notifikasi ke user (requester)
