<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CRM FruitStand | Suppliers</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-green-50 font-sans flex h-screen">

  <!-- Sidebar -->
  <aside class="w-64 bg-green-700 text-white flex flex-col shadow-lg">
    <div class="p-6 text-center border-b border-green-600">
      <h1 class="text-2xl font-bold">🍍 CRM FruitStand</h1>
    </div>

    <nav class="flex-1 p-4">
      <ul class="space-y-2">
        <li><a href="/dashboard" class="block px-4 py-2 rounded-lg hover:bg-green-600 transition">🏠 Dashboard</a></li>
        <li><a href="/customers" class="block px-4 py-2 rounded-lg hover:bg-green-600 transition">👥 Customers</a></li>
        <li><a href="/suppliers" class="block px-4 py-2 rounded-lg bg-green-600">🚚 Suppliers</a></li>
        <li><a href="#" class="block px-4 py-2 rounded-lg hover:bg-green-600 transition">🍎 Inventory</a></li>
        <li><a href="#" class="block px-4 py-2 rounded-lg hover:bg-green-600 transition">💰 Sales</a></li>
      </ul>
    </nav>

    <div class="p-4 border-t border-green-600">
      <button id="logoutBtn" class="w-full bg-red-500 hover:bg-red-600 py-2 rounded-lg font-semibold transition">Logout</button>
    </div>
  </aside>

  <!-- Main Content -->
  <main class="flex-1 flex flex-col">
    <nav class="bg-green-600 text-white p-4 flex justify-between items-center shadow-lg">
      <div>
        <h1 class="text-xl font-bold">Supplier Management</h1>
      </div>
      <div class="text-sm">
        <span id="userInfo"></span>
      </div>
    </nav>

    <section class="p-8 overflow-y-auto flex-1">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold text-green-700">🚚 Suppliers List</h2>
        <button id="addSupplierBtn" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">+ Add New Supplier</button>
      </div>

      <div class="bg-white shadow-md rounded-2xl p-6 overflow-x-auto">
        <table class="w-full border-collapse">
          <thead>
            <tr class="bg-green-100 text-green-800">
              <th class="p-3 text-left">#</th>
              <th class="p-3 text-left">Supplier Name</th>
              <th class="p-3 text-left">Contact Person</th>
              <th class="p-3 text-left">Contact Number</th>
              <th class="p-3 text-left">Address</th>
              <th class="p-3 text-left">Payment Terms</th>
              <th class="p-3 text-left">Actions</th>
            </tr>
          </thead>
          <tbody id="supplierTable" class="text-gray-700"></tbody>
        </table>
      </div>
    </section>
  </main>

  <!-- Supplier Modal (for Add/Edit) -->
  <div id="supplierModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
    <div class="bg-white rounded-xl shadow-lg w-96 p-6">
      <h2 id="modalTitle" class="text-xl font-bold mb-4">Edit Supplier</h2>
      <form id="supplierForm" class="flex flex-col gap-3">
        <input type="text" id="supplierName" class="border rounded p-2" placeholder="Supplier Name" required>
        <input type="text" id="contactPerson" class="border rounded p-2" placeholder="Contact Person" required>
        <input type="text" id="contactNumber" class="border rounded p-2" placeholder="Contact Number" required>
        <input type="text" id="address" class="border rounded p-2" placeholder="Address" required>
        <select id="paymentTerms" class="border rounded p-2" required>
          <option value="Cash">Cash</option>
          <option value="GCash">GCash</option>
        </select>
        <div class="flex justify-end gap-2 mt-2">
          <button type="button" id="cancelBtn" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-1 rounded">Cancel</button>
          <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-1 rounded" id="saveBtn">Save</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    let suppliers = [
      { id: 1, supplier_name: "Fresh Fruits Inc.", contact_person: "Juan Dela Cruz", contact_number: "09123456789", address: "Davao City", payment_terms: "Cash" },
      { id: 2, supplier_name: "Tropical Suppliers", contact_person: "Maria Santos", contact_number: "09998887777", address: "Tagum City", payment_terms: "GCash" },
      { id: 3, supplier_name: "Organic Farms", contact_person: "Pedro Reyes", contact_number: "09223334444", address: "Davao del Norte", payment_terms: "Cash" }
    ];

    const user = JSON.parse(localStorage.getItem('loggedInUser'));
    if (!user) window.location.href = '/';
    else document.getElementById('userInfo').textContent = `${user.email}`;

    document.getElementById('logoutBtn').addEventListener('click', () => {
      localStorage.removeItem('loggedInUser');
      window.location.href = '/';
    });

    const table = document.getElementById('supplierTable');
    const supplierModal = document.getElementById('supplierModal');
    const supplierForm = document.getElementById('supplierForm');
    const modalTitle = document.getElementById('modalTitle');

    let currentEditId = null;

    function renderTable() {
      table.innerHTML = '';
      suppliers.forEach(s => {
        table.innerHTML += `
          <tr class="border-b hover:bg-green-50 transition">
            <td class="p-3">${s.id}</td>
            <td class="p-3">${s.supplier_name}</td>
            <td class="p-3">${s.contact_person}</td>
            <td class="p-3">${s.contact_number}</td>
            <td class="p-3">${s.address}</td>
            <td class="p-3">${s.payment_terms}</td>
            <td class="p-3 flex space-x-2">
              <button class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm editBtn" data-id="${s.id}">✏️ Edit</button>
              <button class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-sm archiveBtn" data-id="${s.id}">📦 Archive</button>
            </td>
          </tr>
        `;
      });

      document.querySelectorAll('.editBtn').forEach(btn => {
        btn.addEventListener('click', () => {
          currentEditId = parseInt(btn.dataset.id);
          const supplier = suppliers.find(s => s.id === currentEditId);
          modalTitle.textContent = 'Edit Supplier';
          supplierForm.supplierName.value = supplier.supplier_name;
          supplierForm.contactPerson.value = supplier.contact_person;
          supplierForm.contactNumber.value = supplier.contact_number;
          supplierForm.address.value = supplier.address;
          supplierForm.paymentTerms.value = supplier.payment_terms;
          supplierModal.classList.remove('hidden');
          supplierModal.classList.add('flex');
        });
      });

      document.querySelectorAll('.archiveBtn').forEach(btn => {
        btn.addEventListener('click', () => {
          const id = parseInt(btn.dataset.id);
          if (confirm(`Are you sure you want to archive ${suppliers.find(s => s.id === id).supplier_name}?`)) {
            suppliers = suppliers.filter(s => s.id !== id);
            renderTable();
          }
        });
      });
    }

    // Add New Supplier button
    document.getElementById('addSupplierBtn').addEventListener('click', () => {
      currentEditId = null;
      modalTitle.textContent = 'Add New Supplier';
      supplierForm.reset();
      supplierModal.classList.remove('hidden');
      supplierModal.classList.add('flex');
    });

    // Cancel button
    document.getElementById('cancelBtn').addEventListener('click', () => {
      supplierModal.classList.add('hidden');
    });

    // Save supplier (add or edit)
    supplierForm.addEventListener('submit', e => {
      e.preventDefault();
      const newSupplier = {
        supplier_name: supplierForm.supplierName.value,
        contact_person: supplierForm.contactPerson.value,
        contact_number: supplierForm.contactNumber.value,
        address: supplierForm.address.value,
        payment_terms: supplierForm.paymentTerms.value
      };

      if (currentEditId) {
        // Edit existing supplier
        suppliers = suppliers.map(s => s.id === currentEditId ? { ...s, ...newSupplier } : s);
      } else {
        // Add new supplier
        const nextId = suppliers.length ? Math.max(...suppliers.map(s => s.id)) + 1 : 1;
        suppliers.push({ id: nextId, ...newSupplier });
      }

      supplierModal.classList.add('hidden');
      renderTable();
    });

    renderTable();
  </script>

</body>
</html>
