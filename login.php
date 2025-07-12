<?php include 'partials/layout.php'; ?>
<?php include 'partials/navbar.php'; ?>

<style>
  .login-wrapper {
    min-height: calc(100vh - 160px); /* mengurangi tinggi navbar dan footer */
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
  }

  .login-box {
    width: 100%;
    max-width: 420px;
    background-color: #fff;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  }

  .login-box h2 {
    text-align: center;
    margin-bottom: 1.5rem;
  }

  .login-box input {
    width: 100%;
    padding: 0.6rem 0.75rem;
    margin-top: 0.25rem;
    border: 1px solid #ccc;
    background-color: #fff;
    color: #000;
    border-radius: 8px;
  }
  .login-box button {
    display: block;
    margin: 1rem auto 0;
    padding: 0.6rem 2rem;
    font-size: 2rem;
    margin-top: 1rem;
    cursor: pointer;
  }

  .login-box p {
    text-align: center;
    margin-top: 1rem;
  }
</style>

<main>
  <section class="login-wrapper">
    <div class="login-box">
      <h2 class="h2 section-title">Login</h2>

      <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
        <p style="color: green;">Registrasi berhasil! Silakan login.</p>
      <?php endif; ?>

      <form action="login-process.php" method="POST">
        <div>
          <label for="email">Email</label>
          <input type="email" name="email" id="email" required>
        </div>

        <div style="margin-top: 1rem;">
          <label for="password">Password</label>
          <input type="password" name="password" id="password" required>
        </div>

        <button type="submit" class="btn btn-primary">Login</button>
      </form>

      <p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>
    </div>
  </section>
</main>

<?php include 'partials/footer.php'; ?>
