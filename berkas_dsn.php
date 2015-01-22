<?php
	session_start();
	include_once("dosen.php");
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
	<title>Berkas</title>
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
						<li><a href="daftar_mhs.php">Daftar Mahasiswa</a></li>
						<li class="active"><a href="berkas_dsn.php">Berkas</a></li>
						<li><a href="progress_mhs.php">Progress Mahasiswa</a></li>
					</ul>
				</div>
				<div class="col-lg-8">
	           		<div class="container-fluid">
		           		<div class="panel panel-danger">
		           			<div class="panel-heading">
		           				Berkas Saya
		           			</div>
		           			<div class="panel-body">
		           				<?php
		           					if($obj->showBerkasDsn($id_dsn))
		           					{
		           						echo '<ul class="list-group">';
		           						foreach ($obj->showBerkasDsn($id_dsn) as $value) {
		           							extract($value);
		           							echo '			                    		
			           						<li class="list-group-item"><i class="fa fa-file fa-2x fa-lg"></i> '.$nama_berkas.'
					                    		<div class="pull-right">
					                    		<a href="'.$dir_berkas.'"><i class="fa fa-download fa-2x"></i></a>
				                    			</div>
				                    		</li>';
		           						}
			                    		echo '</ul>';
		           					}
		           				?>
		           			</div>
		           		</div>
		           		<div class="panel panel-warning">
		           			<div class="panel-heading">
		           				Berkas Mahasiswa
		           			</div>
		           			<div class="panel-body">
		           				<?php
		           					if($obj->daftarMhs($id_dsn))
		           					{
		           						foreach ($obj->daftarMhs($id_dsn) as $key) {
		           							extract($key);
				           					if($obj->showBerkasMhs($id_mhs))
				           					{
				           						echo
				           						'<div class="panel panel-success">
				           							<div class="panel-heading">'.$nama.'</div>
				           							<div class="panel-body">
				           							<ul class="list-group">';
				           						foreach ($obj->showBerkasMhs($id_mhs) as $value) {
				           							extract($value);
				           							echo '
					           						<li class="list-group-item"><i class="fa fa-file fa-2x fa-lg"></i> '.$nama_berkas.'
							                    		<div class="pull-right">
							                    		<a href="'.$dir_berkas.'"><i class="fa fa-download fa-2x"></i></a>
						                    			</div>
						                    		</li>';
				           						}
					                    		echo '</ul></div></div>';
				           					}
				           				}
				           			}
		           				?>
		           			</div>
		           		</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>