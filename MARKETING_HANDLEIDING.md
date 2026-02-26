# BlockAB — Handleiding voor Marketing

**Voor wie:** Marketingmedewerkers die A/B tests willen opzetten, bewaken en afsluiten.
**Aanname:** BlockAB is geïnstalleerd en de pagina's zijn klaar voor gebruik.

---

## Wat is een A/B test?

Je toont bezoekers verschillende versies van hetzelfde blok (bijv. een andere headline, knoptekst of afbeelding). BlockAB houdt bij welke versie de meeste conversies oplevert en berekent automatisch of het verschil statistisch betrouwbaar is.

---

## 1. Navigeren naar BlockAB

Ga in de manager naar **Components → BlockAB**.

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
| **Test Groep** | Een unieke code zonder spaties, bijv. `signup_cta`. Deze code gebruik je straks om de MIGX blokken aan de test te koppelen. |
| **Beschrijving** | Optioneel: je hypothese of doel |
| **Actief** | Laat **uit** totdat je de blokken hebt gekoppeld op de pagina |
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
| **Variant Key** | De letter voor deze variant: `A`, `B`, `C`, … De eerste beschikbare letter wordt automatisch voorgesteld. |
| **Naam** | Beschrijvende naam van de variant, bijv. `Aanmelden`, `Start gratis` of `Probeer nu` |
| **Beschrijving** | Optioneel: noteer hier kort wat je in deze variant test, bijv. `Nadruk op gratis instapdrempel` |
| **Gewicht** | Hoe vaak deze variant getoond wordt t.o.v. andere varianten. Laat op `100` staan voor een gelijke verdeling. |
| **Actief** | Laat **aan** staan om de variant mee te nemen in de test |

Maak minimaal **2 varianten** aan. Meer is mogelijk, maar met weinig websiteverkeer duurt het langer om betrouwbare resultaten te krijgen.

---

## 4. Blokken koppelen op de pagina

Nu ga je voor elke variant een MIGX blok aanmaken (of een bestaand blok aanpassen) op de pagina die je wilt testen.

1. Ga naar de pagina in de manager
2. Klik op het MIGX veld (bijv. "Pagina samenstellen")
3. Klik op **Voeg item toe** en kies het bloktype, of open een bestaand blok via **Bewerk**
4. Ga naar het tabblad **A/B settings**
5. Selecteer je test bij **A/B Test Group** — de variantendropdown vult zich automatisch
6. Selecteer de juiste **Variant** (A, B, C) — de eerste vrije variant wordt automatisch geselecteerd
7. Vul de overige blokinstellingen in (tekst, afbeelding, etc.) zodat ze overeenkomen met de variant
8. Sla het blok op
9. Herhaal dit voor elke variant

> **Tip:** Maak één blok per variant. Zorg dat alle blokken hetzelfde bloktype gebruiken en dat alleen het element dat je test verschilt.

---

## 5. De test activeren

Zodra alle blokken zijn gekoppeld:

1. Rechtsklik op de test in het grid → **Bewerken**
2. Zet **Actief** op aan
3. Sla op

De test loopt nu. Bezoekers krijgen automatisch willekeurig een variant te zien.

---

## 6. Statistieken bekijken

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

## 7. De test stoppen

Als de groene banner verschijnt en je bent overtuigd van de winnaar:

![Groene banner met Test Stoppen knop](docs/screenshots/stap6-winnaar-banner.png)

1. Klik op **Test Stoppen** in de groene banner
2. Bevestig de actie

De test wordt op inactief gezet. Alle varianten blijven nog zichtbaar op de website.

**Ruim daarna de verliezende blokken op:**
1. Ga naar de pagina in de manager
2. Open het MIGX veld
3. Verwijder alle blokken behalve het winnende blok
4. Verwijder bij het winnende blok de A/B-instellingen (leeg het A/B Test Group veld) zodat het blok altijd wordt getoond
5. Sla de pagina op

---

## 8. Een test archiveren

Gesloten tests kun je archiveren zodat het Tests overzicht overzichtelijk blijft:

1. Rechtsklik op de test → **Archiveren**
2. Bevestig

Gearchiveerde tests zijn terug te vinden in het tabblad **Gearchiveerd** en zijn daar ook te herstellen als dat nodig is.

> ⚠ **Let op:** Als je kiest voor **Verwijderen** (permanent) zijn alle statistieken voorgoed weg. Gebruik daarom bij voorkeur **Archiveren**.

---

## 9. Wanneer is een resultaat betrouwbaar?

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

## 10. Tips voor goede tests

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
3. Blokken koppelen op de pagina (MIGX → A/B settings tab)
          ↓
4. Test zetten op Actief in BlockAB
          ↓
5. Test bewaken via statistieken (dagelijks of wekelijks)
          ↓
6. Groene banner → winnaar bepalen → test stoppen
          ↓
7. Verliezende blokken verwijderen → test archiveren
```

---

*Technische vragen over installatie of sjablonen? Neem contact op met je developer.*
