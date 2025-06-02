<?php
session_start();
include("../../config/config.php");

// Ambil data user dari database
$users = [];
$sql = "SELECT id, email, phone, name, username, password, created_at FROM users ORDER BY created_at DESC";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>List User</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Remix Icon CDN for modern icons -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap');

    body {
        font-family: 'Inter', sans-serif;
        background: linear-gradient(135deg, #f0f4ff 0%, #e0e7ff 100%);
    }

    .glass {
        background: rgba(255, 255, 255, 0.85);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.10);
        backdrop-filter: blur(6px);
        border-radius: 1.5rem;
    }

    .modern-table th {
        background: #f1f5f9;
        color: #6366f1;
        font-weight: 700;
        letter-spacing: 0.05em;
    }

    .modern-table td {
        background: transparent;
    }

    .modern-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        font-weight: 500;
        border-radius: 0.5rem;
        transition: all 0.2s;
        box-shadow: 0 2px 8px 0 rgba(99, 102, 241, 0.08);
    }

    .modern-btn-edit {
        background: linear-gradient(90deg, #6366f1 0%, #818cf8 100%);
        color: #fff;
    }

    .modern-btn-edit:hover {
        background: linear-gradient(90deg, #818cf8 0%, #6366f1 100%);
        transform: translateY(-2px) scale(1.04);
    }

    .modern-btn-delete {
        background: linear-gradient(90deg, #ef4444 0%, #f87171 100%);
        color: #fff;
    }

    .modern-btn-delete:hover {
        background: linear-gradient(90deg, #f87171 0%, #ef4444 100%);
        transform: translateY(-2px) scale(1.04);
    }

    .modern-modal {
        background: rgba(255, 255, 255, 0.97);
        border-radius: 1.25rem;
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
        padding: 2rem;
        max-width: 420px;
        width: 100%;
    }

    .modern-input {
        border: 1.5px solid #e0e7ff;
        border-radius: 0.75rem;
        padding: 0.75rem 1rem;
        font-size: 1rem;
        transition: border 0.2s;
        background: #f8fafc;
    }

    .modern-input:focus {
        border-color: #6366f1;
        outline: none;
        background: #fff;
    }

    .modern-label {
        font-weight: 600;
        color: #6366f1;
        margin-bottom: 0.25rem;
        display: block;
    }

    .modern-title {
        font-size: 2.25rem;
        font-weight: 800;
        color: #6366f1;
        letter-spacing: -0.03em;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .modern-title i {
        font-size: 2.5rem;
        color: #818cf8;
    }

    .modern-modal-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #6366f1;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .modern-modal-title i {
        font-size: 1.75rem;
        color: #818cf8;
    }
    </style>
</head>

<body class="flex min-h-screen">
    <div class="flex w-full">
        <!-- Sidebar -->
        <?php include('../../components/Slidebar.php'); ?>

        <!-- Main Content -->
        <div class="content flex-1 p-8">
            <div class="modern-title">
                <i class="ri-user-3-line"></i>
                Daftar Pengguna
            </div>
            <div class="glass p-6 rounded-2xl shadow-lg overflow-x-auto">
                <table class="modern-table min-w-full divide-y divide-indigo-100 rounded-xl overflow-hidden">
                    <thead>
                        <tr>
                            <th class="px-4 py-3 text-left text-xs uppercase">Email</th>
                            <th class="px-4 py-3 text-left text-xs uppercase">Phone</th>
                            <th class="px-4 py-3 text-left text-xs uppercase">Name</th>
                            <th class="px-4 py-3 text-left text-xs uppercase">Username</th>
                            <th class="px-4 py-3 text-left text-xs uppercase">Password</th>
                            <th class="px-4 py-3 text-left text-xs uppercase">Tanggal Akun</th>
                            <th class="px-4 py-3 text-center text-xs uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-indigo-50">
                        <?php if(!empty($users)): ?>
                        <?php foreach($users as $user): ?>
                        <?php if($user['id'] == 30) continue; ?>
                        <tr class="hover:bg-indigo-50 transition">
                            <td class="px-4 py-3"><?php echo htmlspecialchars($user['email']); ?></td>
                            <td class="px-4 py-3"><?php echo htmlspecialchars($user['phone']); ?></td>
                            <td class="px-4 py-3"><?php echo htmlspecialchars($user['name']); ?></td>
                            <td class="px-4 py-3"><?php echo htmlspecialchars($user['username']); ?></td>
                            <td class="px-4 py-3 tracking-widest">
                                <?php echo substr(htmlspecialchars($user['password']), 0, 5); ?></td>
                            <td class="px-4 py-3"><?php echo date("d M Y", strtotime($user['created_at'])); ?></td>
                            <td class="px-4 py-3 text-center space-x-2">
                                <!-- Edit Button -->
                                <button class="editBtn modern-btn modern-btn-edit px-3 py-1.5"
                                    data-id="<?php echo $user['id']; ?>"
                                    data-email="<?php echo htmlspecialchars($user['email']); ?>"
                                    data-phone="<?php echo htmlspecialchars($user['phone']); ?>"
                                    data-name="<?php echo htmlspecialchars($user['name']); ?>"
                                    data-username="<?php echo htmlspecialchars($user['username']); ?>"
                                    data-password="<?php echo htmlspecialchars($user['password']); ?>"
                                    data-created_at="<?php echo htmlspecialchars($user['created_at']); ?>" title="Edit">
                                    <i class="ri-edit-2-line"></i> Edit
                                </button>
                                <!-- Delete Button -->
                                <form action="hapus_user.php" method="POST" class="inline-block hapusForm">
                                    <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                    <button type="submit" class="modern-btn modern-btn-delete px-3 py-1.5"
                                        title="Hapus">
                                        <i class="ri-delete-bin-6-line"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="7" class="px-4 py-3 text-center text-indigo-400">Belum ada data pengguna.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Edit User -->
    <div id="editModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-40 hidden z-50">
        <div class="modern-modal relative">
            <div class="modern-modal-title">
                <i class="ri-user-settings-line"></i>
                Edit Pengguna
            </div>
            <form id="editForm" action="edit_user.php" method="POST">
                <input type="hidden" name="id" id="edit_id">
                <div class="mb-4">
                    <label for="edit_email" class="modern-label">Email</label>
                    <input type="email" id="edit_email" name="email" required class="modern-input w-full">
                </div>
                <div class="mb-4">
                    <label for="edit_phone" class="modern-label">Phone</label>
                    <input type="text" id="edit_phone" name="phone" required class="modern-input w-full">
                </div>
                <div class="mb-4">
                    <label for="edit_name" class="modern-label">Name</label>
                    <input type="text" id="edit_name" name="name" required class="modern-input w-full">
                </div>
                <div class="mb-4">
                    <label for="edit_username" class="modern-label">Username</label>
                    <input type="text" id="edit_username" name="username" required class="modern-input w-full">
                </div>
                <div class="mb-4">
                    <label for="edit_password" class="modern-label">Password</label>
                    <input type="text" id="edit_password" name="password" required class="modern-input w-full">
                    <small class="text-indigo-400">Hanya 5 karakter pertama yang akan ditampilkan di list.</small>
                </div>
                <div class="flex justify-end gap-3 mt-2">
                    <button type="button" id="closeEditModal"
                        class="modern-btn px-4 py-1.5 bg-gray-200 text-indigo-600 hover:bg-gray-300 transition">Batal</button>
                    <button type="submit" class="modern-btn modern-btn-edit px-4 py-1.5"><i class="ri-save-3-line"></i>
                        Update</button>
                </div>
            </form>
            <button id="closeEditModalX"
                class="absolute top-3 right-3 text-indigo-400 hover:text-indigo-700 text-2xl focus:outline-none"
                title="Tutup">
                <i class="ri-close-line"></i>
            </button>
        </div>
    </div>

    <!-- Script untuk Modal Edit dan SweetAlert -->
    <script>
    // SweetAlert untuk tombol Edit
    const editModal = document.getElementById('editModal');
    const closeEditModal = document.getElementById('closeEditModal');
    const closeEditModalX = document.getElementById('closeEditModalX');
    const editButtons = document.querySelectorAll('.editBtn');

    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            Swal.fire({
                title: 'Edit Pengguna',
                text: 'Apakah Anda yakin ingin mengedit data pengguna ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#6366f1',
                cancelButtonColor: '#e0e7ff',
                confirmButtonText: 'Ya, Edit'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('edit_id').value = this.dataset.id;
                    document.getElementById('edit_email').value = this.dataset.email;
                    document.getElementById('edit_phone').value = this.dataset.phone;
                    document.getElementById('edit_name').value = this.dataset.name;
                    document.getElementById('edit_username').value = this.dataset.username;
                    document.getElementById('edit_password').value = this.dataset.password;
                    editModal.classList.remove('hidden');
                }
            });
        });
    });

    [closeEditModal, closeEditModalX].forEach(btn => {
        btn.addEventListener('click', function() {
            editModal.classList.add('hidden');
        });
    });

    // SweetAlert untuk tombol Update (submit form edit)
    document.getElementById('editForm').addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Update Pengguna',
            text: 'Yakin ingin menyimpan perubahan?',
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#6366f1',
            cancelButtonColor: '#e0e7ff',
            confirmButtonText: 'Ya, Update'
        }).then((result) => {
            if (result.isConfirmed) {
                e.target.submit();
            }
        });
    });

    // SweetAlert untuk tombol Hapus
    document.querySelectorAll('.hapusForm').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Hapus Pengguna',
                text: 'Yakin ingin menghapus pengguna ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#e0e7ff',
                confirmButtonText: 'Ya, Hapus'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
    </script>
</body>

</html>