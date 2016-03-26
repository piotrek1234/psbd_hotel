-- phpMyAdmin SQL Dump
-- version 4.1.6
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 09 Wrz 2014, 12:57
-- Server version: 5.6.16
-- PHP Version: 5.5.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `hotel`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `kategorie`
--

CREATE TABLE IF NOT EXISTS `kategorie` (
  `idKategorii` tinyint(4) NOT NULL AUTO_INCREMENT,
  `nazwa` varchar(45) NOT NULL,
  PRIMARY KEY (`idKategorii`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Zrzut danych tabeli `kategorie`
--

INSERT INTO `kategorie` (`idKategorii`, `nazwa`) VALUES
(1, 'standard'),
(2, 'dla niepełnosprawnych'),
(3, 'z dostawką dla dziecka');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `kategorieuslug`
--

CREATE TABLE IF NOT EXISTS `kategorieuslug` (
  `kategoria` smallint(6) NOT NULL AUTO_INCREMENT,
  `nazwa` varchar(45) NOT NULL,
  PRIMARY KEY (`kategoria`),
  UNIQUE KEY `idKategorii_UNIQUE` (`kategoria`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Zrzut danych tabeli `kategorieuslug`
--

INSERT INTO `kategorieuslug` (`kategoria`, `nazwa`) VALUES
(1, 'alkohole niskoprocentowe'),
(2, 'alkohole wysokoprocentowe'),
(3, 'napoje'),
(4, 'przekąski'),
(5, 'dania na ciepło'),
(6, 'papierosy'),
(7, 'inne');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `klienci`
--

CREATE TABLE IF NOT EXISTS `klienci` (
  `idKlienta` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(45) NOT NULL,
  `haslo` varchar(45) NOT NULL,
  `imie` varchar(45) NOT NULL,
  `nazwisko` varchar(45) NOT NULL,
  `nazwaFirmy` varchar(45) NOT NULL,
  `telefon` varchar(20) NOT NULL,
  `email` varchar(45) DEFAULT NULL,
  `adresKraj` varchar(45) NOT NULL,
  `adresMiasto` varchar(45) NOT NULL,
  `adresUlica` varchar(100) NOT NULL,
  `adresKod` varchar(10) NOT NULL,
  `kontoAktywne` tinyint(1) NOT NULL,
  `NIP` bigint(20) DEFAULT NULL,
  `regon` bigint(20) DEFAULT NULL,
  `czyFirma` tinyint(1) NOT NULL DEFAULT '0',
  `idFirmy` int(11) DEFAULT NULL,
  PRIMARY KEY (`idKlienta`),
  UNIQUE KEY `idKlienta_UNIQUE` (`idKlienta`),
  UNIQUE KEY `login_UNIQUE` (`login`),
  KEY `fk_klienci_klienci1_idx` (`idFirmy`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=26 ;

--
-- Zrzut danych tabeli `klienci`
--

INSERT INTO `klienci` (`idKlienta`, `login`, `haslo`, `imie`, `nazwisko`, `nazwaFirmy`, `telefon`, `email`, `adresKraj`, `adresMiasto`, `adresUlica`, `adresKod`, `kontoAktywne`, `NIP`, `regon`, `czyFirma`, `idFirmy`) VALUES
(20, 'adam', '1d7c2923c1684726dc23d2901c4d8157', 'Adam', 'Zwykły', '', '123 456 7', '', 'Polska', 'Warszawa', 'Prosta 100', '01-234', 1, 0, NULL, 0, NULL),
(21, 'jan', 'fa27ef3ef6570e32a79e74deca7c1bc3', 'Jan', 'Janowski', '', '798 456123', 'jan@jan.pl', 'Polska', 'Kraków', 'Wawelska 12', '32-147', 1, 0, NULL, 0, NULL),
(22, 'nokia', '0c23a8bf29a191f18aee814737e2a6ec', 'Dariusz', 'Sałata', 'Nokia', '123 456 798', '', 'Polska', 'Warszawa', 'Żaryna 12', '02-510', 1, 1234567891, 132456789, 1, NULL),
(24, 'piotr', '99fdb06613cd9b8f328b6cadd98b1c23', 'Piotr', 'Marecki', '', '586 479 413', '', 'Polska', 'Gdańsk', 'Sopocka 111', '90-784', 1, 0, NULL, 0, 22),
(25, 'anna', 'a70f9e38ff015afaa9ab0aacabee2e13', 'Anna', 'Nowa', '', '456 963 147', '', 'Polska', 'Poznań', 'Sienkiewicza 543', '50-716', 0, 0, NULL, 0, NULL);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `pokoje`
--

CREATE TABLE IF NOT EXISTS `pokoje` (
  `idPokoju` int(11) NOT NULL AUTO_INCREMENT,
  `pojemnosc` int(11) NOT NULL,
  `opis` varchar(450) DEFAULT NULL,
  `kategoria` tinyint(4) NOT NULL,
  PRIMARY KEY (`idPokoju`),
  UNIQUE KEY `idPomieszczenia_UNIQUE` (`idPokoju`),
  KEY `fk_pokoje_kategorie1_idx` (`kategoria`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

--
-- Zrzut danych tabeli `pokoje`
--

INSERT INTO `pokoje` (`idPokoju`, `pojemnosc`, `opis`, `kategoria`) VALUES
(5, 1, 'Standardowy pokój dla jednej osoby. Bez szału.', 1),
(6, 1, 'Dla niepełnosprawnego', 2),
(7, 2, 'Standard dla 2 osób.', 1),
(8, 2, 'Idealny dla pary z dzieckiem.', 3),
(9, 2, 'Dwuosobowy pokój o nieco lepszym standardzie.', 1),
(10, 3, 'Trzyosobowy pokój z ekskluzywnym wyposażeniem.', 1),
(11, 3, 'Dla pary z dużym i małym dzieckiem.', 3),
(12, 4, 'Czteroosobowy po taniości.', 1);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `pokoje_sezony`
--

CREATE TABLE IF NOT EXISTS `pokoje_sezony` (
  `idSezonu` int(11) NOT NULL,
  `idPokoju` int(11) NOT NULL,
  `cenaSobota` decimal(10,2) NOT NULL,
  `cenaNiedziela` decimal(10,2) NOT NULL,
  `cenaZwykla` decimal(10,2) NOT NULL,
  PRIMARY KEY (`idSezonu`,`idPokoju`),
  KEY `fk_pokoje_sezony_pokoje1_idx` (`idPokoju`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `pokoje_sezony`
--

INSERT INTO `pokoje_sezony` (`idSezonu`, `idPokoju`, `cenaSobota`, `cenaNiedziela`, `cenaZwykla`) VALUES
(12, 5, '50.00', '55.00', '45.00'),
(12, 6, '60.00', '60.00', '55.00'),
(12, 7, '90.00', '95.00', '85.00'),
(12, 8, '95.00', '95.00', '85.00'),
(12, 9, '100.00', '105.00', '95.00'),
(12, 10, '185.00', '205.00', '155.00'),
(12, 11, '135.00', '145.00', '125.00'),
(12, 12, '135.00', '135.00', '125.00'),
(13, 5, '45.00', '50.00', '40.00'),
(13, 6, '55.00', '55.00', '50.00'),
(13, 7, '85.00', '90.00', '80.00'),
(13, 8, '90.00', '90.00', '80.00'),
(13, 9, '95.00', '100.00', '90.00'),
(13, 10, '170.00', '200.00', '150.00'),
(13, 11, '130.00', '140.00', '120.00'),
(13, 12, '130.00', '130.00', '120.00'),
(16, 5, '40.00', '45.00', '40.00'),
(16, 6, '50.00', '50.00', '50.00'),
(16, 7, '80.00', '80.00', '70.00'),
(16, 8, '80.00', '90.00', '80.00'),
(16, 9, '90.00', '100.00', '90.00'),
(16, 10, '120.00', '125.00', '120.00'),
(16, 11, '130.00', '130.00', '120.00'),
(16, 12, '120.00', '125.00', '120.00'),
(17, 5, '50.00', '50.00', '50.00'),
(17, 6, '75.00', '75.00', '75.00'),
(17, 7, '120.00', '120.00', '120.00'),
(17, 8, '100.00', '100.00', '100.00'),
(17, 9, '140.00', '140.00', '140.00'),
(17, 10, '200.00', '200.00', '200.00'),
(17, 11, '150.00', '150.00', '150.00'),
(17, 12, '150.00', '150.00', '150.00'),
(18, 5, '50.00', '50.00', '50.00'),
(18, 6, '50.00', '50.00', '50.00'),
(18, 7, '90.00', '90.00', '90.00'),
(18, 8, '100.00', '100.00', '100.00'),
(18, 9, '100.00', '100.00', '100.00'),
(18, 10, '200.00', '200.00', '200.00'),
(18, 11, '100.00', '100.00', '100.00'),
(18, 12, '130.00', '130.00', '130.00');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `pokoje_wyposazenie`
--

CREATE TABLE IF NOT EXISTS `pokoje_wyposazenie` (
  `idWyposazenia` int(11) NOT NULL,
  `idPokoju` int(11) NOT NULL,
  PRIMARY KEY (`idWyposazenia`,`idPokoju`),
  KEY `fk_pokoje_wyposazenie_pokoje1_idx` (`idPokoju`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `pokoje_wyposazenie`
--

INSERT INTO `pokoje_wyposazenie` (`idWyposazenia`, `idPokoju`) VALUES
(1, 5),
(2, 5),
(5, 5),
(1, 6),
(2, 6),
(4, 6),
(5, 6),
(6, 6),
(1, 7),
(2, 7),
(3, 7),
(4, 7),
(5, 7),
(6, 7),
(7, 7),
(10, 7),
(1, 8),
(2, 8),
(3, 8),
(4, 8),
(5, 8),
(8, 8),
(2, 9),
(4, 9),
(5, 9),
(6, 9),
(7, 9),
(10, 9),
(1, 10),
(4, 10),
(6, 10),
(7, 10),
(9, 10),
(10, 10),
(11, 10),
(1, 11),
(2, 11),
(4, 11),
(5, 11),
(6, 11),
(2, 12),
(4, 12),
(5, 12);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `pomieszczenia`
--

CREATE TABLE IF NOT EXISTS `pomieszczenia` (
  `numerPomieszczenia` varchar(6) NOT NULL,
  `kluczWRecepcji` tinyint(1) NOT NULL,
  `zdjecie` varchar(256) DEFAULT NULL,
  `idSali` int(11) DEFAULT NULL,
  `idPokoju` int(11) DEFAULT NULL,
  `czySala` tinyint(1) NOT NULL,
  PRIMARY KEY (`numerPomieszczenia`),
  UNIQUE KEY `numerPomieszczenia_UNIQUE` (`numerPomieszczenia`),
  KEY `fk_pomieszczenia_saleKonferencyjne1_idx` (`idSali`),
  KEY `fk_pomieszczenia_pokoje1_idx` (`idPokoju`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `pomieszczenia`
--

INSERT INTO `pomieszczenia` (`numerPomieszczenia`, `kluczWRecepcji`, `zdjecie`, `idSali`, `idPokoju`, `czySala`) VALUES
('1', 1, 'img/pok/1os_1.png', NULL, 6, 0),
('10', 1, NULL, 3, NULL, 1),
('101', 0, 'img/pok/1os_1.png', NULL, 5, 0),
('102', 1, 'img/pok/2os_1.png', NULL, 9, 0),
('15', 1, NULL, 4, NULL, 1),
('2', 1, 'img/pok/1os_1.png', NULL, 6, 0),
('205', 1, 'img/pok/2os_3.png', NULL, 8, 0),
('215', 1, 'img/pok/3os_2.png', NULL, 11, 0),
('25', 1, NULL, 5, NULL, 1),
('270', 1, 'img/pok/2os_1.png', NULL, 9, 0),
('323', 1, 'img/pok/3os_1.png', NULL, 10, 0),
('342', 1, 'img/pok/2os_2.png', NULL, 7, 0),
('400', 1, 'img/pok/4os_1.png', NULL, 12, 0),
('402', 1, 'img/pok/4os_2.png', NULL, 12, 0);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `pozycjerachunkow`
--

CREATE TABLE IF NOT EXISTS `pozycjerachunkow` (
  `idRachunku` int(11) NOT NULL,
  `idUslugi` int(11) NOT NULL,
  `ilosc` int(11) NOT NULL,
  PRIMARY KEY (`idRachunku`,`idUslugi`),
  KEY `fk_pozycjeRachunkow_rachunki1_idx` (`idRachunku`),
  KEY `fk_pozycjeRachunkow_uslugi1_idx` (`idUslugi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `pozycjerachunkow`
--

INSERT INTO `pozycjerachunkow` (`idRachunku`, `idUslugi`, `ilosc`) VALUES
(9, 41, 1),
(10, 27, 1),
(10, 48, 2);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `pracownicy`
--

CREATE TABLE IF NOT EXISTS `pracownicy` (
  `idPracownika` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(45) NOT NULL,
  `haslo` varchar(45) NOT NULL,
  `imie` varchar(45) NOT NULL,
  `nazwisko` varchar(45) NOT NULL,
  `telefon` varchar(20) NOT NULL,
  `email` varchar(45) DEFAULT NULL,
  `stanowisko` tinyint(4) NOT NULL,
  PRIMARY KEY (`idPracownika`),
  UNIQUE KEY `idPracownika_UNIQUE` (`idPracownika`),
  KEY `fk_pracownicy_stanowiska1_idx` (`stanowisko`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

--
-- Zrzut danych tabeli `pracownicy`
--

INSERT INTO `pracownicy` (`idPracownika`, `login`, `haslo`, `imie`, `nazwisko`, `telefon`, `email`, `stanowisko`) VALUES
(7, 'szef', '329e1b6e62b9ad24c85bb737add9ee8f', 'Jan', 'Kowalski', '654 987 321', 'szef@hotel.pl', 3),
(8, 'barman', '1e7f0bbc56c5ba6791108be53a75f494', 'Andrzej', 'Barmański', '1234564', 'andrzej@gmail.com', 4),
(9, 'rec', '0b2c082c00e002a2f571cbe340644239', 'Anna', 'Nowak', '5698 717', 'anna@op.pl', 1),
(10, 'man', '39c63ddb96a31b9610cd976b896ad4f0', 'Zbigniew', 'WIerzbicki', '0 578 476 54', 'wierzbicki@hotel.pl', 2),
(11, 'men', 'd2fc17cc2feffa1de5217a3fd29e91e8', 'Janusz', 'Miły', '11 222 33 45', 'janusz@hotel.pl', 2);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `rachunki`
--

CREATE TABLE IF NOT EXISTS `rachunki` (
  `idRachunku` int(11) NOT NULL AUTO_INCREMENT,
  `idKlienta` int(11) NOT NULL,
  `kosztPomieszczenia` decimal(10,2) NOT NULL DEFAULT '0.00',
  `dataWystawienia` date NOT NULL,
  `znizka` tinyint(4) DEFAULT '0',
  `czyZaplacony` tinyint(1) NOT NULL DEFAULT '0',
  `czyFaktura` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`idRachunku`),
  UNIQUE KEY `idRachunku_UNIQUE` (`idRachunku`),
  KEY `fk_rachunki_klienci1_idx` (`idKlienta`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Zrzut danych tabeli `rachunki`
--

INSERT INTO `rachunki` (`idRachunku`, `idKlienta`, `kosztPomieszczenia`, `dataWystawienia`, `znizka`, `czyZaplacony`, `czyFaktura`) VALUES
(9, 21, '2000.00', '2014-09-07', 1, 1, 1),
(10, 20, '0.00', '2014-09-07', 0, 0, 0);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `rezerwacje`
--

CREATE TABLE IF NOT EXISTS `rezerwacje` (
  `idRezerwacji` int(11) NOT NULL AUTO_INCREMENT,
  `stan` tinyint(4) NOT NULL,
  `zaliczka` decimal(10,2) DEFAULT NULL,
  `okresOd` date NOT NULL,
  `okresDo` date NOT NULL,
  `idKlienta` int(11) NOT NULL,
  `numerPomieszczenia` varchar(6) NOT NULL,
  PRIMARY KEY (`idRezerwacji`),
  UNIQUE KEY `idRezerwacji_UNIQUE` (`idRezerwacji`),
  KEY `fk_rezerwacje_klienci_idx` (`idKlienta`),
  KEY `fk_rezerwacje_stanyRezerwacji1_idx` (`stan`),
  KEY `fk_rezerwacje_pomieszczenia1_idx` (`numerPomieszczenia`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=33 ;

--
-- Zrzut danych tabeli `rezerwacje`
--

INSERT INTO `rezerwacje` (`idRezerwacji`, `stan`, `zaliczka`, `okresOd`, `okresDo`, `idKlienta`, `numerPomieszczenia`) VALUES
(26, 2, '100.00', '2014-09-10', '2014-09-12', 20, '101'),
(27, 1, NULL, '2014-09-07', '2014-09-07', 20, '270'),
(28, 1, NULL, '2014-11-11', '2014-11-15', 21, '215'),
(29, 5, '200.00', '2014-12-30', '2015-01-02', 21, '323'),
(30, 1, NULL, '2014-10-10', '2014-10-11', 22, '15'),
(31, 2, '100.00', '2014-12-30', '2015-01-05', 24, '101'),
(32, 1, NULL, '2014-09-08', '2014-09-08', 20, '342');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `salekonferencyjne`
--

CREATE TABLE IF NOT EXISTS `salekonferencyjne` (
  `idSali` int(11) NOT NULL AUTO_INCREMENT,
  `iloscMiejsc` int(11) NOT NULL,
  `cena` decimal(10,2) NOT NULL,
  PRIMARY KEY (`idSali`),
  UNIQUE KEY `idPomieszczenia_UNIQUE` (`idSali`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Zrzut danych tabeli `salekonferencyjne`
--

INSERT INTO `salekonferencyjne` (`idSali`, `iloscMiejsc`, `cena`) VALUES
(3, 20, '700.00'),
(4, 25, '1000.00'),
(5, 30, '1500.00');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `sale_wyposazenie`
--

CREATE TABLE IF NOT EXISTS `sale_wyposazenie` (
  `idSali` int(11) NOT NULL,
  `idWyposazenia` int(11) NOT NULL,
  PRIMARY KEY (`idSali`,`idWyposazenia`),
  KEY `fk_sale_wyposazenie_wyposazenieSali1_idx` (`idWyposazenia`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `sale_wyposazenie`
--

INSERT INTO `sale_wyposazenie` (`idSali`, `idWyposazenia`) VALUES
(4, 1),
(5, 1),
(3, 2),
(4, 2),
(5, 3),
(5, 4);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `sezony`
--

CREATE TABLE IF NOT EXISTS `sezony` (
  `idSezonu` int(11) NOT NULL AUTO_INCREMENT,
  `nazwaSezonu` varchar(45) NOT NULL,
  `odDaty` date NOT NULL,
  `doDaty` date NOT NULL,
  PRIMARY KEY (`idSezonu`),
  UNIQUE KEY `idSezonu_UNIQUE` (`idSezonu`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=19 ;

--
-- Zrzut danych tabeli `sezony`
--

INSERT INTO `sezony` (`idSezonu`, `nazwaSezonu`, `odDaty`, `doDaty`) VALUES
(12, 'jesień 2014', '2014-09-01', '2014-12-01'),
(13, 'zima 2014', '2014-12-02', '2015-03-01'),
(16, 'poza sezonami', '2014-01-01', '2024-01-01'),
(17, 'święta/sylwester 2014', '2014-12-24', '2015-01-02'),
(18, '11 listopada 2014', '2014-11-11', '2014-11-11');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `stanowiska`
--

CREATE TABLE IF NOT EXISTS `stanowiska` (
  `idStanowiska` tinyint(4) NOT NULL AUTO_INCREMENT,
  `nazwa` varchar(45) NOT NULL,
  PRIMARY KEY (`idStanowiska`),
  UNIQUE KEY `idStanowiska_UNIQUE` (`idStanowiska`),
  UNIQUE KEY `nazwa_UNIQUE` (`nazwa`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Zrzut danych tabeli `stanowiska`
--

INSERT INTO `stanowiska` (`idStanowiska`, `nazwa`) VALUES
(4, 'barman'),
(3, 'dyrektor'),
(2, 'manager'),
(1, 'recepcjonista');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `stanyrezerwacji`
--

CREATE TABLE IF NOT EXISTS `stanyrezerwacji` (
  `stan` tinyint(4) NOT NULL AUTO_INCREMENT,
  `nazwa` varchar(45) NOT NULL,
  PRIMARY KEY (`stan`),
  UNIQUE KEY `stan_UNIQUE` (`stan`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Zrzut danych tabeli `stanyrezerwacji`
--

INSERT INTO `stanyrezerwacji` (`stan`, `nazwa`) VALUES
(1, 'czeka na zaliczkę'),
(2, 'wpłacona zaliczka'),
(3, 'anulowana'),
(4, 'trwa'),
(5, 'historyczna');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `typywyposazenia`
--

CREATE TABLE IF NOT EXISTS `typywyposazenia` (
  `typ` tinyint(4) NOT NULL AUTO_INCREMENT,
  `nazwa` varchar(45) NOT NULL,
  PRIMARY KEY (`typ`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Zrzut danych tabeli `typywyposazenia`
--

INSERT INTO `typywyposazenia` (`typ`, `nazwa`) VALUES
(1, 'podstawowe'),
(2, 'dodatkowe'),
(3, 'ekskluzywne');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `uslugi`
--

CREATE TABLE IF NOT EXISTS `uslugi` (
  `idUslugi` int(11) NOT NULL AUTO_INCREMENT,
  `kategoria` smallint(6) NOT NULL,
  `nazwa` varchar(45) NOT NULL,
  `cena` decimal(10,2) NOT NULL,
  PRIMARY KEY (`idUslugi`),
  UNIQUE KEY `idUslugi_UNIQUE` (`idUslugi`),
  KEY `fk_uslugi_kategorieUslug1_idx` (`kategoria`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=52 ;

--
-- Zrzut danych tabeli `uslugi`
--

INSERT INTO `uslugi` (`idUslugi`, `kategoria`, `nazwa`, `cena`) VALUES
(16, 1, 'piwo 0,5l', '10.00'),
(18, 3, 'sok jabłkowy 0,33l', '5.00'),
(19, 4, 'frytki małe', '7.00'),
(20, 4, 'zapiekanka', '6.00'),
(21, 4, 'frytki duże', '10.00'),
(22, 4, 'lody', '12.00'),
(23, 4, 'orzeszki', '3.70'),
(24, 5, 'bigos', '18.00'),
(25, 5, 'żurek', '7.00'),
(26, 5, 'rosół', '7.50'),
(27, 5, 'schabowy, ziemniaki, surówka', '18.00'),
(28, 6, 'Malboro', '14.70'),
(29, 6, 'LM', '13.00'),
(30, 6, 'Pall Mall', '12.85'),
(41, 1, 'piwo 1l', '16.00'),
(42, 2, 'wódka 50ml', '4.00'),
(43, 2, 'whisky', '10.00'),
(45, 2, 'wino białe (kieliszek)', '15.00'),
(46, 2, 'wino czerwone (kieliszek)', '15.00'),
(47, 3, 'cola 0,33l', '5.00'),
(48, 3, 'pepsi 0,33l', '5.00'),
(49, 7, 'gumy orbit', '3.00'),
(50, 7, 'zapalniczka', '4.20'),
(51, 7, 'zapałki', '0.20');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `wyposazeniepokoju`
--

CREATE TABLE IF NOT EXISTS `wyposazeniepokoju` (
  `idWyposazenia` int(11) NOT NULL AUTO_INCREMENT,
  `typ` tinyint(4) NOT NULL,
  `nazwa` varchar(45) NOT NULL,
  PRIMARY KEY (`idWyposazenia`),
  UNIQUE KEY `idWyposazenia_UNIQUE` (`idWyposazenia`),
  KEY `fk_wyposazeniePokoju_typyWyposazenia1_idx` (`typ`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

--
-- Zrzut danych tabeli `wyposazeniepokoju`
--

INSERT INTO `wyposazeniepokoju` (`idWyposazenia`, `typ`, `nazwa`) VALUES
(1, 1, 'ręczniki'),
(2, 1, 'czajnik elektryczny'),
(3, 1, 'suszarka'),
(4, 1, 'lodówka'),
(5, 1, 'naczynia i sztućce'),
(6, 2, 'telewizor'),
(7, 2, 'wifi'),
(8, 2, 'leżaki'),
(9, 3, 'jacuzzi'),
(10, 3, 'klimatyzacja'),
(11, 3, 'barek');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `wyposazeniesali`
--

CREATE TABLE IF NOT EXISTS `wyposazeniesali` (
  `idWyposazenia` int(11) NOT NULL AUTO_INCREMENT,
  `nazwa` varchar(45) NOT NULL,
  PRIMARY KEY (`idWyposazenia`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Zrzut danych tabeli `wyposazeniesali`
--

INSERT INTO `wyposazeniesali` (`idWyposazenia`, `nazwa`) VALUES
(1, 'projektor'),
(2, 'tablica z pisakami'),
(3, 'nagłośnienie'),
(4, 'czajnik elektryczny');

--
-- Ograniczenia dla zrzutów tabel
--

--
-- Ograniczenia dla tabeli `klienci`
--
ALTER TABLE `klienci`
  ADD CONSTRAINT `fk_klienci_klienci1` FOREIGN KEY (`idFirmy`) REFERENCES `klienci` (`idKlienta`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ograniczenia dla tabeli `pokoje`
--
ALTER TABLE `pokoje`
  ADD CONSTRAINT `fk_pokoje_kategorie1` FOREIGN KEY (`kategoria`) REFERENCES `kategorie` (`idKategorii`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ograniczenia dla tabeli `pokoje_sezony`
--
ALTER TABLE `pokoje_sezony`
  ADD CONSTRAINT `fk_pokoje_sezony_pokoje1` FOREIGN KEY (`idPokoju`) REFERENCES `pokoje` (`idPokoju`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_pokoje_sezony_sezony1` FOREIGN KEY (`idSezonu`) REFERENCES `sezony` (`idSezonu`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ograniczenia dla tabeli `pokoje_wyposazenie`
--
ALTER TABLE `pokoje_wyposazenie`
  ADD CONSTRAINT `fk_pokoje_wyposazenie_pokoje1` FOREIGN KEY (`idPokoju`) REFERENCES `pokoje` (`idPokoju`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_pokoje_wyposazenie_wyposazeniePokoju1` FOREIGN KEY (`idWyposazenia`) REFERENCES `wyposazeniepokoju` (`idWyposazenia`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ograniczenia dla tabeli `pomieszczenia`
--
ALTER TABLE `pomieszczenia`
  ADD CONSTRAINT `fk_pomieszczenia_pokoje1` FOREIGN KEY (`idPokoju`) REFERENCES `pokoje` (`idPokoju`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_pomieszczenia_saleKonferencyjne1` FOREIGN KEY (`idSali`) REFERENCES `salekonferencyjne` (`idSali`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ograniczenia dla tabeli `pozycjerachunkow`
--
ALTER TABLE `pozycjerachunkow`
  ADD CONSTRAINT `fk_pozycjeRachunkow_rachunki1` FOREIGN KEY (`idRachunku`) REFERENCES `rachunki` (`idRachunku`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_pozycjeRachunkow_uslugi1` FOREIGN KEY (`idUslugi`) REFERENCES `uslugi` (`idUslugi`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ograniczenia dla tabeli `pracownicy`
--
ALTER TABLE `pracownicy`
  ADD CONSTRAINT `fk_pracownicy_stanowiska1` FOREIGN KEY (`stanowisko`) REFERENCES `stanowiska` (`idStanowiska`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ograniczenia dla tabeli `rachunki`
--
ALTER TABLE `rachunki`
  ADD CONSTRAINT `fk_rachunki_klienci1` FOREIGN KEY (`idKlienta`) REFERENCES `klienci` (`idKlienta`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ograniczenia dla tabeli `rezerwacje`
--
ALTER TABLE `rezerwacje`
  ADD CONSTRAINT `fk_rezerwacje_klienci` FOREIGN KEY (`idKlienta`) REFERENCES `klienci` (`idKlienta`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_rezerwacje_pomieszczenia1` FOREIGN KEY (`numerPomieszczenia`) REFERENCES `pomieszczenia` (`numerPomieszczenia`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_rezerwacje_stanyRezerwacji1` FOREIGN KEY (`stan`) REFERENCES `stanyrezerwacji` (`stan`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ograniczenia dla tabeli `sale_wyposazenie`
--
ALTER TABLE `sale_wyposazenie`
  ADD CONSTRAINT `fk_sale_wyposazenie_saleKonferencyjne1` FOREIGN KEY (`idSali`) REFERENCES `salekonferencyjne` (`idSali`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_sale_wyposazenie_wyposazenieSali1` FOREIGN KEY (`idWyposazenia`) REFERENCES `wyposazeniesali` (`idWyposazenia`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ograniczenia dla tabeli `uslugi`
--
ALTER TABLE `uslugi`
  ADD CONSTRAINT `fk_uslugi_kategorieUslug1` FOREIGN KEY (`kategoria`) REFERENCES `kategorieuslug` (`kategoria`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ograniczenia dla tabeli `wyposazeniepokoju`
--
ALTER TABLE `wyposazeniepokoju`
  ADD CONSTRAINT `fk_wyposazeniePokoju_typyWyposazenia1` FOREIGN KEY (`typ`) REFERENCES `typywyposazenia` (`typ`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
