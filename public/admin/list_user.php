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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        integrity="sha512-somehashhere" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer" />


    <!-- Font Awesome CDN -->

    <style>
    @media (max-width: 768px) {
        .content {
            margin-left: 60px;
        }
    }
    </style>
</head>

<body class="bg-gray-100 flex min-h-screen">
    <div class="flex">
        <!-- Sidebar -->
        <?php include('../../components/Slidebar.php'); ?>

        <!-- Konten Utama -->
        <div class="content flex-1 p-6">
            <h1 class="text-3xl font-bold mb-6">Daftar Pengguna</h1>
            <div class="bg-white p-4 rounded shadow overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Phone</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Username</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Password</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Akun
                            </th>
                            <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if(!empty($users)): ?>
                        <?php foreach($users as $user): ?>
                        <tr>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($user['email']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($user['phone']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($user['name']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($user['username']); ?></td>
                            <td class="px-4 py-2"><?php echo substr(htmlspecialchars($user['password']), 0, 5); ?></td>
                            <td class="px-4 py-2"><?php echo date("d M Y", strtotime($user['created_at'])); ?></td>
                            <td class="px-4 py-2 text-center space-x-2">
                                <!-- Tombol Edit -->
                                <button
                                    class="editBtn inline-block bg-yellow-500 text-white px-2 py-1 rounded hover:bg-yellow-600 transition"
                                    data-id="<?php echo $user['id']; ?>"
                                    data-email="<?php echo htmlspecialchars($user['email']); ?>"
                                    data-phone="<?php echo htmlspecialchars($user['phone']); ?>"
                                    data-name="<?php echo htmlspecialchars($user['name']); ?>"
                                    data-username="<?php echo htmlspecialchars($user['username']); ?>"
                                    data-password="<?php echo htmlspecialchars($user['password']); ?>"
                                    data-created_at="<?php echo htmlspecialchars($user['created_at']); ?>" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <!-- Tombol Hapus -->
                                <form action="hapus_user.php" method="POST" class="inline-block"
                                    onsubmit="return confirm('Yakin ingin menghapus pengguna ini?');">
                                    <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                    <button type="submit"
                                        class="bg-red-600 text-white px-2 py-1 rounded hover:bg-red-700 transition"
                                        title="Hapus">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="7" class="px-4 py-2 text-center text-gray-500">Belum ada data pengguna.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Edit User -->
    <div id="editModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white rounded-lg shadow-lg max-w-md w-full p-4">
            <h2 class="text-xl font-bold mb-3">Edit Pengguna</h2>
            <form id="editForm" action="edit_user.php" method="POST">
                <input type="hidden" name="id" id="edit_id">
                <div class="mb-3">
                    <label for="edit_email" class="block text-gray-700 font-medium">Email</label>
                    <input type="email" id="edit_email" name="email" required
                        class="w-full border rounded px-3 py-2 mt-1">
                </div>
                <div class="mb-3">
                    <label for="edit_phone" class="block text-gray-700 font-medium">Phone</label>
                    <input type="text" id="edit_phone" name="phone" required
                        class="w-full border rounded px-3 py-2 mt-1">
                </div>
                <div class="mb-3">
                    <label for="edit_name" class="block text-gray-700 font-medium">Name</label>
                    <input type="text" id="edit_name" name="name" required class="w-full border rounded px-3 py-2 mt-1">
                </div>
                <div class="mb-3">
                    <label for="edit_username" class="block text-gray-700 font-medium">Username</label>
                    <input type="text" id="edit_username" name="username" required
                        class="w-full border rounded px-3 py-2 mt-1">
                </div>
                <div class="mb-3">
                    <label for="edit_password" class="block text-gray-700 font-medium">Password</label>
                    <input type="text" id="edit_password" name="password" required
                        class="w-full border rounded px-3 py-2 mt-1">
                    <small class="text-gray-500">Hanya 5 karakter pertama yang akan ditampilkan di list.</small>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" id="closeEditModal"
                        class="bg-gray-600 text-white px-3 py-1 rounded hover:bg-gray-700 transition">Batal</button>
                    <button type="submit"
                        class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700 transition">Update</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Script untuk Modal Edit -->
    <script>
    const editModal = document.getElementById('editModal');
    const closeEditModal = document.getElementById('closeEditModal');
    const editButtons = document.querySelectorAll('.editBtn');

    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Ambil data dari atribut data
            document.getElementById('edit_id').value = this.dataset.id;
            document.getElementById('edit_email').value = this.dataset.email;
            document.getElementById('edit_phone').value = this.dataset.phone;
            document.getElementById('edit_name').value = this.dataset.name;
            document.getElementById('edit_username').value = this.dataset.username;
            document.getElementById('edit_password').value = this.dataset.password;
            // Tampilkan modal edit
            editModal.classList.remove('hidden');
        });
    });

    closeEditModal.addEventListener('click', function() {
        editModal.classList.add('hidden');
    });
    </script>
</body>

</html>