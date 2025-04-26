<?= $this->extend('layout/auth') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12 col-md-12 col-lg-6">
            <div class="row h-100">
                <div class="col-sm-12 pt-5 my-auto">
                    <div class="col-12 col-lg-6 col-md-12 mx-auto w-100">
                        <div class="d-flex justify-content-center">
                            <div class="media">
                                <div class="mr-3">
                                    <img src="<?= base_url('assets/images/logo.png') ?>" alt="Logo" class="width-100">
                                </div>
                                <div class="media-body">
                                    <h3>SIMS PPOB</h3>
                                </div>
                            </div>
                        </div>

                        <div class="desc-title">
                            <p class="title">Lengkapi data untuk membuat akun</p>
                        </div>

                        <div class="registerForm">
                            <div id="alert-placeholder"></div>

                            <form id="registerForm">
                                <div class="form-group">
                                    <input type="email" class="form-control" name="email" placeholder="masukan email anda">
                                </div>

                                <div class="form-group">
                                    <input type="text" class="form-control" name="first_name" placeholder="nama depan">
                                </div>

                                <div class="form-group">
                                    <input type="text" class="form-control" name="last_name" placeholder="nama belakang">
                                </div>

                                <div class="form-group">
                                    <input type="password" class="form-control" name="password" placeholder="buat password">
                                </div>

                                <div class="form-group">
                                    <input type="password" class="form-control" name="retype_password" placeholder="konfirmasi password">
                                </div>

                                <button type="submit" class="btn btn-danger btn-block">Register</button>
                                <p class="text-center">sudah punya akun ? login <a href="<?= base_url('login') ?>" style="color: #dc3545">disini</a></p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 d-none d-lg-block background-pink">
            <div class="imgbox">
                <img src="<?= base_url('assets/images/ilustrasi_login.png') ?>" class="center-fit" alt="Image Login">
            </div>
        </div>

    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        $('#registerForm').on('submit', function(e) {
            e.preventDefault();
            var formData = $(this).serialize();

            $.ajax({
                type: "POST",
                url: "/register",
                data: formData,
                dataType: "json",
                beforeSend: function() {
                    $('button[type=submit]').attr('disabled', true).text('Loading...');
                    $('#alert-placeholder').html('');
                    $('.text-danger').remove(); // hapus error sebelumnya
                    $('.is-invalid').removeClass('is-invalid');
                },
                success: function(response) {
                    $('button[type=submit]').attr('disabled', false).text('Masuk');

                    if (response.status) {
                        $('#alert-placeholder').html('<div class="alert alert-success">' + response.message + '</div>');
                        setTimeout(function() {
                            window.location.href = response.redirect;
                        }, 1000);
                    } else {
                        if (response.errors) {
                            $.each(response.errors, function(key, val) {
                                var input = $('[name="' + key + '"]');
                                input.addClass('is-invalid');
                                if (input.next('.text-danger').length === 0) {
                                    input.after('<div class="text-danger">' + val + '</div>');
                                }
                            });
                        } else {
                            // Kalau error umum
                            $('#alert-placeholder').html('<div class="alert alert-danger">' + response.message + '</div>');
                        }
                    }
                },
                error: function(xhr, status, error) {
                    $('button[type=submit]').attr('disabled', false).text('Login');
                    $('#alert-placeholder').html('<div class="alert alert-danger">Terjadi kesalahan koneksi atau server error.</div>');
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>