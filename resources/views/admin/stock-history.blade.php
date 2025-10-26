@extends('admin.layout.main')

@section('child-content')
    <div class="header-wrapper d-flex justify-content-between align-items-center mt-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('master-data.stocks') }}" class="text-decoration-none text-primary">
                        Stocks
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                    Stock History
                </li>
            </ol>
        </nav>
        <a href="{{ route('master-data.stocks') }}" class="btn btn-outline-primary btn-sm">
            <i class="fa-solid fa-arrow-left me-1"></i> Back
        </a>
    </div>

    <div class="card shadow-sm mt-4">
        <div class="card-header d-flex align-items-center">
            <h5 class="mb-0">Product Stock History</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="stockHistoryTable" class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Date</th>
                            <th>Transaction Code</th>
                            <th>Transaction Type</th>
                            <th>Input Qty</th>
                            <th>Output Qty</th>
                            <th>Balance Qty</th>
                            <th>Officer</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        var variant_id = "{{ $variant_id }}";

        $(document).ready(function() {
            stockHistoryTable();
        });

        function stockHistoryTable() {
            var table = $('#stockHistoryTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('master-data.stocks.history', '') }}/" + variant_id,
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'transaction_code',
                        name: 'transaction_code'
                    },
                    {
                        data: 'transaction_type',
                        name: 'transaction_type'
                    },
                    {
                        data: 'input_qty',
                        name: 'input_qty',
                        className: 'text-center'
                    },
                    {
                        data: 'output_qty',
                        name: 'output_qty',
                        className: 'text-center'
                    },
                    {
                        data: 'balance_qty',
                        name: 'balance_qty',
                        className: 'text-center'
                    },
                    {
                        data: 'officer',
                        name: 'officer'
                    }
                ]
            });
        }
    </script>
@endsection
