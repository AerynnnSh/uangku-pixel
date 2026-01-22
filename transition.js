document.addEventListener("DOMContentLoaded", function () {
  const links = document.querySelectorAll("a");
  const forms = document.querySelectorAll("form");

  // 1. Handle Link Clicks
  links.forEach((link) => {
    link.addEventListener("click", function (e) {
      // Abaikan link hash, target blank, atau link download/logout tanpa animasi
      if (
        this.href.includes("#") ||
        this.target === "_blank" ||
        this.href.includes("export.php")
      )
        return;

      e.preventDefault();
      const target = this.href;

      document.body.classList.add("exiting");

      // WAKTU DIPERCEPAT JADI 200ms (0.2 detik)
      setTimeout(() => {
        window.location.href = target;
      }, 200);
    });
  });

  // 2. Handle Form Submits
  forms.forEach((form) => {
    form.addEventListener("submit", function (e) {
      // Validasi manual form transaksi
      if (form.id === "transactionForm") {
        const d = form.querySelector('input[name="tanggal"]').value;
        const c = form.querySelector('input[name="kategori"]').value;
        const a = form.querySelector('input[name="jumlah"]').value;
        if (!d || !c || !a || new Date(d) > new Date().setHours(0, 0, 0, 0))
          return;
      }

      if (form.checkValidity()) {
        document.body.classList.add("exiting");
      }
    });
  });
});
