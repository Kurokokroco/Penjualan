<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Laporan Penjualan</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
            color: #000;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            margin-bottom: 5px;
        }

        .header p {
            margin: 0;
        }

        .periode {
            margin-bottom: 20px;
        }

        .ringkasan {
            display: flex;
            gap: 30px;
            margin-bottom: 20px;
        }

        .ringkasan div {
            border: 1px solid #ddd;
            padding: 10px 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        th {
            background: #eee;
        }

        .text-right {
            text-align: right;
        }

        .footer {
            margin-top: 40px;
            text-align: right;
        }

        @media print {

            .no-print {
                display: none;
            }

            body {
                margin: 10px;
            }

        }
    </style>

</head>


<body>


    <div class="no-print" style="margin-bottom: 20px;">

        <button onclick="window.print()">
            Cetak Laporan
        </button>

        <button onclick="window.close()">
            Tutup
        </button>

    </div>


    <div class="header">

        <h1>
            MINIMARKET
        </h1>

        <p>
            Laporan Penjualan
        </p>

    </div>


    <div class="periode">

        <strong>
            Periode:
        </strong>

        {{ $tanggalMulai ?: 'Semua' }}

        s/d

        {{ $tanggalSelesai ?: 'Semua' }}

    </div>


    <div class="ringkasan">

        <div>

            <strong>
                Total Transaksi
            </strong>

            <br>

            {{ $totalTransaksi }}

        </div>


        <div>

            <strong>
                Total Pendapatan
            </strong>

            <br>

            Rp {{ number_format($totalPendapatan, 0, ',', '.') }}

        </div>

    </div>


    <table>

        <thead>

            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Pelanggan</th>
                <th>Total</th>  
                <th>Pembayaran</th>
                <th>Kembalian</th>
            </tr>

        </thead>


        <tbody>

            @forelse ($penjualan as $item)

                <tr>

                    <td>
                        {{ $loop->iteration }}
                    </td>

                    <td>
                        {{ $item->tanggal_penjualan }}
                    </td>

                    <td>
                        {{ $item->pelanggan->nama_pelanggan ?? 'Umum' }}
                    </td>

                    <td class="text-right">
                        Rp {{ number_format($item->total, 0, ',', '.') }}
                    </td>

                    <td class="text-right">
                        Rp {{ number_format($item->pembayaran, 0, ',', '.') }}
                    </td>

                    <td class="text-right">
                        Rp {{ number_format($item->kembalian, 0, ',', '.') }}
                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="6" style="text-align: center;">

                        Tidak ada data penjualan.

                    </td>

                </tr>

            @endforelse

        </tbody>


        <tfoot>

            <tr>

                <th colspan="3" class="text-right">

                    Total Pendapatan

                </th>

                <th colspan="3" class="text-right">

                    Rp {{ number_format($totalPendapatan, 0, ',', '.') }}

                </th>

            </tr>

        </tfoot>

    </table>


    <div class="footer">

        <p>
            Dicetak pada:
            {{ now()->format('d-m-Y H:i') }}
        </p>

    </div>


</body>

</html>