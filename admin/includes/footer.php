    <div class="credits">
        Developed by <a href="https://www.nsprowebtech.com">NS Pro Web Tech</a>
    </div>
</footer><!-- End Footer -->

<!-- Global Details Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Record Details</h5>
                <?php $closeIcon = v('theme_modal_close_icon', 'fas fa-times'); ?>
                <button type="button" class="btn-custom-close ms-auto" data-bs-dismiss="modal">
                    <i class="<?php echo $closeIcon; ?>"></i>
                </button>
            </div>
            <div id="detailsContent" class="modal-body">
                <div class="text-center p-3">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Global Premium Delete Modal -->
<div class="modal fade" id="globalDeleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-danger">
                <h5 class="modal-title fw-bold text-white"><i class="fas fa-exclamation-triangle me-2"></i>Confirm Delete</h5>
                <button type="button" class="btn-custom-close ms-auto" data-bs-dismiss="modal">
                    <i class="<?php echo v('theme_modal_close_icon', 'fas fa-times'); ?>"></i>
                </button>
            </div>
            <div class="modal-body text-center py-5">
                <div class="mb-4">
                    <div class="display-1 text-danger animate__animated animate__pulse animate__infinite">
                        <i class="fas fa-trash-alt"></i>
                    </div>
                </div>
                <h4 class="fw-bold mb-2">Are you sure?</h4>
                <p class="text-muted px-4" id="globalDeleteText">You are about to delete this record. This action might be irreversible.</p>
                <div class="bg-light p-3 rounded-3 mx-4">
                    <span class="text-dark fw-bold" id="globalDeleteTarget"></span>
                </div>
            </div>
            <div class="modal-footer border-0 justify-content-center pb-4">
                <button type="button" class="btn btn-light px-4 fw-bold" data-bs-dismiss="modal">No, Keep it</button>
                <button type="button" class="btn btn-danger px-5 fw-bold shadow-sm" id="globalDeleteConfirmBtn">Yes, Delete Now</button>
            </div>
        </div>
    </div>
</div>

<!-- Vendor JS Files -->
<script src="<?php echo BASE_URL; ?>assets/vendor/jquery/jquery-3.7.1.min.js"></script>
<script src="<?php echo BASE_URL; ?>assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- Dropzone.js for premium uploads -->
<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
<script src="<?php echo BASE_URL; ?>assets/vendor/sweetalert2/sweetalert2.all.min.js"></script>

<!-- DataTables Local Bundle -->
<script src="<?php echo BASE_URL; ?>assets/vendor/datatable/js/datatables.min.js"></script>

<!-- Back to Top -->
<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="fas fa-arrow-up"></i></a>

<!-- Template Main JS File -->
<script src="<?php echo BASE_URL; ?>assets/js/admin.js"></script>
<!-- Location Cascade Module (State → District → City) -->
<script src="<?php echo BASE_URL; ?>assets/js/location-cascade.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        /**
         * SweetAlert2 Toast Configuration (Global)
         */
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        // Trigger session-based success toasts
        <?php if(isset($_SESSION['success'])): ?>
        Toast.fire({
            icon: 'success',
            title: '<?php echo $_SESSION['success']; unset($_SESSION['success']); ?>'
        });
        <?php endif; ?>

        // Trigger session-based error toasts
        <?php if(isset($_SESSION['error'])): ?>
        Toast.fire({
            icon: 'error',
            title: '<?php echo $_SESSION['error']; unset($_SESSION['error']); ?>'
        });
        <?php endif; ?>

        /**
         * Global Premium DataTables Initialization
         */
        if ($.fn.DataTable) {
            $('.datatable-premium').each(function() {
                if (!$.fn.DataTable.isDataTable(this)) {
                    const $table = $(this);
                    const addBtnData = $table.data('add-btn');
                    
                    let buttonsConfig = [];
                    
                    // 1. Add "Add New" Button if data provided
                    if (addBtnData) {
                        buttonsConfig.push({
                            text: `<i class="${addBtnData.icon || 'fas fa-plus'} me-1"></i> ${addBtnData.text}`,
                            className: 'btn btn-sm btn-primary shadow-sm border-0 me-2',
                            action: function() {
                                if (addBtnData.onclick) {
                                    eval(addBtnData.onclick);
                                } else if (addBtnData.href) {
                                    window.location.href = addBtnData.href;
                                }
                            }
                        });
                    }

                    // 2. Add Export Collection (Dropdown)
                    buttonsConfig.push({ 
                        extend: 'collection',
                        text: '<i class="fas fa-download me-1"></i> Export',
                        className: 'btn btn-sm btn-primary shadow-sm border-0',
                        autoClose: true,
                        buttons: [
                            { extend: 'excel', className: 'dropdown-item', text: '<i class="fas fa-file-excel me-2 text-success"></i> Excel' },
                            { extend: 'pdf', className: 'dropdown-item', text: '<i class="fas fa-file-pdf me-2 text-danger"></i> PDF' },
                            { extend: 'print', className: 'dropdown-item', text: '<i class="fas fa-print me-2 text-info"></i> Print' }
                        ]
                    });

                    $(this).DataTable({
                        width: '100%',
                        autoWidth: false,
                        scrollX: false,      
                        scrollY: false,     
                        pageLength: 10,
                        lengthMenu: [10, 25, 50, 100],
                        order: [], // Disable initial sort
                        columnDefs: [
                            { orderable: false, targets: 0 }, // Disable sort on S.No.
                            { orderable: false, targets: -1 } // Disable sort on Action
                        ],
                        language: {
                            search: "_INPUT_",
                            searchPlaceholder: "Search records...",
                            lengthMenu: "Show _MENU_ entries",
                            paginate: {
                                previous: '<i class="fas fa-chevron-left"></i>',
                                next: '<i class="fas fa-chevron-right"></i>'
                            }
                        },
                        // Layout: f=filter (search), B=buttons (Add/Export), l=length, p=pagination
                        // Top: [Search] ... [Add] [Export]
                        // Bottom: [Length] [Pagination]
                        dom: '<"d-flex justify-content-between align-items-center mb-2" f B>rt<"d-flex justify-content-between align-items-center mt-2"lp>',
                        buttons: buttonsConfig,
                        drawCallback: function() {
                            $(this).find('thead th').addClass('bg-light text-dark fw-bold border-bottom');
                            $('.dataTables_paginate .paginate_button').addClass('btn btn-sm mx-1');
                            
                            // Force table width 100%
                            const $table = $(this);
                            $table.css('width', '100%');
                            $table.closest('.dataTables_scrollBody').css('width', '100%');
                            $table.closest('.dataTables_scroll').find('.dataTables_scrollHeadInner, .dataTables_scrollHeadInner table').css('width', '100%');
                            
                            // Recalculate columns to fix alignment
                            setTimeout(() => {
                                if ($.fn.DataTable.isDataTable($table)) {
                                    $table.DataTable().columns.adjust();
                                }
                            }, 150);

                            // Ensure buttons have a gap
                            const $wrapper = $table.closest('.dataTables_wrapper');
                            const $buttons = $wrapper.find('.dt-buttons');
                            $buttons.addClass('d-flex align-items-center gap-2');
                        }
                    });
                }
            });
        }
    });
</script>

</body>
</html>