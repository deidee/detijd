# ![detijd](https://deidee.com/logo.png?str=deTijd)

Toon de tijd in de stijl van [deidee](https://github.com/deidee/).

## Te doen

- [x] Geanimeerde gif genereren.
- [x] Huidige (server)tijd tonen.
- [ ] Aftellen naar een bepaalde datum.
- [ ] Optellen vanaf een bepaalde datum.
- [ ] Plekje geven op hetCDN.
- [ ] Alleen cijfers die veranderen opnieuw tekenen.
- [ ] Dubbele punten laten knipperen.
- [ ] Er rekening mee houden dat de minuut en het uur ook kunnen veranderen binnen de 60 frames.
- [ ] Code opschonen.
- [ ] Performance verbeteren.
- [ ] Kunnen exporteren naar JPG.
- [ ] Kunnen exporteren naar PNG.
- [ ] Kunnen exporteren naar SVG.

## Gebruik

```php
require_once 'class.detijd.php';
$tijd = new Detijd;
echo $tijd;
```