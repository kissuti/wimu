# 🌐 Wimu Webshop
[![PHP](https://img.shields.io/badge/PHP-%5E8.0-blue)](https://www.php.net/)

Üdvözöllek a **Wimu Webshop** oldalán, ahol mindent megtalálhatsz ami csak szükséged van rá. Mindig bővül a kínálatunk.

---

## Mostani Szerkesztők
|  👤**Név**        | 🔗 **GitHub Profil** | 
|-------------------|-----------------------|
| 👤 Csaba | [GitHub](https://github.com/kissuti) |
| 👤 Rajmund | [GitHub](https://github.com/0Rajjjjjmi0) |

---

## **Technológiák amiket használunk**

### Backend  
- **PHP 8.0+**: Erőteljes és gyors szerveroldali nyelv.  
- **MySQL**: Adataid biztonságos tárolása egy strukturált adatbázisban.  

### Frontend  
- **HTML5**: A modern és reszponzív weboldal alapja.  
- **BOOTSTRAP (CSS, SASS)**: Stílusos és könnyen karbantartható megjelenés.  
- **JavaScript**: Interaktív elemek és dinamikus funkciók.

## Admin Panel

- **Termék felvétel**: Lehetőséged van feltölteni közvetlenül egy admin felületről egy terméket.
- **Termék módosítása**: Bármelyik terméket tudod módosítani.
- **Termék törlése:**: Egy gomb nyomás és törölve, ha már a készletről kifogyott.
- **Rendelési lista**: Kik rendeltek, mit és mennyiért.
- **Kategóriák felvétele**: Kategóriát fel tudsz venni, ahhoz egy alkategóriát és még ahhoz is. Törölni is lehet közvetlenül innen.

  ![CSS3](https://img.shields.io/badge/css3-%231572B6.svg?style=for-the-badge&logo=css3&logoColor=white) ![HTML5](https://img.shields.io/badge/html5-%23E34F26.svg?style=for-the-badge&logo=html5&logoColor=white) ![MicrosoftSQLServer](https://img.shields.io/badge/Microsoft%20SQL%20Server-CC2927?style=for-the-badge&logo=microsoft%20sql%20server&logoColor=white)

---

## **Vizsgaremek mappái**

```plaintext
wimu/
├── admin/
│   ├── kategoriak.php       # Kategóriák felvétel, módosítás, törlés
│   ├── rendeles_lista.php   # Rendelt termékek
│   ├── termek_fevetel.php   # Termék felvétel
│   ├── termek_modositas.php # Termék módosítás
│   ├── termek_torles.php    # Termék törlés
├── img/                     # Képek tárolása
├── svgicons/                # SVG ikonok tárolása
├── 1_kosar_tartalma.php     # (fejlesztés)
├── 2_vasarloi_adatok.php    # (fejlesztés)
├── 3_fizetesi_modok.php     # (fejlesztés)
├── 4_rendeles_osszegzes.php # (fejlesztés)
├── 5_rendeles_elkuld.php    # (fejlesztés)
├── alja.php                 # -
├── alja_2.php               # -
├── belepes.php              # Belépés az oldalra
├── dbconn.php               # Adatbázis kapcsolódás
├── fuggvenyek.php           # Összeg a termékre
├── index.php                # Termékek listája, keresés
├── info.php                 # ÁSZF és ÁSZ
├── kilepes.php              # Kilépés
├── korabbi_rendelesek.php   # Rendelések
├── kosar.php                # Kosár tartalma
├── kosarba_tesz.php         # Termék helyezése a kosárba
├── leiras.php               # Termék leírása
├── profil_modosit.php       # Profil módosítás
├── profil_modosit2.php      # Profil módosítás (fejlesztés)
├── reg.php                  # Regisztráció
├── reg_aktival.php          # (fejlesztés)
├── reg_ellenoriz.php        # Ellenőrzni a regisztrációt
├── teteje.php               # -||-
├── teteje_2.php             # Bejelentkező és regisztráció
├── wimu.sql                 # Teljes SQL fájl
```

## **Weboldal használata**

### Feltétel

- **XAMPP**: Letöltés és *Apache*, *MySQL* elínditás. [Letöltés](https://www.apachefriends.org/hu/index.html).
- **Mappa másolása**: Másold be a `wimu` mappát a `htdocs` könyvtárba (pl. `C:/xampp/htdocs/wimu/`).
- **Adatbázis**: Nyissad meg a(z) `http://localhost/phpmyadmin` oldalt.
   - **Adatbázis létrehozás**: Hozzál létre egy (`wimu`) nevezetű adatbázist.
   - **SQL**: Az *SQL* fájlt megtalálod ennél a repo-nál.
- **Weboldal címe**: Írd be ezt a címet a böngészőbe: `http://localhost/wimu/`
