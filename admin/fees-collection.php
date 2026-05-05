<?php
// admin/fees-collection.php
require_once('../common/config.php');
include(__DIR__ . "/includes/header.php");
?>

<div class="pagetitle">
    <h1>Fees Collection</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo ADMIN_BASE_URL; ?>index.php">Home</a></li>
            <li class="breadcrumb-item">Student Accounting</li>
            <li class="breadcrumb-item active">Fees Collection</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <!-- Search Student -->
        <div class="col-lg-12">
            <div class="card mb-4">
                <div class="card-body pt-4">
                    <form class="row g-3 justify-content-center" id="searchStudentForm" novalidate>
                        <div class="col-md-8">
                            <div class="input-group input-group-lg shadow-sm">
                                <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-primary"></i></span>
                                <input type="text" class="form-control border-start-0" id="search-input" placeholder="Registration No. or Mobile Number..." required>
                                <button class="btn btn-primary px-5 fw-bold" type="submit" id="btn-find">FIND STUDENT</button>
                            </div>
                            <div class="text-center mt-2 small text-muted">Enter student registration number or phone to load fee ledger.</div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Student Profile & Ledger -->
        <div class="col-lg-12 d-none" id="ledgerSection">
            <div class="card">
                <div class="card-body pt-4">
                    <div class="row">
                        <!-- Student Mini-Profile -->
                        <div class="col-md-3 border-end">
                            <div class="text-center mb-4">
                                <div class="position-relative d-inline-block">
                                    <img src="<?php echo BASE_URL; ?>media/general/default-avatar.png" id="st-photo" alt="Profile" class="rounded-circle border border-4 border-light shadow-sm mb-3" width="130" height="130">
                                    <span class="position-absolute bottom-0 end-0 bg-success border border-white rounded-circle p-2 shadow-sm" title="Active"></span>
                                </div>
                                <h5 class="fw-bold text-dark mb-1" id="st-name">-</h5>
                                <div class="badge bg-primary-light text-primary border border-primary px-3 mb-3" id="st-roll">-</div>
                                
                                <div class="text-start bg-light rounded p-3 small">
                                    <div class="mb-2"><label class="text-muted d-block fw-bold small">COURSE</label><span class="fw-bold text-dark" id="st-course">-</span></div>
                                    <div class="mb-2"><label class="text-muted d-block fw-bold small">CENTER</label><span class="fw-bold text-dark" id="st-center">-</span></div>
                                    <div class="mb-0"><label class="text-muted d-block fw-bold small">CONTACT</label><span class="fw-bold text-dark" id="st-mobile">-</span></div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Fee Ledger & Transactions -->
                        <div class="col-md-9 ps-md-4">
                            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                                <h5 class="fw-bold text-dark mb-0"><i class="fas fa-file-invoice-dollar me-2 text-primary"></i>Fee Component Ledger</h5>
                                <span class="badge bg-info text-white rounded-pill px-3" id="st-group">-</span>
                            </div>

                            <div class="table-responsive mb-4">
                                <table class="table table-hover align-middle datatable-premium" id="ledgerTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="60">S.No.</th>
                                            <th class="text-start">Fee Component</th>
                                            <th class="text-center">Total Fee</th>
                                            <th class="text-center">Paid Amount</th>
                                            <th class="text-center">Balance Due</th>
                                            <th class="text-end" data-no-sort>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Dynamic rows -->
                                    </tbody>
                                    <tfoot class="table-light fw-bold">
                                        <tr>
                                            <td colspan="2" class="text-end">GRAND TOTAL:</td>
                                            <td class="text-center" id="g-total">-</td>
                                            <td class="text-center text-success" id="g-paid">-</td>
                                            <td class="text-center text-danger" id="g-balance">-</td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <h6 class="fw-bold text-dark mb-3"><i class="fas fa-history me-2 text-primary"></i>Recent Payment History</h6>
                            <div class="list-group list-group-flush border rounded overflow-hidden" id="recentTransactions">
                                <!-- Dynamic transactions -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Payment Modal -->
<div class="modal fade" id="payModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header py-3">
                <h5 class="modal-title fw-bold"><i class="fas fa-cash-register me-2"></i>Collect Fee Payment</h5>
                <button type="button" class="btn-custom-close ms-auto" data-bs-dismiss="modal">
                    <i class="<?php echo v('theme_modal_close_icon', 'fas fa-times'); ?>"></i>
                </button>
            </div>
            <form id="collectPaymentForm" novalidate>
                <input type="hidden" name="action" value="collect_payment">
                <input type="hidden" name="admission_id" id="pay-st-id">
                <input type="hidden" name="allocation_id" id="pay-allocation-id">
                <input type="hidden" name="fee_type_id" id="pay-fee-type-id">
                
                <div class="modal-body p-4">
                    <div class="mb-4 text-center border-bottom pb-3">
                        <label class="text-muted small fw-bold d-block mb-1">FEE COMPONENT</label>
                        <h4 class="fw-bold text-primary mb-0" id="pay-fee-name-display">-</h4>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Amount to Collect (₹) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light fw-bold">₹</span>
                                <input type="number" class="form-control form-control-lg fw-bold text-primary" name="amount" id="pay-amount" placeholder="0.00" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Payment Mode <span class="text-danger">*</span></label>
                            <select class="form-select form-select-lg" name="payment_mode" required>
                                <option value="cash">Cash Payment</option>
                                <option value="online">Online / UPI</option>
                                <option value="bank">Bank Transfer</option>
                                <option value="cheque">Cheque</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Reference / TXN ID (Optional)</label>
                            <input type="text" class="form-control" name="transaction_id" placeholder="e.g. UPI Ref, Bank Txn, Cheque No.">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Payment Date</label>
                            <input type="date" class="form-control" name="payment_date" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 p-3">
                    <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal">CANCEL</button>
                    <button type="submit" class="btn btn-success px-5 fw-bold" id="btn-confirm">
                        <i class="fas fa-check-circle me-2"></i>CONFIRM PAYMENT
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
'use strict';
const HANDLER = '<?php echo BASE_URL; ?>ajax/accounting_handler.php';
let payModal;

document.addEventListener('DOMContentLoaded', function() {
    payModal = new bootstrap.Modal(document.getElementById('payModal'));
    
    document.getElementById('searchStudentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const search = document.getElementById('search-input').value;
        if(!search) return;
        
        const btn = document.getElementById('btn-find');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>FINDING...';
        
        const fd = new FormData();
        fd.append('action', 'get_student_ledger');
        fd.append('search', search);
        
        fetch(HANDLER, { method: 'POST', body: fd })
        .then(r => r.json()).then(res => {
            btn.disabled = false;
            btn.innerHTML = 'FIND STUDENT';
            if(res.success) {
                renderLedger(res);
                document.getElementById('ledgerSection').classList.remove('d-none');
                window.scrollTo({top: document.getElementById('ledgerSection').offsetTop - 20, behavior: 'smooth'});
            } else {
                alert(res.message);
                document.getElementById('ledgerSection').classList.add('d-none');
            }
        });
    });

    document.getElementById('collectPaymentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        if(!this.checkValidity()) { this.classList.add('was-validated'); return; }
        
        const btn = document.getElementById('btn-confirm');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>PROCESSING...';
        
        const fd = new FormData(this);
        fetch(HANDLER, { method: 'POST', body: fd })
        .then(r => r.json()).then(res => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check-circle me-2"></i>CONFIRM PAYMENT';
            if(res.success) {
                alert(res.message);
                payModal.hide();
                document.getElementById('searchStudentForm').dispatchEvent(new Event('submit'));
            } else {
                alert(res.message);
            }
        });
    });
});

function renderLedger(res) {
    const s = res.student;
    const currentStudentId = s.id;
    document.getElementById('st-name').textContent = s.full_name;
    document.getElementById('st-roll').textContent = s.roll_number || 'PENDING';
    document.getElementById('st-course').textContent = s.course_name;
    document.getElementById('st-center').textContent = `${s.center_name} (${s.center_code})`;
    document.getElementById('st-mobile').textContent = s.mobile;
    if(s.photo) document.getElementById('st-photo').src = '<?php echo BASE_URL; ?>media/students/' + s.photo;
    
    const tbody = document.querySelector('#ledgerTable tbody');
    tbody.innerHTML = '';
    
    let gTotal = 0, gPaid = 0, gBalance = 0;
    let groupName = 'N/A';
    
    res.ledger.forEach((item, index) => {
        groupName = item.group_name;
        const paid = parseFloat(item.paid_amount || 0);
        const total = parseFloat(item.total_amount);
        const balance = total - paid;
        
        gTotal += total;
        gPaid += paid;
        gBalance += balance;
        
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${index + 1}</td>
            <td class="text-start fw-bold text-dark">${item.type_name}</td>
            <td class="text-center">₹ ${total.toLocaleString('en-IN', {minimumFractionDigits: 2})}</td>
            <td class="text-center text-success fw-bold">₹ ${paid.toLocaleString('en-IN', {minimumFractionDigits: 2})}</td>
            <td class="text-center ${balance > 0 ? 'text-danger' : 'text-success'} fw-bold">₹ ${balance.toLocaleString('en-IN', {minimumFractionDigits: 2})}</td>
            <td class="text-end">
                ${balance > 0 ? 
                `<button class="btn btn-sm btn-primary rounded-pill px-3 fw-bold" onclick='openPayModal(${JSON.stringify(item)}, ${balance}, ${currentStudentId})'>
                    <i class="fas fa-rupee-sign me-1"></i>Pay Now
                </button>` : 
                `<span class="badge bg-success-light text-success border border-success rounded-pill px-3"><i class="fas fa-check-circle me-1"></i> Fully Paid</span>`}
            </td>
        `;
        tbody.appendChild(tr);
    });
    
    document.getElementById('st-group').textContent = groupName;
    document.getElementById('g-total').textContent = `₹ ${gTotal.toLocaleString('en-IN', {minimumFractionDigits: 2})}`;
    document.getElementById('g-paid').textContent = `₹ ${gPaid.toLocaleString('en-IN', {minimumFractionDigits: 2})}`;
    document.getElementById('g-balance').textContent = `₹ ${gBalance.toLocaleString('en-IN', {minimumFractionDigits: 2})}`;
    
    // Render Transactions
    const txList = document.getElementById('recentTransactions');
    txList.innerHTML = '';
    if(res.transactions.length === 0) {
        txList.innerHTML = '<div class="list-group-item text-center py-4 text-muted">No recent payment transactions found.</div>';
    } else {
        res.transactions.forEach(tx => {
            const div = document.createElement('div');
            div.className = 'list-group-item d-flex justify-content-between align-items-center py-3';
            div.innerHTML = `
                <div class="d-flex align-items-center">
                    <div class="bg-success-light text-success rounded-circle p-2 me-3" style="width:40px;height:40px;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <div>
                        <span class="fw-bold text-dark d-block">${tx.transaction_id || 'NEB-PAY-#'+tx.id}</span>
                        <small class="text-muted">Mode: <span class="text-uppercase fw-bold">${tx.payment_mode}</span> • Ref: ${tx.transaction_id || 'N/A'}</small>
                    </div>
                </div>
                <div class="text-end d-flex align-items-center gap-3">
                    <div class="me-3">
                        <span class="fw-bold text-success d-block">+ ₹${parseFloat(tx.amount_paid).toLocaleString('en-IN', {minimumFractionDigits: 2})}</span>
                        <small class="text-muted">${new Date(tx.payment_date).toLocaleDateString('en-GB', {day:'2-digit', month:'short', year:'numeric'})}</small>
                    </div>
                    <button class="btn btn-sm btn-outline-secondary btn-icon" title="Print Receipt" onclick="window.open('${HANDLER}?action=print_receipt&id=${tx.id}')"><i class="fas fa-print"></i></button>
                </div>
            `;
            txList.appendChild(div);
        });
    }
}

function openPayModal(item, balance, studentId) {
    document.getElementById('pay-st-id').value = studentId;
    document.getElementById('pay-allocation-id').value = item.allocation_id;
    document.getElementById('pay-fee-type-id').value = item.fee_type_id;
    document.getElementById('pay-fee-name-display').textContent = item.type_name;
    document.getElementById('pay-amount').value = balance;
    document.getElementById('pay-amount').max = balance;
    payModal.show();
}
</script>

<?php include(__DIR__ . "/includes/footer.php"); ?>
