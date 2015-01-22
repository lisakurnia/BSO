<!DOCTYPE html>
<html>
<head>
    <title>Registrasi</title>   
	<link href="assets/css/bootstrap.css" rel="stylesheet">
    <link href="assets/font-awesome/css/font-awesome.css" rel="stylesheet" />

    <script type="text/javascript" src="assets/js/jquery-1.11.1.js"></script>
    <script type="text/javascript" src="assets/js/bootstrap.min.js"></script>
    <style>
    body {
    padding-top: 75px;
	}

    </style>
</head>
<body>
<?php
	include_once("dosen.php");
  	$obj = new dosen;
  	if(isset($_POST['submit'])) {
  		$target = NULL;
  		$uploadOk = 1;
  		if(isset($_FILES['foto']) && is_uploaded_file($_FILES['foto']['tmp_name'])) //cek jika telah upload file 
  		{
  			$filename  = basename($_FILES['foto']['name']);
			$extension = pathinfo($filename, PATHINFO_EXTENSION);
			$source = $_FILES['foto']['tmp_name'];
			
			if($_FILES['foto']['size'] > 1000000)
			{
				echo "Maaf, ukuran file terlalu besar";
				$uploadOk = 0;
			}
			if($extension != "jpg" && $extension != "png" && $extension != "jpeg")
			{
				echo "Maaf, hanya format JPG, PNG dan JPEG yang diperbolehkan";
				$uploadOk = 1;
			}
			if($uploadOk == 0)
			{
				echo "<br>File, tidak dapat diaupload!";
			}
			else
			{
				do
				{
					$new = rand(00000,99999);
    				$newfilename=$new.".".$extension;
    				$dir = "./upload/dir_foto/".$extension."/";
    				if(!file_exists($dir))
    				{
    					mkdir($dir);
    				}
					$target = $dir.$newfilename;
				}while(file_exists($target)); //mebuat nama file baru dari file upload
				move_uploaded_file($source, $target);
			}
  		}
  		if($uploadOk == 1)
		{
	  		extract($_POST);
		   	if($obj->register($nama, $nidn, $jen_kel, $tempat_lahir, $tgl_lahir, $alamat, $telp, $email, $bdg_keahlian, $username, $password, $target)) {
		     	if($id_dsn = $obj->getIdDosen($username))
		     	{
		      		$obj->addKlmpBimbingan($id_dsn);
		      		$_SESSION['id_dsn'] = $id_dsn;
		     		header("location:home_dsn.php");
		     	}
		   	}
		   	else
		    {
		    	echo '<div class="alert alert-danger" role="alert">
				<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
			    <span class="sr-only">Error:</span>
			    Registrasi Gagal! Username sudah ada!
		       </div>';
		    }
	   	}
 	}
 ?>
	<div class="container-fluid">
	 <div class="navbar navbar-default navbar-fixed-top" role="navigation">
            <div class="navbar-header">
                <div class="container">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#collapseNavbar">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="index.html">Bimbingan Skripsi Online <em>DINUS</em></a>
                    <div class="collapse navbar-collapse" id="collapseNavbar">
                        <ul class="nav navbar-nav">
                            <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">REGISTRASI <b class="caret"></b></a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="registrasi_dsn.php">Registrasi Dosen</a>
                                    </li>
                                    <li class="divider"></li>
                                    <li>
                                        <a href="registrasi_mhs.php">Registrasi Mahasiswa</a>
                                    </li>
                                </ul>
                            </li>
                            <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">LOGIN <b class="caret"></b></a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a data-toggle="modal" data-target="#login-modal-dsn">Login Dosen</a>
                                    </li>
                                    <li class="divider"></li>
                                    <li>
                                        <a data-toggle="modal" data-target="#login-modal-mhs">Login Mahasiswa</a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>  
        </div>
	<div class="container-fluid">
		<ul class="nav nav-tabs">
			<li class="active"><a href="registrasi_dsn.php">Registrasi Dosen</a></li>
			<li><a href="registrasi_mhs.php">Registrasi Mahasiswa</a></li>
		</ul>
		<div class="panel panel-primary">
			<div class="panel-heading">
				Lengkapi kolom data diri di bawah dengan tepat
			</div>
			<div class="panel-body">
				<div class="container-fluid">
					<div class="row">
		      			<form class="form-horizontal" role="form" action="registrasi_dsn.php" method="POST" enctype="multipart/form-data">
			        		<div class="col-md-7">
				        		<legend>Informasi Data Diri</legend>
				        		<div class="form-group">
				          			<label class="control-label col-sm-3" for="nama">Nama Lengkap :</label>
				          			<div class="col-sm-8">
				            			<input type="text" class="form-control" name="nama" id="nama" required>
				          			</div>
				        		</div>
				        		<div class="form-group">
				          			<label class="control-label col-sm-3" for="nidn">NIDN :</label>
				          			<div class="col-sm-8">
				            			<input type="text" class="form-control" name="nidn" id="nidn" required>
				          			</div>
				        		</div>
				        		<div class="form-group">
				          			<label class="control-label col-sm-3" for="bdg_keahlian">Bidang Keahlian :</label>
				          			<div class="col-sm-8">
				            			<select class="form-control" name="bdg_keahlian" id="bdg_keahlian" required>
				            				<option value="">Pilih Bindang Keahlian</option>
				            				<option value="Ilmu Komputer">Ilmu Komputer</option>
				            				<option value="Ilmu Komputer">Ilmu Ekonomi dan Bisnis</option>
				            				<option value="Ilmu Komputer">Ilmu Bahasa dan Sastra</option>
				            				<option value="Ilmu Komputer">Ilmu Kesehatan</option>
				            			</select>
				          			</div>
				        		</div>
				        		<div class="form-group">
				          			<label class="control-label col-sm-3" for="jen_kel">Jenis Kelamin :</label>
				          			<div class="col-sm-8">
				          				<label class="radio-inline"><input type="radio" name="jen_kel" value="Laki-laki" checked>Laki-laki</label>
			            				<label  class="radio-inline"><input type="radio" name="jen_kel" value="Perempuan">Perempuan</label>
				          			</div>
				        		</div>
				        		<div class="form-group">
				          			<label class="control-label col-sm-3" for="tempat_lahir">Tempat Lahir :</label>
				          			<div class="col-sm-8">
				            			<input type="text" class="form-control" name="tempat_lahir" id="tempat_lahir" required>
				          			</div>
				        		</div>
				        		<div class="form-group">
				          			<label class="control-label col-sm-3" for="tgl_lahir">Tanggal Lahir :</label>
				          			<div class="col-sm-8">
				            			<input type="date" class="form-control" name="tgl_lahir" id="tgl_lahir" required>
				          			</div>
				        		</div>
				        		<div class="form-group">
				          			<label class="control-label col-sm-3" for="alamat">Alamat :</label>
				          			<div class="col-sm-8">
				            			<textarea class="form-control" rows="5" name="alamat" id="alamat" required></textarea>
				          			</div>
				        		</div>
				        		<div class="form-group">
				          			<label class="control-label col-sm-3" for="telp">Telepon :</label>
				          			<div class="col-sm-8">
				            			<input type="text" class="form-control" name="telp" id="telp" required>
				          			</div>
				        		</div>
				        		<div class="form-group">
				          			<label class="control-label col-sm-3" for="email">Email :</label>
				          			<div class="col-sm-8">
				            			<input type="email" class="form-control" name="email" id="email" required>
				          			</div>
				        		</div>
			      			</div>
			      			<div class="col-md-5">
			      					<legend>Autentikasi Akun</legend>
			        				<div class="form-group">
				          				<label class="control-label col-sm-3" for="username">Username :</label>
				          				<div class="col-sm-8">
				            				<input type="text" class="form-control" name="username" id="username" required>
				          				</div>
			        				</div>
			        				<div class="form-group">
				          				<label class="control-label col-sm-3" for="password">Password :</label>
				          				<div class="col-sm-8">
				            				<input type="password" class="form-control" name="password" id="password" required>
				          				</div>
			        				</div>
			        				<div class="form-group">
				          				<label class="control-label col-sm-3" for="ulang_pass">Ulangi password :</label>
				          				<div class="col-sm-8">
				            				<input type="password" class="form-control" id="ulang_pass" required>
				          				</div>
			        				</div>
			        				<div class="form-group">
				          				<label class="control-label col-sm-3" for="foto">Upload Foto :</label>
				          				<div class="col-sm-8">
				            				<input type="file" name="foto" id="foto">
				          				</div>
			        				</div>
			        				<div class="form-group">        
			          					<div class="col-sm-offset-3 col-sm-6">
			            					<input type="submit" class="btn btn-primary" name="submit" value="Submit" />
			            					<button type="reset" class="btn btn-danger">Batal</button>
			            				</div>
			        				</div>
			        		</div>	
		        		</form>
		        	</div>
	      		</div>
	    	</div>
		</div>
	</div>
	</div>
</body>
</html>
<div class="modal modal-success fade" id="login-modal-dsn" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="modal-title" id="myModalLabel">Login Dosen</h3>
            </div>
            <div class="modal-body">
                <br/>
                    <form role="form" action="index.php" method="POST">
                        <div class="form-group">
                            <input type="text" name="username" placeholder="Username" class="form-control">
                        </div>
                        <div class="form-group">
                            <input type="password" name="password" placeholder="Password" class="form-control">
                        </div>
                        <input type="submit" class="btn btn-success" name="login_dsn" value="Login">
                    </form>
            </div>
            <div class="clearfix"></div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>
<div class="modal modal-success fade" id="login-modal-mhs" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="modal-title" id="myModalLabel">Login Mahasiswa</h3>
            </div>
            <div class="modal-body">
                <br/>
                    <form role="form" action="index.php" method="POST">
                        <div class="form-group">
                            <input type="text" name="username" placeholder="Username" class="form-control">
                        </div>
                        <div class="form-group">
                            <input type="password" name="password" placeholder="Password" class="form-control">
                        </div>
                        <input type="submit" class="btn btn-success" name="login_mhs" value="Login">
                    </form>
            </div>
            <div class="clearfix"></div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>