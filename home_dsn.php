<?php
	session_start();
	include_once("dosen.php");
	$obj = new dosen;
	if(!$obj->isLogIn())
	{
		header("location:index.php");
	}
	else
	{
		$id_dsn = $_SESSION['id_dsn'];
		if(isset($_POST['send']))
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
			extract($_POST);
			# insert data ke table instruksi
			$id_instruksi = $obj->writeInstruction($instruksi, $id_bimbingan);
			if($berkas != NULL)
  			{
  				$obj->makeBerkasDsnColumn($id_instruksi, $berkas, $dir_berkas);
  			}
			if(isset($dikumpulkan))
			{
				$obj->makePenugasanColumn($id_instruksi);
			}
		}
		if(isset($_POST['ok']) || isset($_POST['revisi']) || isset($_POST['finish']))
		{
			extract($_POST);
			if(isset($ok))
			{
				$validasi = 'OK';
			}
			else if(isset($revisi))
			{
				$validasi = 'Revisi';
				$obj->RevisionOrder($id_instruksi);
			}
			else if(isset($finish))
			{
				$validasi = 'Bimbingan Selesai';
			}
			$obj->validateAssingment($id_instruksi, $validasi);
		}
		if(isset($_POST['comment']))
		{
			extract($_POST);
			$obj->addComment($id_komentar_mhs, $komentar);
		}
	}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Home</title>   
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
						<li class="active"><a href="home_dsn.php">Laman Bimbingan</a></li>
						<li><a href="daftar_mhs.php">Daftar Mahasiswa</a></li>
						<li><a href="berkas_dsn.php">Berkas</a></li>
						<li><a href="progress_mhs.php">Progress Mahasiswa</a></li>
					</ul>
				</div>
				<div class="col-lg-8">
	           		<div class="container-fluid">
						<div class="panel panel-primary">
	                		<div class="panel-heading">
	                			<h4>Tulis Instruksi</h4>
							</div>
							<div class="panel-body">
								<form  action="home_dsn.php" method="POST" enctype="multipart/form-data">
									<div class="input-group input-inline">
										<span class="input-group-addon">Kirim Kepada :</span>
										<select class="form-control form-control-inline input-sm" id="MhsTujuan" name="id_bimbingan" required>
											<option value="">-- Daftar Mahasiswa --</option>
											<?php
												foreach($obj->getDaftarMhs($id_dsn) as $value)
												{
													extract($value);
													echo '<option value="'.$id_bimbingan.'" name="id_bimbingan">'.$nama_mhs.'</option>';
												}
											?>
										</select>
									</div>
				                    <textarea class="form-control" rows="5" name="instruksi" placeholder="Tuliskan Instruksi"></textarea>
				                    <div class="pull-right">
				                    	<input type="checkbox" value="1" id="dikumpulkan" name="dikumpulkan" /> <label for="dikumpulkan">Dikumpulkan</label>
									</div>
									<label>Tambahkan Berkas</label>
				                    <input type="file" name="berkas" id="berkas">
									<input class="btn btn-primary pull-right" type="submit" name="send" value="Kirim" />
				                </form>      
			                </div>
			            </div>
			         <?php
	           			if($obj->isInstructionExist($id_dsn))
	           			{
	           				echo'<div class="panel panel-success">
		           			<div class="panel-heading">
		           				Daftar Instruksi
		           			</div>
		           			<div class="panel-body">';
	           					foreach($obj->showInstructions($id_dsn) as $value)
								{
									extract($value);
									echo '
									<div class="well well-sm">
									<em>Kepada : '.$nama.' </em>
									<div class="pull-right">'.$dibuat_pd.'</div>
									<br><div class="well well-sm">'.$instruksi.'</div>';
									if($obj->isBerkasDsnExist($id_instruksi))
									{
										extract($obj->isBerkasDsnExist($id_instruksi));
										echo '<div class="well well-sm">
										<i class="fa fa-file fa-3x"><h5><a href="'.$dir_berkas.'">'.$nama_berkas.'</a></h5></i>
										</div>';
									}
									if($obj->isAssignment($id_instruksi))
				           			{
				           				if($obj->isTurnIn($id_instruksi))
				           				{
				           					echo '<hr>
				           					<em>Pengumpulan Tugas</em>';
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
												if($obj->isValidated($id_instruksi))
												{
													extract($obj->isValidated($id_instruksi));
													if(($validasi == 'Revisi' && $mengumpulkan == 1) || ($validasi == NULL))
													{
														echo '<strong>Validasi tugas :</strong>
														<form action="home_dsn.php" method="POST">
															<div class="input-group input-inline">
																<input type="hidden" name="id_instruksi" value="'.$id_instruksi.'" />
																<div class="btn-group">
																	<input class="btn btn-sm btn-info" type="submit" name="ok" value="OK" />
																	<input class="btn btn-sm btn-warning" type="submit" name="revisi" value="Revisi" />
																	<input class="btn btn-sm btn-danger" type="submit" name="finish" value="Bimbingan Selesai" />
																</div>
															</div>
														</form>
														<br>';
													}
												}
												echo '</div>';
											}
				           				}
				           			}
									if($obj->isHaveComment($id_instruksi))
									{
										echo '<em>Komentar : </em>';
										foreach($obj->showComment($id_instruksi) as $value)
										{
											extract($value);
											echo '<div class="well well-sm"><strong>'.$nama.' : </strong> <br>
												 <div class="pull-right">'.$dibuat_pd.'</div>
												 '.$komentar_mhs.'
												  </div>';
											if($komentar_dsn != NULL)
											{
												echo '<div class="well well-sm"><strong>Saya : </strong> <br>
													  <div class="pull-right">'.$dibuat_pd.'</div>
													  '.$komentar_dsn.'
													  </div>';
											}
										}
										echo '
											<form action="home_dsn.php" method="POST">
												<div class="input-group">
													<input type="hidden" name="id_komentar_mhs" value="'.$id_komentar_mhs.'" />
													<input type="text" class="form-control" name="komentar" placeholder="Tulis Komentar"/>
												<div class="input-group-btn">
													<input class="btn-sm btn-success" type="submit" name="comment" value="Kirim"/>
												</div>
												</div>
											</form>
									</div>';
									}
									echo '</div>';
								}
	           				}
	           			?>
           			</div>
				</div>
			</div>
		</div>
	</div>
	</div>
</body>
</html>