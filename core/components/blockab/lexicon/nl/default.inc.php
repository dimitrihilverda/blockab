<?php
/**
 * BlockAB Dutch Lexicon
 *
 * @package blockab
 * @subpackage lexicon
 */

$_lang['blockab'] = 'BlockAB';
$_lang['blockab.menu_desc'] = 'Beheer A/B tests voor MIGX blokken';
$_lang['blockab.back_to_overview'] = 'Terug naar Test Overzicht';
$_lang['blockab.about'] = 'Over BlockAB';
$_lang['blockab.about_text'] = "BlockAB — A/B test module voor MIGX blokken\n\nMet BlockAB stel je A/B tests in voor content blokken die via MIGX worden beheerd. Het systeem registreert weergaves en conversies per variant en berekent automatisch statistische significantie via een chi-kwadraat test.\n\n© 2026 Moving-in.nl";
$_lang['blockab.manual'] = 'Handleiding';
$_lang['blockab.manual_text'] = 'De documentatie van BlockAB is beschikbaar op GitHub:\nhttps://github.com/dimitrihilverda/blockab';

// Tests
$_lang['blockab.test'] = 'Test';
$_lang['blockab.tests'] = 'Tests';
$_lang['blockab.test_create'] = 'Test Aanmaken';
$_lang['blockab.test_update'] = 'Test Bewerken';
$_lang['blockab.test_duplicate'] = 'Test Dupliceren';
$_lang['blockab.test_remove'] = 'Test Verwijderen';
$_lang['blockab.test_remove_confirm'] = 'Weet je zeker dat je deze test wilt verwijderen?';
$_lang['blockab.test_remove_permanent'] = 'Permanent Verwijderen';
$_lang['blockab.test_remove_dialog_msg'] = 'Wil je deze test archiveren of permanent verwijderen?<br><br><b>Archiveren</b> &mdash; test en statistieken blijven bewaard, de test wordt gedeactiveerd.<br><b>Permanent verwijderen</b> &mdash; de test &eacute;n alle statistieken worden definitief verwijderd en zijn niet meer terug te halen.';
$_lang['blockab.test_archive'] = 'Test Archiveren';
$_lang['blockab.test_archive_confirm'] = 'Weet je zeker dat je deze test wilt archiveren? De test wordt gedeactiveerd.';
$_lang['blockab.test_unarchive'] = 'Test Terughalen';
$_lang['blockab.test_unarchive_confirm'] = 'Weet je zeker dat je deze test wilt terughalen uit het archief?';

// Test velden
$_lang['blockab.test.name'] = 'Naam';
$_lang['blockab.test.description'] = 'Beschrijving';
$_lang['blockab.test.test_group'] = 'Test Groep';
$_lang['blockab.test.test_group_desc'] = 'Unieke identifier voor deze test (bijv. "homepage_hero")';
$_lang['blockab.test.active'] = 'Actief';
$_lang['blockab.test.archived'] = 'Gearchiveerd';
$_lang['blockab.test.smartoptimize'] = 'Slim Optimaliseren';
$_lang['blockab.test.smartoptimize_desc'] = 'Automatisch optimaliseren naar best presterende variant';
$_lang['blockab.test.threshold'] = 'Drempelwaarde';
$_lang['blockab.test.threshold_desc'] = 'Aantal conversies voordat optimalisatie start';
$_lang['blockab.test.randomize'] = 'Randomize %';
$_lang['blockab.test.randomize_desc'] = 'Percentage van de tijd dat een random variant getoond wordt (na drempel)';
$_lang['blockab.test.resources'] = 'Resources';
$_lang['blockab.test.resources_desc'] = 'Komma-gescheiden resource IDs (optioneel). Gebruik 5> voor kinderen, 3-5 voor bereik';
$_lang['blockab.test.contexts'] = 'Contexts';
$_lang['blockab.test.contexts_desc'] = 'Komma-gescheiden context keys (optioneel)';

// Varianten
$_lang['blockab.variation'] = 'Variant';
$_lang['blockab.variations'] = 'Varianten';
$_lang['blockab.variation_create'] = 'Variant Aanmaken';
$_lang['blockab.variation_update'] = 'Variant Bewerken';
$_lang['blockab.variation_duplicate'] = 'Variant Dupliceren';
$_lang['blockab.variation_remove'] = 'Variant Verwijderen';
$_lang['blockab.variation_remove_confirm'] = 'Weet je zeker dat je deze variant wilt verwijderen?';

// Variant velden
$_lang['blockab.variation.name'] = 'Naam';
$_lang['blockab.variation.variant_key'] = 'Variant Key';
$_lang['blockab.variation.variant_key_desc'] = 'Key gebruikt in MIGX blokken (bijv. "A", "B", "C")';
$_lang['blockab.variation.description'] = 'Beschrijving';
$_lang['blockab.variation.active'] = 'Actief';
$_lang['blockab.variation.weight'] = 'Gewicht';
$_lang['blockab.variation.weight_desc'] = 'Hoger gewicht = vaker getoond (standaard: 100)';

// Statistieken
$_lang['blockab.stats'] = 'Statistieken';
$_lang['blockab.stats.picks'] = 'Weergaves';
$_lang['blockab.stats.conversions'] = 'Conversies';
$_lang['blockab.stats.conversion_rate'] = 'Conversie Ratio';
$_lang['blockab.stats.no_data'] = 'Nog geen data';
$_lang['blockab.stats.total_views'] = 'Totaal Weergaves';
$_lang['blockab.stats.best_rate'] = 'Beste Rate';
$_lang['blockab.stats.days'] = 'dagen';
$_lang['blockab.stats.period'] = 'Periode';
$_lang['blockab.stats.status'] = 'Status';
$_lang['blockab.stats.significant_95'] = 'Statistisch significant (95% confidence)';
$_lang['blockab.stats.significant_99'] = 'Statistisch significant (99% confidence)';
$_lang['blockab.stats.not_significant'] = 'Test loopt — nog niet conclusief';
$_lang['blockab.stats.insufficient_data'] = 'Onvoldoende data';
$_lang['blockab.stats.winner'] = 'Winnaar';
$_lang['blockab.stats.stop_test'] = 'Test Stoppen';
$_lang['blockab.stats.stop_test_confirm'] = 'Weet je zeker dat je de test wilt stoppen? De test wordt gedeactiveerd.';
$_lang['blockab.stats.min_samples_warning'] = '{views} weergaves — minimaal {needed} nodig';
$_lang['blockab.stats.variant'] = 'Variant';
$_lang['blockab.stats.test_stopped'] = 'Test gestopt';

// Berichten
$_lang['blockab.test_saved'] = 'Test succesvol opgeslagen';
$_lang['blockab.test_removed'] = 'Test succesvol verwijderd';
$_lang['blockab.variation_saved'] = 'Variant succesvol opgeslagen';
$_lang['blockab.variation_removed'] = 'Variant succesvol verwijderd';

// Fouten
$_lang['blockab.error.test_not_found'] = 'Test niet gevonden';
$_lang['blockab.error.variation_not_found'] = 'Variant niet gevonden';
$_lang['blockab.error.missing_testgroup'] = 'Test groep is verplicht';
$_lang['blockab.error.missing_variant_key'] = 'Variant key is verplicht';
$_lang['blockab.error.test_name_required'] = 'Test naam is verplicht';
$_lang['blockab.error.variation_name_required'] = 'Variant naam is verplicht';
$_lang['blockab.error.variation_key_required'] = 'Variant key is verplicht';
$_lang['blockab.error.test_required'] = 'Test is verplicht';
$_lang['blockab.error.variant_key_exists'] = 'Deze variant key bestaat al voor deze test';
$_lang['blockab.error.test_group_exists'] = 'Deze test groep bestaat al. Kies een unieke test groep naam.';
$_lang['blockab.error.test_save_failed'] = 'Test opslaan mislukt';
$_lang['blockab.error.variation_save_failed'] = 'Variant opslaan mislukt';
