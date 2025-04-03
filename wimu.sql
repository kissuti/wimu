-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Gép: 127.0.0.1
-- Létrehozás ideje: 2025. Ápr 03. 18:19
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
(1, 'Általános laptop', '', 'Laptop', 'altalanos_laptop.jpg', 'Általános felhasználásra, munkára és iskolára tökéletes.', 'Az általános laptop egy olyan sokoldalú, hordozható számítógép, melyet mindennapi feladatokhoz, például internetböngészéshez, irodai alkalmazások futtatásához, multimédiás tartalmak megtekintéséhez vagy könnyed játékhoz terveztek.', 59990, 'darab', 1, 2, 3, 9),
(2, 'Ultrabook', '', 'Laptop', 'altalanos_laptop2.jpg', 'Elegáns, könnyű, könnyen hordózható.', 'Az ultrabook egy rendkívül vékony, könnyű és elegáns laptop, melyet a mobilitás és a stílus ötvözésére terveztek. Ezek az eszközök prémium anyagokból, például alumíniumból készülnek, így strapabíróak, mégis kompakt kialakításuknak köszönhetően könnyen beleférnek a táskába. Ultrabookokban energiahatékony, nagy teljesítményű processzorok, gyors SSD-meghajtók és elegendő RAM található, melyek biztosítják a zökkenőmentes multitaskingot és a gyors rendszerindítást. A 13 hüvelykes kijelzők magas felbontást és élénk, részletgazdag képet kínálnak, míg a modern csatlakozási lehetőségek, például USB-C, HDMI és WiFi, megkönnyítik a külső eszközök csatlakoztatását. Emellett az ultrabookok hosszan tartó akkumulátorüzemidővel és gyors töltési funkciókkal rendelkeznek, így ideálisak a folyamatos, útközbeni használathoz.', 99990, 'darab', 1, 2, 3, 10),
(3, 'Gaming Laptop', '', 'Laptop', 'gaming_laptop.jpg', 'Nagy akkumulátor élettartam. Erős hardver. Kiváló hűtés.', 'A gaming laptopok egyik legfontosabb jellemzője az erős processzor, amely lehetővé teszi a gyors adatfeldolgozást és a gördülékeny játékélményt. Emellett a nagy teljesítményű dedikált videokártya elengedhetetlen, hogy a játékok grafikája részletgazdag és élethű legyen. A legtöbb gaming laptop legalább 16 GB RAM-mal rendelkezik, de a csúcskategóriás modellek akár 32 vagy 64 GB memóriával is felszerelhetők a maximális teljesítmény érdekében.', 124990, 'darab', 1, 2, 3, 10),
(4, 'Munka állomás', '', 'workstation', 'workstation.jpg', 'A workstation egy professzionális, nagy teljesítményű számítógép.', 'A workstationok célja, hogy a professzionális felhasználók számára megbízható és nagy teljesítményű megoldást kínáljanak. Ezek a rendszerek általában többmagos, magas órajelű processzorokkal, 16 GB vagy annál nagyobb RAM-mal és professzionális, dedikált videókártyával vannak felszerelve. Az erőteljes hardver kombinálva van fejlett hűtési megoldásokkal, amelyek garantálják a stabil működést hosszú és intenzív munkamenetek alatt is. Emellett a széleskörű csatlakozási lehetőségek ? USB, HDMI, DisplayPort és Ethernet portok ? megkönnyítik a professzionális munkaállomásokba való integrációt. Ezek a jellemzők teszik a workstationokat ideálissá a grafikai tervezéshez, 3D rendereléshez, videószerkesztéshez és más, nagy számítási igényű feladatokhoz.', 99990, 'darab', 1, 2, 4, 30);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `kategoriak`
--

CREATE TABLE `kategoriak` (
  `id` bigint(6) UNSIGNED NOT NULL,
  `nev` varchar(150) NOT NULL,
  `szulo1` bigint(6) UNSIGNED DEFAULT NULL,
  `szulo2` bigint(6) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin2 COLLATE=latin2_hungarian_ci;

--
-- A tábla adatainak kiíratása `kategoriak`
--

INSERT INTO `kategoriak` (`id`, `nev`, `szulo1`, `szulo2`) VALUES
(5, '2-in-1 eszközök (átváltó táblagépek/laptopok)', 1, 2),
(4, 'Asztali számítógépek (workstation, otthoni PC-k)', 1, 2),
(1, 'Elektronika', NULL, NULL),
(13, 'Felsők és blúzok', 10, 11),
(15, 'Férfi ruházat', 10, NULL),
(16, 'Ingek és pólók', 10, 15),
(3, 'Laptopok (általános, gaming, ultrabook)', 1, 2),
(7, 'LED televíziók', 1, 6),
(17, 'Nadrágok (farmerek, chino)', 10, 15),
(12, 'Női ruhák (koktélruha, hétköznapi viselet)', 10, 11),
(11, 'Női ruházat', 10, NULL),
(8, 'OLED televíziók', 1, 6),
(10, 'Ruházat', NULL, NULL),
(9, 'Smart TV-k', 1, 6),
(2, 'Számítógépek és Laptopok', 1, NULL),
(14, 'Szoknyák és nadrágok', 10, 11),
(6, 'Televíziók', 1, NULL),
(18, 'Zakók és öltönyök', 10, 15);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `kosar`
--

CREATE TABLE `kosar` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `arucikk_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `ugyfel_id` bigint(20) UNSIGNED DEFAULT NULL,
  `rendeles_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `session_id` varchar(100) DEFAULT '',
  `db` int(4) UNSIGNED NOT NULL DEFAULT 0,
  `mikor` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin2 COLLATE=latin2_hungarian_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `megtekintve`
--

CREATE TABLE `megtekintve` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ugyfel_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `session_id` varchar(100) NOT NULL DEFAULT '',
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
  `kifizetve` datetime NOT NULL DEFAULT current_timestamp(),
  `teljesitve` datetime NOT NULL DEFAULT current_timestamp(),
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
-- Tábla szerkezet ehhez a táblához `rendeles_tetelek`
--

CREATE TABLE `rendeles_tetelek` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `rendeles_id` bigint(20) UNSIGNED NOT NULL,
  `arucikk_id` bigint(20) UNSIGNED NOT NULL,
  `db` int(4) UNSIGNED NOT NULL,
  `ar_huf` bigint(6) UNSIGNED NOT NULL
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
(1, 'admin@admin.com', 'admin', '', '$2y$10$E9qNWWHdHaCfOb5AeCp4Q.Yd8DFbYbK0NGeYbi0GHsloonfbNxIde', '2025-04-03 17:46:37', '2025-04-03 17:46:37', '', '', '0itl1ffa8sf6tcvhspnh4gfsvi', '2025-04-03 19:13:36', '', 0, '', '', '', '', '', '', '', '', 'admin'),
(2, 'profile@profile.com', 'Profile', '', '$2y$10$Mwat8SmgpHSlTkdALbbfeO/TtJOmVaV8zJjUO9ntd6nXaF8C2Ie9e', '2025-04-03 17:47:04', '2025-04-03 17:47:04', '', '', '', '0000-00-00 00:00:00', '+36 50 111 1111', 0, 'Magyarország', '7632', 'Pécs', 'Utca utca 2.', 'Profile', '7632', 'Pécs', 'Utca utca 2.', 'user');

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
  ADD KEY `nev` (`nev`,`szulo1`,`szulo2`),
  ADD KEY `szulo1` (`szulo1`),
  ADD KEY `szulo2` (`szulo2`);

--
-- A tábla indexei `kosar`
--
ALTER TABLE `kosar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `arucikk_id` (`arucikk_id`,`ugyfel_id`,`session_id`,`mikor`),
  ADD KEY `rendeles_id` (`rendeles_id`),
  ADD KEY `ugyfel_id` (`ugyfel_id`);

--
-- A tábla indexei `megtekintve`
--
ALTER TABLE `megtekintve`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ugyfel_id` (`ugyfel_id`,`session_id`,`arucikk_id`,`mikor`),
  ADD KEY `arucikk_id` (`arucikk_id`);

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
-- A tábla indexei `rendeles_tetelek`
--
ALTER TABLE `rendeles_tetelek`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rendeles_id` (`rendeles_id`),
  ADD KEY `arucikk_id` (`arucikk_id`);

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT a táblához `kategoriak`
--
ALTER TABLE `kategoriak`
  MODIFY `id` bigint(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT a táblához `kosar`
--
ALTER TABLE `kosar`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT a táblához `megtekintve`
--
ALTER TABLE `megtekintve`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `naplo`
--
ALTER TABLE `naplo`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `rendelesek`
--
ALTER TABLE `rendelesek`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `rendeles_tetelek`
--
ALTER TABLE `rendeles_tetelek`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `ugyfel`
--
ALTER TABLE `ugyfel`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Megkötések a kiírt táblákhoz
--

--
-- Megkötések a táblához `kategoriak`
--
ALTER TABLE `kategoriak`
  ADD CONSTRAINT `kategoriak_ibfk_1` FOREIGN KEY (`szulo1`) REFERENCES `kategoriak` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `kategoriak_ibfk_2` FOREIGN KEY (`szulo2`) REFERENCES `kategoriak` (`id`) ON DELETE SET NULL;

--
-- Megkötések a táblához `kosar`
--
ALTER TABLE `kosar`
  ADD CONSTRAINT `kosar_ibfk_1` FOREIGN KEY (`arucikk_id`) REFERENCES `arucikk` (`id`),
  ADD CONSTRAINT `kosar_ibfk_2` FOREIGN KEY (`ugyfel_id`) REFERENCES `ugyfel` (`id`);

--
-- Megkötések a táblához `megtekintve`
--
ALTER TABLE `megtekintve`
  ADD CONSTRAINT `megtekintve_ibfk_1` FOREIGN KEY (`ugyfel_id`) REFERENCES `ugyfel` (`id`),
  ADD CONSTRAINT `megtekintve_ibfk_2` FOREIGN KEY (`arucikk_id`) REFERENCES `arucikk` (`id`);

--
-- Megkötések a táblához `rendelesek`
--
ALTER TABLE `rendelesek`
  ADD CONSTRAINT `rendelesek_ibfk_1` FOREIGN KEY (`ugyfel_id`) REFERENCES `ugyfel` (`id`);

--
-- Megkötések a táblához `rendeles_tetelek`
--
ALTER TABLE `rendeles_tetelek`
  ADD CONSTRAINT `rendeles_tetelek_ibfk_1` FOREIGN KEY (`rendeles_id`) REFERENCES `rendelesek` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rendeles_tetelek_ibfk_2` FOREIGN KEY (`arucikk_id`) REFERENCES `arucikk` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rendeles_tetelek_ibfk_3` FOREIGN KEY (`rendeles_id`) REFERENCES `rendelesek` (`id`),
  ADD CONSTRAINT `rendeles_tetelek_ibfk_4` FOREIGN KEY (`arucikk_id`) REFERENCES `arucikk` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
