
<?php
	include_once("mahasiswa.php");
	session_start();
	$obj = new mahasiswa;
	if(!$obj->isLogIn())
	{
		header("location:index.php");
	}
	if(isset($_POST['logout']))
	{
		$obj->logout();
	}
	else
	{
		$id_mhs = $_SESSION['id_mhs'];

		# mengumpulkan tugas
		if(isset($_POST['turn_in']) || isset($_POST['revisi']))
		{
			if(isset($_FILES['berkas']) && is_uploaded_file($_FILES['berkas']['tmp_name'])) //cek jika telah upload file 
  			{
	  			$filename  = basename($_FILES['berkas']['name']);
				$extension = pathinfo($filename, PATHINFO_EXTENSION);
				$source = $_FILES['berkas']['tmp_name'];
				if(!is_dir("./upload/".$extension))
				{
					mkdir("./upload/".$extension);
				}
				do
				{
					$new = rand(00000,99999);
    				$newfilename=$new.".".$extension;
    				$berkas= $filename;
					$dir_berkas = "./upload/".$extension."/".$newfilename;
				}while(file_exists($dir_berkas)); //membuat nama file baru dari file upload
				move_uploaded_file($source, $dir_berkas);
  			}
  			else
  			{
  				$berkas= NULL;
  				$dir_berkas = NULL;
  			}
  			if(isset($_POST['turn_in']))
  			{
  				$id_berkas = NULL;
				extract($_POST);
				if($berkas != NULL)
				{
					$id_berkas = $obj->makeBerkasMhsColumn($berkas, $dir_berkas);
				}
				$id_pengumpulan_tgs = $obj->turnInAssignment($id_instruksi, $pesan, $id_berkas);
  			}
  			else if(isset($_POST['revisi']))
  			{
  				$id_berkas = NULL;
  				extract($_POST);
				if($berkas != NULL)
				{
					$id_berkas = $obj->makeBerkasMhsColumn($berkas, $dir_berkas);
				}
				$id_pengumpulan_tgs = $obj->revisiAssignment($id_instruksi, $pesan, $id_berkas);
  			}
		}
		if(isset($_POST['comment']))
		{
			extract($_POST);
			$obj->addComment($id_instruksi, $komentar_mhs);
		}
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Laman Bimbingan</title>
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
						<a class="navbar-brand" href="home_mhs.php">Bimbingan Skripsi Online <em>DINUS</em></a>
						<div class="nav notify-row" id="top_menu">
			                <ul class="nav top-menu">
			                    <li id="header_inbox_bar" class="dropdown">
			                        <a data-toggle="dropdown" class="dropdown-toggle" href="home_mhs.php#">
			                            <i class="fa fa-cogs"></i>
			                        </a>
			                        <ul class="dropdown-menu extended tasks-bar">
			                            <div class="notify-arrow notify-arrow-green"></div>
			                            <li>
			                            	<p class="green">Akun User</p>
			                            </li>
			                            <li>
			                                <div class="task-info">
			                                	<a href="profil_mhs.php">
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
						<li class="active"><a href="home_mhs.php">Laman Bimbingan</a></li>
						<li><a href="berkas_mhs.php">Berkas</a></li>
						<li><a href="info_pembimbing.php">Informasi Pembimbing</a></li>
					</ul>
				</div>
				<div class="col-lg-8">
	           		<div class="container-fluid">
		            <div class="panel panel-success">
		           		<div class="panel-heading">
		           		Daftar Instruksi
		           		</div>
		           		<div class="panel-body">
		           		<?php
		           			if($obj->isInstructionExist($id_mhs))
		           			{
		           				foreach ($obj->showInstructions($id_mhs) as $value) 
		           				{
		           					extract($value);
		           					echo '
									<div class="well well-sm">
									<br>
										<em>Instruksi</em>
										<div class="well well-sm">												
											<div class="pull-right">'.$dibuat_pd.'</div>'.$instruksi.'</div>';
				           					if($obj->isAssignment($id_instruksi))
				           					{
				           						if($obj->isTurnIn($id_instruksi))
				           						{
				           							echo '<em>Pengumpulan Tugas</em>';
				           							if($obj->isValidated($id_instruksi))
				           							{
				           								extract($obj->isValidated($id_instruksi));
														if($validasi == 'OK')
														{
															echo '<span class="pull-right badge bg-success">'.$validasi.'</span>';
														}
														else if($validasi == 'Revisi')
														{
															echo '<span class="pull-right badge bg-important">'.$validasi.'</span>';
														}
														else
														{
															echo '<span class="pull-right badge bg-info">'.$validasi.'</span>';	
														}
				           							}
													if($obj->showTurnedInData($id_instruksi))
													{
														extract($obj->showTurnedInData($id_instruksi));
														echo '
															<div class="well well-sm">
																<div class="pull-right">'.$dibuat_pd.'</div>
																<br><div class="well well-sm">'.$pesan.'</div>';
															if($obj->isBerkasMhsExist($id_pengumpulan_tgs))
															{
																extract($obj->isBerkasMhsExist($id_pengumpulan_tgs));
																echo '<div class="well well-sm">
																<i class="fa fa-file fa-3x"><h5><a href="'.$dir_berkas.'">'.$nama_berkas.'</a></h5></i>
																</div>';
															}
														echo '</div>';
														if($obj->isValidated($id_instruksi))
														{
															extract($obj->isValidated($id_instruksi));
															if($validasi == 'Revisi' && $mengumpulkan == 0)
															{
																echo '
																<em>Kumpulkan Revisi :</em>
																<form action="home_mhs.php" method="POST" enctype="multipart/form-data">
																	<input type="hidden" name="id_pengumpulan_tgs" value="'.$id_pengumpulan_tgs.'" />
																	<input type="hidden" name="id_instruksi" value="'.$id_instruksi.'" />
																	<textarea class="form-control" rows="5" name="pesan" placeholder="Tulis Balasan"></textarea>
									                  				<div class="pull-right">
																		<input class="btn-sm btn-primary pull-right" type="submit" name="revisi" value="Kirim" />
									                  				</div>
									                  				<label>Tambahkan Berkas</label>
									                            	<input type="file" name="berkas">     
																</form>
																<br>';
															}
															else if($validasi == 'OK')
															{
																echo '<div class="pull-right"><i class="fa fa-check-square-o text-primary"></i> Telah Divalidasi</div><br>';
															}
															else if($validasi == 'Bimbingan Selesai')
															{
																echo '<div class="pull-right"><i class="fa fa-graduation-cap"></i>Bimbingan Telah Diselesaikan</div><br>';
															}
															else
															{
																echo '<div class="pull-right"><i class="fa fa-spinner fa-spin text-danger"></i> Menunggu Tangapan Pembimbing</div><br>';
															}
														}
													}
				           						}
				           						else
				           						{
				           							echo '
													<form action="home_mhs.php" method="POST" enctype="multipart/form-data">
														<input type="hidden" name="id_instruksi" value="'.$id_instruksi.'" />
														<textarea class="form-control" rows="5" name="pesan" placeholder="Tulis Balasan"></textarea>
						                  				<div class="pull-right">
															<input class="btn-sm btn-primary pull-right" type="submit" name="turn_in" value="Kirim" />
						                  				</div>
						                  				<label>Tambahkan Berkas</label>
						                            	<input type="file" name="berkas">     
													</form>
													<br>';
				           						}
				           					}
				           					if($obj->isHaveComment($id_instruksi))
											{

												echo '<em>Komentar : </em>';
												foreach($obj->showComment($id_instruksi) as $value)
												{
													extract($value);
													echo '<div class="well well-sm"><strong>Saya : </strong> <br>
														 <div class="pull-right">'.$dibuat_pd.'</div>
														 '.$komentar_mhs.'
														  </div>';
													if($komentar_dsn != NULL)
													{
														echo '<div class="well well-sm"><strong>Pembimbing : </strong> <br>
															  <div class="pull-right">'.$dibuat_pd.'</div>
															  '.$komentar_dsn.'
															  </div>';
													}
												}
											}
											echo '
													<form action="home_mhs.php" method="POST">
														<div class="input-group">
															<input type="hidden" name="id_instruksi" value="'.$id_instruksi.'" />
															<input type="text" class="form-control" name="komentar_mhs" placeholder="Tulis Komentar"/>
														<div class="input-group-btn">
															<input class="btn-sm btn-success" type="submit" name="comment" value="Kirim"/>
														</div>
														</div>
													</form>
											</div>';
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