-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 19, 2018 at 09:01 AM
-- Server version: 10.1.26-MariaDB
-- PHP Version: 7.0.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_kota`
--

-- --------------------------------------------------------

--
-- Table structure for table `cost`
--

CREATE TABLE `cost` (
  `id` int(11) NOT NULL,
  `origin` int(11) NOT NULL,
  `destination` int(11) NOT NULL,
  `courier` varchar(11) NOT NULL,
  `cost` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cost`
--

INSERT INTO `cost` (`id`, `origin`, `destination`, `courier`, `cost`) VALUES
(1, 444, 152, 'jne oke', 13000),
(2, 444, 152, 'jne reg', 15000),
(3, 444, 152, 'jne yes', 26000),
(4, 444, 152, 'pos pkk', 15000),
(5, 444, 152, 'pos endb', 26000),
(6, 444, 152, 'pos pdg', 26000),
(7, 444, 152, 'pos pvg', 26000),
(8, 444, 152, 'tiki trc', 52000),
(9, 444, 152, 'tiki reg', 14000),
(10, 444, 152, 'tiki eco', 12000),
(11, 444, 152, 'tiki ons', 21000),
(12, 444, 152, 'tiki sds', 157000),
(13, 444, 152, 'tiki hds', 37000),
(14, 444, 255, 'jne oke', 7000),
(15, 444, 255, 'jne reg', 8000),
(16, 444, 255, 'pos pkk', 10000),
(17, 444, 255, 'pos endb', 22000),
(18, 444, 255, 'pos pdg', 22000),
(19, 444, 255, 'pos pvg', 22000),
(20, 444, 255, 'tiki reg', 7000),
(21, 444, 255, 'tiki eco', 5000),
(22, 444, 255, 'tiki ons', 14000),
(23, 444, 255, 'tiki sds', 125000),
(24, 444, 255, 'pos pkk', 10000),
(25, 444, 255, 'pos endb', 22000),
(26, 444, 255, 'pos pdg', 22000),
(27, 444, 255, 'pos pvg', 22000),
(28, 444, 419, 'jne oke', 12000),
(29, 444, 419, 'jne reg', 14000),
(30, 444, 419, 'jne yes', 21000),
(31, 444, 419, 'tiki tds', 21000),
(32, 444, 419, 'tiki reg', 13000),
(33, 444, 419, 'tiki eco', 9000),
(38, 444, 419, 'pos pkk', 16000),
(39, 444, 419, 'pos pdg', 23000),
(40, 444, 419, 'pos pvg', 23000),
(41, 444, 312, 'jne oke', 42000),
(42, 444, 312, 'jne reg', 49000);

-- --------------------------------------------------------

--
-- Table structure for table `kota`
--

CREATE TABLE `kota` (
  `id_kota` int(3) NOT NULL,
  `nama_kota` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `kota`
--

INSERT INTO `kota` (`id_kota`, `nama_kota`) VALUES
(1, 'Banda Aceh'),
(2, 'Langsa'),
(3, 'Lhokseumawe'),
(4, 'Meulaboh'),
(5, 'Sabang'),
(6, 'Subulussalam'),
(7, 'Denpasar'),
(8, 'Pangkalpinang'),
(9, 'Cilegon'),
(10, 'Serang'),
(11, 'Tangerang Selatan'),
(12, 'Tangerang'),
(13, 'Bengkulu'),
(14, 'Gorontalo'),
(15, 'Jakarta Barat'),
(16, 'Jakarta Pusat'),
(17, 'Jakarta Selatan'),
(18, 'Jakarta Timur'),
(19, 'Jakarta Utara'),
(20, 'Sungai Penuh'),
(21, 'Jambi'),
(22, 'Bandung'),
(23, 'Bekasi'),
(24, 'Bogor'),
(25, 'Cimahi'),
(26, 'Cirebon'),
(27, 'Depok'),
(28, 'Sukabumi'),
(29, 'Tasikmalaya'),
(30, 'Banjar'),
(31, 'Magelang'),
(32, 'Pekalongan'),
(33, 'Purwokerto'),
(34, 'Salatiga'),
(35, 'Semarang'),
(36, 'Surakarta'),
(37, 'Tegal'),
(38, 'Batu'),
(39, 'Blitar'),
(40, 'Kediri'),
(41, 'Madiun'),
(42, 'Malang'),
(43, 'Mojokerto'),
(44, 'Pasuruan'),
(45, 'Probolinggo'),
(46, 'Surabaya'),
(47, 'Pontianak'),
(48, 'Singkawang'),
(49, 'Banjarbaru'),
(50, 'Banjarmasin'),
(51, 'Palangkaraya'),
(52, 'Balikpapan'),
(53, 'Bontang'),
(54, 'Samarinda'),
(55, 'Tarakan'),
(56, 'Batam'),
(57, 'Tanjungpinang'),
(58, 'Bandar Lampung'),
(59, 'Metro'),
(60, 'Ternate'),
(61, 'Tidore Kepulauan'),
(62, 'Ambon'),
(63, 'Tual'),
(64, 'Bima'),
(65, 'Mataram'),
(66, 'Kupang'),
(67, 'Sorong'),
(68, 'Jayapura'),
(69, 'Dumai'),
(70, 'Pekanbaru'),
(71, 'Makassar'),
(72, 'Palopo'),
(73, 'Parepare'),
(74, 'Palu'),
(75, 'Bau-Bau'),
(76, 'Kendari'),
(77, 'Bitung'),
(78, 'Kotamobagu'),
(79, 'Manado'),
(80, 'Tomohon'),
(81, 'Bukittinggi'),
(82, 'Padang'),
(83, 'Padangpanjang'),
(84, 'Pariaman'),
(85, 'Payakumbuh'),
(86, 'Sawahlunto'),
(87, 'Solok'),
(88, 'Lubuklinggau'),
(89, 'Pagaralam'),
(90, 'Palembang'),
(91, 'Prabumulih'),
(92, 'Binjai'),
(93, 'Medan'),
(94, 'Padang Sidempuan'),
(95, 'Pematangsiantar'),
(96, 'Sibolga'),
(97, 'Tanjungbalai'),
(98, 'Tebingtinggi'),
(99, 'Yogyakarta');

-- --------------------------------------------------------

--
-- Table structure for table `provinsi`
--

CREATE TABLE `provinsi` (
  `id_provinsi` int(3) NOT NULL,
  `nama_provinsi` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `provinsi`
--

INSERT INTO `provinsi` (`id_provinsi`, `nama_provinsi`) VALUES
(1, 'Nanggro Aceh Darussalam'),
(2, 'Bali'),
(3, 'Bangka Belitung'),
(4, 'Banten'),
(5, 'Bengkulu'),
(6, 'Gorontalo'),
(7, 'Jakarta'),
(8, 'Jambi'),
(9, 'Jawa Barat'),
(10, 'Jawa Tengah'),
(11, 'Jawa Timur'),
(12, 'Kalimantan Barat'),
(13, 'Kalimantan Selatan'),
(14, 'Kalimantan Tengah'),
(15, 'Kalimantan Timur'),
(16, 'Kalimantan Utara'),
(17, 'Kepulauan Riau'),
(18, 'Lampung'),
(19, 'Maluku Utara'),
(20, 'Maluku'),
(21, 'Nusa Tenggara Barat'),
(22, 'Nusa Tenggara Timur'),
(23, 'Papua Barat'),
(24, 'Papua'),
(25, 'Riau'),
(26, 'Sulawesi Selatan'),
(27, 'Sulawesi Tengah'),
(28, 'Sulawesi Tenggara'),
(29, 'Sulawesi Utara'),
(30, 'Sumatera Barat'),
(31, 'Sumatera Selatan'),
(32, 'Sumatera Utara'),
(33, 'Yogyakarta'),
(34, 'Sulawesi Barat');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cost`
--
ALTER TABLE `cost`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kota`
--
ALTER TABLE `kota`
  ADD PRIMARY KEY (`id_kota`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cost`
--
ALTER TABLE `cost`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `kota`
--
ALTER TABLE `kota`
  MODIFY `id_kota` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
