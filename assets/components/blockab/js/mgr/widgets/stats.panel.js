BlockAB.panel.Stats = function(config) {
    config = config || {};
    Ext.applyIf(config, {
        id: 'blockab-panel-stats',
        border: false,
        autoHeight: true,
        html: '<div id="blockab-stats-summary" style="padding: 20px;"><p>Loading statistics...</p></div>'
    });
    BlockAB.panel.Stats.superclass.constructor.call(this, config);

    this.currentDays = 30;
    this.currentTestData = null;

    this.on('afterrender', function() {
        this.loadStats(this.currentDays);
    }, this);
};

Ext.extend(BlockAB.panel.Stats, MODx.Panel, {
    loadStats: function(days) {
        this.currentDays = days || 0;
        MODx.Ajax.request({
            url: BlockAB.config.connectorUrl,
            params: {
                action: 'mgr/stats/getstats',
                test: BlockAB.config.test_id,
                days: this.currentDays
            },
            listeners: {
                'success': {
                    fn: function(r) {
                        this.currentTestData = r.object;
                        this.displayStats(r.object);
                    },
                    scope: this
                }
            }
        });
    },

    stopTest: function() {
        if (!this.currentTestData) return;
        var test = this.currentTestData.test;
        var self = this;

        Ext.Msg.confirm(
            _('blockab.stats.stop_test') || 'Test Stoppen',
            _('blockab.stats.stop_test_confirm') || 'Weet je zeker dat je de test wilt stoppen?',
            function(btn) {
                if (btn !== 'yes') return;
                MODx.Ajax.request({
                    url: BlockAB.config.connectorUrl,
                    params: {
                        action: 'mgr/test/update',
                        id: test.id,
                        name: test.name,
                        test_group: test.test_group,
                        active: 0
                    },
                    listeners: {
                        'success': {
                            fn: function() {
                                MODx.msg.status(_('blockab.test_saved') || 'Test opgeslagen');
                                self.loadStats(self.currentDays);
                            },
                            scope: self
                        },
                        'failure': {
                            fn: function(r) {
                                Ext.Msg.alert('Error', r.message || 'Het stoppen van de test is mislukt.');
                            },
                            scope: self
                        }
                    }
                });
            }
        );
    },

    displayStats: function(data) {
        var sig = data.significance || {};
        var variations = data.variations || [];
        var days = data.days || 0;
        var testActive = !data.test || data.test.active == 1;

        // Store winner for use in the Varianten tab grid
        BlockAB.currentWinner = sig.winner || null;

        // --- Date range buttons ---
        var periodLabel = _('blockab.stats.period') + ': ';
        var btnHtml = '<div class="blockab-date-range">';
        btnHtml += '<span>' + periodLabel + '</span>';
        var periods = [7, 30, 60, 90];
        for (var p = 0; p < periods.length; p++) {
            var d = periods[p];
            var active = (days === d) ? ' blockab-date-range-active' : '';
            btnHtml += '<button class="blockab-date-btn' + active + '" onclick="Ext.getCmp(\'blockab-panel-stats\').loadStats(' + d + ')">' + d + ' ' + _('blockab.stats.days') + '</button>';
        }
        btnHtml += '</div>';

        // --- Stats cards ---
        var bestRate = data.best_rate || 0;
        var cardsHtml = '<div class="blockab-stats-cards">';
        cardsHtml += '<div class="blockab-stats-card"><div class="blockab-stats-card-value">' + data.total_picks + '</div><div class="blockab-stats-card-label">' + _('blockab.stats.total_views') + '</div></div>';
        cardsHtml += '<div class="blockab-stats-card"><div class="blockab-stats-card-value">' + data.total_conversions + '</div><div class="blockab-stats-card-label">' + _('blockab.stats.conversions') + '</div></div>';
        cardsHtml += '<div class="blockab-stats-card"><div class="blockab-stats-card-value">' + bestRate.toFixed(2) + '%</div><div class="blockab-stats-card-label">' + _('blockab.stats.best_rate') + '</div></div>';
        cardsHtml += '</div>';

        // --- Significance banner ---
        var bannerHtml = '';
        var stopBtn = '';
        if (sig.significant && testActive) {
            stopBtn = ' <button class="blockab-stop-btn" onclick="Ext.getCmp(\'blockab-panel-stats\').stopTest()">' + (_('blockab.stats.stop_test') || 'Test Stoppen') + '</button>';
        }

        if (!testActive) {
            bannerHtml = '<div class="blockab-significance-banner blockab-banner-grey">';
            bannerHtml += '&#9632; ' + (_('blockab.stats.test_stopped') || 'Test gestopt');
            if (sig.winner) {
                bannerHtml += ' &mdash; ' + (_('blockab.stats.winner') || 'Winnaar') + ': <strong>' + sig.winner + '</strong>';
            }
            bannerHtml += '</div>';
        } else if (data.total_picks === 0) {
            bannerHtml = '<div class="blockab-significance-banner blockab-banner-grey">' + (_('blockab.stats.no_data') || 'Geen data') + '</div>';
        } else if (!sig.min_samples_met) {
            var needed = Math.max(0, 100 - data.total_picks);
            bannerHtml = '<div class="blockab-significance-banner blockab-banner-yellow">';
            bannerHtml += '&#9203; ' + (_('blockab.stats.not_significant') || 'Test loopt \u2014 nog niet conclusief');
            if (needed > 0) {
                var warnTpl = _('blockab.stats.min_samples_warning') || '{views} weergaves \u2014 minimaal {needed} nodig';
                bannerHtml += ' &mdash; ' + warnTpl.replace('{views}', data.total_picks).replace('{needed}', 100);
            }
            bannerHtml += '</div>';
        } else if (sig.significant) {
            var confKey = (sig.confidence === 99) ? 'blockab.stats.significant_99' : 'blockab.stats.significant_95';
            var confLabel = _(confKey) || (sig.confidence + '% confidence');
            bannerHtml = '<div class="blockab-significance-banner blockab-banner-green">';
            bannerHtml += '&#10003; ' + (_('blockab.stats.winner') || 'Winnaar') + ': <strong>' + (sig.winner || '?') + '</strong>';
            bannerHtml += ' &mdash; ' + confLabel;
            bannerHtml += stopBtn;
            bannerHtml += '</div>';
        } else {
            bannerHtml = '<div class="blockab-significance-banner blockab-banner-yellow">';
            bannerHtml += '&#9203; ' + (_('blockab.stats.not_significant') || 'Test loopt \u2014 nog niet conclusief');
            bannerHtml += '</div>';
        }

        // --- Variant table ---
        var tableHtml = '';
        if (variations.length > 0) {
            // Find best rate for progress bar scaling
            var maxRate = 0;
            for (var i = 0; i < variations.length; i++) {
                if (variations[i].conversionrate > maxRate) {
                    maxRate = variations[i].conversionrate;
                }
            }

            tableHtml = '<table class="blockab-stats-table blockab-variant-table">';
            tableHtml += '<thead><tr>';
            tableHtml += '<th>' + (_('blockab.stats.variant') || 'Variant') + '</th>';
            tableHtml += '<th>' + _('blockab.variation.name') + '</th>';
            tableHtml += '<th style="text-align:right">' + _('blockab.stats.picks') + '</th>';
            tableHtml += '<th style="text-align:right">' + _('blockab.stats.conversions') + '</th>';
            tableHtml += '<th style="text-align:right">Rate %</th>';
            tableHtml += '<th style="min-width:120px"></th>';
            tableHtml += '<th>' + _('blockab.stats.status') + '</th>';
            tableHtml += '</tr></thead><tbody>';

            for (var j = 0; j < variations.length; j++) {
                var v = variations[j];
                var isWinner = sig.significant && sig.winner && (v.variant_key === sig.winner);
                var rowClass = isWinner ? ' class="blockab-winner-row"' : '';
                var badge = isWinner ? ' &#127942;' : '';
                var lowSample = (v.picks < 30) ? ' <span class="blockab-sample-warning" title="' + _('blockab.stats.min_samples_warning').replace('{views}', v.picks).replace('{needed}', 30) + '">&#9888;</span>' : '';

                var barWidth = (maxRate > 0) ? Math.round((v.conversionrate / maxRate) * 100) : 0;
                var barHtml = '<div class="blockab-progress-bar-wrap"><div class="blockab-progress-bar" style="width:' + barWidth + '%"></div></div>';

                var statusText = '';
                if (isWinner) {
                    statusText = _('blockab.stats.winner');
                } else if (v.picks < 30) {
                    statusText = _('blockab.stats.insufficient_data');
                }

                tableHtml += '<tr' + rowClass + '>';
                tableHtml += '<td><strong>' + v.variant_key + '</strong>' + badge + '</td>';
                tableHtml += '<td>' + v.name + '</td>';
                tableHtml += '<td style="text-align:right">' + v.picks + lowSample + '</td>';
                tableHtml += '<td style="text-align:right">' + v.conversions + '</td>';
                tableHtml += '<td style="text-align:right"><strong>' + v.conversionrate.toFixed(2) + '%</strong></td>';
                tableHtml += '<td>' + barHtml + '</td>';
                tableHtml += '<td>' + statusText + '</td>';
                tableHtml += '</tr>';
            }

            tableHtml += '</tbody></table>';
        }

        var html = '<div style="padding: 20px;">';
        html += btnHtml;
        html += cardsHtml;
        html += bannerHtml;
        html += tableHtml;
        html += '</div>';

        var el = document.getElementById('blockab-stats-summary');
        if (el) {
            el.innerHTML = html;
        }
    }
});

Ext.reg('blockab-panel-stats', BlockAB.panel.Stats);
