<?php
	class dosen{
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
		public function checkUsername($register)
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
		public function register($nama, $nidn, $jen_kel, $tempat_lahir, $tgl_lahir, $alamat, $telp, $email, $bdg_keahlian, $username, $password, $target) 
		{
			try
			{
				if($this->checkUsername($username))
				{
					$sql = "INSERT INTO dosen SET nama=:nama, nidn=:nidn, jen_kel=:jen_kel, tempat_lahir=:tempat_lahir, tgl_lahir=:tgl_lahir, alamat=:alamat,
							telp=:telp, email=:email, bdg_keahlian=:bdg_keahlian, username=:username, password=:password, dir_foto=:dir_foto";
					$q = $this->conn->prepare($sql);
					$q->execute(array(':nama'=>$nama,
										':nidn'=>$nidn,
										':jen_kel'=>$jen_kel,
										':tempat_lahir'=>$tempat_lahir,
										':tgl_lahir'=>$tgl_lahir, 
										':alamat'=>$alamat,
										':telp'=>$telp,
										':email'=>$email,
										':bdg_keahlian'=>$bdg_keahlian,
										':username'=>$username,
										':password'=>md5($password),
										':dir_foto'=>$target));
				}
				else
				{
					echo 'Username Sudah Ada!';
				}
			}catch(PDOException $e)
			{
				echo $e->getMessage();
				return false;
			}
			return true;
		}
		public function getIdDosen($username)
		{
			$stmt = $this->conn->prepare("SELECT * FROM dosen WHERE username= :user");
			$stmt->bindParam(':user', $username);
			$stmt->execute();
			$id_dsn = $stmt->fetchColumn();
			return $id_dsn;
		}
		public function generateKodeBimbingan($length = 5)// melakukan generate 5 digit kode bimbingan
		{
			$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$charactersLength = strlen($characters);
			$randomString = '';
			for ($i = 0; $i < $length; $i++) {
				$randomString .= $characters[rand(0, $charactersLength - 1)];
			}
			return $randomString;
		}
		public function addKlmpBimbingan($id_dsn)// membuat kelompok bimbingan baru untuk dosen
		{
			$kode_bimbingan = $this->generateKodeBimbingan();
			do
			{ 
				$stmt = $this->conn->prepare("SELECT * FROM klmp_bimbingan WHERE kode_bimbingan=:kode");
				$stmt->bindParam(':kode', $kode_bimbingan);
				$stmt->execute();
				$row = $stmt->fetch(PDO::FETCH_NUM);
				if($row == 0)// jika kode bimbingan belum ada
				{
					$sql ="INSERT INTO klmp_bimbingan VALUES(:kode_bimbingan, :id_dsn)";
					$q = $this->conn->prepare($sql);
					$q->execute(array(':kode_bimbingan'=>$kode_bimbingan,
									  ':id_dsn'=>$id_dsn));
					$created = true;
				}
				else
				{
					$created = false;
				}
			}while($created == false); //menghindari agar kode bimbingan tidak sama
			return true;
		}
		public function Login($username, $password)
		{
			$password = md5($password);
			try{			
				$stmt = $this->conn->prepare("SELECT * FROM dosen WHERE username= :user AND password= :pass");
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
			if(isset($_SESSION['id_dsn']))
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
		public function getDaftarMhs($id_dsn)// Melakukan query untuk mendapatkan data mahasiswa yang dibimbing olen dosen 
		{
			try
			{
				$sql = "SELECT id_bimbingan, (SELECT nama FROM mahasiswa WHERE mahasiswa.id_mhs = bimbingan.id_mhs)
						AS nama_mhs FROM bimbingan WHERE kode_bimbingan = 
						(SELECT kode_bimbingan FROM klmp_bimbingan WHERE id_dsn = :id_dsn)";
				$q = $this->conn->prepare($sql) or die("Gagal!");
				$q->bindParam(':id_dsn', $id_dsn);
				$q->execute();
				while($r = $q->fetch(PDO::FETCH_ASSOC)){
						$data[]=$r;
				}	
			}catch(PDOException $e)
			{
				return $e->getMessage();
			}
			return $data;
		}
		public function makePenugasanColumn($id_instruksi)
		{
			try{
				$sql = "INSERT INTO penugasan VALUES(NULL, :id_instruksi, NULL, 0)";
				$stmt = $this->conn->prepare($sql);
				$stmt->bindParam(':id_instruksi', $id_instruksi);
				$stmt->execute();
			}catch(PDOException $e)
			{
				echo $e->getMessage();
				return false;
			}
			return true;
		}
		public function makeBerkasDsnColumn($id_instruksi, $berkas, $dir_berkas)
		{
			try{
				$sql = "INSERT INTO berkas_dsn VALUES(NULL, :id_instruksi, :nama_berkas, :dir_berkas)";
				$stmt = $this->conn->prepare($sql);
				$stmt->execute(array(':id_instruksi'=>$id_instruksi,
									 ':nama_berkas'=>$berkas,
									 ':dir_berkas'=>$dir_berkas));
			}catch(PDOExecute $e)
			{
				echo $e->getMessage();
				return false;
			}
			return true;
		}
		public function makeKomentarColumn($id_instruksi)
		{
			try
			{
				$sql = "INSERT INTO komentar VALUES(NULL, :id_instruksi)";
				$stmt = $this->conn->prepare($sql);
				$stmt->bindParam(':id_instruksi', $id_instruksi);
				$stmt->execute();
			}catch(PDOException $e)
			{
				echo $e->getMessage();
				return false;
			}
			return $true;
		}
		public function writeInstruction($instruksi, $id_bimbingan)// menulis instruksi
		{
			try
			{
				$sql = "INSERT INTO instruksi VALUES(NULL, :instruksi, NULL, :id_bimbingan)";
				$q = $this->conn->prepare($sql);
				$q->execute(array(':instruksi'=>$instruksi,
								  ':id_bimbingan'=>$id_bimbingan));
			}catch(PDOException $e)
			{
				echo $e->getMessage();
				return false;
			}
			return $this->conn->lastInsertId();
		}
		public function isInstructionExist($id_dsn)
		{
				$sql = "SELECT * FROM instruksi
						WHERE id_bimbingan
						IN (
							SELECT id_bimbingan
							FROM bimbingan
							WHERE kode_bimbingan = 
							( 
								SELECT kode_bimbingan
								FROM klmp_bimbingan
								WHERE id_dsn = :id_dsn 
							)
						)";
				$q = $this->conn->prepare($sql);
				$q->bindParam(':id_dsn', $id_dsn);
				$q->execute();
				$row = $q->fetch(PDO::FETCH_NUM);					
				if($row)
					return true;
				else
					return false;
		}
		public function showInstructions($id_dsn)// menampilkan instruksi dari kelompok bimbingan dosen tertentu
		{
			try{
				$sql = "SELECT instruksi.id_instruksi, instruksi.instruksi, instruksi.dibuat_pd, mahasiswa.nama
						FROM instruksi
						LEFT JOIN bimbingan
						INNER JOIN mahasiswa ON bimbingan.id_mhs = mahasiswa.id_mhs ON instruksi.id_bimbingan = bimbingan.id_bimbingan
						AND kode_bimbingan = ( 
						SELECT kode_bimbingan
						FROM klmp_bimbingan
						WHERE id_dsn = :id_dsn) ORDER BY instruksi.dibuat_pd DESC";
				$q = $this->conn->prepare($sql);
				$q->bindParam(':id_dsn', $id_dsn);
				$q->execute();
				while($r = $q->fetch(PDO::FETCH_ASSOC)){
					$data[]=$r;
				}
			}catch(PDOException $e)
			{
				return $e->getMessage();
			}
			return $data;	
		}
		public function isBerkasDsnExist($id_instruksi)// cek apakah instruksi disertai berkas dan tampilkan berkas jika ada
		{
			try{
				$sql = "SELECT * FROM berkas_dsn WHERE id_instruksi = :id_instruksi";
				$stmt = $this->conn->prepare($sql);
				$stmt->bindParam(':id_instruksi', $id_instruksi);
				$stmt->execute();
				$data = $stmt->fetch(PDO::FETCH_ASSOC);
			}catch(PDOException $e)
			{
				echo $e->getMessage();
				return false;
			}
			return $data;
		}
		public function isAssignment($id_instruksi)// cek apakah instruksi merupakan penugasan (mengumpulkan tugas)
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
				return true;
			else
				return false;
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
		public function validateAssingment($id_instruksi, $validasi)
		{
			try
			{
				$sql = "UPDATE penugasan SET validasi = :validasi WHERE id_instruksi = :id_instruksi";
				$stmt = $this->conn->prepare($sql);
				$stmt->bindParam(':id_instruksi', $id_instruksi);
				$stmt->bindParam(':validasi', $validasi);
				$stmt->execute();
			}catch(PDOException $e)
			{
				echo $e->getMessage();
				return false;
			}
			return true;
		}
		public function RevisionOrder($id_instruksi)
		{
			try
			{
				$sql = "UPDATE penugasan SET mengumpulkan = 0 WHERE id_instruksi = :id_instruksi";
				$stmt = $this->conn->prepare($sql);
				$stmt->bindParam(':id_instruksi', $id_instruksi);
				$stmt->execute();
			}catch(PDOException $e)
			{
				echo $e->getMessage();
				return false;
			}
			return true;	
		}
		public function writeComment($id_komentar, $id_instruksi, $komentar_dsn)
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
						$q = "INSERT INTO komentar_dsn VALUES(NULL, :id_komentar, :komentar_dsn, NULL)";
						$statement = $this->conn->prepare($q);
						$statement->bindParam(':id_komentar', $id_komentar);
						$statement->bindParam(':komentar_dsn', $komentar_dsn);
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
					$q = "INSERT INTO komentar_dsn VALUES(NULL, :id_komentar, :komentar_dsn, NULL)";
					$statement = $this->conn->prepare($q);
					$statement->bindParam(':id_komentar', $id_komentar);
					$statement->bindParam(':komentar_dsn', $komentar_dsn);
					$statement->execute();
				}catch(PDOException $er)
				{
					echo $er->getMessage();
					return false;
				}
				return true;
			}
		}
		public function addComment($id_komentar_mhs, $komentar)
		{
			$sql = "INSERT INTO komentar_dsn VALUES(null, :id_komentar_mhs, :komentar, null)";
			$q = $this->conn->prepare($sql) or die("Gagal!");
			$q->bindParam(':id_komentar_mhs', $id_komentar_mhs);			
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
			while($r = $q->fetch(PDO::FETCH_BOTH))
			{
				$data[] = $r;
			}
			return $data;
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
		/*** DAFTAR MAHASISWA ***/
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
		/*** BERKAS ***/
		public function showBerkasDsn($id_dsn)
		{
			$sql = "SELECT nama_berkas, dir_berkas, dir_berkas FROM berkas_dsn
					WHERE id_instruksi
					IN (
						SELECT id_instruksi
						FROM instruksi
						WHERE id_bimbingan ORDER BY instruksi.dibuat_pd
						IN (
							SELECT id_bimbingan
							FROM bimbingan
							WHERE kode_bimbingan = ( 
							SELECT kode_bimbingan
							FROM klmp_bimbingan
							WHERE id_dsn = :id_dsn )
							)
					)";												   
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
			else
				return false;			
		}
		public function showBerkasMhs($id_mhs)
		{
			$sql = "SELECT nama_berkas, dir_berkas FROM berkas_mhs
					WHERE id_berkas_mhs 
					IN (
						SELECT id_berkas_mhs
						FROM pengumpulan_tgs
						WHERE id_tugas
						IN (
							SELECT id_tugas
							FROM penugasan
							WHERE id_instruksi
							IN (
								SELECT id_instruksi
								FROM instruksi
								WHERE id_bimbingan = 
								( 
									SELECT id_bimbingan
									FROM bimbingan
									WHERE id_mhs = :id_mhs )
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
		/*** INFORMASI MAHSISWA ***/
		public function infoMhs($id_mhs)
		{
			$sql = "SELECT nama, nim, prodi, jen_kel, tempat_lahir, tgl_lahir, alamat, telp, email, dir_foto, jdl_skripsi
					FROM mahasiswa WHERE id_mhs = :id_mhs";												   
			$q = $this->conn->prepare($sql) or die("Gagal!");
			$q->bindParam(':id_mhs', $id_mhs);
			$q->execute();
			$data = $q->fetch(PDO::FETCH_ASSOC);
			return $data;			
		}
		public function showProgressMhs($id_mhs)
		{
			$sql = "SELECT instruksi.instruksi, penugasan.id_tugas, penugasan.validasi, penugasan.mengumpulkan, pengumpulan_tgs.dibuat_pd, pengumpulan_tgs.id_berkas_mhs, berkas_mhs.nama_berkas, berkas_mhs.dir_berkas
					FROM instruksi
					LEFT JOIN penugasan ON instruksi.id_instruksi = penugasan.id_instruksi
					LEFT JOIN pengumpulan_tgs ON penugasan.id_tugas = pengumpulan_tgs.id_tugas
					LEFT JOIN berkas_mhs ON pengumpulan_tgs.id_berkas_mhs = berkas_mhs.id_berkas_mhs
					WHERE instruksi.id_bimbingan
					IN (
						SELECT id_bimbingan
						FROM bimbingan
						WHERE id_mhs = :id_mhs
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
		public function profilDsn($id_dsn)
		{
			$sql = "SELECT dosen.nama, dosen.nidn, dosen.jen_kel, dosen.tempat_lahir, dosen.tgl_lahir, dosen.alamat, dosen.telp, dosen.email, dosen.bdg_keahlian, dosen.dir_foto, klmp_bimbingan.kode_bimbingan
					FROM dosen
					LEFT JOIN klmp_bimbingan ON dosen.id_dsn = klmp_bimbingan.id_dsn
					WHERE dosen.id_dsn = :id_dsn";												   
			$q = $this->conn->prepare($sql) or die("Gagal!");
			$q->bindParam(':id_dsn', $id_dsn);
			$q->execute();
			$data = $q->fetch(PDO::FETCH_ASSOC);
			return $data;
		}
	}
?>