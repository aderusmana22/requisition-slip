<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Payment Proof - {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7fc; font-family: 'Segoe UI', sans-serif; }
        .upload-card { max-width: 500px; margin: 50px auto; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); border: none; }
        .card-header { background: linear-gradient(135deg, #ee5a24 0%, #ff6b6b 100%); color: white; border-radius: 15px 15px 0 0 !important; padding: 25px; text-align: center; }
        .btn-upload { background-color: #ee5a24; border: none; padding: 12px; font-weight: bold; }
        .btn-upload:hover { background-color: #d34e1e; }
        .success-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: white; border-radius: 15px; display: none; align-items: center; justify-content: center; flex-direction: column; z-index: 10; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card upload-card position-relative">
            
            <div class="success-overlay" id="successOverlay">
                <i class="fas fa-check-circle text-success" style="font-size: 4rem; margin-bottom: 20px;"></i>
                <h4 class="text-success">Upload Successful!</h4>
                <p class="text-muted text-center px-4">Thank you. Your payment proof has been received and will be reviewed shortly.</p>
                <button onclick="window.close()" class="btn btn-outline-secondary mt-3">Close Window</button>
            </div>

            <div class="card-header">
                <h4 class="mb-1"><i class="fas fa-file-invoice-dollar me-2"></i>Payment Proof</h4>
                <p class="mb-0 opacity-75">Requisition #{{ $requisition->no_srs }}</p>
            </div>
            
            <div class="card-body p-4">
                <form id="uploadForm" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="alert alert-warning border-0 bg-warning bg-opacity-10 text-warning d-flex align-items-center mb-4">
                        <i class="fas fa-info-circle me-3 fs-4"></i>
                        <small>Please upload the payment proof to continue the process. Accepted formats: PNG, JPG (Max 1MB).</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Payment Date</label>
                        <input type="date" name="payment_date" class="form-control form-control-lg" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Upload Document</label>
                        <input type="file" name="payment_document" class="form-control form-control-lg" accept="image/png, image/jpeg" required>
                        <div class="form-text mt-2"><i class="fas fa-exclamation-triangle me-1"></i> Max size: 1MB. Format: PNG, JPG, JPEG.</div>
                        <div class="invalid-feedback d-block mt-2 fw-bold" id="errorMsg"></div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-upload btn-lg text-white" id="submitBtn">
                            <i class="fas fa-cloud-upload-alt me-2"></i>Submit Proof
                        </button>
                    </div>
                </form>
            </div>
            <div class="card-footer text-center bg-transparent border-0 py-3 text-muted" style="font-size: 0.8rem;">
                &copy; {{ date('Y') }} {{ config('app.name') }}
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('uploadForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const btn = document.getElementById('submitBtn');
            const errorDiv = document.getElementById('errorMsg');
            const fileInput = this.querySelector('input[type="file"]');
            const file = fileInput.files[0];

            errorDiv.textContent = '';
            
            // Client-side Validation
            if(file) {
                if(file.size > 1048576) { // 1MB
                    errorDiv.textContent = 'File size exceeds 1MB limit.';
                    return;
                }
                const validTypes = ['image/png', 'image/jpeg', 'image/jpg'];
                if(!validTypes.includes(file.type)) {
                    errorDiv.textContent = 'Invalid file format. Please upload PNG or JPG.';
                    return;
                }
            }

            // Prepare UI
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Uploading...';
            
            const formData = new FormData(this);

            // AJAX Submit
            fetch("{{ route('complain.public.upload.store', $requisition->id) }}?signature={{ request()->query('signature') }}&expires={{ request()->query('expires') }}", {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json().then(data => ({status: response.status, body: data})))
            .then(result => {
                if (result.status === 200) {
                    document.getElementById('successOverlay').style.display = 'flex';
                } else {
                    throw new Error(result.body.message || 'Upload failed');
                }
            })
            .catch(error => {
                errorDiv.textContent = error.message;
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-cloud-upload-alt me-2"></i>Submit Proof';
            });
        });
    </script>
</body>
</html>