# BlockAB — Handleiding voor Marketing

**Voor wie:** Marketingmedewerkers die A/B tests willen opzetten, bewaken en afsluiten.
**Aanname:** BlockAB is geïnstalleerd en de pagina's zijn klaar voor gebruik.

---

## Wat is een A/B test?

Je toont bezoekers verschillende versies van hetzelfde blok (bijv. een andere headline, knoptekst of afbeelding). BlockAB houdt bij welke versie de meeste conversies oplevert en berekent automatisch of het verschil statistisch betrouwbaar is.

---

## 1. Navigeren naar BlockAB

Ga in het MODX beheer naar **Components → BlockAB**.

Je ziet twee tabbladen:
- **Tests** — alle actieve en lopende tests
- **Gearchiveerd** — afgesloten tests

---

## 2. Een nieuwe test aanmaken

Klik op **Test aanmaken** (of rechtsklik → Aanmaken in het grid).

Vul in:

| Veld | Wat vul je in? |
|---|---|
| **Naam** | Duidelijke beschrijving, bijv. `Homepage Hero — Headline test` |
| **Test Group** | Een unieke code zonder spaties, bijv. `homepage_hero_headline`. **Geef deze code door aan de developer** — die koppelt de MIGX blokken hieraan. |
| **Beschrijving** | Optioneel: je hypothese of doel |
| **Actief** | Laat uit totdat de developer de blokken heeft gekoppeld |

Klik **Opslaan**.

---

## 3. Varianten toevoegen

Na het opslaan verschijnt onderaan het scherm een **Varianten** overzicht.

Klik op **Variant aanmaken** en vul per variant in:

| Veld | Wat vul je in? |
|---|---|
| **Naam** | Beschrijvende naam, bijv. `Variant A — Origineel` of `Variant B — Prijs in headline` |
| **Variant Key** | De letter die je aan de developer doorgeeft: `A`, `B`, `C`, … |
| **Gewicht** | Hoe vaak deze variant getoond wordt t.o.v. andere varianten. Laat op `100` staan voor een gelijke verdeling. |

Maak minimaal **2 varianten** aan. Meer is mogelijk, maar met weinig websiteverkeer duurt het langer om betrouwbare resultaten te krijgen.

> **Geef na dit stap de Test Group key en de Variant Keys door aan de developer.**
> De developer koppelt de juiste MIGX blokken aan elke variant.

---

## 4. De test activeren

Zodra de developer de blokken heeft gekoppeld:

1. Rechtsklik op de test in het grid → **Bewerken**
2. Zet **Actief** op aan
3. Sla op

De test loopt nu. Bezoekers krijgen automatisch willekeurig een variant te zien.

---

## 5. Statistieken bekijken

Klik op een test om de statistieken te openen.

### Bovenaan: drie overzichtsgetallen

| Getal | Betekenis |
|---|---|
| **Total Views** | Hoeveel keer een variant is getoond aan unieke bezoekers |
| **Total Conversions** | Hoeveel bezoekers daarna een conversie hebben uitgevoerd |
| **Best Rate** | Het hoogste conversiepercentage van alle varianten |

### De significantiebanner

Hieronder zie je direct of de test al conclusies toelaat:

| Kleur | Betekenis |
|---|---|
| 🟢 **Groen** | Statistisch significant resultaat — er is een betrouwbare winnaar |
| 🟡 **Geel** | Test loopt — nog niet genoeg data voor een conclusie |
| ⬜ **Grijs** | Nog geen data |

Bij een **groene banner** staat ook de betrouwbaarheid vermeld: **95% confidence** (goed) of **99% confidence** (uitstekend). Dit betekent dat de kans dat het verschil op toeval berust kleiner is dan 5% resp. 1%.

### De variantentabel

Per variant zie je:

- **Views** — aantal unieke vertoningen
- **Conversies** — aantal conversies
- **Rate %** — conversiepercentage
- **Progressbar** — visuele vergelijking t.o.v. de best presterende variant
- **Status** — 🏆 winnaar of ⚠ te weinig data (< 30 views)

De winnende variant heeft een **groene achtergrond**.

### Datumfilter

Gebruik de knoppen **7 / 30 / 60 / 90 dagen** om te zien hoe de test in een specifieke periode heeft gepresteerd.

---

## 6. De test stoppen

Als de groene banner verschijnt en je bent overtuigd van de winnaar:

1. Klik op **Stop test** in de groene banner
2. Bevestig de actie

De test wordt op inactief gezet. Alle varianten blijven nog zichtbaar op de website totdat de developer de winnaar als standaard instelt.

> **Laat de developer weten welke variant heeft gewonnen**, zodat die de MIGX blokken kan opschonen.

---

## 7. Een test archiveren

Gesloten tests kun je archiveren zodat het Tests overzicht overzichtelijk blijft:

1. Rechtsklik op de test → **Archiveren**
2. Bevestig

Gearchiveerde tests zijn terug te vinden in het tabblad **Gearchiveerd** en zijn daar ook te herstellen als dat nodig is.

> ⚠ **Let op:** Als je kiest voor **Verwijderen** (permanent) zijn alle statistieken voorgoed weg. Gebruik daarom bij voorkeur **Archiveren**.

---

## 8. Wanneer is een resultaat betrouwbaar?

BlockAB doet dit automatisch via een statistische test (chi-kwadraat). Maar hier zijn praktische richtlijnen:

| Situatie | Wat doen? |
|---|---|
| Groene banner, 95% confidence | Je mag concluderen. Stop de test als je klaar bent. |
| Groene banner, 99% confidence | Uitstekende betrouwbaarheid. Winnaar is duidelijk. |
| Gele banner, minder dan 100 weergaven | Wacht. Te weinig data voor conclusies. |
| Gele banner, meer dan 100 weergaven | Test loopt goed. Wacht op meer conversies. |
| Al weken actief, geen winnaar | Overweeg radicalere varianten of test een ander element. |

**Vuistregel:** Laat een test minimaal **2 weken** lopen, ook als er al snel een winnaar lijkt.

---

## 9. Tips voor goede tests

**Test één ding tegelijk**
Verander alleen de headline, óf de knoptekst, óf de afbeelding. Niet meerdere dingen tegelijk — dan weet je niet wat het verschil maakte.

**Formuleer eerst een hypothese**
_"Ik verwacht dat het noemen van een prijs in de headline meer offerteaanvragen oplevert, omdat bezoekers dan direct weten wat ze kunnen verwachten."_

**Kies impactvolle elementen**
Hero blokken, call-to-action knoppen en headlines hebben de meeste impact op conversie.

**Heb geduld**
Een test met weinig verkeer heeft weken nodig. Forceer geen conclusies op basis van te weinig data — de banner vertelt je wanneer het tijd is.

**Documenteer de uitkomsten**
Noteer wat je getest hebt en wat je hebt geleerd. Dat helpt bij toekomstige tests.

---

## Snel overzicht: de workflow

```
1. Test aanmaken in BlockAB (naam + test group key)
          ↓
2. Varianten aanmaken (A, B, C met namen en keys)
          ↓
3. Test group key + variant keys doorgeven aan developer
          ↓
4. Developer koppelt MIGX blokken → jij zet test op Actief
          ↓
5. Test bewaken via statistieken (dagelijks of wekelijks)
          ↓
6. Groene banner → winnaar bepalen → test stoppen
          ↓
7. Winnaar doorgeven aan developer → test archiveren
```

---

*Vragen over de technische kant? Neem contact op met de developer.*
