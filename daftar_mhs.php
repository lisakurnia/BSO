<?php
	include_once("dosen.php");
	session_start();
	$obj = new dosen;
	
	if(!$obj->isLogIn())
	{
		header("location:index.php");
	}
	$id_dsn = $_SESSION['id_dsn'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Daftar Mahasiswa</title>   
	<link href="assets/css/bootstrap.css" rel="stylesheet">
    <link href="assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="assets/css/style-responsive.css" rel="stylesheet">

    <script type="text/javascript" src="assets/js/jquery-1.11.1.js"></script>
    <script type="text/javascript" src="assets/js/bootstrap.min.js"></script>
    </head>
<body>
	<div class="container-fluid">
		<header>
			<div class="navbar navbar-default navbar-fixed-top" role="navigation">
				<div class="navbar-header">
					<div class="container">
						<a class="navbar-brand" href="home_dsn.php">Bimbingan Skripsi Online <em>DINUS</em></a>
						<div class="nav notify-row" id="top_menu">
			                <ul class="nav top-menu">
			                    <li id="header_inbox_bar" class="dropdown">
			                        <a data-toggle="dropdown" class="dropdown-toggle" href="home_dsn.php#">
			                            <i class="fa fa-cogs"></i>
			                        </a>
			                        <ul class="dropdown-menu extended tasks-bar">
			                            <div class="notify-arrow notify-arrow-green"></div>
			                            <li>
			                            	<p class="green">Akun User</p>
			                            </li>
			                            <li>
			                                <div class="task-info">
			                                	<a href="profil_dsn.php">
			                                		<span>Profil</span>
			                                	</a>
			                                </div>
			                            </li>
			                            <li>
			                                <div class="task-info"><a href="#">Pengaturan</a></div>
			                            </li>
			                        </ul>
			                    </li>
			                </ul>
			            </div>
			           	<div class="top-menu">
            				<ul class="nav pull-right top-menu">
                    		<li><a class="logout" href="logout.php">Keluar</a></li>
            				</ul>
            			</div>
					</div>
				</div>	
			</div>
		</header>
		<div class="container-fluid" style="padding-top : 50px">
			<div class="row">
				<div class="col-lg-2">
					<ul class="nav nav-tabs nav-stacked" data-spy="affix">
						<li><a href="home_dsn.php">Laman Bimbingan</a></li>
						<li class="active"><a href="daftar_mhs.php">Daftar Mahasiswa</a></li>
						<li><a href="berkas_dsn.php">Berkas</a></li>
						<li><a href="progress_mhs.php">Progress Mahasiswa</a></li>
					</ul>
				</div>
				<div class="col-lg-8">
	           		<div class="container-fluid">
	           			<div class="panel panel-info">
	           				<div class="panel-heading">
	           				Daftar Mahasiswa
	           				</div>
	           				<div class="panel-body">
	           				<?php
	           					if($obj->daftarMhs($id_dsn))
	           					{
	           						echo '<table class="table table-bordered table-striped">
									<thead>
									<tr>
										<th>No.</th>
										<th>NIM</th>
										<th>Nama</th>
										<th>Bidang Studi</th>
										<th>Judul Skripsi</th>
										<th>Status</th>
									</tr>';
									$no = 1;
	           						foreach ($obj->daftarMhs($id_dsn) as $value) {
	           							extract($value);
	           							echo '
	           							<tr>
											<td>'.$no++.'</td>
											<td>'.$nim.'</td>
											<td><a href="info_mhs.php?id='.$id_mhs.'">'.$nama.'</a></td>
											<td>'.$prodi.'</td>
											<td>'.$jdl_skripsi.'</td>
											<td><a href="progress_mhs.php">Lihat Progress</a></td>
										</tr>';
	           						}
	           						echo '</table>';
	           					}
	           				?>
	           				</div>
	           			</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	</div>
</body>
</html>