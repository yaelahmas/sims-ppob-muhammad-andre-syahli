<?= $this->extend('layout/page') ?>

<?= $this->section('styles') ?>
<style>
    .profile-img {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border-radius: 50%;
        border: 3px solid #ddd;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div id="alert-placeholder"></div>

<div class="container">
    <div class="text-center mb-4">
        <input type="file" id="uploadProfileImage" accept="image/*" style="display:none;">
        <label for="uploadProfileImage" style="cursor: pointer;">
            <?php if ($profile['data']['profile_image'] == "https://minio.nutech-integrasi.com/take-home-test/null") { ?>
                <img src="<?= base_url('assets/images/profile_photo.png') ?>" alt="Foto Profil" class="profile-img mb-2">
            <?php } else { ?>
                <img src="<?= $profile['data']['profile_image']; ?>" alt="Foto Profil" class="profile-img mb-2">
            <?php } ?>
        </label>

        <h5 class="mt-2"><?= esc($profile['data']['first_name'] ?? '') . ' ' . esc($profile['data']['last_name'] ?? '') ?></h5>
    </div>

    <div class="form-section mx-auto" style="max-width: 500px;">
        <form id="profileForm">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" name="email" id="email" placeholder="masukan email anda" value="<?= esc($profile['data']['email'] ?? ''); ?>" readonly>
            </div>

            <div class="mb-3">
                <label for="first_name" class="form-label">Nama Depan</label>
                <input type="text" class="form-control" name="first_name" id="first_name" placeholder="nama depan" value="<?= esc($profile['data']['first_name'] ?? ''); ?>">
            </div>

            <div class="mb-3">
                <label for="last_name" class="form-label">Nama Belakang</label>
                <input type="text" class="form-control" name="last_name" id="last_name" placeholder="nama belakang" value="<?= esc($profile['data']['last_name'] ?? ''); ?>">
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-danger btn-block">Simpan</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        $('#profileForm').on('submit', function(e) {
            e.preventDefault();
            var formData = $(this).serialize();

            $.ajax({
                type: "POST",
                url: "/edit-profile",
                data: formData,
                dataType: "json",
                beforeSend: function() {
                    $('button[type=submit]').attr('disabled', true).text('Loading...');
                    $('#alert-placeholder').html('');
                    $('.text-danger').remove(); // hapus error sebelumnya
                    $('.is-invalid').removeClass('is-invalid'); // reset class error
                },
                success: function(response) {
                    $('button[type=submit]').attr('disabled', false).text('Simpan');

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
                            $('#alert-placeholder').html('<div class="alert alert-danger">' + response.message + '</div>');
                        }
                    }
                },
                error: function(xhr, status, error) {
                    $('button[type=submit]').attr('disabled', false).text('Simpan');
                    $('#alert-placeholder').html('<div class="alert alert-danger">Terjadi kesalahan koneksi atau server error.</div>');
                }
            });
        });

        $('#uploadProfileImage').on('change', function() {
            var file = this.files[0];
            if (file) {
                var formData = new FormData();
                formData.append('file', file);

                $.ajax({
                    type: "POST",
                    url: "/edit-profile-image",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.status) {
                            $('#alert-placeholder').html('<div class="alert alert-success">' + response.message + '</div>');
                            setTimeout(function() {
                                window.location.href = response.redirect;
                            }, 1000);
                        } else {
                            $('#alert-placeholder').html('<div class="alert alert-danger">' + response.message + '</div>');
                        }
                    },
                    error: function(xhr, status, error) {
                        $('#alert-placeholder').html('<div class="alert alert-danger">Terjadi kesalahan koneksi atau server error.</div>');
                    }
                });
            }
        });
    });
</script>
<?= $this->endSection() ?>