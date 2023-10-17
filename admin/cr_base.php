<?php
// Создание пустой базы mysqli
include 'options/options.php';

$link = mysqli_connect($ser_mysql, $mysqladmin, $mysqlpass);
if (!$link) { die('Ошибка соединения: ' . mysqli_error()); }

$res = mysqli_query ($link,'create database if not exists comps;');
if (!$res) { die('Ошибка создания базы: ' . mysqli_error()); }

$res = mysqli_select_db ($link,'comps');
if (!$res) { die('Ошибка выбора базы: ' . mysqli_error()); }

$res = mysqli_query ($link,"create table if not exists comps (
	id INTEGER AUTO_INCREMENT PRIMARY KEY,
	hostname VARCHAR(100) UNIQUE,
	cpu_num INTEGER,
	cpu_name VARCHAR(100),
	mem_total INTEGER,
	video VARCHAR(100),
	ubuntu_ver VARCHAR(100),
	upd_info VARCHAR(100))");
if (!$res) { die('Ошибка создания таблицы: ' . mysqli_error()); }

$res = mysqli_query ($link,"create table if not exists stats (
	id INTEGER AUTO_INCREMENT PRIMARY KEY,
	hostname VARCHAR(100) UNIQUE,
	ipaddr VARCHAR(100),
	user VARCHAR(100),
	gecos VARCHAR(200),
	cached VARCHAR(100),
	prn VARCHAR(200),
	uptime VARCHAR(100),
	nowtime INTEGER)");
if (!$res) { die('Ошибка создания таблицы: ' . mysqli_error()); }

$res = mysqli_query ($link,"create table if not exists lastlogon (
	id INTEGER PRIMARY KEY,
	account VARCHAR(100) UNIQUE,
	time VARCHAR(100),
	pc VARCHAR(100))");
if (!$res) { die('Ошибка создания таблицы: ' . mysqli_error()); }
	
mysqli_close($link);
?>




