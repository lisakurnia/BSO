<?php
	class mahasiswa{
		private $host="localhost";
		private $user="root";
		private $db="bso";
		private $pass="";
		private $conn;

		public function __construct()
		{
			$this->conn = new PDO("mysql:host=".$this->host.";dbname=".$this->db,$this->user,$this->pass);
			$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		/*** REGISTER ***/
		public function checkUsername($username)
		{
			$stmt = $this->conn->prepare("SELECT * FROM mahasiswa WHERE username=:user");
			$stmt->bindParam(':user', $username);
			$stmt->execute();
			$row = $stmt->fetch(PDO::FETCH_NUM);
			if($row == 0)
			{
				return true;
			}
			else
				return false;
		}
		public function register($nim, $nama, $prodi, $jen_kel, $tempat_lahir, $tgl_lahir, $alamat, $telp, $email, $kode_bimbingan, $username, $password, $target, $jdl_skripsi)// registrasi mahasiswa
		{
			try{
				if($this->checkUsername($username))
				{
					$stmt = $this->conn->prepare("SELECT * FROM klmp_bimbingan WHERE kode_bimbingan=:kode");
					$stmt->bindParam(':kode', $kode_bimbingan);
					$stmt->execute();
					$row = $stmt->fetch(PDO::FETCH_NUM); // cek jika kode bimbingan sesuai
					if($row){
						$sql = "INSERT INTO mahasiswa VALUES(null, :nama, :nim, :prodi, :jen_kel, :tempat_lahir, :tgl_lahir, :alamat,
								:telp, :email, :username, :password, :dir_foto, :jdl_skripsi)";
						$q = $this->conn->prepare($sql);
						$q->execute(array(':nama'=>$nama,
										  ':nim'=>$nim,
										  ':prodi'=>$prodi,
										  ':jen_kel'=>$jen_kel,
										  ':tempat_lahir'=>$tempat_lahir,
										  ':tgl_lahir'=>$tgl_lahir, 
										  ':alamat'=>$alamat,
										  ':telp'=>$telp,
										  ':email'=>$email,
										  ':username'=>$username,
										  ':password'=>md5($password),
										  ':dir_foto'=>$target,
										  ':jdl_skripsi'=>$jdl_skripsi));
					}
				}
				else
				{
					echo 'Username sudah Ada!';
				}
			}catch(PDOException $e)
			{
				echo $e->getMessage();
				return false;
			}
			return true;
		}
		public function getIdMhs($username)
		{
			$stmt = $this->conn->prepare("SELECT * FROM mahasiswa WHERE username= :user");
			$stmt->bindParam(':user', $username);
			$stmt->execute();
			$id_mhs = $stmt->fetchColumn();
			return $id_mhs;
		}
		public function addBimbingan($kode_bimbingan, $id_mhs)
		{
			$sql ="INSERT INTO bimbingan VALUES(NULL, :kode_bimbingan, :id_mhs)";
			$q = $this->conn->prepare($sql);
			$q->execute(array(':kode_bimbingan'=>$kode_bimbingan,
							  ':id_mhs'=>$id_mhs));
			return true;
		}

		/*** LOGIN ***/
		public function Login($username, $password)
		{
			$password = md5($password);
			try{			
				$stmt = $this->conn->prepare("SELECT * FROM mahasiswa WHERE username= :user AND password= :pass");
				$stmt->bindParam(':user', $username, PDO::PARAM_STR);
				$stmt->bindParam(':pass', $password, PDO::PARAM_STR, 40);
				$stmt->execute();
				$id_dsn = $stmt->fetchColumn();
			}catch(PDOException $e)
			{
				return $e->getMessage();;
			}
			return $id_dsn;
		}
		public function isLogIn()// Cek Login
		{
			if(isset($_SESSION['id_mhs']))
			{
				return true;
			}
			else
				return false;
		}
		/*** LOGOUT ***/
		public function logout()
		{
			session_start();
			session_unset();
			session_destroy();
			header("location:index.php");
		}
		/*** HOME MAHASISWA ***/
		/*** LAMAN BIMBINGAN ***/
		public function isInstructionExist($id_mhs)
		{
			try{
				$sql = "SELECT * FROM instruksi
						WHERE id_bimbingan = ( SELECT id_bimbingan
											   FROM bimbingan
						                       WHERE id_mhs = :id_mhs )";
				$stmt = $this->conn->prepare($sql);
				$stmt->bindParam(':id_mhs', $id_mhs);
				$stmt->execute();
				$row = $stmt->fetch(PDO::FETCH_NUM);
			}catch(PDOException $e)
			{
				echo $e->getMessage();
				return false;
			}
			if($row)
			{
				return true;
			}
		}
		public function showInstructions($id_mhs)
		{
			try{
				$sql = "SELECT * FROM instruksi
						WHERE id_bimbingan = ( SELECT id_bimbingan
											   FROM bimbingan
						                       WHERE id_mhs = :id_mhs )
						ORDER BY dibuat_pd DESC";
				$stmt = $this->conn->prepare($sql);
				$stmt->bindParam(':id_mhs', $id_mhs);
				$stmt->execute();
			}catch(PDOException $e)
			{
				return $e->getMessage();
			}
			while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
				$data[]=$row;
			}
			return $data;
		}
		public function isTurnIn($id_instruksi)
		{
			try{
				$sql = "SELECT * FROM pengumpulan_tgs
						WHERE id_tugas = ( SELECT id_tugas
											FROM penugasan
											WHERE id_instruksi = :id_instruksi ) ";
				$stmt = $this->conn->prepare($sql);
				$stmt->bindParam(':id_instruksi', $id_instruksi);
				$stmt->execute();
				$row = $stmt->fetch(PDO::FETCH_NUM);
			}catch(PDOException $e)
			{
				echo $e->getMessage();
				return false;
			}
			if($row)
			{
				return true;
			}
		}
		public function isAssignment($id_instruksi)
		{
			try{
				$sql = "SELECT * FROM penugasan WHERE id_instruksi = :id_instruksi";
				$stmt = $this->conn->prepare($sql);
				$stmt->bindParam(':id_instruksi', $id_instruksi);
				$stmt->execute();
				$row = $stmt->fetch(PDO::FETCH_NUM);
			}catch(PDOException $e)
			{
				echo $e->getMessage();
				return false;
			}
			if($row)
			{
				return true;
			}			
		}
		public function turnInAssignment($id_instruksi, $pesan, $id_berkas)
		{
			try
			{
				$sql = "INSERT INTO pengumpulan_tgs VALUES(NULL, (SELECT id_tugas FROM penugasan WHERE id_instruksi = :id_instruksi), :pesan, NULL, :id_berkas_mhs)";
				$stmt = $this->conn->prepare($sql);
				$stmt->bindParam(':id_instruksi', $id_instruksi);
				$stmt->bindParam(':pesan', $pesan);
				$stmt->bindParam(':id_berkas_mhs', $id_berkas);
				$stmt->execute();
				$stmt2 = $this->conn->prepare("UPDATE penugasan SET mengumpulkan = 1 WHERE id_instruksi = :id_instruksi");
				$stmt2->bindParam(':id_instruksi', $id_instruksi);
				$stmt2->execute();
			}catch(PDOException $e)
			{
				echo $e->getMessage();
				return false;
			}
			return true;
		}
		public function makeBerkasMhsColumn($berkas, $dir_berkas)
		{
			try
			{
				$sql = "INSERT INTO berkas_mhs VALUES(NULL, :nama_berkas, :dir_berkas)";
				$stmt = $this->conn->prepare($sql);
				$stmt->bindParam(':nama_berkas',$berkas);
				$stmt->bindParam(':dir_berkas',$dir_berkas);
				$stmt->execute();
			}catch(PDOException $e)
			{
				echo $e->getMessage();
				return false;
			}
			return $this->conn->lastInsertId();
		}
		public function showTurnedInData($id_instruksi)
		{
			try
			{
				$sql = "SELECT * FROM pengumpulan_tgs
						WHERE id_tugas = ( SELECT id_tugas FROM penugasan
										   WHERE id_instruksi = :id_instruksi)";
				$stmt = $this->conn->prepare($sql);
				$stmt->bindParam(':id_instruksi', $id_instruksi);
				$stmt->execute();
				$data = $stmt->fetch(PDO::FETCH_ASSOC);
			}catch(PDOException $e)
			{
				return $e->getMessage();
			}
			return $data;
		}
		public function isBerkasMhsExist($id_pengumpulan_tgs)
		{
			try
			{
				$sql = "SELECT * FROM berkas_mhs WHERE id_berkas_mhs = (
						SELECT id_berkas_mhs FROM pengumpulan_tgs WHERE id_pengumpulan_tgs = :id_pengumpulan_tgs)";
				$stmt = $this->conn->prepare($sql);
				$stmt->bindParam(':id_pengumpulan_tgs',$id_pengumpulan_tgs);
				$stmt->execute();
				$data = $stmt->fetch(PDO::FETCH_ASSOC);
			}catch(PDOException $e)
			{
				return $e->getMessage();
			}
			return $data;
		}
		public function isValidated($id_instruksi)
		{
			try
			{
				$sql = "SELECT validasi, mengumpulkan FROM penugasan WHERE id_instruksi = :id_instruksi";
				$stmt = $this->conn->prepare($sql);
				$stmt->bindParam(':id_instruksi', $id_instruksi);
				$stmt->execute();
				$data = $stmt->fetch(PDO::FETCH_ASSOC);
			}catch(PDOException $e)
			{	
				echo $e->getMessage();
				return false;
			}
			if($data)
				return $data;
			else
				return false;
		}
		public function revisiAssignment($id_instruksi, $pesan, $id_berkas)
		{
			try
			{
				$sql = "UPDATE pengumpulan_tgs SET pesan = :pesan, dibuat_pd = NULL, id_berkas_mhs = :id_berkas_mhs
						WHERE id_tugas = (SELECT id_tugas FROM penugasan WHERE id_instruksi = :id_instruksi)";
				$stmt = $this->conn->prepare($sql);
				$stmt->execute(array(':id_instruksi'=>$id_instruksi,
									 ':pesan'=>$pesan,
									 ':id_berkas_mhs'=>$id_berkas));
				$stmt2 = $this->conn->prepare("UPDATE penugasan SET mengumpulkan = 1 WHERE id_instruksi = :id_instruksi");
				$stmt2->bindParam(':id_instruksi', $id_instruksi);
				$stmt2->execute(); 
			}catch(PDOException $e)
			{
				echo $e->getMessage();
				return false;
			}
			return true;	
		}
		public function writeComment($id_komentar, $id_instruksi, $komentar_mhs)
		{
			if($id_komentar == NULL) // jika belum pernah menulis komentar membuat id_komentar baru untuk dihubungkan ke komentar dosen ataupun mahasiswa
			{
				try{
					$sql = "INSERT INTO komentar VALUES(NULL, :id_instruksi)";
					$stmt = $this->conn->prepare($sql);
					$stmt->bindParam(':id_instruksi', $id_instruksi);
					$stmt->execute();
					$id_komentar = $this->conn->lastInsertId();
					if($id_komentar)
					{
						$q = "INSERT INTO komentar_mhs VALUES(NULL, :id_komentar, :komentar_mhs, NULL)";
						$statement = $this->conn->prepare($q);
						$statement->bindParam(':id_komentar', $id_komentar);
						$statement->bindParam(':komentar_mhs', $komentar_mhs);
						$statement->execute();
					}
				}catch(PDOException $e)
				{
					echo $e->getMessage();
					return false;
				}
				return true;
			}
			else // jika sudah membuat kolom id_komentar langsung membuat kolom komentar untuk dosen
			{
				try{
					$q = "INSERT INTO komentar_mhs VALUES(NULL, :id_komentar, :komentar_mhs, NULL)";
					$statement = $this->conn->prepare($q);
					$statement->bindParam(':id_komentar', $id_komentar);
					$statement->bindParam(':komentar_mhs', $komentar_mhs);
					$statement->execute();
				}catch(PDOException $er)
				{
					echo $er->getMessage();
					return false;
				}
				return true;
			}
		}
		public function isHaveComment($id_instruksi)
		{
			$result = $this->conn->prepare("SELECT * FROM komentar_mhs WHERE id_instruksi = :id_instruksi");
			$result->bindParam(':id_instruksi', $id_instruksi);
			$result->execute();
			$row = $result->fetch(PDO::FETCH_NUM);
			if($row > 0)
				return true;
			else
				return false;
		}
		public function addComment($id_instruksi, $komentar)
		{
			$sql = "INSERT INTO komentar_mhs VALUES(null, :id_instruksi, :komentar, null)";
			$q = $this->conn->prepare($sql) or die("Gagal!");
			$q->bindParam(':id_instruksi', $id_instruksi);			
			$q->bindParam(':komentar', $komentar);
			$q->execute();
			return true;
		}
		public function showComment($id_instruksi)
		{
			$sql = "SELECT komentar_mhs.id_komentar_mhs, komentar_mhs.komentar AS komentar_mhs, komentar_mhs.dibuat_pd AS dibuat_pd_mhs, (
					SELECT komentar_dsn.komentar
					FROM komentar_dsn
					WHERE komentar_mhs.id_komentar_mhs = komentar_dsn.id_komentar_mhs
					) AS komentar_dsn, (
						SELECT komentar_dsn.dibuat_pd
						FROM komentar_dsn
						WHERE komentar_mhs.id_komentar_mhs = komentar_dsn.id_komentar_mhs
						) AS dibuat_pd_dsn
						FROM komentar_mhs
						WHERE komentar_mhs.id_instruksi = :id_instruksi 
						ORDER BY dibuat_pd_mhs";												   
			$q = $this->conn->prepare($sql) or die("Gagal!");
			$q->bindParam(':id_instruksi', $id_instruksi);
			$q->execute();
			while($r = $q->fetch(PDO::FETCH_ASSOC))
			{
				$data[] = $r;
			}
			return $data;
		}
		/*** BERKAS ***/
		public function showBerkasMhs($id_mhs)
		{
			$sql = "SELECT nama_berkas, dir_berkas FROM berkas_mhs
					WHERE id_berkas_mhs
					IN 
					(
						SELECT id_berkas_mhs FROM penugasan
						WHERE id_tugas
						IN 
						(
							SELECT id_tugas FROM penugasan WHERE id_instruksi
							IN 
							(
								SELECT id_instruksi FROM instruksi
								WHERE id_bimbingan = 
								( 
									SELECT id_bimbingan FROM bimbingan WHERE id_mhs = :id_mhs 
								)
							)
						)
					)";												   
			$q = $this->conn->prepare($sql) or die("Gagal!");
			$q->bindParam(':id_mhs', $id_mhs);
			$q->execute();
			$sql2 = $sql;
			$stmt = $this->conn->prepare($sql2);
			$stmt->bindParam(':id_mhs', $id_mhs);
			$stmt->execute();
			$row = $stmt->fetch(PDO::FETCH_NUM);
			while($r = $q->fetch(PDO::FETCH_ASSOC))
			{
				$data[] = $r;
			}
			if($row)
			{
				return $data;
			}	
			else
				return false;
		}
		public function showBerkasDsn($id_mhs)
		{
			$sql = "SELECT nama_berkas, dir_berkas, dir_berkas
					FROM berkas_dsn
					WHERE id_instruksi
					IN 
					(
						SELECT id_instruksi
						FROM instruksi
						WHERE id_bimbingan
						ORDER BY instruksi.dibuat_pd
						IN 
						(
							SELECT id_bimbingan
							FROM bimbingan
							WHERE id_mhs = $id_mhs
						)
					)";												   
			$q = $this->conn->prepare($sql) or die("Gagal!");
			$q->bindParam(':id_mhs', $id_mhs);
			$q->execute();
			$sql2 = $sql;
			$stmt = $this->conn->prepare($sql2);
			$stmt->bindParam(':id_mhs', $id_mhs);
			$stmt->execute();
			$row = $stmt->fetch(PDO::FETCH_NUM);
			while($r = $q->fetch(PDO::FETCH_ASSOC))
			{
				$data[] = $r;
			}
			if($row)
			{
				return $data;
			}	
			else
				return false;			
		}
		public function infoPembimbing($id_mhs)
		{
			$sql = "SELECT id_dsn, nama, nidn, alamat, telp, email, bdg_keahlian, dir_foto FROM dosen
					WHERE id_dsn = 
					( 
						SELECT id_dsn
						FROM klmp_bimbingan
						WHERE kode_bimbingan = 
						( 
							SELECT kode_bimbingan
							FROM bimbingan
							WHERE id_mhs = :id_mhs 
						) 
					)";												   
			$q = $this->conn->prepare($sql) or die("Gagal!");
			$q->bindParam(':id_mhs', $id_mhs);
			$q->execute();
			$data = $q->fetch(PDO::FETCH_ASSOC);
			return $data;			
		}
		public function daftarMhs($id_dsn)
		{
			$sql = "SELECT id_mhs, nama, nim, prodi, alamat, telp, email, jdl_skripsi
					FROM mahasiswa
					WHERE id_mhs IN (
						SELECT id_mhs
						FROM bimbingan
						WHERE kode_bimbingan = ( 
										SELECT kode_bimbingan
										FROM klmp_bimbingan
										WHERE id_dsn = :id_dsn ))";												   
			$q = $this->conn->prepare($sql) or die("Gagal!");
			$q->bindParam(':id_dsn', $id_dsn);
			$q->execute();
			$sql2 = $sql;
			$stmt = $this->conn->prepare($sql2);
			$stmt->bindParam(':id_dsn', $id_dsn);
			$stmt->execute();
			$row = $stmt->fetch(PDO::FETCH_NUM);
			while($r = $q->fetch(PDO::FETCH_ASSOC))
			{
				$data[] = $r;
			}
			if($row)
			{
				return $data;
			}
			else return false;
		}
		public function profilMhs($id_mhs)
		{
			$sql = "SELECT nama, nim, prodi, tempat_lahir, tgl_lahir, alamat, telp, email, dir_foto
					FROM mahasiswa
					WHERE id_mhs = :id_mhs";												   
			$q = $this->conn->prepare($sql) or die("Gagal!");
			$q->bindParam(':id_mhs', $id_mhs);
			$q->execute();
			$data = $q->fetch(PDO::FETCH_ASSOC);
			return $data;			
		}
	} 
?>