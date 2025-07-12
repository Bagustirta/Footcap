<?php include 'partials/layout.php'; ?>
<?php include 'partials/navbar.php'; ?>

<style>
  .register-wrapper {
    min-height: calc(100vh - 160px);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
  }

  .register-box {
    width: 100%;
    max-width: 420px;
    background-color: #fff;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  }

  .register-box h2 {
    text-align: center;
    margin-bottom: 1.5rem;
  }

  .register-box input {
    width: 100%;
    padding: 0.6rem 0.75rem;
    margin-top: 0.25rem;
    border: 1px solid #ccc;
    background-color: #fff;
    color: #000;
    border-radius: 8px;
  }

  .register-box button {
    display: block;
    margin: 1rem auto 0;
    padding: 0.6rem 2rem;
    font-size: 2rem;
    cursor: pointer;
  }

  .register-box p {
    text-align: center;
    margin-top: 1rem;
  }
</style>

<main>
  <section class="register-wrapper">
    <div class="register-box">
      <h2 class="h2 section-title">Register</h2>
      <form action="register-process.php" method="POST">
        <div>
          <label for="name">Nama Lengkap</label>
          <input type="text" id="name" name="name" required>
        </div>
        <div style="margin-top: 1rem;">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" required>
        </div>
        <div style="margin-top: 1rem;">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" required>
        </div>
        <div style="margin-top: 1rem;">
          <label for="confirm_password">Konfirmasi Password</label>
          <input type="password" id="confirm_password" name="confirm_password" required>
        </div>

        <!-- Hidden input to force 'user' role -->
        <input type="hidden" name="role" value="user">

        <button type="submit" class="btn btn-primary">Register</button>
      </form>
      <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
    </div>
  </section>
</main>
