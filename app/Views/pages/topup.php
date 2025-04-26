<?= $this->extend('layout/page') ?>

<?= $this->section('styles') ?>
<style>
    .amount-box {
        border: 1px solid #ccc;
        border-radius: .25rem;
        padding: .375rem .75rem;
        cursor: pointer;
        text-align: center;
        transition: all 0.3s;
        margin: 5px;
    }

    .amount-box:hover {
        background-color: #f8f9fa;
        border-color: #dc3545;
    }

    .amount-box.selected {
        background-color: #dc3545;
        color: #fff;
        border-color: #dc3545;
    }
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
    <p class="mb-1">Silahkan masukan</p>

    <div class="row">
        <div class="col-md-6">
            <div class="topupForm">
                <div class="mb-3">
                    <h5 for="amount" class="form-label">Nominal Top Up</h5>
                    <input type="number" class="form-control" name="top_up_amount" id="amount" placeholder="masukan nominal Top Up">
                </div>

                <button type="submit" id="topupBtn" class="btn btn-secondary btn-block" disabled>Top Up</button>
                <a href="<?= base_url('/'); ?>" class="btn btn-outline-danger btn-block">Kembali</a>
            </div>
        </div>

        <div class="col-md-6">
            <div class="row">
                <div class="col-4">
                    <div class="amount-box" data-value="10000">Rp10.000</div>
                </div>
                <div class="col-4">
                    <div class="amount-box" data-value="20000">Rp20.000</div>
                </div>
                <div class="col-4">
                    <div class="amount-box" data-value="50000">Rp50.000</div>
                </div>
                <div class="col-4">
                    <div class="amount-box" data-value="100000">Rp100.000</div>
                </div>
                <div class="col-4">
                    <div class="amount-box" data-value="250000">Rp250.000</div>
                </div>
                <div class="col-4">
                    <div class="amount-box" data-value="500000">Rp500.000</div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        let isBalance = <?= json_encode($balance['data']['balance']) ?>; // dari PHP ke JS
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

        $('#topupBtn').on('click', function() {
            let top_up_amount = $('#amount').val();

            Swal.fire({
                html: `
                    <p style="font-size: 16px; margin-top: 10px;">Anda yakin ingin melakukan Top Up sebesar</p>
                    <h3 style="font-weight: bold; margin-top: 5px;">Rp ${formatRupiah(top_up_amount)} ?</h3>
                `,
                showCancelButton: true,
                confirmButtonText: 'Ya, lanjutkan Top Up',
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
                        url: "/topup",
                        data: {
                            top_up_amount: top_up_amount
                        },
                        dataType: "json",
                        beforeSend: function() {
                            $('button[type=submit]').attr('disabled', true).text('Loading...');
                            $('#alert-placeholder').html('');
                        },
                        success: function(response) {
                            $('button[type=submit]').attr('disabled', false).text('Top Up');

                            if (response.status) {
                                Swal.fire({
                                    html: `
                                        <p style="font-size: 16px; margin-top: 10px;">Top Up sebesar</p>
                                        <h3 style="font-weight: bold; margin-top: 5px;">Rp ${formatRupiah(top_up_amount)}</h3>
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
                                $('#alert-placeholder').html('<div class="alert alert-danger">' + response.message + '</div>');
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

        const amountInput = document.getElementById('amount');
        const topupBtn = document.getElementById('topupBtn');
        const amountBoxes = document.querySelectorAll('.amount-box');

        amountInput.addEventListener('input', function() {
            updateButtonState();
            clearSelectedBoxes();
        });

        amountBoxes.forEach(box => {
            box.addEventListener('click', function() {
                amountInput.value = box.getAttribute('data-value');
                clearSelectedBoxes();
                box.classList.add('selected');
                updateButtonState();
            });
        });

        function updateButtonState() {
            if (amountInput.value.trim() !== '' && parseInt(amountInput.value) > 0) {
                topupBtn.classList.remove('btn-secondary');
                topupBtn.classList.add('btn-danger');
                topupBtn.disabled = false;
            } else {
                topupBtn.classList.add('btn-secondary');
                topupBtn.classList.remove('btn-danger');
                topupBtn.disabled = true;
            }
        }

        function clearSelectedBoxes() {
            amountBoxes.forEach(box => box.classList.remove('selected'));
        }
    });
</script>
<?= $this->endSection() ?>