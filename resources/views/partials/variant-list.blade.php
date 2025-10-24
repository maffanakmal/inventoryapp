@if ($variants->isEmpty())
    <div class="text-center text-muted py-4">
        No variants available.
    </div>
@else
    <div class="row">
        @foreach ($variants as $variant)
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        @if ($variant->variant_image)
                            <img src="{{ asset('storage/' . $variant->variant_image) }}" class="card-img-top"
                                alt="{{ $variant->variant_name }}">
                        @endif
                        <h6 class="fw-bold mb-1">{{ $variant->variant_name }}</h6>
                        <small class="text-muted">{{ $variant->sku }}</small>
                        <p class="text-muted small mb-2">{{ $variant->variant_description ?? 'No description' }}</p>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                <span class="fw-semibold text-primary">
                                    Rp {{ number_format($variant->variant_price, 0, ',', '.') }}
                                </span><br>
                                <small class="text-muted">Stock: {{ $variant->stock_quantity }}</small>
                            </div>
                            <div class="text-end">
                                <button class="btn btn-sm btn-outline-warning me-1" onclick="editVariant(this)" data-id="{{ $variant->variant_id }}">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteVariant(this)" data-id="{{ $variant->variant_id }}">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
