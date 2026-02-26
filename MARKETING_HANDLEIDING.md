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

![Test aanmaken formulier](docs/screenshots/stap2-test-aanmaken.png)

Vul in:

| Veld | Wat vul je in? |
|---|---|
| **Naam** | Duidelijke beschrijving, bijv. `Aanmeldformulier CTA` |
| **Test Groep** | Een unieke code zonder spaties, bijv. `signup_cta`. **Geef deze code door aan de developer** — die koppelt de MIGX blokken hieraan. |
| **Beschrijving** | Optioneel: je hypothese of doel |
| **Actief** | Laat **uit** totdat de developer de blokken heeft gekoppeld |
| **Slim Optimaliseren** | Laat **aan** staan — BlockAB stuurt automatisch meer verkeer naar de best presterende variant |
| **Drempelwaarde** | Aantal conversies voordat slim optimaliseren start (standaard: 100). Niet aanpassen tenzij je daar een reden voor hebt. |
| **Randomize %** | Percentage bezoekers dat na de drempelwaarde nog steeds willekeurig een variant ziet (standaard: 25). Niet aanpassen. |

Klik **Opslaan**.

---

## 3. Varianten toevoegen

Na het opslaan verschijnt onderaan het scherm een **Varianten** overzicht.

Klik op **Variant aanmaken** en vul per variant in:

![Variant aanmaken formulier](docs/screenshots/stap3-variant-aanmaken.png)

| Veld | Wat vul je in? |
|---|---|
| **Variant Key** | De letter die je aan de developer doorgeeft: `A`, `B`, `C`, … De eerste beschikbare letter wordt automatisch voorgesteld. |
| **Naam** | Beschrijvende naam van de variant, bijv. `Aanmelden`, `Start gratis` of `Probeer nu` |
| **Beschrijving** | Optioneel: noteer hier kort wat je in deze variant test, bijv. `Nadruk op gratis instapdrempel` |
| **Gewicht** | Hoe vaak deze variant getoond wordt t.o.v. andere varianten. Laat op `100` staan voor een gelijke verdeling. |
| **Actief** | Laat **aan** staan om de variant mee te nemen in de test |

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

Klik op een test om de statistieken te openen en ga naar het tabblad **Statistieken**.

![Statistieken pagina](docs/screenshots/stap5-statistieken.png)

### Periode

Bovenaan kun je kiezen over welke periode je de statistieken wilt zien: **7 dagen**, **30 dagen**, **60 dagen** of **90 dagen**. De actieve periode is blauw gemarkeerd.

### De drie overzichtsgetallen

| Getal | Betekenis |
|---|---|
| **Totaal Weergaves** | Hoeveel keer een variant is getoond aan unieke bezoekers |
| **Conversies** | Hoeveel van die bezoekers daarna een conversie hebben uitgevoerd |
| **Beste Rate** | Het hoogste conversiepercentage van alle varianten |

### De statusbanner

Direct onder de kaartjes zie je een banner die aangeeft hoe de test ervoor staat:

| Banner | Betekenis |
|---|---|
| 🟢 **Groen** — *✓ Winnaar: X — Statistisch significant (95% / 99% confidence)* | Er is een betrouwbare winnaar. De knop **Test Stoppen** verschijnt hier. |
| 🟡 **Geel** — *Test loopt — nog niet conclusief* | De test is actief maar heeft nog niet genoeg data. Wacht. |
| ⬜ **Grijs** — *Test gestopt — Winnaar: X* | De test is gestopt. De winnaar staat vermeld. |
| ⬜ **Grijs** — *Geen data* | De test is nog niet gestart of heeft nog geen weergaves. |

Bij de **groene banner** staat ook de betrouwbaarheid: **95% confidence** of **99% confidence**. Hoe hoger, hoe zekerder het resultaat.

### De variantentabel

Per variant zie je:

| Kolom | Betekenis |
|---|---|
| **Variant** | De letter (A, B, C) en een 🏆 bij de winnaar |
| **Naam** | De naam die je bij het aanmaken hebt ingevuld |
| **Weergaves** | Aantal unieke vertoningen |
| **Conversies** | Aantal conversies |
| **Rate %** | Conversiepercentage — hoe hoger, hoe beter |
| **Progressbar** | Visuele vergelijking t.o.v. de best presterende variant |
| **Status** | *Winnaar* bij de winnende variant |

De **winnende rij heeft een groene achtergrond** en een groene progressbar. Varianten met minder dan 30 weergaves krijgen een waarschuwingspictogram — die data is nog niet betrouwbaar.

### Datumfilter

Gebruik de knoppen **7 / 30 / 60 / 90 dagen** om te zien hoe de test in een specifieke periode heeft gepresteerd.

---

## 6. De test stoppen

Als de groene banner verschijnt en je bent overtuigd van de winnaar:

![Groene banner met Test Stoppen knop](docs/screenshots/stap6-winnaar-banner.png)

1. Klik op **Test Stoppen** in de groene banner
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
