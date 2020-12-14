# WordPress options Item

Biblioteka umożliwiająca zapisywanie ustrukturyzowanych danych w oparciu o mechanizm zapisywania opcji WordPressa.

- Zapisuje dane w postaci tablicy wykorzystując WordPressowy mechanizm `network_options` tworząc `ItemOpcję`.
- Każda ItemOpcja stanowi swoistą tabelę z rekordami, imitując tabelę bazodanową.
- Każdy wpis ItemOpcji posiada rekordy:
  - klucz numeryczny `id`,
  - datę dodania `date_added`,
  - datę aktualizacji `date_updated`,
  - id użytkownika, który dodał wpis `user_added`,
  - id użytkownika, który zmodyfikował wpis `user_updated`.
- Istnieje możliwość kaskadowego usuwania wpisów z różnych ItemOpcji. W takim przypadku należy określić referencję do odpowiedniego rekordu z innej ItemOpcji.
- Można dowolnie określić czy rekord ItemOpcji zapisuje wartość `null`.

## Wymagania

  * PHP > 5.6.0
  * Wordpress > 5.3.6

## Instalacja

Instalacja za pomocą Composera:
```php
composer require sinfonie/wordpress_options_item
```

Instalacja za pomocą git poprzez https:
```php
git clone https://github.com/sinfonie/wordpress_options_item.git
```
Pobierz w formie .zip:
https://github.com/sinfonie/wordpress_options_item/archive/master.zip

## Konfiguracja

Proszę pamiętać o określeniu odpowiedniej ścieżki dla biblioteki w twoim projekcie.

Szczegóły implementacji znajdziesz pod poniższym linkiem: