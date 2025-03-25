-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Gép: 127.0.0.1
-- Létrehozás ideje: 2025. Már 26. 00:37
-- Kiszolgáló verziója: 10.4.32-MariaDB
-- PHP verzió: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Adatbázis: `wimu`
--

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `arucikk`
--

CREATE TABLE `arucikk` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nev` varchar(100) NOT NULL,
  `nev2` varchar(100) NOT NULL,
  `rovidnev` varchar(30) NOT NULL,
  `foto` varchar(100) NOT NULL,
  `leiras` text NOT NULL,
  `hosszu_leiras` longtext NOT NULL,
  `ar_huf` bigint(6) UNSIGNED NOT NULL DEFAULT 0,
  `egyseg` varchar(50) NOT NULL,
  `kat1` bigint(6) UNSIGNED NOT NULL DEFAULT 0,
  `kat2` bigint(6) UNSIGNED NOT NULL DEFAULT 0,
  `kat3` bigint(6) UNSIGNED NOT NULL DEFAULT 0,
  `raktaron` bigint(20) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin2 COLLATE=latin2_hungarian_ci;

--
-- A tábla adatainak kiíratása `arucikk`
--

INSERT INTO `arucikk` (`id`, `nev`, `nev2`, `rovidnev`, `foto`, `leiras`, `hosszu_leiras`, `ar_huf`, `egyseg`, `kat1`, `kat2`, `kat3`, `raktaron`) VALUES
(1, 'Általános laptop', '', 'Laptop', 'altalanos_laptop.jpg', 'Általános felhasználásra, munkára és iskolára tökéletes.', 'Az általános laptop egy olyan sokoldalú, hordozható számítógép, melyet mindennapi feladatokhoz ? például internetböngészéshez, irodai alkalmazások futtatásához, multimédiás tartalmak megtekintéséhez vagy könnyed játékhoz terveztek.', 59990, 'darab', 1, 2, 3, 15),
(2, 'Ultrabook', '', 'Laptop', 'altalanos_laptop2.jpg', 'Elegáns, könnyű, könnyen hordózható.', 'Az ultrabook egy rendkívül vékony, könnyű és elegáns laptop, melyet a mobilitás és a stílus ötvözésére terveztek. Ezek az eszközök prémium anyagokból, például alumíniumból készülnek, így strapabíróak, mégis kompakt kialakításuknak köszönhetően könnyen beleférnek a táskába. Ultrabookokban energiahatékony, nagy teljesítményű processzorok, gyors SSD-meghajtók és elegendő RAM található, melyek biztosítják a zökkenőmentes multitaskingot és a gyors rendszerindítást. A 13?14 hüvelykes kijelzők magas felbontást és élénk, részletgazdag képet kínálnak, míg a modern csatlakozási lehetőségek ? például USB-C, HDMI és Wi?Fi ? megkönnyítik a külső eszközök csatlakoztatását. Emellett az ultrabookok hosszan tartó akkumulátorüzemidővel és gyors töltési funkciókkal rendelkeznek, így ideálisak a folyamatos, útközbeni használathoz.', 99990, 'darab', 1, 2, 3, 28),
(3, 'Gaming Laptop', '', 'Laptop', 'gaming_laptop.jpg', 'qweqweqweqwe', 'qweqwewerrrr', 132000, 'darab', 1, 2, 3, 30),
(4, 'Munka állomás', '', 'workstation', 'workstation.jpg', 'A workstation egy professzionális, nagy teljesítményű számítógép.', 'A workstationok célja, hogy a professzionális felhasználók számára megbízható és nagy teljesítményű megoldást kínáljanak. Ezek a rendszerek általában többmagos, magas órajelű processzorokkal, 16 GB vagy annál nagyobb RAM-mal és professzionális, dedikált videókártyával vannak felszerelve. Az erőteljes hardver kombinálva van fejlett hűtési megoldásokkal, amelyek garantálják a stabil működést hosszú és intenzív munkamenetek alatt is. Emellett a széleskörű csatlakozási lehetőségek ? USB, HDMI, DisplayPort és Ethernet portok ? megkönnyítik a professzionális munkaállomásokba való integrációt. Ezek a jellemzők teszik a workstationokat ideálissá a grafikai tervezéshez, 3D rendereléshez, videószerkesztéshez és más, nagy számítási igényű feladatokhoz.', 119000, 'darab', 1, 2, 4, 58);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `kategoriak`
--

CREATE TABLE `kategoriak` (
  `id` bigint(6) UNSIGNED NOT NULL,
  `nev` varchar(150) NOT NULL,
  `szulo1` bigint(6) UNSIGNED NOT NULL DEFAULT 0,
  `szulo2` bigint(6) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin2 COLLATE=latin2_hungarian_ci;

--
-- A tábla adatainak kiíratása `kategoriak`
--

INSERT INTO `kategoriak` (`id`, `nev`, `szulo1`, `szulo2`) VALUES
(5, '2-in-1 eszközök (átváltó táblagépek/laptopok)', 1, 2),
(48, 'Arckrémek (hidratáló, anti-aging)', 46, 47),
(4, 'Asztali számítógépek (workstation, otthoni PC-k)', 1, 2),
(69, 'Barkács Eszközök', 64, 0),
(47, 'Bőrápolás', 46, 0),
(20, 'Bútorok', 19, 0),
(71, 'Csiszológépek', 64, 69),
(24, 'Dekoráció', 19, 0),
(1, 'Elektronika', 0, 0),
(23, 'Étkező bútorok', 19, 20),
(25, 'Faliképek és poszterek', 19, 24),
(13, 'Felsők és blúzok', 10, 11),
(15, 'Férfi ruházat', 10, 0),
(29, 'Fitness Eszközök', 28, 0),
(38, 'Főzőedények', 37, 0),
(70, 'Fúrógépek és csavarozók', 64, 69),
(30, 'Futópadok, elliptikus trénerek', 28, 29),
(66, 'Fűnyírók (elektromos, benzines)', 64, 65),
(55, 'Gyermekjátékok', 0, 0),
(51, 'Hajápolás', 46, 0),
(53, 'Hajformázó termékek (zselé, hab)', 46, 51),
(54, 'Hajszárítók és hajvasalók', 46, 51),
(22, 'Hálószoba bútorok (ágyak, komódok)', 19, 20),
(61, 'Hinták és csúszdák', 55, 60),
(62, 'Homokozók és kiegészítők', 55, 60),
(16, 'Ingek és pólók', 10, 15),
(32, 'Jóga- és pilates kellékek', 28, 29),
(44, 'Kávéfőzők (eszpresszó, filteres)', 37, 42),
(45, 'Kenyérpirítók', 37, 42),
(35, 'Kerékpárok és kiegészítők', 28, 33),
(63, 'Kerékpárok és rollerek', 55, 60),
(64, 'Kert és Barkács', 0, 0),
(65, 'Kerti Szerszámok', 64, 0),
(37, 'Konyhai Eszközök', 0, 0),
(42, 'Konyhai Kisgépek', 37, 0),
(41, 'Kuktafazekak', 37, 38),
(40, 'Lábasok és fazekak (rozsdamentes acél, zománcozott)', 37, 38),
(26, 'Lámpák és világítástechnika', 19, 24),
(3, 'Laptopok (általános, gaming, ultrabook)', 1, 2),
(7, 'LED televíziók', 1, 6),
(68, 'Locsolórendszerek', 64, 65),
(72, 'Mérőeszközök (vízmérték, mérőszalag)', 64, 69),
(67, 'Metszőollók és fűrészek', 64, 65),
(17, 'Nadrágok (farmerek)', 10, 15),
(21, 'Nappali bútorok (kanapék, fotelok)', 19, 20),
(12, 'Női ruhák (koktélruha, hétköznapi viselet)', 10, 11),
(11, 'Női ruházat', 10, 0),
(59, 'Nyelvtanuló játékok', 55, 56),
(56, 'Oktató Játékok', 55, 0),
(8, 'OLED televíziók', 1, 6),
(19, 'Otthoni Kiegészítők', 0, 0),
(33, 'Outdoor és Kaland', 28, 0),
(57, 'Puzzle-k és kirakók', 55, 56),
(10, 'Ruházat', 0, 0),
(52, 'Samponok és balzsamok (normál, száraz hajra)', 46, 51),
(39, 'Serpenyők (tapadásmentes, öntöttvas)', 37, 38),
(9, 'Smart TV-k', 1, 6),
(28, 'Sport és Szabadidő', 0, 0),
(36, 'Sportcipők és sportruházat', 28, 33),
(31, 'Súlyzók, edzőszalagok', 28, 29),
(60, 'Szabadtéri Játékok', 55, 0),
(2, 'Számítógépek és Laptopok', 1, 0),
(46, 'Szépségápolás', 0, 0),
(14, 'Szoknyák és nadrágok', 10, 11),
(27, 'Szőnyegek és dísztárgyak', 19, 24),
(6, 'Televíziók', 1, 0),
(49, 'Testápolók', 46, 47),
(50, 'Tisztítók és tonikok', 46, 47),
(58, 'Tudományos készletek', 55, 56),
(34, 'Túrafelszerelések (táskák, sátrak, hálózsákok)', 28, 33),
(43, 'Turmixgépek és mixerek', 37, 42),
(18, 'Zakók és öltönyök', 10, 15);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `kosar`
--

CREATE TABLE `kosar` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `arucikk_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `ugyfel_id` bigint(20) UNSIGNED DEFAULT 0,
  `rendeles_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `session_id` varchar(100) DEFAULT '',
  `db` int(4) UNSIGNED NOT NULL DEFAULT 0,
  `mikor` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin2 COLLATE=latin2_hungarian_ci;

--
-- A tábla adatainak kiíratása `kosar`
--

INSERT INTO `kosar` (`id`, `arucikk_id`, `ugyfel_id`, `rendeles_id`, `session_id`, `db`, `mikor`) VALUES
(5, 1, 1, 0, '', 15, '2025-03-26 00:32:03');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `megtekintve`
--

CREATE TABLE `megtekintve` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ugyfel_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `session_id` varchar(100) NOT NULL,
  `arucikk_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `mikor` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin2 COLLATE=latin2_hungarian_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `naplo`
--

CREATE TABLE `naplo` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(100) NOT NULL,
  `mikor` datetime NOT NULL DEFAULT current_timestamp(),
  `sikeres` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `sikertelen` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `kilepes` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin2 COLLATE=latin2_hungarian_ci;

--
-- A tábla adatainak kiíratása `naplo`
--

INSERT INTO `naplo` (`id`, `email`, `mikor`, `sikeres`, `sikertelen`, `kilepes`) VALUES
(1, 'admin@admin.com', '2025-03-25 23:21:21', 1, 0, '2025-03-25 23:21:21'),
(2, 'admin@admin.com', '2025-03-25 23:56:01', 1, 0, '2025-03-25 23:56:01'),
(3, 'admin@admin.com', '2025-03-25 23:57:38', 1, 0, '2025-03-25 23:57:38'),
(4, 'admin@admin.com', '2025-03-25 23:59:35', 1, 0, '2025-03-25 23:59:35'),
(5, 'admin@admin.com', '2025-03-26 00:22:41', 1, 0, '2025-03-26 00:22:41'),
(6, 'admin@admin.com', '2025-03-26 00:24:15', 1, 0, '2025-03-26 00:24:15'),
(7, 'admin@admin.com', '2025-03-26 00:28:38', 1, 0, '2025-03-26 00:28:38'),
(8, 'admin@admin.com', '2025-03-26 00:29:31', 1, 0, '2025-03-26 00:29:31');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `rendelesek`
--

CREATE TABLE `rendelesek` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ugyfel_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `kod` varchar(50) NOT NULL,
  `torolve` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `idopont` datetime NOT NULL DEFAULT current_timestamp(),
  `fizetendo` bigint(6) UNSIGNED NOT NULL DEFAULT 0,
  `fizetesi_mod` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `kifizetve` date NOT NULL DEFAULT current_timestamp(),
  `teljesitve` date NOT NULL DEFAULT current_timestamp(),
  `nev` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telefon` varchar(50) NOT NULL,
  `kulfoldi` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `orszag` varchar(100) NOT NULL,
  `irszam` varchar(20) NOT NULL,
  `varos` varchar(100) NOT NULL,
  `utca` varchar(100) NOT NULL,
  `sz_nev` varchar(100) NOT NULL,
  `sz_irszam` varchar(20) NOT NULL,
  `sz_varos` varchar(100) NOT NULL,
  `sz_utca` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin2 COLLATE=latin2_hungarian_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `ugyfel`
--

CREATE TABLE `ugyfel` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(100) NOT NULL,
  `nev` varchar(100) NOT NULL,
  `kod` varchar(20) NOT NULL,
  `jelszo` varchar(255) NOT NULL,
  `reg_idopont` datetime NOT NULL DEFAULT current_timestamp(),
  `megerositve` datetime NOT NULL DEFAULT current_timestamp(),
  `uj_email` varchar(100) NOT NULL,
  `uj_kod` varchar(50) NOT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `ervenyes` datetime DEFAULT NULL,
  `telefon` varchar(50) NOT NULL,
  `kulfoldi` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `orszag` varchar(100) NOT NULL,
  `irszam` varchar(20) NOT NULL,
  `varos` varchar(100) NOT NULL,
  `utca` varchar(100) NOT NULL,
  `sz_nev` varchar(200) NOT NULL,
  `sz_irszam` varchar(20) NOT NULL,
  `sz_varos` varchar(100) NOT NULL,
  `sz_utca` varchar(100) NOT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=latin2 COLLATE=latin2_hungarian_ci;

--
-- A tábla adatainak kiíratása `ugyfel`
--

INSERT INTO `ugyfel` (`id`, `email`, `nev`, `kod`, `jelszo`, `reg_idopont`, `megerositve`, `uj_email`, `uj_kod`, `session_id`, `ervenyes`, `telefon`, `kulfoldi`, `orszag`, `irszam`, `varos`, `utca`, `sz_nev`, `sz_irszam`, `sz_varos`, `sz_utca`, `role`) VALUES
(1, 'admin@admin.com', 'admin', '', '$2y$10$xql9sFrWGZAeQSmklYMwUeEizH6uqkimVKqghh6o0d7lBCcy8Rud2', '2025-03-25 23:21:17', '2025-03-25 23:21:17', '', '', 'bfa6pqhel2mchch48eqnmjq8rk', '2025-03-26 01:29:31', '06-30303030', 0, 'Magyarország', '7632', 'Pécs', 'Utca utca 2.', 'admin', '7632', 'Pécs', 'Utca utca 2.', 'admin');

--
-- Indexek a kiírt táblákhoz
--

--
-- A tábla indexei `arucikk`
--
ALTER TABLE `arucikk`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kat1` (`kat1`,`kat2`,`kat3`),
  ADD KEY `raktaron` (`raktaron`);

--
-- A tábla indexei `kategoriak`
--
ALTER TABLE `kategoriak`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nev` (`nev`,`szulo1`,`szulo2`);

--
-- A tábla indexei `kosar`
--
ALTER TABLE `kosar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `arucikk_id` (`arucikk_id`,`ugyfel_id`,`session_id`,`mikor`),
  ADD KEY `rendeles_id` (`rendeles_id`);

--
-- A tábla indexei `megtekintve`
--
ALTER TABLE `megtekintve`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ugyfel_id` (`ugyfel_id`,`session_id`,`arucikk_id`,`mikor`);

--
-- A tábla indexei `naplo`
--
ALTER TABLE `naplo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email` (`email`,`mikor`,`sikeres`,`sikertelen`,`kilepes`);

--
-- A tábla indexei `rendelesek`
--
ALTER TABLE `rendelesek`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ugyfel_id` (`ugyfel_id`,`idopont`,`email`),
  ADD KEY `fizetesi_mod` (`fizetesi_mod`),
  ADD KEY `kod` (`kod`),
  ADD KEY `kifizetve` (`kifizetve`),
  ADD KEY `teljesitve` (`teljesitve`),
  ADD KEY `torolve` (`torolve`),
  ADD KEY `nev` (`nev`);

--
-- A tábla indexei `ugyfel`
--
ALTER TABLE `ugyfel`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email` (`email`),
  ADD KEY `kod` (`kod`),
  ADD KEY `nev` (`nev`),
  ADD KEY `session_id` (`session_id`,`ervenyes`);

--
-- A kiírt táblák AUTO_INCREMENT értéke
--

--
-- AUTO_INCREMENT a táblához `arucikk`
--
ALTER TABLE `arucikk`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT a táblához `kategoriak`
--
ALTER TABLE `kategoriak`
  MODIFY `id` bigint(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT a táblához `kosar`
--
ALTER TABLE `kosar`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT a táblához `megtekintve`
--
ALTER TABLE `megtekintve`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `naplo`
--
ALTER TABLE `naplo`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT a táblához `rendelesek`
--
ALTER TABLE `rendelesek`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `ugyfel`
--
ALTER TABLE `ugyfel`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
