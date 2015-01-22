<?php

include_once("dosen.php");
session_start();

if(isset($_SESSION['message']))
{
    echo $_SESSION['message'];
}
if(isset($_SESSION['id_dsn']))
{
    $message = "User is already logged in";
}
if(!isset($_POST['username'], $_POST['password']))
{
    $message = "Username dan password tidak valid";
}
else
{
    if(isset($_POST['login_dsn']))
    {  
        include_once("dosen.php");
        $obj = new dosen;
        extract($_POST);
        if($obj->Login($username, $password))
        {
            $_SESSION['id_dsn'] = $obj->Login($username, $password);
            $message = "Berhasil Login";
            header("location:home_dsn.php");
        }
    }
    else if(isset($_POST['login_mhs']))
    {
        include_once("mahasiswa.php");
        $obj = new mahasiswa;
        extract($_POST);
        if($obj->Login($username, $password))
        {
            $_SESSION['id_mhs'] = $obj->Login($username, $password);
            $message = "Berhasil Login";
            header("location:home_mhs.php");
        }
    }
    else
    {
        $message =  "Username atau password salah!";
    }
}
$_SESSION['message'] = $message;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>   
	<link href="assets/css/bootstrap.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
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
    <div class="container-fluid">
        <div class="navbar navbar-default navbar-fixed-top" role="navigation">
            <div class="navbar-header">
                <div class="container">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#collapseNavbar">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="index.php">Bimbingan Skripsi Online <em>DINUS</em></a>
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
        <div class="container">
              <div id="myCarousel" class="carousel slide">
                <div class="carousel-inner">
                  <div class="item active">
                    <img src="img/1.jpg" alt="" width="750px" height="500px"> 
                  </div>
                  <div class="item">
                    <img src="img/3.jpg" alt="" width="750px" height="500px">
                  </div>
                  <div class="item">
                    <img src="img/4.jpg" alt="" width="750px" height="500px">
                  </div>
                </div>
                <a class="left carousel-control" href="#myCarousel" data-slide="prev"><i class="fa fa-angle-left"></i></a>
                <a class="right carousel-control" href="#myCarousel" data-slide="next">&rsaquo;</a>
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