<?php
session_start();
require_once 'conn.php';

// Pastikan user login
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}

$user_id = $_SESSION['user_id'];

// Ambil item dari database
$cart_items = [];
$result = $conn->query("
  SELECT p.*, ci.quantity 
  FROM cart_items ci 
  JOIN products p ON ci.product_id = p.id 
  WHERE ci.user_id = $user_id
");

while ($row = $result->fetch_assoc()) {
  $subtotal = $row['price'] * $row['quantity'];
  $cart_items[] = [
    'product' => $row,
    'quantity' => $row['quantity'],
    'subtotal' => $subtotal
  ];
}

include 'partials/layout.php';
include 'partials/navbar.php';
?>

<main class="container py-5">
  <h2 class="mb-4">Keranjang</h2>

<?php if (empty($cart_items)): ?>
  <!-- Tampilkan jika kosong -->
  <div class="row">
    <div class="col-lg-8 d-flex align-items-center justify-content-center" style="min-height: 300px;">
      <div class="d-flex align-items-center bg-white rounded shadow-sm p-4">
        <img src="assets/images/keranjang.svg" width="130" class="me-4">
        <div>
          <h5 class="fw-bold mb-2">Wah, keranjang belanjamu kosong</h5>
          <p class="text-muted mb-3">Yuk, isi dengan barang-barang impianmu!</p>
          <div class="text-center">
            <a href="index.php" class="btn btn-success btn-sm px-3 rounded-pill d-flex justify-content-center align-items-center" style="height: 32px;">
              Mulai Belanja
            </a>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="bg-white p-4 rounded shadow-sm">
        <h5 class="fw-bold mb-3">Ringkasan belanja</h5>
        <div class="d-flex justify-content-between mb-2">
          <span>Total</span><span>-</span>
        </div>
        <button class="btn btn-secondary w-100 mt-2 py-2 rounded-pill" disabled>Beli</button>
      </div>
    </div>
  </div>
<?php else: ?>
  <!-- Tampilkan item -->
  <form action="checkout.php" method="POST">
  <div class="row">
    <div class="col-lg-8">
      <div class="form-check mb-3">
        <input class="form-check-input" type="checkbox" id="selectAll">
        <label class="form-check-label" for="selectAll">Pilih Semua</label>
      </div>

      <?php foreach ($cart_items as $i => $item): 
        $p = $item['product'];
        $qty = $item['quantity'];
      ?>
      <div class="card mb-3 cart-item p-3 shadow-sm">
        <div class="d-flex align-items-center gap-3">
          <input type="checkbox" class="form-check-input selectItem" name="selected[]" value="<?= $p['id'] ?>">
          <img src="<?= htmlspecialchars($p['image_url']) ?>" alt="Foto" width="80" class="rounded">
          <div class="flex-fill">
            <h6><?= htmlspecialchars($p['name']) ?></h6>
            <p class="mb-1 text-muted">Warna: <?= $p['color'] ?> | Ukuran: <?= $p['size'] ?></p>
            <div class="d-flex align-items-center gap-2">
              <div class="qty-control d-flex align-items-center gap-1">
                <button type="button" class="btn btn-sm btn-outline-secondary minus-btn">−</button>
                <input type="number" name="quantity[<?= $p['id'] ?>]" class="form-control form-control-sm qty-input" value="<?= $qty ?>" min="1">
                <button type="button" class="btn btn-sm btn-outline-secondary plus-btn">+</button>
              </div>
              <strong>Rp<?= number_format($p['price'], 0, ',', '.') ?></strong>
            </div>
          </div>
          <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeItem(<?= $p['id'] ?>, this)">🗑️</button>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <div class="col-lg-4">
      <div class="bg-white p-4 rounded shadow-sm">
        <h5 class="fw-bold mb-3">Ringkasan belanja</h5>
        <div class="d-flex justify-content-between mb-3">
          <span>Total</span>
          <strong id="summaryTotal">Rp0</strong>
        </div>
        <button type="submit" class="btn btn-success w-100 rounded-pill py-2" id="btnCheckout">Beli</button>
      </div>
    </div>
  </div>
</form>
<?php endif; ?>
</main>

<?php if (!empty($cart_items)): ?>
<script>
  const selectAll   = document.getElementById('selectAll');
  const selectItems = document.querySelectorAll('.selectItem');
  const qtyInputs   = document.querySelectorAll('.qty-input');
  const summaryTotal = document.getElementById('summaryTotal');
  const plusBtns    = document.querySelectorAll('.plus-btn');
  const minusBtns   = document.querySelectorAll('.minus-btn');

  // harga & stok per urutan item
  const prices = <?= json_encode(array_column(array_column($cart_items, 'product'), 'price')) ?>;
  const stocks = <?= json_encode(array_column(array_column($cart_items, 'product'), 'stock')) ?>;

  /* ---------- PILIH SEMUA ---------- */
  selectAll.addEventListener('change', () => {
    selectItems.forEach(cb => cb.checked = selectAll.checked);
    updateSummary();
  });

  selectItems.forEach(cb => cb.addEventListener('change', updateSummary));
  qtyInputs .forEach(inp => inp.addEventListener('input',  () => limitInput(inp)));
  qtyInputs .forEach(inp => inp.addEventListener('change', () => limitInput(inp)));

  /* ---------- TOMBOL + / - ---------- */
  plusBtns.forEach((btn, i)  => btn.addEventListener('click', () => adjustQty(i, +1)));
  minusBtns.forEach((btn, i) => btn.addEventListener('click', () => adjustQty(i, -1)));

  /* ---------- FUNGSI BANTUAN ---------- */
  function adjustQty(index, delta){
    let cur = parseInt(qtyInputs[index].value) || 1;
    const max = stocks[index];

    cur = Math.min(Math.max(cur + delta, 1), max);   // batas 1‒stok
    qtyInputs[index].value = cur;

    updateSummary();
    saveQuantity(index);
  }

  function limitInput(inputEl){
    const i   = [...qtyInputs].indexOf(inputEl);
    const max = stocks[i];
    let val   = parseInt(inputEl.value) || 1;

    if (val > max){
      inputEl.value = max;
      alert("Jumlah melebihi stok. Diset ke maksimum.");
    }else if (val < 1){
      inputEl.value = 1;
    }
    updateSummary();
    saveQuantity(i);
  }

  function updateSummary(){
    let total = 0;
    selectItems.forEach((cb, i) => {
      if (cb.checked){
        total += prices[i] * parseInt(qtyInputs[i].value);
      }
    });
    summaryTotal.textContent = new Intl.NumberFormat('id-ID', {
      style:'currency', currency:'IDR', minimumFractionDigits:0
    }).format(total);
  }

  function saveQuantity(index){
    const productId = selectItems[index].value;
    const newQty    = qtyInputs[index].value;

    fetch('update-cart.php', {
      method:'POST',
      headers:{'Content-Type':'application/x-www-form-urlencoded'},
      body:`product_id=${productId}&quantity=${newQty}`
    })
    .then(r => r.json())
    .then(d => {
      if(d.status !== 'success'){
        alert(d.message || 'Gagal menyimpan jumlah');
      }
    });
  }

  updateSummary();

  function saveQuantity(index) {
    const productId = selectItems[index].value;
    const newQty = qtyInputs[index].value;

    fetch('update-cart.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      body: `product_id=${productId}&quantity=${newQty}`
    })
    .then(res => res.json())
    .then(data => {
      if (data.status !== 'success') {
        alert('Gagal menyimpan perubahan jumlah');
      }
    });
  }

  function removeItem(productId, element) {
    if (!confirm('Hapus item ini dari keranjang?')) return;

    fetch('remove-item.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      body: `product_id=${productId}`
    })
    .then(res => res.json())
    .then(data => {
      if (data.status === 'success') {
        const card = element.closest('.cart-item');
        card.remove();
        updateSummary();  // update total harga

        // Jika semua item dihapus, reload agar tampilan kosong muncul
        if (document.querySelectorAll('.cart-item').length === 0) {
          location.reload();
        }
      } else {
        alert('Gagal menghapus item.');
      }
    });
  }

  document.getElementById('btnCheckout').addEventListener('click', function(e) {
    const anyChecked = [...selectItems].some(cb => cb.checked);
    if (!anyChecked) {
      e.preventDefault();
      alert("Silakan pilih minimal satu produk untuk dibeli.");
    }
  });

</script>
<?php endif; ?>
