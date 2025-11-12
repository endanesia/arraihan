<?php
session_start();
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../inc/db.php';

// Fetch all messages
$messages = [];
if (function_exists('db') && db()) {
    $res = db()->query("SELECT * FROM contact_messages ORDER BY tgl DESC");
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $messages[] = $row;
        }
    }
}

$pageTitle = 'Pesan Masuk';
include __DIR__ . '/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-envelope"></i> Pesan Masuk
                    </h5>
                    <span class="badge bg-primary"><?= count($messages) ?> Pesan</span>
                </div>
                <div class="card-body">
                    <?php if (empty($messages)): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Belum ada pesan masuk.
                    </div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="15%">Nama</th>
                                    <th width="15%">Email</th>
                                    <th width="12%">WhatsApp</th>
                                    <th width="35%">Pesan</th>
                                    <th width="13%">Tanggal</th>
                                    <th width="5%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($messages as $index => $msg): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= e($msg['nama']) ?></td>
                                    <td>
                                        <a href="mailto:<?= e($msg['email']) ?>">
                                            <?= e($msg['email']) ?>
                                        </a>
                                    </td>
                                    <td>
                                        <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $msg['wa']) ?>" target="_blank">
                                            <i class="fab fa-whatsapp text-success"></i>
                                            <?= e($msg['wa']) ?>
                                        </a>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-link text-start p-0" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#messageModal<?= $msg['id'] ?>">
                                            <?= e(substr($msg['pesan'], 0, 80)) ?><?= strlen($msg['pesan']) > 80 ? '...' : '' ?>
                                        </button>
                                    </td>
                                    <td>
                                        <small>
                                            <?= date('d/m/Y H:i', strtotime($msg['tgl'])) ?>
                                        </small>
                                    </td>
                                    <td>
                                        <a href="message-delete.php?id=<?= $msg['id'] ?>" 
                                           class="btn btn-sm btn-danger" 
                                           onclick="return confirm('Yakin ingin menghapus pesan ini?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                
                                <!-- Modal for full message -->
                                <div class="modal fade" id="messageModal<?= $msg['id'] ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Pesan dari <?= e($msg['nama']) ?></h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <strong>Email:</strong><br>
                                                    <a href="mailto:<?= e($msg['email']) ?>"><?= e($msg['email']) ?></a>
                                                </div>
                                                <div class="mb-3">
                                                    <strong>WhatsApp:</strong><br>
                                                    <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $msg['wa']) ?>" target="_blank">
                                                        <?= e($msg['wa']) ?>
                                                    </a>
                                                </div>
                                                <div class="mb-3">
                                                    <strong>Tanggal:</strong><br>
                                                    <?= date('d F Y, H:i', strtotime($msg['tgl'])) ?>
                                                </div>
                                                <div class="mb-3">
                                                    <strong>Pesan:</strong><br>
                                                    <div class="border p-3 bg-light rounded">
                                                        <?= nl2br(e($msg['pesan'])) ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $msg['wa']) ?>" 
                                                   target="_blank" 
                                                   class="btn btn-success">
                                                    <i class="fab fa-whatsapp"></i> Balas via WhatsApp
                                                </a>
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>
