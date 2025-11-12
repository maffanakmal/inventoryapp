@extends('admin.layout.main')

@section('child-content')
    <div class="header-wrapper d-flex justify-content-between align-items-center mt-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('transactions.history') }}" class="text-decoration-none text-primary">
                        Transactions
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                    Transaction Detail
                </li>
            </ol>
        </nav>
        <a href="{{ route('transactions.history') }}" class="btn btn-outline-primary btn-sm">
            <i class="fa-solid fa-arrow-left me-1"></i> Back
        </a>
    </div>

    <div class="card shadow-sm mt-4">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
            <h5 class="mb-0 fw-semibold">
                <span id="transaction_code" class="text-primary"></span>
            </h5>
            <button id="exportPdf" class="btn btn-sm btn-primary mt-2 mt-sm-0">
                <i class="fa-solid fa-file-pdf me-1"></i> Export PDF
            </button>
        </div>
        <div class="card-body">
            <div class="row mb-2">
                <div class="col-md-4 fw-semibold text-muted">Supplier</div>
                <div class="col-md-8"><span id="supplier" class="text-dark"></span></div>
            </div>
            <div class="row mb-2">
                <div class="col-md-4 fw-semibold text-muted">Contact</div>
                <div class="col-md-8"><span id="contact" class="text-dark"></span></div>
            </div>
            <div class="row mb-2">
                <div class="col-md-4 fw-semibold text-muted">Total Items</div>
                <div class="col-md-8"><span id="total_items" class="text-dark"></span></div>
            </div>
            <div class="row mb-2">
                <div class="col-md-4 fw-semibold text-muted">Total Price</div>
                <div class="col-md-8"><span id="total_price" class="text-dark"></span></div>
            </div>
            <div class="row mb-2">
                <div class="col-md-4 fw-semibold text-muted">Notes</div>
                <div class="col-md-8"><span id="notes" class="text-dark"></span></div>
            </div>
            <div class="row mb-2">
                <div class="col-md-4 fw-semibold text-muted">Officer</div>
                <div class="col-md-8"><span id="officer" class="text-dark"></span></div>
            </div>
            <div class="row mb-0">
                <div class="col-md-4 fw-semibold text-muted">Transaction Date</div>
                <div class="col-md-8"><span id="transaction_date" class="text-dark"></span></div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mt-4">
        <div class="card-header">
            <h5 class="mb-0 fw-semibold">Variant Details</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="variantTable" class="table align-middle mb-0 w-100">
                    <thead class="table-light text-center">
                        <tr>
                            <th>No</th>
                            <th>Variant Name</th>
                            <th>Batch Number</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            const transactionId = "{{ $transactionId }}";

            $.ajax({
                url: `/transactions/detail/${transactionId}`,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 200) {
                        const t = response.transaction;
                        const items = response.items;

                        $('#transaction_code').text(t.transaction_code);
                        $('#supplier').text(t.supplier);
                        $('#contact').text(t.contact ?? '-');
                        $('#total_items').text(t.total_items);
                        $('#total_price').text(t.total_price);
                        $('#notes').text(t.notes ?? '-');
                        $('#officer').text(t.officer);
                        $('#transaction_date').text(t.created_at_formatted);

                        const tbody = $('table tbody');
                        tbody.empty();
                        items.forEach(item => {
                            tbody.append(`
                        <tr class="text-center">
                            <td>${item.no}</td>
                            <td>${item.variant_name}</td>
                            <td>${item.batch_number}</td>
                            <td>${item.quantity}</td>
                            <td>${item.price}</td>
                            <td>${item.subtotal}</td>
                        </tr>
                    `);
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire('Error', 'Failed to load transaction detail.', 'error');
                }
            });

            $('#exportPdf').on('click', function() {
                window.open(`/transactions/export-pdf/${transactionId}`, '_blank');
            });
        });
    </script>
@endsection
