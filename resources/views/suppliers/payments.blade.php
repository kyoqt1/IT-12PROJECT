<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>CRM FruitStand | Supplier Payments</title>
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
    <h1 class="text-xl font-bold">Supplier Payments</h1>
    <div class="text-sm"><span id="userInfo"></span></div>
  </nav>

  <section class="p-8 overflow-y-auto flex-1">
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-2xl font-bold text-green-700">💵 Payments</h2>
      <button id="addPaymentBtn" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">+ Add Payment</button>
    </div>

    <div class="bg-white shadow-md rounded-2xl p-6 overflow-x-auto">
      <table class="w-full border-collapse">
        <thead>
          <tr class="bg-green-100 text-green-800">
            <th class="p-3 text-left">#</th>
            <th class="p-3 text-left">Supplier</th>
            <th class="p-3 text-left">Total Due</th>
            <th class="p-3 text-left">Paid</th>
            <th class="p-3 text-left">Remaining</th>
            <th class="p-3 text-left">Status</th>
            <th class="p-3 text-left">Method</th>
            <th class="p-3 text-left">Actions</th>
          </tr>
        </thead>
        <tbody id="paymentTable" class="text-gray-700"></tbody>
      </table>
    </div>
  </section>
</main>

<!-- Modal -->
<div id="paymentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
  <div class="bg-white rounded-xl shadow-lg w-96 p-6">
    <h2 id="modalTitle" class="text-xl font-bold mb-4">Pay Supplier</h2>
    <form id="paymentForm" class="flex flex-col gap-3">
      <p id="supplierName" class="font-semibold text-green-700"></p>
      <p>Total Due: ₱<span id="totalDue"></span></p>
      <p>Remaining Balance: ₱<span id="remainingBalance"></span></p>
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

// Always fetch latest suppliers, transactions, and payments from localStorage
function getSuppliers(){ return JSON.parse(localStorage.getItem('suppliers')) || []; }
function getTransactions(){ return JSON.parse(localStorage.getItem('transactions')) || []; }
function getPayments(){ return JSON.parse(localStorage.getItem('payments')) || []; }

const paymentTable = document.getElementById('paymentTable');
const paymentModal = document.getElementById('paymentModal');
const paymentForm = document.getElementById('paymentForm');
const supplierNameEl = document.getElementById('supplierName');
const totalDueEl = document.getElementById('totalDue');
const remainingBalanceEl = document.getElementById('remainingBalance');
const paymentAmountEl = document.getElementById('paymentAmount');
const paymentDateEl = document.getElementById('paymentDate');
const paymentMethodEl = document.getElementById('paymentMethod');

let currentSupplierId = null;

// Calculate total due for a supplier
function getTotalDue(supplierId){
  const transactions = getTransactions();
  const payments = getPayments();
  const supplierTransactions = transactions.filter(t=>t.supplier_id===supplierId);
  const total = supplierTransactions.reduce((sum,t)=> sum + t.quantity*t.unit_price,0);
  const paid = payments.filter(p=>p.supplier_id===supplierId).reduce((sum,p)=> sum + p.amount,0);
  return {total, paid, remaining: total - paid};
}

// Render payments table
function renderPayments(){
  const suppliers = getSuppliers();
  const transactions = getTransactions();
  const payments = getPayments();
  
  paymentTable.innerHTML = '';
  let filteredSuppliers = suppliers.filter(s=>{
    const {remaining} = getTotalDue(s.id);
    return remaining > 0;
  });

  if(filteredSuppliers.length === 0){
    paymentTable.innerHTML = `<tr><td colspan="8" class="p-3 text-center text-gray-500">No suppliers with pending payments!</td></tr>`;
    return;
  }

  filteredSuppliers.forEach((s,i)=>{
    const {total, paid, remaining} = getTotalDue(s.id);
    const status = remaining > 0 ? 'Pending' : 'Paid';
    const lastPayment = payments.filter(p=>p.supplier_id===s.id).slice(-1)[0];
    const method = lastPayment ? lastPayment.method : '-';
    paymentTable.innerHTML += `
      <tr class="border-b hover:bg-green-50 transition">
        <td class="p-3">${i+1}</td>
        <td class="p-3">${s.supplier_name}</td>
        <td class="p-3">₱${total.toFixed(2)}</td>
        <td class="p-3">₱${paid.toFixed(2)}</td>
        <td class="p-3">₱${remaining.toFixed(2)}</td>
        <td class="p-3 font-semibold text-yellow-600">${status}</td>
        <td class="p-3">${method}</td>
        <td class="p-3">
          <button class="bg-green-600 hover:bg-green-700 text-white px-2 py-1 rounded payBtn" data-id="${s.id}">Pay</button>
        </td>
      </tr>
    `;
  });
  setupPayButtons();
}
renderPayments();

// Setup pay buttons
function setupPayButtons(){
  document.querySelectorAll('.payBtn').forEach(btn=>{
    btn.addEventListener('click', ()=>{
      currentSupplierId = parseInt(btn.dataset.id);
      const suppliers = getSuppliers();
      const {total, paid, remaining} = getTotalDue(currentSupplierId);
      supplierNameEl.textContent = suppliers.find(s=>s.id===currentSupplierId).supplier_name;
      totalDueEl.textContent = total.toFixed(2);
      remainingBalanceEl.textContent = remaining.toFixed(2);
      paymentAmountEl.value = remaining.toFixed(2);
      paymentMethodEl.value = 'Cash';
      paymentDateEl.value = new Date().toISOString().slice(0,10);
      paymentModal.classList.remove('hidden');
      paymentModal.classList.add('flex');
    });
  });
}

// Add Payment button
document.getElementById('addPaymentBtn').addEventListener('click', ()=>{
  const suppliers = getSuppliers();
  const unpaidSupplier = suppliers.find(s=>getTotalDue(s.id).remaining > 0);
  if(!unpaidSupplier){
    alert("No suppliers with pending payments!");
    return;
  }
  currentSupplierId = unpaidSupplier.id;
  const {total, paid, remaining} = getTotalDue(currentSupplierId);
  supplierNameEl.textContent = unpaidSupplier.supplier_name;
  totalDueEl.textContent = total.toFixed(2);
  remainingBalanceEl.textContent = remaining.toFixed(2);
  paymentAmountEl.value = remaining.toFixed(2);
  paymentMethodEl.value = 'Cash';
  paymentDateEl.value = new Date().toISOString().slice(0,10);
  paymentModal.classList.remove('hidden');
  paymentModal.classList.add('flex');
});

// Cancel payment
document.getElementById('cancelPaymentBtn').addEventListener('click', ()=>{
  paymentModal.classList.add('hidden');
});

// Save payment
paymentForm.addEventListener('submit', e=>{
  e.preventDefault();
  const amount = parseFloat(paymentAmountEl.value);
  const date = paymentDateEl.value;
  const method = paymentMethodEl.value;

  if(amount <= 0){
    alert("Payment must be greater than 0");
    return;
  }

  let payments = getPayments();
  payments.push({supplier_id: currentSupplierId, amount, date, method});
  localStorage.setItem('payments', JSON.stringify(payments));
  paymentModal.classList.add('hidden');
  renderPayments();
});

// Optional: refresh table if localStorage changes (new transaction)
window.addEventListener('storage', ()=>{
  renderPayments();
});
</script>
</body>
</html>
