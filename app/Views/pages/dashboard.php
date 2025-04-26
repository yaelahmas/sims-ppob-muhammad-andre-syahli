<?= $this->extend('layout/page') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/owl.carousel.min.css') ?>" />
<link rel="stylesheet" href="<?= base_url('assets/css/owl.theme.default.min.css') ?>" />
<style>
  .service-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    margin-bottom: 20px;
  }

  .service-icon {
    width: 60px;
    height: 60px;
    background: #f5f5f5;
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 8px;
  }

  .promo-card {
    background: #f5f5f5;
    border-radius: 15px;
    padding: 0px;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    color: #333;
  }

  .promo-card.red {
    background: #F44336;
    color: white;
  }

  .promo-card.pink {
    background: #F48FB1;
    color: white;
  }

  .promo-card.blue {
    background: #03A9F4;
    color: white;
  }

  .promo-card.gray {
    background: #90A4AE;
    color: white;
  }

  .promo-card.brown {
    background: #A1887F;
    color: white;
  }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div id="alert-placeholder"></div>

<div class="d-flex flex-wrap align-items-start justify-content-between mb-4">

  <div class="d-flex flex-column align-items-center mb-3 mb-md-0">
    <?php if ($profile['data']['profile_image'] == "https://minio.nutech-integrasi.com/take-home-test/null") { ?>
      <img src="<?= base_url('assets/images/profile_photo.png') ?>" alt="Foto Profil" width="60" height="60" class="rounded-circle mb-2">
    <?php } else { ?>
      <img src="<?= $profile['data']['profile_image']; ?>" alt="Foto Profil" width="60" height="60" class="rounded-circle mb-2">
    <?php } ?>

    <div>
      <p class="mb-1">Selamat datang,</p>
      <h5 class="mb-0"><?= esc($profile['data']['first_name'] ?? '') . ' ' . esc($profile['data']['last_name'] ?? '') ?></h5>
    </div>
  </div>

  <div class="saldo-card flex-grow-1 ms-md-5" style="max-width: 800px;">
    <p class="mb-1">Saldo anda</p>
    <h2 class="mb-2" id="saldo-value">Rp •••••</h2>
    <a href="javascript:void(0);" id="toggle-saldo" class="text-white text-decoration-underline small">
      Lihat Saldo <i class="fa fa-eye"></i>
    </a>
  </div>

</div>

<div class="row text-center mb-5">
  <?php foreach ($services['data'] as $row) { ?>
    <div class="col-3 col-md-1 service-item">
      <a href="<?= base_url('transaction/') .  strtolower($row['service_code']); ?>">
        <div class="service-icon">
          <img src="<?= $row['service_icon']; ?>" alt="<?= $row['service_name']; ?>" width="100%">
        </div>
      </a>
      <small><?= $row['service_name']; ?></small>
    </div>
  <?php } ?>
</div>

<p class="mb-3">Temukan promo menarik</p>
<div class="owl-carousel owl-theme">
  <?php foreach ($banners['data'] as $row) { ?>
    <div class="item">
      <div class="promo-card">
        <img src="<?= $row['banner_image']; ?>" alt="<?= $row['banner_name']; ?>" width="100%">
      </div>
    </div>
  <?php } ?>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/owl.carousel.js') ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  $(document).ready(function() {
    let isBalance = <?= json_encode($balance['data']['balance']) ?>;
    let isTampil = false;

    $('#toggle-saldo').click(function() {
      if (!isTampil) {
        $('#saldo-value').text('Rp ' + formatRupiah(isBalance));
        $(this).html('Tutup Saldo <i class="fa fa-eye-slash"></i>');
      } else {
        $('#saldo-value').text('Rp •••••');
        $(this).html('Lihat Saldo <i class="fa fa-eye"></i>');
      }
      isTampil = !isTampil;
    });

    function formatRupiah(angka) {
      return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    var owl = $('.owl-carousel');
    owl.owlCarousel({
      loop: true,
      margin: 10,
      autoplay: true,
      autoplayTimeout: 2500,
      autoplayHoverPause: true,
      responsive: {
        0: {
          items: 1
        },
        600: {
          items: 3
        },
        1000: {
          items: 4
        }
      }
    });
  });
</script>
<?= $this->endSection() ?>