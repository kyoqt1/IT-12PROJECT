<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>CRM FruitStand | Supplier Transactions</title>
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
      <li><a href="/inventory" class="block px-4 py-2 rounded-lg hover:bg-green-600 transition">🍎 Inventory</a></li>
      <li><a href="/sales" class="block px-4 py-2 rounded-lg hover:bg-green-600 transition">💰 Sales</a></li>
    </ul>
  </nav>
  <div class="p-4 border-t border-green-600">
    <button id="logoutBtn" class="w-full bg-red-500 hover:bg-red-600 py-2 rounded-lg font-semibold transition">Logout</button>
  </div>
</aside>

<!-- Main Content -->
<main class="flex-1 flex flex-col">
  <nav class="bg-green-600 text-white p-4 flex justify-between items-center shadow-lg">
    <h1 class="text-xl font-bold">Supplier Transactions</h1>
    <div class="text-sm"><span id="userInfo"></span></div>
  </nav>

  <section class="p-8 overflow-y-auto flex-1">
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-2xl font-bold text-green-700">📦 Transactions</h2>
      <button id="addTransactionBtn" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">+ Add Transaction</button>
    </div>

    <div class="bg-white shadow-md rounded-2xl p-6 overflow-x-auto">
      <table class="w-full border-collapse">
        <thead>
          <tr class="bg-green-100 text-green-800">
            <th class="p-3 text-left">#</th>
            <th class="p-3 text-left">Supplier</th>
            <th class="p-3 text-left">Product</th>
            <th class="p-3 text-left">Quantity</th>
            <th class="p-3 text-left">Unit Price</th>
            <th class="p-3 text-left">Total</th>
            <th class="p-3 text-left">Date</th>
            <th class="p-3 text-left">Status</th>
            <th class="p-3 text-left">Actions</th>
          </tr>
        </thead>
        <tbody id="transactionTable" class="text-gray-700"></tbody>
      </table>
    </div>
  </section>
</main>

<!-- Modal for Adding Transaction -->
<div id="transactionModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
  <div class="bg-white rounded-xl shadow-lg w-96 p-6">
    <h2 id="modalTitle" class="text-xl font-bold mb-4">Add Transaction</h2>
    <form id="transactionForm" class="flex flex-col gap-3">
      <select id="supplierSelect" class="border rounded p-2" required></select>
      <select id="productSelect" class="border rounded p-2" required></select>
      <input type="number" id="quantity" placeholder="Quantity" class="border rounded p-2" required>
      <input type="number" step="0.01" id="unitPrice" placeholder="Unit Price" class="border rounded p-2" required>
      <input type="date" id="transactionDate" class="border rounded p-2" required>
      <div class="flex justify-end gap-2 mt-2">
        <button type="button" id="cancelBtn" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-1 rounded">Cancel</button>
        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-1 rounded">Save</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal for Payment -->
<div id="paymentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
  <div class="bg-white rounded-xl shadow-lg w-96 p-6">
    <h2 id="paymentModalTitle" class="text-xl font-bold mb-4">Pay Supplier</h2>
    <form id="paymentForm" class="flex flex-col gap-3">
      <p id="paymentSupplierName" class="font-semibold text-green-700"></p>
      <p>Total Due: ₱<span id="paymentTotalDue"></span></p>
      <p>Remaining: ₱<span id="paymentRemaining"></span></p>
      <input type="number" step="0.01" id="paymentAmount" placeholder="Payment Amount" class="border rounded p-2" required>
      <select id="paymentMethod" class="border rounded p-2" required>
        <option value="Cash">Cash</option>
        <option value="Gcash">Gcash</option>
      </select>
      <input type="date" id="paymentDate" class="border rounded p-2" required>
      <div class="flex justify-end gap-2 mt-2">
        <button type="button" id="cancelPaymentBtn" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-1 rounded">Cancel</button>
        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-1 rounded">Pay</button>
      </div>
    </form>
  </div>
</div>

<script>
// User info
const user = JSON.parse(localStorage.getItem('loggedInUser'));
if(!user) window.location.href='/';
else document.getElementById('userInfo').textContent = user.email;

document.getElementById('logoutBtn').addEventListener('click', ()=>{
  localStorage.removeItem('loggedInUser');
  window.location.href='/';
});

// Data
let suppliers = JSON.parse(localStorage.getItem('suppliers')) || [
  {id:1, supplier_name:"Fresh Fruits Inc."},
  {id:2, supplier_name:"Tropical Suppliers"},
  {id:3, supplier_name:"Organic Farms"}
];
localStorage.setItem('suppliers', JSON.stringify(suppliers));

let inventory = JSON.parse(localStorage.getItem('inventory')) || [];
localStorage.setItem('inventory', JSON.stringify(inventory));

let transactions = JSON.parse(localStorage.getItem('transactions')) || [];
localStorage.setItem('transactions', JSON.stringify(transactions));

let payments = JSON.parse(localStorage.getItem('payments')) || [];
localStorage.setItem('payments', JSON.stringify(payments));

const transactionTable = document.getElementById('transactionTable');
const transactionModal = document.getElementById('transactionModal');
const transactionForm = document.getElementById('transactionForm');
const supplierSelect = document.getElementById('supplierSelect');
const productSelect = document.getElementById('productSelect');

let currentTransactionIndex = null;

// Populate dropdowns
function populateSuppliers(){
  supplierSelect.innerHTML = '';
  suppliers.forEach(s=>{
    const opt = document.createElement('option');
    opt.value = s.id;
    opt.textContent = s.supplier_name;
    supplierSelect.appendChild(opt);
  });
}
populateSuppliers();

function populateProducts(){
  const supplierId = parseInt(supplierSelect.value);
  const filtered = inventory.filter(p=>p.supplier_id === supplierId);
  productSelect.innerHTML = '';
  filtered.forEach(p=>{
    const opt = document.createElement('option');
    opt.value = p.id;
    opt.textContent = p.product_name;
    productSelect.appendChild(opt);
  });
}
populateProducts();

// Render Table
function renderTable(){
  transactionTable.innerHTML = '';
  transactions.forEach((t,i)=>{
    const supplierName = suppliers.find(s=>s.id===t.supplier_id)?.supplier_name || '-';
    const product = inventory.find(p=>p.id===t.product_id);
    const productName = product?.product_name || '-';
    const total = t.quantity*t.unit_price;

    // Check payment status
    const paidAmount = payments.filter(p=>p.transactionIndex===i).reduce((sum,p)=>sum+p.amount,0);
    const status = paidAmount >= total ? 'Paid' : 'Pending';
    const showPayButton = status === 'Pending' ? `<button class="bg-green-600 hover:bg-green-700 text-white px-2 py-1 rounded payBtn" data-index="${i}">Pay</button>` : '';

    transactionTable.innerHTML += `
      <tr class="border-b hover:bg-green-50 transition">
        <td class="p-3">${i+1}</td>
        <td class="p-3">${supplierName}</td>
        <td class="p-3">${productName}</td>
        <td class="p-3">${t.quantity}</td>
        <td class="p-3">₱${t.unit_price.toFixed(2)}</td>
        <td class="p-3">₱${total.toFixed(2)}</td>
        <td class="p-3">${t.date}</td>
        <td class="p-3 font-semibold ${status==='Pending'?'text-yellow-600':'text-green-600'}">${status}</td>
        <td class="p-3">${showPayButton}</td>
      </tr>
    `;
  });

  setupPayButtons();
}

// Setup Pay Button
function setupPayButtons(){
  document.querySelectorAll('.payBtn').forEach(btn=>{
    btn.addEventListener('click', ()=>{
      currentTransactionIndex = parseInt(btn.dataset.index);
      const t = transactions[currentTransactionIndex];
      const total = t.quantity*t.unit_price;
      const paidAmount = payments.filter(p=>p.transactionIndex===currentTransactionIndex).reduce((sum,p)=>sum+p.amount,0);
      document.getElementById('paymentSupplierName').textContent = suppliers.find(s=>s.id===t.supplier_id)?.supplier_name;
      document.getElementById('paymentTotalDue').textContent = total.toFixed(2);
      document.getElementById('paymentRemaining').textContent = (total-paidAmount).toFixed(2);
      document.getElementById('paymentAmount').value = (total-paidAmount).toFixed(2);
      document.getElementById('paymentMethod').value = 'Cash';
      document.getElementById('paymentDate').value = new Date().toISOString().slice(0,10);
      document.getElementById('paymentModal').classList.remove('hidden');
      document.getElementById('paymentModal').classList.add('flex');
    });
  });
}

renderTable();

// Add Transaction
document.getElementById('addTransactionBtn').addEventListener('click', ()=>{
  transactionForm.reset();
  populateProducts();
  transactionModal.classList.remove('hidden');
  transactionModal.classList.add('flex');
});

document.getElementById('cancelBtn').addEventListener('click', ()=>{
  transactionModal.classList.add('hidden');
});

// Save Transaction
transactionForm.addEventListener('submit', e=>{
  e.preventDefault();
  const newT = {
    supplier_id: parseInt(supplierSelect.value),
    product_id: parseInt(productSelect.value),
    quantity: parseInt(document.getElementById('quantity').value),
    unit_price: parseFloat(document.getElementById('unitPrice').value),
    date: document.getElementById('transactionDate').value
  };
  transactions.push(newT);
  localStorage.setItem('transactions', JSON.stringify(transactions));
  transactionModal.classList.add('hidden');
  renderTable();
});

// Payment Modal
document.getElementById('cancelPaymentBtn').addEventListener('click', ()=>{
  document.getElementById('paymentModal').classList.add('hidden');
});

document.getElementById('paymentForm').addEventListener('submit', e=>{
  e.preventDefault();
  const amount = parseFloat(document.getElementById('paymentAmount').value);
  const date = document.getElementById('paymentDate').value;
  const method = document.getElementById('paymentMethod').value;

  payments.push({transactionIndex: currentTransactionIndex, amount, date, method});
  localStorage.setItem('payments', JSON.stringify(payments));
  document.getElementById('paymentModal').classList.add('hidden');
  renderTable();
});
</script>
</body>
</html>
