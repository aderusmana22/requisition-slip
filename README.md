📌 Alur Proses Permintaan
1. Kategori SAMPLE
🔹 Sample Finished Goods

RnD (5302) / QA (5302)
Requester → Atasan Requester → Business Controller → Outward WH Supervisor (tracking)

SnM Normal (5300)
Requester → Atasan Requester → Business Controller → Outward WH Supervisor → Transportation

SnM Special Order (5300)

Requester mengisi form ukuran khusus

Requester → Atasan Requester → Business Controller → QA (tracking & QA isi form, lalu submit)

🔹 Sample Packaging

SnM (5300)
Requester → Atasan Requester → Business Controller

Dialog "Print Batch Number?"

Ya:
Inward WH Supervisor → Material Support Supervisor → Inward WH Supervisor → Transportation

Tidak:
Inward WH Supervisor → Transportation

RnD (5302)
Requester → Atasan Requester → Business Controller → Inward WH Supervisor (tracking)

2. Kategori PACKAGING REPLACEMENT (4914)

Persetujuan:

Requester → QA

Jika ditolak → Requester wajib upload bukti pembayaran

Dilanjutkan ke: Atasan Requester → Business Controller

Pengiriman:
Sama dengan Sample Packaging SnM, dengan dialog "Print Batch Number?"

3. Kategori FREE GOODS

Dari Dept. SnM
Requester → SnM Manager (atasan) → Business Controller → Outward WH Supervisor (tracking)

Dari Dept. selain SnM
Requester → HCD Dept. Head → Business Controller → Outward WH Supervisor (tracking)

📢 Notifikasi

Semua kategori & subkategori → setelah tracking & approval selesai → notifikasi kembali ke Requester.



flowchart TD

subgraph SAMPLE
    A1[Requester] --> A2[Atasan Requester] --> A3[Business Controller]

    %% Finished Goods
    A3 -->|RnD/QA (5302)| A4[Outward WH Supervisor]

    A3 -->|SnM Normal (5300)| A5[Outward WH Supervisor] --> A6[Transportation]

    A3 -->|SnM Special Order (5300)| A7[Quality Assurance]

    %% Packaging
    A3 -->|Sample Packaging SnM| DIALOG{"Print Batch Number?"}
    DIALOG -- Yes --> P1[Inward WH Supervisor] --> P2[Material Support Supervisor] --> P3[Inward WH Supervisor] --> P4[Transportation]
    DIALOG -- No --> P5[Inward WH Supervisor] --> P6[Transportation]

    A3 -->|Sample Packaging RnD (5302)| P7[Inward WH Supervisor]
end

subgraph PACKAGING_REPLACEMENT
    B1[Requester] --> B2[QA]
    B2 -- Rejected --> B3[Upload Bukti Pembayaran]
    B2 --> B4[Atasan Requester] --> B5[Business Controller]
    B5 -->|Pengiriman| DIALOG
end

subgraph FREE_GOODS
    C1[Requester]
    C1 -->|Dept SnM| C2[SnM Manager] --> C3[Business Controller] --> C4[Outward WH Supervisor]
    C1 -->|Dept selain SnM| C5[HCD Dept Head] --> C3
end

%% Notifikasi
A4 --> N[Notifikasi ke Requester]
A6 --> N
A7 --> N
P4 --> N
P6 --> N
P7 --> N
B5 --> N
C4 --> N



