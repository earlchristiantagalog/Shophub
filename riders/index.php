<?php
session_start();
require '../includes/db.php';

// Redirect if not logged in as rider
if(!isset($_SESSION['rider_auth']) || !isset($_SESSION['rider_id'])) {
    header('Location: ../rider_login.php');
    exit;
}

$rider_id = $_SESSION['rider_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Rider Dashboard</title>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/html5-qrcode@2.3.8/minified/html5-qrcode.min.js"></script>
<style>
    body { transition: background 0.3s, color 0.3s; }
    body.dark { background: #121212; color: #fff; }
    .highlight { background-color: #f0f9ff !important; }
</style>
</head>
<body class="bg-gray-100 min-h-screen dark:bg-gray-900 dark:text-white">

<nav class="bg-white dark:bg-gray-800 shadow p-4 flex justify-between items-center">
    <h1 class="text-xl font-bold">Rider Dashboard</h1>
    <div>
        <button id="toggleDark" class="mr-4 px-3 py-1 bg-gray-700 text-white rounded">Toggle Dark</button>
        <a href="../logout.php" class="text-red-600 font-semibold">Logout</a>
    </div>
</nav>

<div class="max-w-5xl mx-auto mt-6 p-4">

    <!-- QR Scanner -->
    <div class="mb-6">
        <h2 class="text-lg font-bold mb-2">Scan Order QR</h2>
        <select id="cameraSelect" class="mb-2 p-2 border rounded"></select>
        <div id="reader" style="width:100%; height:300px; border:1px solid #ccc;"></div>
        <input type="text" id="manualScan" placeholder="Or enter Order ID manually"
            class="w-full mt-2 p-3 border rounded-lg shadow" />
    </div>

    <!-- Display Scanned Order Info -->
    <div id="orderInfo" class="bg-white dark:bg-gray-800 p-5 shadow rounded-lg hidden">
        <h2 class="text-lg font-bold mb-3">Order Information</h2>
        <p><strong>Order ID:</strong> <span id="infoOrderID"></span></p>
        <p><strong>Customer:</strong> <span id="infoName"></span></p>
        <p><strong>Phone:</strong> <span id="infoPhone"></span></p>
        <p><strong>Address:</strong> <span id="infoAddress"></span></p>
        <div class="mt-4 space-x-2">
            <button id="deliveredBtn" class="bg-green-600 text-white px-3 py-1 rounded">Delivered</button>
            <button id="failedBtn" class="bg-red-600 text-white px-3 py-1 rounded">Failed</button>
        </div>
    </div>

    <!-- Assigned Orders Table -->
    <div class="bg-white dark:bg-gray-800 p-5 shadow rounded-lg overflow-x-auto mt-6">
        <h2 class="text-lg font-bold mb-3">Assigned Deliveries</h2>
        <table id="ordersTable" class="w-full text-left border-collapse dark:border-gray-700">
            <thead>
                <tr class="border-b dark:border-gray-700">
                    <th class="p-2">Order ID</th>
                    <th class="p-2">Customer</th>
                    <th class="p-2">Phone</th>
                    <th class="p-2">Address</th>
                    <th class="p-2 text-center">Action</th>
                </tr>
            </thead>
            <tbody id="ordersBody">
                <!-- Orders will be dynamically injected here -->
            </tbody>
        </table>
    </div>
</div>

<script>
// Dark Mode Toggle
const body = document.body;
document.getElementById('toggleDark').addEventListener('click', ()=>{ body.classList.toggle('dark'); });

// QR Scanner
const html5QrCode = new Html5Qrcode("reader");
const cameraSelect = document.getElementById("cameraSelect");

Html5Qrcode.getCameras().then(cameras => {
    if(cameras && cameras.length){
        cameras.forEach(cam => {
            const option = document.createElement("option");
            option.value = cam.id;
            option.text = cam.label || cam.id;
            cameraSelect.appendChild(option);
        });
        const rearCam = cameras.find(c => /back|rear|environment/i.test(c.label));
        startScanner(rearCam ? rearCam.id : cameras[0].id);
    } else {
        alert("No camera found on this device.");
    }
}).catch(err => console.error(err));

cameraSelect.addEventListener("change", ()=>startScanner(cameraSelect.value));

function startScanner(cameraId){
    html5QrCode.stop().catch(()=>{});
    html5QrCode.start(
        { deviceId: { exact: cameraId } },
        { fps: 10, qrbox: 250 },
        decodedText => fetchOrderInfo(decodedText),
        err => {}
    ).catch(err => alert("Unable to access camera: " + err));
}

// Manual input
document.getElementById('manualScan').addEventListener('keypress', function(e){
    if(e.key==='Enter'){ 
        const orderID = this.value.trim(); 
        if(orderID) fetchOrderInfo(orderID); 
    }
});

// Fetch order info
function fetchOrderInfo(orderID){
    fetch('get_order.php?order_id='+encodeURIComponent(orderID))
    .then(res=>res.json())
    .then(data=>{
        if(data.success){
            document.getElementById('orderInfo').classList.remove('hidden');
            document.getElementById('infoOrderID').innerText = data.order_id;
            document.getElementById('infoName').innerText = data.customer_name;
            document.getElementById('infoPhone').innerText = data.phone;
            document.getElementById('infoAddress').innerText = data.address;

            document.getElementById('deliveredBtn').onclick = ()=>updateStatus(data.order_id,'delivered');
            document.getElementById('failedBtn').onclick = ()=>updateStatus(data.order_id,'failed');

            const row = document.getElementById('row_'+data.order_id);
            if(row) row.classList.add('highlight');
        } else alert(data.message);
    });
}

// Update order status
function updateStatus(orderID,status){
    fetch(`update_status.php?order_id=${orderID}&status=${status}`)
    .then(res=>res.json())
    .then(data=>{
        if(data.success){
            alert(`Order ${orderID} marked as ${status}`);
            const row=document.getElementById('row_'+orderID);
            if(row) row.remove();
            document.getElementById('orderInfo').classList.add('hidden');
        } else alert(data.message);
    });
}

// Dynamically fetch assigned orders
function fetchOrders(){
    fetch('fetch_orders.php')
    .then(res=>res.json())
    .then(data=>{
        if(data.success){
            const tbody = document.getElementById('ordersBody');
            tbody.innerHTML = '';
            data.orders.forEach(o=>{
                const tr = document.createElement('tr');
                tr.id = 'row_'+o.order_id;
                tr.className = 'border-b dark:border-gray-700';
                tr.innerHTML = `
                    <td class="p-2">${o.order_id}</td>
                    <td class="p-2">${o.first_name} ${o.last_name}</td>
                    <td class="p-2">${o.phone}</td>
                    <td class="p-2">${o.address_line_1}, ${o.barangay}, ${o.city}, ${o.province}</td>
                    <td class="p-2 text-center">
                        <button onclick="updateStatus(${o.order_id}, 'delivered')" class='bg-green-600 text-white px-3 py-1 rounded mr-1'>Delivered</button>
                        <button onclick="updateStatus(${o.order_id}, 'failed')" class='bg-red-600 text-white px-3 py-1 rounded'>Failed</button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }
    })
    .catch(err=>console.error(err));
}

// Initial fetch and auto-refresh every 10s
fetchOrders();
setInterval(fetchOrders, 10000);

</script>
</body>
</html>
