<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warehouse Approval Review - {{ $levelDescription }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .header-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
            border-radius: 0 0 20px 20px;
            margin-bottom: 2rem;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 1.5rem;
        }
        .card-header {
            background: linear-gradient(45deg, #f8f9fa, #e9ecef);
            border-radius: 15px 15px 0 0 !important;
            border-bottom: 2px solid #dee2e6;
        }
        .btn-warehouse-approve {
            background: linear-gradient(45deg, #28a745, #20c997);
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-warehouse-approve:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
            color: white;
        }
        .btn-warehouse-reject {
            background: linear-gradient(45deg, #dc3545, #fd7e14);
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-warehouse-reject:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.4);
            color: white;
        }
        .level-badge {
            background: linear-gradient(45deg, #6f42c1, #e83e8c);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
        }
        .table th {
            background-color: #f8f9fa;
            border-top: none;
            color: #495057;
            font-weight: 600;
        }
        .loading-spinner {
            display: none;
        }
        .warehouse-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: rgba(255, 255, 255, 0.8);
        }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid">
        <!-- Header Section -->
        <div class="header-section">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-warehouse warehouse-icon me-3"></i>
                            <div>
                                <h2 class="mb-1">Warehouse Approval Review</h2>
                                <p class="mb-0 opacity-75">{{ $levelDescription }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-end">
                        <span class="level-badge">
                            <i class="fas fa-layer-group me-1"></i>
                            Level {{ $approvalLog->level }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <!-- Requisition Information Card -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2 text-primary"></i>
                        Requisition Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Requisition Number:</strong></td>
                                    <td>{{ $requisition->no_srs }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Customer:</strong></td>
                                    <td>{{ $requisition->customer->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Requester:</strong></td>
                                    <td>{{ $requisition->requester->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Request Date:</strong></td>
                                    <td>{{ \Carbon\Carbon::parse($requisition->request_date)->format('d M Y') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Account:</strong></td>
                                    <td>{{ $requisition->account }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Cost Center:</strong></td>
                                    <td>{{ $requisition->cost_center }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Category:</strong></td>
                                    <td>
                                        <span class="badge bg-info">{{ $requisition->category }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Print Batch:</strong></td>
                                    <td>
                                        @if($requisition->print_batch)
                                            <span class="badge bg-success">
                                                <i class="fas fa-check-circle me-1"></i>Yes
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-times-circle me-1"></i>No
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($requisition->objectives)
                    <div class="mt-3">
                        <strong>Objectives:</strong>
                        <p class="mt-2 p-3 bg-light rounded">{{ $requisition->objectives }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Requisition Items Card -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2 text-success"></i>
                        Requisition Items
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Description</th>
                                    <th>Quantity Required</th>
                                    <th>Quantity Issued</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($requisition->requisitionItems as $item)
                                <tr>
                                    <td>
                                        <strong>{{ $item->itemMaster->item_name ?? 'N/A' }}</strong><br>
                                        <small class="text-muted">{{ $item->itemMaster->item_code ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        @if($item->itemMaster && $item->itemMaster->ItemDetails)
                                            @foreach($item->itemMaster->ItemDetails as $detail)
                                                <div class="small">
                                                    {{ $detail->description ?? 'N/A' }}
                                                    @if($detail->specification)
                                                        <br><em class="text-muted">{{ $detail->specification }}</em>
                                                    @endif
                                                </div>
                                            @endforeach
                                        @else
                                            <span class="text-muted">No description available</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $item->quantity_required ?? 0 }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">{{ $item->quantity_issued ?? 0 }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No items found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Approval Form Card -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-clipboard-check me-2 text-warning"></i>
                        Warehouse Approval Decision - {{ $levelDescription }}
                    </h5>
                </div>
                <div class="card-body">
                    <form id="warehouseApprovalForm" method="POST" action="{{ route('complain.warehouse.process') }}">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">
                        <input type="hidden" name="id" value="{{ $requisition->id }}">
                        
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label class="form-label"><strong>Decision:</strong></label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="status" id="approve" value="approve" checked>
                                    <label class="btn btn-outline-success btn-lg" for="approve">
                                        <i class="fas fa-check-circle me-2"></i>Approve
                                    </label>

                                    <input type="radio" class="btn-check" name="status" id="reject" value="reject">
                                    <label class="btn btn-outline-danger btn-lg" for="reject">
                                        <i class="fas fa-times-circle me-2"></i>Reject
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="notes" class="form-label">
                                <strong>Notes/Comments:</strong>
                                <span class="text-danger" id="notes-required" style="display: none;">*</span>
                            </label>
                            <textarea class="form-control" id="notes" name="notes" rows="4" 
                                placeholder="Enter your notes or comments here (required for rejection)..."></textarea>
                            <div class="invalid-feedback" id="notes-error"></div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                            <button type="submit" class="btn btn-warehouse-approve me-2" id="submitBtn">
                                <span class="loading-spinner spinner-border spinner-border-sm me-2" role="status"></span>
                                <i class="fas fa-paper-plane me-2"></i>
                                Submit Decision
                            </button>
                            <a href="#" class="btn btn-secondary" onclick="window.close();">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Alert Container -->
            <div id="alertContainer"></div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('warehouseApprovalForm');
            const notesField = document.getElementById('notes');
            const notesRequired = document.getElementById('notes-required');
            const notesError = document.getElementById('notes-error');
            const submitBtn = document.getElementById('submitBtn');
            const loadingSpinner = document.querySelector('.loading-spinner');

            // Handle status change
            document.querySelectorAll('input[name="status"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.value === 'reject') {
                        notesRequired.style.display = 'inline';
                        notesField.required = true;
                        notesField.placeholder = 'Please provide a reason for rejection (required)...';
                    } else {
                        notesRequired.style.display = 'none';
                        notesField.required = false;
                        notesField.placeholder = 'Enter your notes or comments here (optional)...';
                    }
                    // Clear previous validation
                    notesField.classList.remove('is-invalid');
                    notesError.textContent = '';
                });
            });

            // Handle form submission
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(form);
                const status = formData.get('status');
                const notes = formData.get('notes');

                // Validate notes for rejection
                if (status === 'reject' && !notes.trim()) {
                    notesField.classList.add('is-invalid');
                    notesError.textContent = 'Notes/reason is required for rejection.';
                    return;
                }

                // Show loading state
                submitBtn.disabled = true;
                loadingSpinner.style.display = 'inline-block';

                // Submit form via AJAX
                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('success', 'Success!', data.message);
                        
                        // Redirect after 2 seconds
                        setTimeout(() => {
                            window.close();
                        }, 2000);
                    } else {
                        showAlert('danger', 'Error!', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('danger', 'Error!', 'An unexpected error occurred. Please try again.');
                })
                .finally(() => {
                    // Hide loading state
                    submitBtn.disabled = false;
                    loadingSpinner.style.display = 'none';
                });
            });

            function showAlert(type, title, message) {
                const alertContainer = document.getElementById('alertContainer');
                const alert = `
                    <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                        <strong>${title}</strong> ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;
                alertContainer.innerHTML = alert;
                
                // Scroll to alert
                alertContainer.scrollIntoView({ behavior: 'smooth' });
            }
        });
    </script>
</body>
</html>