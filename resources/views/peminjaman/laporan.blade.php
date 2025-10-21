<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    @include('peminjaman.partials.style-laporan')
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p>Tanggal Cetak: {{ $date }}</p>
        @if ($tanggal_mulai && $tanggal_akhir)
            <p>Periode: {{ date('d/m/Y', strtotime($tanggal_mulai)) }} - {{ date('d/m/Y', strtotime($tanggal_akhir)) }}</p>
        @endif
        @if ($status !== 'all')
            <p>Status: <strong>{{ ucfirst($status) }}</strong></p>
        @endif
    </div>
    @include('peminjaman.partials.list-laporan')
</body>
</html>