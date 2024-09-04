-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Sep 04, 2024 at 09:47 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ppdb`
--
CREATE DATABASE IF NOT EXISTS `ppdb` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `ppdb`;

-- --------------------------------------------------------

--
-- Table structure for table `hasil`
--

CREATE TABLE `hasil` (
  `id_lolos` int(11) NOT NULL,
  `id_siswa` varchar(255) NOT NULL,
  `jurusan` varchar(255) NOT NULL,
  `ket` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `j_afirmasi`
--

CREATE TABLE `j_afirmasi` (
  `id_afirmasi` int(11) NOT NULL,
  `nisn` int(16) NOT NULL,
  `doc` varchar(255) NOT NULL,
  `pilihan` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `j_afirmasi`
--

INSERT INTO `j_afirmasi` (`id_afirmasi`, `nisn`, `doc`, `pilihan`) VALUES
(6, 1003, 'doc1', 'DKV');

-- --------------------------------------------------------

--
-- Table structure for table `j_nilai_akademik`
--

CREATE TABLE `j_nilai_akademik` (
  `id_j_nilai` int(11) NOT NULL,
  `nisn` int(16) NOT NULL,
  `nilai_rata` int(11) NOT NULL,
  `pilihan` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `j_zonasi`
--

CREATE TABLE `j_zonasi` (
  `id_zonasi` int(11) NOT NULL,
  `nisn` int(16) NOT NULL,
  `jarak` decimal(10,2) NOT NULL,
  `pilihan` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `j_zonasi`
--

INSERT INTO `j_zonasi` (`id_zonasi`, `nisn`, `jarak`, `pilihan`) VALUES
(3, 1002, 1.20, 'RPL'),
(4, 1001, 6.98, 'RPL');

-- --------------------------------------------------------

--
-- Table structure for table `nilai`
--

CREATE TABLE `nilai` (
  `id_nilai` int(11) NOT NULL,
  `nisn` int(16) NOT NULL,
  `nilai_rata` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nilai`
--

INSERT INTO `nilai` (`id_nilai`, `nisn`, `nilai_rata`) VALUES
(1, 1001, 77.00),
(2, 1002, 84.00),
(3, 1003, 75.00);

-- --------------------------------------------------------

--
-- Table structure for table `pendaftaran`
--

CREATE TABLE `pendaftaran` (
  `id_pendaftaran` int(11) NOT NULL,
  `nisn` int(16) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pendaftaran`
--

INSERT INTO `pendaftaran` (`id_pendaftaran`, `nisn`) VALUES
(1, 1001),
(2, 1002),
(3, 1003);

-- --------------------------------------------------------

--
-- Table structure for table `siswa`
--

CREATE TABLE `siswa` (
  `id_siswa` int(11) NOT NULL,
  `nisn` int(16) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `tgl_lahir` date NOT NULL,
  `alamat` varchar(255) NOT NULL,
  `ortu` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `siswa`
--

INSERT INTO `siswa` (`id_siswa`, `nisn`, `nama`, `tgl_lahir`, `alamat`, `ortu`) VALUES
(1, 1001, 'Siswa 1', '2007-01-13', 'address 1', 'people 1'),
(2, 1002, 'Siswa 2', '2006-04-10', 'address 2', 'people 2'),
(6, 1003, 'Siswa 3', '2024-09-11', 'Address 3', 'people 3'),
(7, 1004, 'Siswa 4', '2024-09-01', 'Address 4', 'people 4');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `hasil`
--
ALTER TABLE `hasil`
  ADD PRIMARY KEY (`id_lolos`);

--
-- Indexes for table `j_afirmasi`
--
ALTER TABLE `j_afirmasi`
  ADD PRIMARY KEY (`id_afirmasi`);

--
-- Indexes for table `j_nilai_akademik`
--
ALTER TABLE `j_nilai_akademik`
  ADD PRIMARY KEY (`id_j_nilai`);

--
-- Indexes for table `j_zonasi`
--
ALTER TABLE `j_zonasi`
  ADD PRIMARY KEY (`id_zonasi`);

--
-- Indexes for table `nilai`
--
ALTER TABLE `nilai`
  ADD PRIMARY KEY (`id_nilai`);

--
-- Indexes for table `pendaftaran`
--
ALTER TABLE `pendaftaran`
  ADD PRIMARY KEY (`id_pendaftaran`);

--
-- Indexes for table `siswa`
--
ALTER TABLE `siswa`
  ADD PRIMARY KEY (`id_siswa`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `hasil`
--
ALTER TABLE `hasil`
  MODIFY `id_lolos` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `j_afirmasi`
--
ALTER TABLE `j_afirmasi`
  MODIFY `id_afirmasi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `j_nilai_akademik`
--
ALTER TABLE `j_nilai_akademik`
  MODIFY `id_j_nilai` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `j_zonasi`
--
ALTER TABLE `j_zonasi`
  MODIFY `id_zonasi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `nilai`
--
ALTER TABLE `nilai`
  MODIFY `id_nilai` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pendaftaran`
--
ALTER TABLE `pendaftaran`
  MODIFY `id_pendaftaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1003;

--
-- AUTO_INCREMENT for table `siswa`
--
ALTER TABLE `siswa`
  MODIFY `id_siswa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
