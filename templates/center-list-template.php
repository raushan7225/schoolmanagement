<?php
$state_filter = $_GET['state'] ?? '';
$search_query = $_GET['query'] ?? '';

$sql = "SELECT * FROM centers WHERE status = 1";
$params = [];

if ($state_filter) {
    $sql .= " AND state = ?";
    $params[] = $state_filter;
}

if ($search_query) {
    $sql .= " AND (name LIKE ? OR director_name LIKE ? OR code LIKE ?)";
    $params[] = "%$search_query%";
    $params[] = "%$search_query%";
    $params[] = "%$search_query%";
}

$sql .= " ORDER BY id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$centers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch available states for filter
$states_stmt = $pdo->query("SELECT DISTINCT state FROM centers WHERE status = 1 ORDER BY state ASC");
$all_states = $states_stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<!-- Search & Filter Section -->
<section class="py-5 bg-light border-bottom">
    <div class="container">
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <form action="" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Select State</label>
                    <select class="form-select border-0 bg-white py-2 shadow-sm" name="state">
                        <option value="">All States</option>
                        <?php foreach($all_states as $st): ?>
                            <option value="<?php echo htmlspecialchars($st); ?>" <?php echo $state_filter == $st ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($st); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label fw-bold">Search Center/Director</label>
                    <input type="text" class="form-control border-0 bg-white py-2 shadow-sm" name="query" value="<?php echo htmlspecialchars($search_query); ?>" placeholder="Enter keywords...">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary-theme w-100 py-2 shadow">SEARCH CENTERS</button>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Content Section -->
<section class="section-padding bg-white">
    <div class="container">
        <div class="table-responsive rounded-4 shadow-sm border">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-primary-theme text-white">
                    <tr>
                        <th class="px-4 py-3">#</th>
                        <th class="py-3">Center & Director</th>
                        <th class="py-3">Photo</th>
                        <th class="py-3">Location Details</th>
                        <th class="py-3">District</th>
                        <th class="py-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($centers): ?>
                        <?php foreach($centers as $index => $c): ?>
                        <tr>
                            <td class="px-4 fw-bold"><?php echo str_pad($index + 1, 2, '0', STR_PAD_LEFT); ?></td>
                            <td>
                                <h6 class="mb-1 fw-bold text-primary-theme"><?php echo htmlspecialchars($c['name']); ?></h6>
                                <span class="small text-muted"><i class="fas fa-user-tie me-1"></i> Director: <?php echo htmlspecialchars($c['director_name']); ?></span>
                            </td>
                            <td>
                                <img src="<?php echo $c['image'] ? BASE_URL . $c['image'] : 'https://placehold.co/50x50/eee/bbb?text=DIR'; ?>" class="rounded-circle border" style="width: 50px; height: 50px; object-fit: cover;" alt="Director">
                            </td>
                            <td>
                                <p class="small mb-0"><i class="fas fa-map-marker-alt text-secondary-theme me-1"></i> <?php echo htmlspecialchars($c['address']); ?></p>
                                <span class="text-xs text-muted">Ph: <?php echo htmlspecialchars($c['mobile']); ?></span>
                            </td>
                            <td><span class="badge bg-light text-dark border px-3"><?php echo htmlspecialchars($c['district']); ?></span></td>
                            <td class="text-center"><span class="badge bg-success px-3">Verified</span></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="fas fa-search fs-1 text-muted mb-3 d-block"></i>
                                <h5 class="text-muted">No centers found matching your criteria.</h5>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="text-center mt-5">
            <p class="text-muted small">Showing <?php echo count($centers); ?> verified centers / <a href="<?php echo BASE_URL; ?>franchise/franchise-application" class="text-primary-theme fw-bold">Apply for Franchise</a></p>
        </div>
    </div>
</section>
