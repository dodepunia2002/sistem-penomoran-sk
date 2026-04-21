<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penomoran SK — Dishub Gianyar</title>
    <style>
        body { font-family: 'Inter', sans-serif; margin: 40px; color: #333; line-height: 1.5; }
        .header { display: flex; align-items: center; gap: 20px; border-bottom: 3px solid #333; padding-bottom: 20px; margin-bottom: 30px; }
        .header img { width: 80px; height: auto; }
        .header-text h1 { margin: 0; font-size: 18px; text-transform: uppercase; letter-spacing: 1px; }
        .header-text h2 { margin: 5px 0 0; font-size: 16px; font-weight: 500; }
        .header-text p { margin: 5px 0 0; font-size: 12px; color: #666; }

        .report-info { margin-bottom: 20px; display: flex; justify-content: space-between; font-size: 13px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 12px; }
        th, td { border: 1px solid #000; padding: 10px; text-align: left; }
        th { background: #f0f0f0; font-weight: bold; text-transform: uppercase; }

        .footer { margin-top: 50px; display: flex; justify-content: flex-end; }
        .signature { text-align: center; min-width: 250px; }
        .signature p { margin: 0; font-size: 14px; }
        .signature .space { height: 80px; }
        
        @media print {
            body { margin: 20px; }
            .no-print { display: none; }
            @page { margin: 1cm; }
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ asset('logo-dishub.png') }}" alt="Logo">
        <div class="header-text">
            <h1>Pemerintah Kabupaten Gianyar</h1>
            <h1>Dinas Perhubungan</h1>
            <p>Jl. Ngurah Rai No. 1, Gianyar, Bali | Telp: (0361) 943044</p>
        </div>
    </div>

    <center><h2 style="text-transform: uppercase; margin-bottom: 10px;">Laporan Riwayat Penomoran SK</h2></center>

    <div class="report-info">
        <div>
            <strong>Periode:</strong> 
            @if(request('month')) {{ \Carbon\Carbon::create()->month(request('month'))->translatedFormat('F') }} @else Semua Bulan @endif
            @if(request('year')) {{ request('year') }} @else Semua Tahun @endif
        </div>
        <div><strong>Dicetak pada:</strong> {{ now()->translatedFormat('d F Y H:i') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th style="width: 150px;">Nomor SK</th>
                <th>Nama Penerima</th>
                <th>Alamat</th>
                <th style="width: 100px;">Tanggal SK</th>
            </tr>
        </thead>
        <tbody>
            @forelse($riwayat as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><strong>{{ $row->nomor_sk }}</strong></td>
                    <td>{{ $row->nama }}</td>
                    <td>{{ $row->alamat }}</td>
                    <td>{{ \Carbon\Carbon::parse($row->tanggal)->translatedFormat('d M Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 20px;">Tidak ada data ditemukan untuk periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <div class="signature">
            <p>Gianyar, {{ now()->translatedFormat('d F Y') }}</p>
            <p>Kepala Dinas Perhubungan,</p>
            <div class="space"></div>
            <p><strong>( __________________________ )</strong></p>
            <p>NIP. ........................................</p>
        </div>
    </div>

    <script>
        window.onload = function() {
            // Uncomment line below to auto-print
            // window.print();
        }
    </script>
</body>
</html>
