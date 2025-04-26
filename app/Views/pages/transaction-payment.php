<?= $this->extend('layout/page') ?>

<?= $this->section('styles') ?>
<style>
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div id="alert-placeholder"></div>

<div class="d-flex flex-wrap align-items-start justify-content-between mb-4">
    <div class="d-flex flex-column align-items-center mb-3 mb-md-0">
        <?php if ($profile['data']['profile_image'] == "https://minio.nutech-integrasi.com/take-home-test/null") { ?>
            <img src="<?= base_url('assets/images/profile_photo.png') ?>" alt="User Avatar" width="60" height="60" class="rounded-circle mb-2">
        <?php } else { ?>
            <img src="<?= $profile['data']['profile_image']; ?>" alt="User Avatar" width="60" height="60" class="rounded-circle mb-2">
        <?php } ?>

        <div>
            <p class="mb-1">Selamat datang,</p>
            <h5 class="mb-0"><?= esc($profile['data']['first_name'] ?? '') . ' ' . esc($profile['data']['last_name'] ?? '') ?></h5>
        </div>
    </div>

    <div class="saldo-card flex-grow-1 ms-md-5" style="max-width: 800px;">
        <p class="mb-1">Saldo anda</p>
        <h2 class="mb-2" id="saldo-value">Rp <?= number_format($balance['data']['balance'], 0, ',', '.'); ?></h2>
        <a href="javascript:void(0);" id="toggle-saldo" class="text-white text-decoration-underline small">
            Tutup Saldo <i class="fa fa-eye-slash"></i>
        </a>
    </div>
</div>


<div class="">
    <p class="mb-1">Pembayaran</p>
    <img src="<?= $service['service_icon']; ?>" alt="<?= $service['service_name']; ?>" width="40">
    <span><?= $service['service_name']; ?></span>
    <div class="paymentForm mt-3">
        <div class="mb-3">
            <input type="number" class="form-control" name="service_tariff" id="service_tariff" value="<?= $service['service_tariff']; ?>" readonly>
        </div>

        <button type="submit" id="paymentBtn" class="btn btn-danger btn-block">Bayar</button>
        <a href="<?= base_url('/'); ?>" class="btn btn-outline-danger btn-block">Kembali</a>
    </div>
</div>


<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        let isBalance = <?= json_encode($balance['data']['balance']) ?>;
        let isTampil = true;

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

        $('#paymentBtn').on('click', function() {
            let service_tariff = $('#service_tariff').val();
            let service_name = <?= json_encode($service['service_name']) ?>;
            let service_code = <?= json_encode($service['service_code']) ?>;

            Swal.fire({
                html: `
                    <p style="font-size: 16px; margin-top: 10px;">Bayar ${service_name} senilai</p>
                    <h3 style="font-weight: bold; margin-top: 5px;">Rp ${formatRupiah(service_tariff)} ?</h3>
                `,
                showCancelButton: true,
                confirmButtonText: 'Ya, lanjutkan Bayar',
                cancelButtonText: 'Batalkan',
                reverseButtons: true,
                customClass: {
                    popup: 'swal2-poppins',
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-secondary'
                },
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: "/transaction",
                        data: {
                            service_code: service_code
                        },
                        dataType: "json",
                        beforeSend: function() {
                            $('button[type=submit]').attr('disabled', true).text('Loading...');
                            $('#alert-placeholder').html('');
                        },
                        success: function(response) {
                            $('button[type=submit]').attr('disabled', false).text('Bayar');

                            if (response.status) {
                                Swal.fire({
                                    html: `
                                        <p style="font-size: 16px; margin-top: 10px;">Pembayaran ${service_name} sebesar</p>
                                        <h3 style="font-weight: bold; margin-top: 5px;">Rp ${formatRupiah(service_tariff)}</h3>
                                        <p style="font-size: 16px; margin-top: 10px;">berhasil!</p>
                                    `,
                                    confirmButtonText: 'Kembali ke Beranda',
                                    reverseButtons: true,
                                    customClass: {
                                        popup: 'swal2-poppins',
                                        confirmButton: 'btn btn-danger',
                                    },
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href = '/';
                                    } else {
                                        location.reload();
                                    }
                                });
                            } else {
                                Swal.fire({
                                    html: `
                                        <p style="font-size: 16px; margin-top: 10px;">Pembayaran ${service_name} sebesar</p>
                                        <h3 style="font-weight: bold; margin-top: 5px;">Rp ${formatRupiah(service_tariff)}</h3>
                                        <p style="font-size: 16px; margin-top: 10px;">gagal</p>
                                    `,
                                    confirmButtonText: 'Kembali ke Beranda',
                                    reverseButtons: true,
                                    customClass: {
                                        popup: 'swal2-poppins',
                                        confirmButton: 'btn btn-danger',
                                    },
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href = '/';
                                    } else {
                                        location.reload();
                                    }
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            $('button[type=submit]').attr('disabled', false).text('Top Up');
                            $('#alert-placeholder').html('<div class="alert alert-danger">Terjadi kesalahan koneksi atau server error.</div>');
                        }
                    });
                }
            });
        });

        function formatRupiah(angka) {
            return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }
    });
</script>
<?= $this->endSection() ?>