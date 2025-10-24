<?php
session_start();
include 'includes/db.php';

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first = $_POST['first_name'];
    $last = $_POST['last_name'];
    $phone = $_POST['phone'];
    $line1 = $_POST['address_line_1'];
    $barangay = $_POST['barangay'];
    $city = $_POST['city'];
    $province = $_POST['province'];
    $region = $_POST['region'];
    $zip = $_POST['zip_code'];
    $is_default = isset($_POST['is_default']) ? 1 : 0;

    if ($is_default) {
        mysqli_query($conn, "UPDATE addresses SET is_default = 0 WHERE user_id = $user_id");
    }

    $stmt = $conn->prepare("INSERT INTO addresses (user_id, first_name, last_name, phone, address_line_1, barangay, city, province, region, zip_code, is_default)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssssssssi", $user_id, $first, $last, $phone, $line1, $barangay, $city, $province, $region, $zip, $is_default);
    $stmt->execute();
    $stmt->close();

    header('Location: profile.php');
    exit;
}
?>

<?php include 'includes/header.php'; ?>

<div class="container my-5">
    <div class="card shadow-sm p-4 border-0">
        <h3 class="mb-4">Add New Address</h3>
        <form method="POST" class="row g-3">

            <!-- Name & Phone -->
            <div class="col-md-6">
                <label class="form-label">First Name</label>
                <input type="text" name="first_name" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Last Name</label>
                <input type="text" name="last_name" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" class="form-control" required>
            </div>

            <!-- Location Select -->
            <div class="col-md-6">
                <label class="form-label">Region</label>
                <select name="region" id="region" class="form-select" required></select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Province</label>
                <select name="province" id="province" class="form-select" disabled required></select>
            </div>
            <div class="col-md-6">
                <label class="form-label">City / Municipality</label>
                <select name="city" id="city" class="form-select" disabled required></select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Barangay</label>
                <select name="barangay" id="barangay" class="form-select" disabled required></select>
            </div>

            <!-- Street & Zip -->
            <div class="col-md-12">
                <label class="form-label">Street Address</label>
                <textarea name="address_line_1" class="form-control" rows="2" required></textarea>
            </div>
            <div class="col-md-4">
                <label class="form-label">Zip Code</label>
                <input type="text" name="zip_code" class="form-control" required>
            </div>

            <!-- Default Address -->
            <div class="col-md-4 d-flex align-items-center">
                <div class="form-check mt-4">
                    <input class="form-check-input" type="checkbox" name="is_default" id="is_default">
                    <label class="form-check-label" for="is_default">Set as Default</label>
                </div>
            </div>

            <!-- Map -->
            <div class="col-md-12">
                <label class="form-label">Pin Your Location</label>
                <div id="map" style="height: 300px; border-radius: 12px;"></div>
                <input type="hidden" name="latitude" id="latitude">
                <input type="hidden" name="longitude" id="longitude">
            </div>

            <!-- Buttons -->
            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-success flex-fill">Save Address</button>
                <a href="profile.php" class="btn btn-secondary flex-fill">Back</a>
            </div>
        </form>
    </div>
</div>

<style>
    body {
        background: #f5f7fa;
    }

    .form-select, .form-control, textarea {
        border-radius: 8px;
        border: 1px solid #ced4da;
        transition: all 0.3s;
    }

    .form-select:focus, .form-control:focus, textarea:focus {
        border-color: #ff914d;
        box-shadow: 0 0 5px rgba(255, 145, 77, 0.3);
        outline: none;
    }

    button {
        border-radius: 8px;
    }

    #map {
        border: 1px solid #ddd;
    }
</style>

<!-- PSGC API + Google Maps -->
<script src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY"></script>
<script>
    let map, marker;
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');

    function initMap() {
        map = new google.maps.Map(document.getElementById("map"), {
            center: { lat: 10.3157, lng: 123.8854 }, // Default Cebu
            zoom: 12,
        });

        marker = new google.maps.Marker({
            position: map.getCenter(),
            map: map,
            draggable: true
        });

        marker.addListener('dragend', () => {
            const pos = marker.getPosition();
            latInput.value = pos.lat();
            lngInput.value = pos.lng();
        });
    }
    window.onload = initMap;

    async function fetchJSON(url) {
        const res = await fetch(url);
        return await res.json();
    }

    const regionSelect = document.getElementById('region');
    const provinceSelect = document.getElementById('province');
    const citySelect = document.getElementById('city');
    const barangaySelect = document.getElementById('barangay');

    // Load Regions
    fetchJSON('https://psgc.gitlab.io/api/regions/').then(regions => {
        regionSelect.innerHTML = '<option value="">Select Region</option>';
        regions.forEach(region => {
            regionSelect.innerHTML += `<option value="${region.name}">${region.name}</option>`;
        });
    });

    regionSelect.addEventListener('change', async () => {
        const regionName = regionSelect.value;
        provinceSelect.disabled = true;
        citySelect.disabled = true;
        barangaySelect.disabled = true;
        provinceSelect.innerHTML = '<option value="">Loading...</option>';

        if (regionName) {
            const regions = await fetchJSON('https://psgc.gitlab.io/api/regions/');
            const region = regions.find(r => r.name === regionName);
            if (region) {
                const provinces = await fetchJSON(`https://psgc.gitlab.io/api/regions/${region.code}/provinces/`);
                provinceSelect.innerHTML = '<option value="">Select Province</option>';
                provinces.forEach(province => {
                    provinceSelect.innerHTML += `<option value="${province.name}">${province.name}</option>`;
                });
                provinceSelect.disabled = false;
            }
        }
    });

    // Province -> City
    provinceSelect.addEventListener('change', async () => {
        const provinceName = provinceSelect.value;
        citySelect.disabled = true;
        barangaySelect.disabled = true;
        citySelect.innerHTML = '<option value="">Loading...</option>';

        if (provinceName) {
            const regions = await fetchJSON('https://psgc.gitlab.io/api/regions/');
            let provinceCode = null;
            for (const r of regions) {
                const provinces = await fetchJSON(`https://psgc.gitlab.io/api/regions/${r.code}/provinces/`);
                const province = provinces.find(p => p.name === provinceName);
                if (province) { provinceCode = province.code; break; }
            }

            if (provinceCode) {
                const cities = await fetchJSON(`https://psgc.gitlab.io/api/provinces/${provinceCode}/cities-municipalities/`);
                citySelect.innerHTML = '<option value="">Select City / Municipality</option>';
                cities.forEach(city => {
                    citySelect.innerHTML += `<option value="${city.name}">${city.name}</option>`;
                });
                citySelect.disabled = false;
            }
        }
    });

    // City -> Barangay
    citySelect.addEventListener('change', async () => {
        const cityName = citySelect.value;
        barangaySelect.disabled = true;
        barangaySelect.innerHTML = '<option value="">Loading...</option>';

        if (cityName) {
            const regions = await fetchJSON('https://psgc.gitlab.io/api/regions/');
            let cityCode = null;

            outerLoop:
            for (const r of regions) {
                const provinces = await fetchJSON(`https://psgc.gitlab.io/api/regions/${r.code}/provinces/`);
                for (const p of provinces) {
                    const cities = await fetchJSON(`https://psgc.gitlab.io/api/provinces/${p.code}/cities-municipalities/`);
                    const city = cities.find(c => c.name === cityName);
                    if (city) {
                        cityCode = city.code;
                        break outerLoop;
                    }
                }
            }

            if (cityCode) {
                const barangays = await fetchJSON(`https://psgc.gitlab.io/api/cities-municipalities/${cityCode}/barangays/`);
                barangaySelect.innerHTML = '<option value="">Select Barangay</option>';
                barangays.forEach(b => {
                    barangaySelect.innerHTML += `<option value="${b.name}">${b.name}</option>`;
                });
                barangaySelect.disabled = false;
            }
        }
    });
</script>

<?php include 'includes/footer.php'; ?>
