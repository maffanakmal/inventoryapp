@extends('admin.layout.main')

@section('child-content')
    <div class="container-fluid mt-3">
        <form id="filterForm" class="row g-3 align-items-end mb-3">
            @csrf
            <div class="col-md-3">
                <label for="supplier" class="form-label fw-semibold">Supplier</label>
                <select id="filter_supplier" name="supplier" class="form-select select-supplier">
                    <option value="">All Supplier</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="date_from" class="form-label fw-semibold">Date From</label>
                <input type="date" id="date_from" name="date_from" class="form-control">
            </div>
            <div class="col-md-3">
                <label for="date_to" class="form-label fw-semibold">Date To</label>
                <input type="date" id="date_to" name="date_to" class="form-control">
            </div>

            <div class="col-md-3 d-flex justify-content-end gap-2">
                <button type="button" id="btnSearch" class="btn btn-sm btn-primary">
                    <i class="fa-solid fa-magnifying-glass me-1"></i> Search
                </button>
                <button type="button" id="btnReset" class="btn btn-sm btn-secondary">
                    <i class="fa-solid fa-rotate-left me-1"></i> Reset
                </button>
                <button type="button" onclick="exportModal()" class="btn btn-sm btn-success">
                    <i class="fa-solid fa-file-export me-1"></i> Export
                </button>
            </div>
        </form>

        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-semibold">
                    Transaction History
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="transactionHistoryTable" class="table table-striped align-middle text-center"
                        style="width:100%">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>Transaction Code</th>
                                <th>Total Items</th>
                                <th>Total Price</th>
                                <th>Supplier</th>
                                <th>Transaction Date</th>
                                <th style="width:100px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exportModalLabel">
                            Export Transaction History
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <form id="exportForm" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="supplier" class="form-label">Supplier</label>
                                <select id="export_supplier" name="supplier" class="form-select select-supplier">
                                    <option value="">Select Supplier</option>
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="date_from" class="form-label">Date From</label>
                                    <input type="date" id="date_from" name="date_from" class="form-control">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="date_to" class="form-label">Date To</label>
                                    <input type="date" id="date_to" name="date_to" class="form-control">
                                </div>
                            </div>

                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="downloadWithItems"
                                    name="downloadWithItems">
                                <label for="downloadWithItems" class="form-check-label">Download with Items</label>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-sm me-2" data-bs-dismiss="modal">
                                <i class="fa-solid fa-xmark me-1"></i> Cancel
                            </button>
                            <button type="submit" id="exportBtn" class="btn btn-primary btn-sm">
                                <i class="fa-solid fa-database me-1"></i> Export
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        let table;

        $(document).ready(function() {
            transactionHistoryTable();

            initSelectSupplier();

            $('#btnSearch').on('click', function() {
                table.ajax.reload();
            });

            $('#btnReset').on('click', function() {
                $('#filterForm')[0].reset();
                table.ajax.reload();
            });

            exportTransaction();
        });

        function transactionHistoryTable() {
            table = $('#transactionHistoryTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('transactions.history') }}',
                    data: function(d) {
                        d.supplier = $('#supplier').val();
                        d.date_from = $('#date_from').val();
                        d.date_to = $('#date_to').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'transaction_code',
                        name: 'transaction_code'
                    },
                    {
                        data: 'total_items',
                        name: 'total_items'
                    },
                    {
                        data: 'total_price',
                        name: 'total_price',
                        className: 'text-end'
                    },
                    {
                        data: 'supplier',
                        name: 'supplier'
                    },
                    {
                        data: 'transaction_date',
                        name: 'transaction_date'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });
        }

        function exportModal() {
            $('#exportForm')[0].reset();
            $('#exportModal').modal('show');
        }

        function initSelectSupplier() {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ route('transactions.supplier') }}',
                type: 'GET',
                success: function(response) {
                    if (response.status == 200) {
                        $('.select-supplier').each(function() {
                            const defaultOption = $(this).attr('id').includes('filter') ?
                                '<option value="">All Supplier</option>' :
                                '<option value="">Select Supplier</option>';

                            $(this).empty().append(defaultOption);

                            response.supplier.forEach(function(item) {
                                $(this).append(
                                    `<option value="${item}">${item}</option>`
                                );
                            }.bind(this));
                        });

                        $('.select-supplier').each(function() {
                            const parentModal = $(this).closest('.modal');
                            const dropdownParent = parentModal.length ? parentModal : $(document.body);

                            $(this).select2({
                                theme: 'bootstrap-5',
                                width: '100%',
                                minimumInputLength: 0,
                                dropdownParent: dropdownParent,
                                language: {
                                    noResults: function() {
                                        return "Results not found";
                                    },
                                    searching: function() {
                                        return "Searching...";
                                    }
                                }
                            }).on('select2:open', function() {
                                setTimeout(function() {
                                    $('.select2-search__field').focus();
                                }, 0);
                            });
                        });

                    }
                },
                error: function(xhr) {
                    if (xhr.status === 500) {
                        let errorResponse = xhr.responseJSON;

                        Swal.fire({
                            icon: errorResponse.icon || "error",
                            title: errorResponse.title || "Error",
                            text: errorResponse.message ||
                                "An unexpected error occurred. Please try again later.",
                        });
                    }
                }
            });
        }

        function exportTransaction() {
            $('#exportForm').on('submit', function(e) {
                e.preventDefault();

                const form = $(this);
                const exportBtn = $('#exportBtn');
                exportBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Exporting...');

                const formData = form.serialize();

                $.ajax({
                    url: "{{ route('transactions.exportExcel') }}",
                    method: "POST",
                    data: formData,
                    xhrFields: {
                        responseType: 'blob'
                    },
                    success: function(blob) {
                        const link = document.createElement('a');
                        const url = window.URL.createObjectURL(blob);
                        link.href = url;
                        link.download = 'transaction_history_' + new Date().toISOString().replace(
                            /[^\d]/g, '') + '.xlsx';
                        link.click();
                        window.URL.revokeObjectURL(url);

                        $('#exportModal').modal('hide');
                        exportBtn.prop('disabled', false).html(
                            '<i class="fa-solid fa-database me-1"></i> Export');
                    },
                    error: function(xhr) {
                        Swal.fire('Error', 'Failed to export data.', 'error');
                        exportBtn.prop('disabled', false).html(
                            '<i class="fa-solid fa-database me-1"></i> Export');
                    }
                });
            });
        }
    </script>
@endsection
