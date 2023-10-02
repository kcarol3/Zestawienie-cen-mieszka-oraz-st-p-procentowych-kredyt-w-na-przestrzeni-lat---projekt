Hello!

Jest to projekt zaliczeniowy na studiach.
Projekt składa się z częsci serwera napisanego w symfony oraz z frontu napisanego we vue.js.
Celem projektu było utworzenie z dostępnych w internecie danych zestawienia stóp procentowych oraz cen mieszkań w miastach na przestrzeni lat.

Projekt zawiera:
- wczytanie danych z mieszkań z pliku .json,
- wczytanie danych stóp z pliku .xml,
- autoryzacja tokenami jwt,
- wykorzystanie biblioteki ORM (Doctrine),
- usługi typu REST,
- usługi typu SOAP,  
- export zestawieani do pliku .json lub .xml,
- prezentacja zestawienia za pomocą wykresu oraz tabeli.

Widoki aplikacji po zalogowaniu:

Tabela:
![Alt text](/../<Zestawienie-cen-mieszka-oraz-st-p-procentowych-kredyt-w-na-przestrzeni-lat---projekt>/path/to/screen1.png?raw=true "Optional Title")

Środowisko testowe:
Windows 10
XAMPP Version: 8.2.0

!Do działania projektu wymagany jest xampp!

Uruchomienie projektu:
1. Zaimportuj bazę danych is_db z poziomu phpMyAdmin.
2. Otwórz z poziomu terminala katolog IS_project.
3. Wykonaj polecenie symfony server:start.
4. W nowym oknie terminala otwórz katalog is_project_front.
5. Wykonaj polecenie npm install.
6. Wykonaj polecenie npm run serve.
6. Strona powinna już dać się otworzyć w przeglądarce na porcie 8080 lub innym na którym pracuje serwer vue (widoczne po uruchomieniu serwera frontu).
7. Dane logowania dla admina to username: "karol2" a hasło to "pass".
8. W razie potrzeby są dołączone pliki do importu rates do importu stóp procentowych oraz Houses do importu mieszkań.