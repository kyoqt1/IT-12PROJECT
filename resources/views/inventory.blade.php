<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>CRM FruitStand | Inventory</title>
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
      <li><a href="/suppliers" class="block px-4 py-2 rounded-lg hover:bg-green-600 transition">🚚 Suppliers</a></li>
      <li><a href="/inventory" class="block px-4 py-2 rounded-lg bg-green-600">🍎 Inventory</a></li>
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
    <h1 class="text-xl font-bold">Inventory Management</h1>
    <div class="text-sm"><span id="userInfo"></span></div>
  </nav>

  <section class="p-8 overflow-y-auto flex-1">
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-2xl font-bold text-green-700">🍎 Inventory List</h2>
      <button id="addProductBtn" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">+ Add New Product</button>
    </div>

    <div class="bg-white shadow-md rounded-2xl p-6 overflow-x-auto">
      <table class="w-full border-collapse">
        <thead>
          <tr class="bg-green-100 text-green-800">
            <th class="p-3 text-left">#</th>
            <th class="p-3 text-left">Name</th>
            <th class="p-3 text-left">Image</th>
            <th class="p-3 text-left">Category</th>
            <th class="p-3 text-left">Quantity</th>
            <th class="p-3 text-left">Unit Price</th>
            <th class="p-3 text-left">Supplier</th>
            <th class="p-3 text-left">Expiry</th>
            <th class="p-3 text-left">Reorder</th>
            <th class="p-3 text-left">Actions</th>
          </tr>
        </thead>
        <tbody id="inventoryTable" class="text-gray-700"></tbody>
      </table>
    </div>
  </section>
</main>

<!-- Modal -->
<div id="productModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
  <div class="bg-white rounded-xl shadow-lg w-96 p-6">
    <h2 id="modalTitle" class="text-xl font-bold mb-4">Add Product</h2>
    <form id="productForm" class="flex flex-col gap-3">
      <input type="text" id="productName" placeholder="Product Name" class="border rounded p-2" required>
      <input type="text" id="category" placeholder="Category" class="border rounded p-2" required>
      <input type="number" id="quantity" placeholder="Quantity in Stock" class="border rounded p-2" required>
      <input type="number" step="0.01" id="unitPrice" placeholder="Unit Price" class="border rounded p-2" required>
      <select id="supplierSelect" class="border rounded p-2" required></select>
      <input type="date" id="expiryDate" class="border rounded p-2">
      <input type="number" id="reorderLevel" placeholder="Reorder Level" class="border rounded p-2" required>

      <!-- Image dropdown -->
      <select id="imageSelect" class="border rounded p-2">
        <option value="">Select Image</option>
      </select>

      <div class="flex justify-end gap-2 mt-2">
        <button type="button" id="cancelBtn" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-1 rounded">Cancel</button>
        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-1 rounded">Save</button>
      </div>
    </form>
  </div>
</div>

<script>
// Mock user
const user = JSON.parse(localStorage.getItem('loggedInUser'));
if(!user) window.location.href='/';
else document.getElementById('userInfo').textContent = user.email;

// Logout
document.getElementById('logoutBtn').addEventListener('click', ()=>{
  localStorage.removeItem('loggedInUser');
  window.location.href='/';
});

// Suppliers
let suppliers = [
  { id:1, supplier_name:"Fresh Fruits Inc." },
  { id:2, supplier_name:"Tropical Suppliers" },
  { id:3, supplier_name:"Organic Farms" }
];

// Inventory
let inventory = [
  { id:1, product_name:"Durian", category:"Fruit", quantity:50, unit_price:120, supplier_id:1, expiry_date:"2025-12-31", reorder_level:5, image:"/images/durian.jpg" },
  { id:2, product_name:"Pomelo", category:"Fruit", quantity:40, unit_price:80, supplier_id:2, expiry_date:"2025-12-20", reorder_level:10, image:"/images/Pomelo.jpg" },
  { id:3, product_name:"Mangosteen", category:"Fruit", quantity:30, unit_price:150, supplier_id:3, expiry_date:"2025-12-15", reorder_level:5, image:"/images/mangoesteen.jpg" },
  { id:4, product_name:"Durian Candy", category:"Candy", quantity:100, unit_price:50, supplier_id:1, expiry_date:"2025-12-31", reorder_level:20, image:"/images/durian_candy.jpg" },
  { id:5, product_name:"Orange", category:"Fruit", quantity:60, unit_price:60, supplier_id:2, expiry_date:"2025-12-25", reorder_level:15, image:"/images/orange.jpg" }
];

// Available images for dropdown
const availableImages = [
  "/images/durian.jpg",
  "/images/Pomelo.jpg",
  "/images/mangoesteen.jpg",
  "/images/durian_candy.jpg",
  "/images/orange.jpg"
];

const table = document.getElementById('inventoryTable');
const productModal = document.getElementById('productModal');
const productForm = document.getElementById('productForm');
const modalTitle = document.getElementById('modalTitle');
const supplierSelect = document.getElementById('supplierSelect');
const imageSelect = document.getElementById('imageSelect');

let currentEditId = null;

// Populate suppliers
function populateSuppliers(){
  supplierSelect.innerHTML='';
  suppliers.forEach(s=>{
    const option = document.createElement('option');
    option.value = s.id;
    option.textContent = s.supplier_name;
    supplierSelect.appendChild(option);
  });
}
populateSuppliers();

// Populate image dropdown
function populateImages(){
  imageSelect.innerHTML='<option value="">Select Image</option>';
  availableImages.forEach(img=>{
    const option = document.createElement('option');
    option.value = img;
    option.textContent = img.split('/').pop();
    imageSelect.appendChild(option);
  });
}
populateImages();

// Render table
function renderTable(){
  table.innerHTML='';
  inventory.forEach(p=>{
    const supplierName = suppliers.find(s=>s.id===p.supplier_id)?.supplier_name||"-";
    table.innerHTML+=`
      <tr class="border-b hover:bg-green-50 transition group">
        <td class="p-3">${p.id}</td>
        <td class="p-3">${p.product_name}</td>
        <td class="p-3"><img src="${p.image}" alt="${p.product_name}" class="w-16 h-16 object-cover rounded-lg"></td>
        <td class="p-3">${p.category}</td>
        <td class="p-3">${p.quantity}</td>
        <td class="p-3">₱${p.unit_price.toFixed(2)}</td>
        <td class="p-3">${supplierName}</td>
        <td class="p-3">${p.expiry_date}</td>
        <td class="p-3">${p.reorder_level}</td>
        <td class="p-3 relative">
          <div class="opacity-0 group-hover:opacity-100 flex space-x-2 transition absolute top-0 right-0">
            <button class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-sm editBtn" data-id="${p.id}">✏️ Edit</button>
            <button class="bg-yellow-500 hover:bg-yellow-600 text-white px-2 py-1 rounded text-sm archiveBtn" data-id="${p.id}">📦 Archive</button>
          </div>
        </td>
      </tr>
    `;
  });

  // Edit
  document.querySelectorAll('.editBtn').forEach(btn=>{
    btn.addEventListener('click', ()=>{
      currentEditId=parseInt(btn.dataset.id);
      const product = inventory.find(p=>p.id===currentEditId);
      modalTitle.textContent="Edit Product";
      productForm.productName.value=product.product_name;
      productForm.category.value=product.category;
      productForm.quantity.value=product.quantity;
      productForm.unitPrice.value=product.unit_price;
      productForm.supplierSelect.value=product.supplier_id;
      productForm.expiryDate.value=product.expiry_date;
      productForm.reorderLevel.value=product.reorder_level;
      imageSelect.value=product.image;
      productModal.classList.remove('hidden');
      productModal.classList.add('flex');
    });
  });

  // Archive
  document.querySelectorAll('.archiveBtn').forEach(btn=>{
    btn.addEventListener('click', ()=>{
      const id=parseInt(btn.dataset.id);
      if(confirm("Archive this product?")){
        inventory=inventory.filter(p=>p.id!==id);
        renderTable();
      }
    });
  });
}
renderTable();

// Add Product button
document.getElementById('addProductBtn').addEventListener('click', ()=>{
  currentEditId=null;
  modalTitle.textContent="Add New Product";
  productForm.reset();
  imageSelect.value="";
  productModal.classList.remove('hidden');
  productModal.classList.add('flex');
});

// Cancel button
document.getElementById('cancelBtn').addEventListener('click', ()=>{
  productModal.classList.add('hidden');
});

// Save product
productForm.addEventListener('submit', e=>{
  e.preventDefault();

  const selectedImage = imageSelect.value || "https://via.placeholder.com/60";

  const newProduct={
    product_name: productForm.productName.value,
    category: productForm.category.value,
    quantity: parseInt(productForm.quantity.value),
    unit_price: parseFloat(productForm.unitPrice.value),
    supplier_id: parseInt(productForm.supplierSelect.value),
    expiry_date: productForm.expiryDate.value,
    reorder_level: parseInt(productForm.reorderLevel.value),
    image: selectedImage
  };

  if(currentEditId){
    inventory=inventory.map(p=>p.id===currentEditId?{id:currentEditId,...newProduct}:p);
  } else {
    const nextId = inventory.length?Math.max(...inventory.map(p=>p.id))+1:1;
    inventory.push({id:nextId,...newProduct});
  }

  productModal.classList.add('hidden');
  renderTable();
});
</script>
</body>
</html>
