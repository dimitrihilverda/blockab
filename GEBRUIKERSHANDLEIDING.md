# BlockAB Gebruikershandleiding

Een complete gids voor het opzetten en gebruiken van A/B tests voor MIGX blokken.

---

## Wat is A/B Testing?

A/B testing (ook wel split testing genoemd) is een methode om twee of meer versies van een webpagina-element te vergelijken om te zien welke beter presteert.

**Voorbeeld:** Je wilt weten of een groene of blauwe knop op je homepage meer bezoekers aanzet tot het invullen van een formulier. Met A/B testing laat je aan 50% van de bezoekers de groene knop zien (variant A) en aan 50% de blauwe knop (variant B). Na een tijd zie je welke knop meer conversies oplevert.

---

## Hoe werkt BlockAB?

BlockAB is speciaal gemaakt voor het testen van **individuele MIGX blokken** op je pagina's. In plaats van hele pagina's te dupliceren, kun je verschillende versies van één blok testen.

### Wat wordt bijgehouden?

BlockAB houdt twee belangrijke statistieken bij:

#### 1. **Weergaven (Picks)**
Elke keer dat een bezoeker een variant van je blok te zien krijgt, telt dit als 1 weergave.

**Belangrijk:**
- Als dezelfde bezoeker de pagina opnieuw bezoekt (binnen dezelfde browsersessie), krijgt hij **dezelfde variant** te zien en wordt dit **niet opnieuw** geteld
- Als de bezoeker zijn browser sluit en later terugkomt (nieuwe sessie), telt dit wel als een nieuwe weergave
- Dit zorgt ervoor dat je betrouwbare data krijgt zonder dubbele tellingen

#### 2. **Conversies**
Een conversie is een gewenste actie die een bezoeker uitvoert na het zien van een variant. Bijvoorbeeld:
- Het invullen van een contactformulier
- Het downloaden van een brochure
- Het aanvragen van een offerte

Je bepaalt zelf wat als conversie telt door de BlockAB conversie tracking op de juiste pagina te plaatsen (meestal de "bedankt" pagina).

### Hoe werkt de variant selectie?

BlockAB werkt slim:

1. **Start fase (Random)**: In het begin krijgen bezoekers willekeurig een variant te zien, met respect voor het gewicht dat je per variant hebt ingesteld
2. **Optimalisatie fase**: Zodra je genoeg conversies hebt verzameld (standaard 100), gaat BlockAB automatisch de best presterende variant vaker tonen
3. **Continue exploratie**: Ook na het bereiken van de drempel krijgt een percentage van de bezoekers (standaard 25%) nog steeds een willekeurige variant te zien, zodat BlockAB blijft leren

Dit noemen we **Smart Optimization**.

---

## Stappenplan: Je eerste A/B test opzetten

### Stap 1: Maak een nieuwe test aan

1. Ga in het MODX menu naar **Components > BlockAB**
2. Klik op de **Create Test** knop
3. Vul de volgende velden in:

**Basisinformatie:**
- **Name**: Geef je test een duidelijke naam, bijvoorbeeld "Homepage Hero Test - Groene vs Blauwe CTA"
- **Description** (optioneel): Notities over wat je test en waarom
- **Test Group**: Dit is de unieke identifier voor deze test, bijvoorbeeld `homepage_hero_cta`. Dit wordt automatisch aangemaakt op basis van de naam, maar je kunt het aanpassen
- **Active**: Vink aan om de test direct te activeren (je kunt dit later ook nog doen)

**Smart Optimization instellingen:**
- **Smart Optimize**: Laat aangevinkt om automatisch de best presterende variant vaker te tonen
- **Conversion Threshold**: Aantal conversies voordat optimalisatie begint (standaard: 100)
- **Randomize Percentage**: Percentage bezoekers dat na threshold nog random een variant ziet (standaard: 25%)

**Geavanceerd (optioneel):**
- **Resources**: Laat leeg om de test op alle pagina's te gebruiken, of specificeer resource IDs
- **Contexts**: Laat leeg om de test in alle contexten te gebruiken

4. Klik op **Save**

### Stap 2: Voeg varianten toe

Na het opslaan van je test zie je onderaan het scherm een **Variations** grid.

1. Klik op **Create Variation**
2. Vul de volgende velden in:

- **Name**: Beschrijvende naam voor deze variant, bijvoorbeeld "Groene CTA knop"
- **Variant Key**: Dit is de letter die je gebruikt in MIGX (bijvoorbeeld A, B, C). De eerste beschikbare key wordt automatisch voorgesteld
- **Description** (optioneel): Extra notities over deze variant
- **Active**: Vink aan om deze variant beschikbaar te maken
- **Weight**: Relatief gewicht voor willekeurige distributie (standaard: 100). Als alle varianten weight 100 hebben, krijgen ze elk een gelijke kans

3. Klik op **Save**
4. Herhaal dit voor elke variant die je wilt testen (minimaal 2 varianten)

**Voorbeeld varianten voor een hero blok test:**
- Variant A (Key: A) - "Control - Originele blauwe knop" - Weight: 100
- Variant B (Key: B) - "Groene knop met urgentie tekst" - Weight: 100
- Variant C (Key: C) - "Rode knop zonder tekst" - Weight: 100

### Stap 3: Maak de content in MIGX

Nu ga je de daadwerkelijke content maken voor elke variant.

1. Ga naar de pagina waar je de test wilt uitvoeren
2. Open de MIGX TV waar je blokken in staan
3. **Maak voor elke variant een apart MIGX blok** met dezelfde content, maar met kleine variaties

Voor elke MIGX item:
1. Vul de normale content in (tekst, afbeelding, etc.)
2. Ga naar het **A/B settings** tabblad
3. Selecteer je **Test Group** uit de dropdown (bijvoorbeeld "Homepage Hero Test - Groene vs Blauwe CTA (homepage_hero_cta)")
4. Selecteer de **Variant** (A, B, of C)
5. Pas de content aan zodat het de variant representeert (bijvoorbeeld verander de knopkleur)

**Voorbeeld:**
- MIGX blok 1: Test Group = `homepage_hero_cta`, Variant = `A - Control - Originele blauwe knop`, blauwe knop
- MIGX blok 2: Test Group = `homepage_hero_cta`, Variant = `B - Groene knop met urgentie tekst`, groene knop
- MIGX blok 3: Test Group = `homepage_hero_cta`, Variant = `C - Rode knop zonder tekst`, rode knop

4. Sla de pagina op

**Belangrijk:** Zorg dat alle andere content (teksten, afbeeldingen, posities) identiek is, behalve het element dat je test. Zo weet je zeker dat verschillen in resultaten komen door de variant en niet door andere factoren.

### Stap 4: Tracking opzetten (conversie meting)

Om te meten welke variant beter presteert, moet je conversies tracken.

1. Bepaal wat je als conversie wilt meten (bijvoorbeeld: formulier verzonden)
2. Ga naar de pagina waar de conversie plaatsvindt (meestal de "bedankt" of "success" pagina)
3. Plaats de volgende code in de template:

```
[[!BlockABConversion]]
```

Dit registreert een conversie voor alle actieve tests die de bezoeker heeft gezien.

**Of** als je alleen specifieke tests wilt meten:

```
[[!BlockABConversion? &tests=`1,2,3`]]
```

(Vervang 1,2,3 met de ID's van je tests)

### Stap 5: Test en monitor

1. **Test je setup:**
   - Bezoek de pagina in een incognito venster
   - Refresh een paar keer en kijk of je verschillende varianten ziet
   - Doorloop het conversieproces om te checken of tracking werkt

2. **Monitor de resultaten:**
   - Ga naar **Components > BlockAB**
   - Klik op je test om de statistieken te zien
   - In het Variations grid zie je voor elke variant:
     - **Picks**: Aantal keer getoond
     - **Conversions**: Aantal conversies
     - **Conversion Rate**: Conversiepercentage (hoe hoger, hoe beter)

3. **Laat de test lopen:**
   - Wacht tot je genoeg data hebt (minimaal 100 conversies wordt aanbevolen)
   - Kijk naar de conversion rate verschillen
   - Zijn de verschillen significant genoeg? (10%+ verschil is meestal betekenisvol)

---

## Resultaten interpreteren

### Statistieken uitgelegd

In het Variations grid van je test zie je:

- **Picks**: Totaal aantal keer dat deze variant is getoond
- **Conversions**: Totaal aantal conversies voor deze variant
- **Conversion Rate**: `(Conversions / Picks) × 100%`

### Welke variant is de winnaar?

Een variant is meestal een duidelijke winnaar als:

1. **Significant verschil**: Minimaal 10-20% beter dan andere varianten
2. **Genoeg data**: Minimaal 100 conversies per variant voor betrouwbare resultaten
3. **Consistent**: Het verschil blijft bestaan over meerdere dagen/weken

**Voorbeeld:**
```
Variant A: 1.250 picks, 45 conversions = 3,6% conversion rate
Variant B: 1.310 picks, 68 conversions = 5,2% conversion rate ← WINNAAR!
Variant C: 1.190 picks, 38 conversions = 3,2% conversion rate
```

Variant B presteert 44% beter dan de originele variant A!

### Wat als er geen duidelijke winnaar is?

Als alle varianten ongeveer hetzelfde presteren:
- Laat de test langer lopen voor meer data
- Of accepteer dat het element weinig impact heeft en focus op andere tests
- Overweeg radicaler verschillende varianten te testen

---

## Test beheren

### Een test pauzeren

1. Open de test in BlockAB
2. Vink **Active** uit
3. Sla op

Nu wordt de test niet meer uitgevoerd en worden alle varianten gewoon getoond (zonder selectie).

### Een test archiveren

Als je klaar bent met een test:

1. Open de test in BlockAB
2. Vink **Archived** aan
3. Sla op

Gearchiveerde tests worden niet meer in overzichten getoond maar blijven beschikbaar voor rapportage.

### Data wissen

Als je opnieuw wilt beginnen met schone data:

1. Open de test in BlockAB
2. Klik op **Clear Data** in de toolbar
3. Bevestig

Dit wist alle picks en conversions, maar behoudt de test en varianten.

### Een test dupliceren

Handig voor het hergebruiken van een test setup:

1. Selecteer de test in het grid
2. Klik **Duplicate Test**
3. Geef een nieuwe naam en test group
4. Alle varianten worden mee gekopieerd (zonder data)

---

## Best Practices

### Planning

✅ **Test één ding tegelijk**: Verander alleen de knopkleur OF de tekst, niet beide
✅ **Formuleer een hypothese**: "Ik denk dat een groene knop beter converteert omdat..."
✅ **Test impactvolle elementen**: Hero blokken, CTA's, headlines hebben meestal de meeste impact
✅ **Genoeg verkeer**: Zorg dat je genoeg bezoekers hebt (minimaal 1000 per week aanbevolen)

### Tijdens de test

✅ **Heb geduld**: Laat tests minimaal 1-2 weken lopen
✅ **Verander niks**: Geen wijzigingen aan de varianten tijdens de test
✅ **Monitor regelmatig**: Check elke paar dagen of alles goed loopt
✅ **Let op seizoenen**: Houd rekening met feestdagen, acties, etc.

### Na de test

✅ **Implementeer de winnaar**: Vervang alle varianten door de winnende versie
✅ **Documenteer**: Noteer wat je geleerd hebt voor toekomstige tests
✅ **Volgende test**: Gebruik de winnaar als nieuwe baseline voor een nieuwe test
✅ **Deel kennis**: Bespreek resultaten met het team

### Veelgemaakte fouten

❌ **Te snel stoppen**: Minimaal 100 conversies per variant is nodig
❌ **Te veel tegelijk testen**: Focus op één test per pagina
❌ **Conclusies trekken op kleine verschillen**: 2-3% verschil is meestal niet significant
❌ **Vergeten te tracken**: Altijd controleren of conversie tracking werkt
❌ **Tests vergeten**: Maak een planning en hou het bij

---

## Praktijkvoorbeelden

### Voorbeeld 1: Hero blok headline test

**Doel:** Bepalen welke headline meer leads oplevert

**Setup:**
- Test Group: `homepage_hero_headline`
- Variant A: "Professionele verhuisservice" (original)
- Variant B: "Stressvrij verhuizen vanaf €99"
- Variant C: "4.8★ beoordeling - 10.000+ tevreden klanten"

**Hypothese:** Een specifieke prijs of social proof in de headline zal beter converteren

**Conversie:** Offerte aanvragen formulier ingevuld

**Resultaat na 3 weken:**
- Variant A: 3.245 views, 89 conversions (2,7%)
- Variant B: 3.198 views, 142 conversions (4,4%) ← WINNAAR
- Variant C: 3.287 views, 98 conversions (3,0%)

**Conclusie:** De prijs noemen in de headline verhoogt conversie met 63%!

### Voorbeeld 2: CTA button kleur test

**Doel:** Optimale button kleur voor download brochure

**Setup:**
- Test Group: `diensten_brochure_cta`
- Variant A: Blauwe button (#0066CC) (original)
- Variant B: Groene button (#00CC66)
- Variant C: Oranje button (#FF6600)

**Hypothese:** Een opvallendere kleur (groen of oranje) zal beter presteren

**Conversie:** Brochure download gestart

**Resultaat na 2 weken:**
- Variant A: 1.876 views, 156 conversions (8,3%)
- Variant B: 1.923 views, 182 conversions (9,5%)
- Variant C: 1.845 views, 197 conversions (10,7%) ← WINNAAR

**Conclusie:** Oranje button presteert 29% beter dan het origineel!

**Vervolgtest:** Test nu oranje button met verschillende teksten ("Download brochure" vs "Gratis brochure")

---

## Veelgestelde vragen

### Hoeveel tests kan ik tegelijk uitvoeren?

Technisch gezien onbeperkt, maar praktisch gezien:
- **1 test per pagina** is ideaal
- Maximum 3-4 tests tegelijk op de hele website
- Te veel tests verwateren je data en vertragen resultaten

### Hoe lang moet een test draaien?

**Minimum:** 1-2 weken
**Optimaal:** Tot je 100+ conversies per variant hebt
**Maximum:** 4-6 weken (daarna zijn resultaten meestal duidelijk)

### Wat als ik weinig bezoekers heb?

- Test alleen de meest impactvolle elementen
- Kies varianten die echt verschillen (niet subtiele wijzigingen)
- Wees geduldig - het kan maanden duren
- Overweeg om eerst verkeer te verhogen via marketing

### Moet ik mijn website aanpassen?

Nee! BlockAB werkt volledig binnen je bestaande MODX setup. Je hoeft alleen:
1. MIGX blokken te dupliceren met varianten
2. De conversie tracking snippet toe te voegen op je "bedankt" pagina

### Wat gebeurt er als ik de test stop?

Als je een test op inactief zet:
- Worden alle varianten gewoon getoond (geen selectie meer)
- Blijven statistieken beschikbaar
- Kunnen bezoekers alle varianten zien

### Kan ik een variant verwijderen tijdens een test?

Ja, maar dit is niet aanbevolen:
- Data van die variant blijft beschikbaar
- Actieve bezoekers die deze variant zagen, krijgen een andere variant
- Dit kan je resultaten beïnvloeden

**Beter:** Zet de variant op inactief in plaats van verwijderen

### Hoe weet ik of mijn verschillen significant zijn?

Vuistregel:
- **<5% verschil**: Waarschijnlijk niet significant, laat test langer lopen
- **5-10% verschil**: Mogelijk significant bij veel data (200+ conversies)
- **10-20% verschil**: Waarschijnlijk significant
- **>20% verschil**: Bijna zeker significant, duidelijke winnaar

Voor perfecte zekerheid kun je online A/B test significance calculators gebruiken.

---

## Technische details (voor developers)

### Template integratie

In je Fenom template:

```smarty
{foreach json_decode($_modx->resource.migx_holder, true) as $block index=$index}

    {* Check if block is part of A/B test *}
    {if $block.ab_test_group}
        {set $shouldShow = $_modx->runSnippet('BlockAB', [
            'testGroup' => $block.ab_test_group,
            'variant' => $block.ab_test_variant
        ])}

        {if !$shouldShow}
            {continue}
        {/if}
    {/if}

    {* Render block normally *}
    {include ('file:modules/' ~ $block.MIGX_formname ~ '/' ~ $block.MIGX_formname ~ '.tpl') block=$block}

{/foreach}
```

### Database tabellen

BlockAB gebruikt 4 tabellen:
- `mdx_blockab_test`: Test definities
- `mdx_blockab_variation`: Variant definities
- `mdx_blockab_pick`: Weergave statistieken (per dag)
- `mdx_blockab_conversion`: Conversie statistieken (per dag)

---

## Support

Heb je problemen of vragen?

1. Check de **error log** in MODX: **Reports > Error Log**
2. Controleer of de templates correct zijn aangepast
3. Verifieer dat de conversie tracking snippet op de juiste pagina staat
4. Test in incognito mode om sessie-issues uit te sluiten

Voor technische problemen, neem contact op met je developer.

---

**Veel succes met je A/B tests! 🚀**

*Door systematisch te testen en optimaliseren kun je de conversie van je website significant verhogen.*
