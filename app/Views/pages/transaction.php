<?= $this->extend('layout/page') ?>

<?= $this->section('styles') ?>
<style>
    .transaction-date,
    .transaction-desc {
        font-size: 12px;
    }

    .transaction-amount.plus {
        color: green;
    }

    .transaction-amount.minus {
        color: red;
    }

    #showMore {
        margin-top: 20px;
        color: #dc3545;
        cursor: pointer;
        text-align: center;
        display: none;
    }

    #noMore {
        margin-top: 20px;
        text-align: center;
        color: grey;
        display: none;
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
        <h2 class="mb-2" id="saldo-value">Rp •••••</h2>
        <a href="javascript:void(0);" id="toggle-saldo" class="text-white text-decoration-underline small">
            Lihat Saldo <i class="fa fa-eye"></i>
        </a>
    </div>

</div>

<p class="mb-3">Semua Transaksi</p>
<ol class="list-group" id="transactionList"></ol>
<div id="showMore">Show more</div>
<div id="noMore">Semua transaksi sudah ditampilkan</div>


<?= $this->endSection() ?>

<?= $this->section('scripts') ?>

<script>
    let offset = 0;
    const limit = 5;

    function transactionHistory() {
        $.ajax({
            url: 'transaction-history',
            method: 'GET',
            data: {
                offset: offset,
                limit: limit
            },
            success: function(response) {
                console.log(response);
                if (response.success) {
                    if (response.data.length > 0) {
                        response.data.forEach(function(trx) {
                            let amountClass = trx.transaction_type === 'TOPUP' ? 'plus' : 'minus';
                            let amountSign = trx.transaction_type === 'TOPUP' ? '+' : '-';

                            $('#transactionList').append(`
                                <li class="list-group-item d-flex justify-content-between align-items-start mt-1">
                                    <div class="ms-2 me-auto">
                                        <div class="transaction-amount ${amountClass}">${amountSign} Rp${parseInt(trx.total_amount).toLocaleString('id-ID')}</div>
                                        <span class="transaction-date">${formatDate(trx.created_on)}</span>
                                    </div>
                                    <span class="transaction-desc">${trx.description}</span>
                                </li>
                            `);
                        });
                        offset += limit;
                        $('#showMore').show();
                    } else {
                        $('#showMore').hide();
                        $('#noMore').show();
                    }
                } else {
                    $('#alert-placeholder').html('<div class="alert alert-danger">' + response.message + '</div>');
                }
            },
            error: function() {
                $('#alert-placeholder').html('<div class="alert alert-danger">Gagal memuat data transaksi.</div>');

            }
        });
    }

    function formatDate(dateString) {
        const options = {
            day: '2-digit',
            month: 'long',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            hour12: false
        };
        return new Date(dateString).toLocaleString('id-ID', options) + ' WIB';
    }

    $(document).ready(function() {
        transactionHistory();

        $('#showMore').click(function() {
            transactionHistory();
        });
    });
</script>
<script>
    $(document).ready(function() {
        let isBalance = <?= json_encode($balance['data']['balance']) ?>; // dari PHP ke JS
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
    });
</script>
<?= $this->endSection() ?>