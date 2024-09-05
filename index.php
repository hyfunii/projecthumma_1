<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PPDB</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #fff;
        }

        .home-section {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
            padding: 50px;
            background-color: #fff;
        }

        .home-text {
            flex: 1;
            padding-left: 50px;
        }

        .home-section p {
            font-size: 1.5rem;
        }

        .home-section h2 {
            font-size: 6rem;
            font-weight: bold;
        }

        .home-image {
            height: auto;
            width: 50%;
            flex: 1;
            padding-right: 50px;
        }

        .home-image img {
            max-width: 100%;
            height: auto;
        }

        .advance-section {
            padding: 50px 0;
        }

        .advance-section .container {
            text-align: center;
        }

        .card {
            position: relative;
            margin: 20px;
            border-radius: 10px;
            cursor: pointer;
            transition: top 1.3s ease;
        }

        .card:hover {
            position: relative;
            top: 0.5rem;
            transition: top 1.3s ease;
        }

        .spanduk {
            background: #0d6fed;
            padding: 1rem;
            color: #fff;
        }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>

    <section>
        <div class="home-section container">
            <div class="home-image">
                <img src="image/5836.jpg" alt="Education Vector">
            </div>
            <div class="home-text">
                <h2>PPDB 2025</h2>
                <p>Daftarkan dirimu ke sekolah favorit</p>
            </div>
        </div>
    </section>

    <section>
        <div class="advance-section">
            <div class="container">
                <h2 class="spanduk">Pendaftaran dapat dilakukan melalui beberapa jalur</h2>
                <div class="row justify-content-center">
                    <div class="col-lg-4 col-md-6">
                        <div class="card shadow">
                            <div class="card-body">
                                <h5 class="card-title">Jalur Afirmasi</h5>
                                <p class="card-text">Jalur untuk calon siswa dari keluarga kurang mampu atau berkebutuhan khusus.</p>
                                <a href="pages/j_afirmasi.php" class="btn btn-primary">Daftar</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="card shadow">
                            <div class="card-body">
                                <h5 class="card-title">Jalur Zonasi</h5>
                                <p class="card-text">Jalur penerimaan berdasarkan lokasi tempat tinggal calon siswa.</p>
                                <a href="pages/j_zonasi.php" class="btn btn-primary">Daftar</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="card shadow">
                            <div class="card-body">
                                <h5 class="card-title">Jalur Nilai Akademik</h5>
                                <p class="card-text">Jalur penerimaan berdasarkan prestasi akademik calon siswa.</p>
                                <a href="pages/j_nilai.php" class="btn btn-primary">Daftar</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>
