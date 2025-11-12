@extends('layouts.app')

@section('title', 'Customers')
@section('page-title', 'Customer Management')

@section('content')
<div class="flex justify-between items-center mb-4">
  <h2 class="text-2xl font-bold text-green-700">👥 Customers List</h2>
  <div class="flex gap-2">
    <button id="newPaymentBtn" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">💵 New Customer Payment</button>
    <button id="addCustomerBtn" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">+ Add Customer</button>
  </div>
</div>

<div class="bg-white shadow-md rounded-2xl p-6 overflow-x-auto">
  <table class="w-full border-collapse">
    <thead>
      <tr class="bg-green-100 text-green-800">
        <th class="p-3 text-left">#</th>
        <th class="p-3 text-left">Name</th>
        <th class="p-3 text-left">Email</th>
        <th class="p-3 text-left">Contact</th>
        <th class="p-3 text-left">Status</th>
        <th class="p-3 text-left">Actions</th>
      </tr>
    </thead>
    <tbody id="customerTable" class="text-gray-700"></tbody>
  </table>
</div>

<!-- Modal for Add/Edit Customer -->
<div id="customerModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
  <div class="bg-white rounded-xl shadow-lg w-96 p-6">
    <h2 id="modalTitle" class="text-xl font-bold mb-4">Add Customer</h2>
    <form id="customerForm" class="flex flex-col gap-3">
      <input type="text" id="customerName" placeholder="Full Name" class="border rounded p-2" required>
      <input type="email" id="customerEmail" placeholder="Email" class="border rounded p-2" required>
      <input type="text" id="customerContact" placeholder="Contact Number" class="border rounded p-2" required>
      <div class="flex justify-end gap-2 mt-2">
        <button type="button" id="cancelCustomerBtn" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-1 rounded">Cancel</button>
        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-1 rounded">Save</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal for Customer Payment -->
<div id="paymentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
  <div class="bg-white rounded-xl shadow-lg w-full max-w-3xl p-6 overflow-y-auto max-h-[90vh]">
    <h2 id="paymentModalTitle" class="text-2xl font-bold mb-4 text-green-700">Customer Payment</h2>
    <form id="paymentForm" class="flex flex-col gap-3">
      <p id="paymentCustomerName" class="font-semibold text-green-700"></p>

      <!-- Purchase Items -->
      <div id="itemsContainer" class="overflow-y-auto max-h-[50vh]">
        <div class="flex gap-2 mb-2 itemRow">
          <input type="text" placeholder="Item Name" class="border rounded p-2 flex-1 itemName" required>
          <input type="number" placeholder="Qty" min="1" class="border rounded p-2 w-20 itemQty" required>
          <input type="number" placeholder="Price" min="0" step="0.01" class="border rounded p-2 w-28 itemPrice" required>
        </div>
      </div>
      <button type="button" id="addItemBtn" class="bg-green-600 hover:bg-green-700 text-white px-2 py-1 rounded w-fit">+ Add Item</button>

      <input type="number" id="customerPayment" placeholder="Amount Paid by Customer" min="0" step="0.01" class="border rounded p-2" required>
      <p>Total: ₱<span id="totalAmount">0.00</span></p>
      <p>Change: ₱<span id="changeAmount">0.00</span></p>

      <div class="flex justify-end gap-2 mt-2">
        <button type="button" id="cancelPaymentBtn" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-1 rounded">Cancel</button>
        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-1 rounded">Complete Payment</button>
      </div>
    </form>
  </div>
</div>

<!-- Receipt -->
<div id="receiptModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
  <div class="bg-white rounded-xl shadow-lg w-96 p-6">
    <h2 class="text-xl font-bold mb-4">Receipt</h2>
    <div id="receiptContent" class="text-gray-700"></div>
    <button id="printReceiptBtn" class="mt-4 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">🖨️ Print Receipt</button>
    <button id="closeReceiptBtn" class="mt-2 bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-lg">Close</button>
  </div>
</div>
@endsection

@section('scripts')
<script>
let customers = [
  { id: 1, name: "Juan Dela Cruz", email: "juan@example.com", contact: "09123456789" },
  { id: 2, name: "Maria Santos", email: "maria@example.com", contact: "09998887777" },
  { id: 3, name: "Pedro Reyes", email: "pedro@example.com", contact: "09223334444" },
];

let payments = [];

const table = document.getElementById('customerTable');
const customerModal = document.getElementById('customerModal');
const customerForm = document.getElementById('customerForm');
const modalTitle = document.getElementById('modalTitle');
let currentEditId = null;

const paymentModal = document.getElementById('paymentModal');
const paymentForm = document.getElementById('paymentForm');
const paymentCustomerName = document.getElementById('paymentCustomerName');
const itemsContainer = document.getElementById('itemsContainer');
const addItemBtn = document.getElementById('addItemBtn');
const customerPaymentInput = document.getElementById('customerPayment');
const totalAmountEl = document.getElementById('totalAmount');
const changeAmountEl = document.getElementById('changeAmount');

const receiptModal = document.getElementById('receiptModal');
const receiptContent = document.getElementById('receiptContent');
const printReceiptBtn = document.getElementById('printReceiptBtn');
const closeReceiptBtn = document.getElementById('closeReceiptBtn');

// Render Customers with Status
function renderCustomers() {
  table.innerHTML = '';
  customers.forEach(c => {
    const payment = payments.find(p => p.customerId === c.id);
    const status = payment ? 'Paid' : 'Pending';
    table.innerHTML += `
      <tr class="border-b hover:bg-green-50 group">
        <td class="p-3">${c.id}</td>
        <td class="p-3">${c.name}</td>
        <td class="p-3">${c.email}</td>
        <td class="p-3">${c.contact}</td>
        <td class="p-3">
          <span class="${status === 'Paid' ? 'text-green-600 font-semibold' : 'text-yellow-600 font-semibold'}">${status}</span>
        </td>
        <td class="p-3 relative">
          <div class="flex space-x-2">
            <button class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-sm editBtn" data-id="${c.id}">✏️ Edit</button>
            <button class="bg-yellow-500 hover:bg-yellow-600 text-white px-2 py-1 rounded text-sm archiveBtn" data-id="${c.id}">🗑️ Archive</button>
            <button class="bg-green-600 hover:bg-green-700 text-white px-2 py-1 rounded text-sm payBtn" data-id="${c.id}">💵 Payment</button>
          </div>
        </td>
      </tr>
    `;
  });

  // Edit
  document.querySelectorAll('.editBtn').forEach(btn => {
    btn.addEventListener('click', () => {
      currentEditId = parseInt(btn.dataset.id);
      const customer = customers.find(c => c.id === currentEditId);
      modalTitle.textContent = "Edit Customer";
      customerForm.customerName.value = customer.name;
      customerForm.customerEmail.value = customer.email;
      customerForm.customerContact.value = customer.contact;
      customerModal.classList.remove('hidden');
      customerModal.classList.add('flex');
    });
  });

  // Archive
  document.querySelectorAll('.archiveBtn').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = parseInt(btn.dataset.id);
      if(confirm("Archive this customer?")) {
        customers = customers.filter(c => c.id !== id);
        renderCustomers();
      }
    });
  });

  // Payment
  document.querySelectorAll('.payBtn').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = parseInt(btn.dataset.id);
      startPaymentForCustomer(id);
    });
  });
}

renderCustomers();

function startPaymentForCustomer(id){
  const customer = customers.find(c => c.id === id);
  paymentCustomerName.textContent = customer.name;
  itemsContainer.innerHTML = `
    <div class="flex gap-2 mb-2 itemRow">
      <input type="text" placeholder="Item Name" class="border rounded p-2 itemName" required>
      <input type="number" placeholder="Qty" min="1" class="border rounded p-2 itemQty" required>
      <input type="number" placeholder="Price" min="0" step="0.01" class="border rounded p-2 itemPrice" required>
    </div>
  `;
  customerPaymentInput.value = '';
  totalAmountEl.textContent = "0.00";
  changeAmountEl.textContent = "0.00";
  paymentModal.classList.remove('hidden');
  paymentModal.classList.add('flex');
  currentEditId = id;
}

// New Customer Payment
document.getElementById('newPaymentBtn').addEventListener('click', ()=>{
  currentEditId = null;
  modalTitle.textContent = "New Customer Payment";
  customerForm.reset();
  customerModal.classList.remove('hidden');
  customerModal.classList.add('flex');

  customerForm.onsubmit = e => {
    e.preventDefault();
    const nextId = customers.length ? Math.max(...customers.map(c => c.id)) + 1 : 1;
    const newCustomer = {
      id: nextId,
      name: customerForm.customerName.value,
      email: customerForm.customerEmail.value,
      contact: customerForm.customerContact.value
    };
    customers.push(newCustomer);
    customerModal.classList.add('hidden');
    renderCustomers();
    startPaymentForCustomer(newCustomer.id);
    customerForm.onsubmit = null;
  };
});

// Add Customer
document.getElementById('addCustomerBtn').addEventListener('click', () => {
  currentEditId = null;
  modalTitle.textContent = "Add Customer";
  customerForm.reset();
  customerModal.classList.remove('hidden');
  customerModal.classList.add('flex');
});

document.getElementById('cancelCustomerBtn').addEventListener('click', () => {
  customerModal.classList.add('hidden');
});

// Payment Logic
addItemBtn.addEventListener('click', () => {
  const div = document.createElement('div');
  div.classList.add('flex', 'gap-2', 'mb-2', 'itemRow');
  div.innerHTML = `
    <input type="text" placeholder="Item Name" class="border rounded p-2 itemName" required>
    <input type="number" placeholder="Qty" min="1" class="border rounded p-2 itemQty" required>
    <input type="number" placeholder="Price" min="0" step="0.01" class="border rounded p-2 itemPrice" required>
  `;
  itemsContainer.appendChild(div);
});

paymentForm.addEventListener('input', () => {
  const itemRows = document.querySelectorAll('.itemRow');
  let total = 0;
  itemRows.forEach(row => {
    const qty = parseFloat(row.querySelector('.itemQty').value) || 0;
    const price = parseFloat(row.querySelector('.itemPrice').value) || 0;
    total += qty * price;
  });
  totalAmountEl.textContent = total.toFixed(2);

  const paid = parseFloat(customerPaymentInput.value) || 0;
  changeAmountEl.textContent = (paid - total > 0 ? paid - total : 0).toFixed(2);
});

document.getElementById('cancelPaymentBtn').addEventListener('click', () => {
  paymentModal.classList.add('hidden');
});

paymentForm.addEventListener('submit', e => {
  e.preventDefault();
  const itemRows = document.querySelectorAll('.itemRow');
  const items = [];
  itemRows.forEach(row => {
    items.push({
      name: row.querySelector('.itemName').value,
      qty: parseFloat(row.querySelector('.itemQty').value),
      price: parseFloat(row.querySelector('.itemPrice').value),
      total: parseFloat(row.querySelector('.itemQty').value) * parseFloat(row.querySelector('.itemPrice').value)
    });
  });
  const total = parseFloat(totalAmountEl.textContent);
  const paid = parseFloat(customerPaymentInput.value);
  const change = parseFloat(changeAmountEl.textContent);

  payments.push({
    customerId: currentEditId,
    items,
    total,
    paid,
    change
  });

  // Show receipt
  let html = `<p class="font-semibold">${customers.find(c=>c.id===currentEditId).name}</p>
    <table class="w-full text-left mt-2 border-collapse">
      <thead>
        <tr class="border-b">
          <th>Item</th><th>Qty</th><th>Price</th><th>Total</th>
        </tr>
      </thead>
      <tbody>`;
  items.forEach(i => {
    html += `<tr>
      <td>${i.name}</td>
      <td>${i.qty}</td>
      <td>₱${i.price.toFixed(2)}</td>
      <td>₱${i.total.toFixed(2)}</td>
    </tr>`;
  });
  html += `</tbody></table>
    <p class="mt-2">Total: ₱${total.toFixed(2)}</p>
    <p>Paid: ₱${paid.toFixed(2)}</p>
    <p>Change: ₱${change.toFixed(2)}</p>`;

  receiptContent.innerHTML = html;
  paymentModal.classList.add('hidden');
  receiptModal.classList.remove('hidden');

  // Refresh customer table to show Paid status
  renderCustomers();
});

// Print Receipt
printReceiptBtn.addEventListener('click', () => {
  const printWindow = window.open('', '', 'height=600,width=400');
  printWindow.document.write('<html><head><title>Receipt</title></head><body>');
  printWindow.document.write(receiptContent.innerHTML);
  printWindow.document.write('</body></html>');
  printWindow.document.close();
  printWindow.print();
});

// Close receipt
closeReceiptBtn.addEventListener('click', () => {
  receiptModal.classList.add('hidden');
});
</script>
@endsection
