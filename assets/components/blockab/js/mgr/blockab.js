var BlockAB = function(config) {
    config = config || {};
    BlockAB.superclass.constructor.call(this, config);
};

Ext.extend(BlockAB, Ext.Component, {
    page: {},
    window: {},
    grid: {},
    tree: {},
    panel: {},
    combo: {},
    config: {}
});

Ext.reg('blockab', BlockAB);
BlockAB = new BlockAB();

BlockAB.currentWinner = null;
BlockAB._aboutWindow = null;
BlockAB._manualWindow = null;

BlockAB.showAbout = function() {
    if (!BlockAB._aboutWindow) {
        var assetsUrl = (BlockAB.config && BlockAB.config.assetsUrl)
            ? BlockAB.config.assetsUrl
            : MODx.config.assets_url + 'components/blockab/';

        var html = '<div class="blockab-about-wrap">'
            + '<div class="blockab-about-header">'
            +   '<img src="' + assetsUrl + 'img/moving-it.svg" class="blockab-about-logo" alt="Moving-IT">'
            +   '<div class="blockab-about-appinfo">'
            +     '<span class="blockab-about-appname"><span class="blockab-title-block">Block</span><span class="blockab-title-ab">AB</span></span>'
            +     '<span class="blockab-about-version">v1.1.15</span>'
            +   '</div>'
            + '</div>'
            + '<div class="blockab-about-divider"></div>'
            + '<div class="blockab-about-body">'
            +   '<p>' + (_('blockab.about_intro') || 'A/B testing module for MIGX content blocks in MODX Revolution.') + '</p>'
            +   '<p>' + (_('blockab.about_description') || 'BlockAB lets you set up A/B tests for individual content blocks.') + '</p>'
            +   '<ul class="blockab-about-features">'
            +     '<li>' + (_('blockab.about_feature_chi') || 'Chi-square significance test (95% &amp; 99% confidence)') + '</li>'
            +     '<li>' + (_('blockab.about_feature_smartoptimize') || 'Smart Optimize &mdash; automatically send more traffic to the winner') + '</li>'
            +     '<li>' + (_('blockab.about_feature_session') || 'Session-based variant persistence') + '</li>'
            +     '<li>' + (_('blockab.about_feature_multiblock') || 'Multiple blocks per test group (full page test)') + '</li>'
            +   '</ul>'
            + '</div>'
            + '<div class="blockab-about-footer">'
            +   '<a href="https://github.com/dimitrihilverda/blockab" target="_blank">&#128196; GitHub</a>'
            +   '<span>&copy; 2026 Moving-in.nl</span>'
            + '</div>'
            + '</div>';

        BlockAB._aboutWindow = new Ext.Window({
            title: _('blockab.about') || 'About BlockAB',
            width: 420,
            autoHeight: true,
            modal: true,
            resizable: false,
            closable: true,
            bodyStyle: 'padding: 0; background: #fff;',
            html: html,
            buttons: [{
                text: 'OK',
                handler: function() { BlockAB._aboutWindow.hide(); }
            }],
            buttonAlign: 'center'
        });
    }
    BlockAB._aboutWindow.show();
};

BlockAB.showManual = function() {
    if (!BlockAB._manualWindow) {
        var html = '<div class="blockab-manual-wrap">'
            + '<p>' + (_('blockab.manual_intro') || 'The full BlockAB documentation is available on GitHub and covers:') + '</p>'
            + '<ul class="blockab-about-features">'
            +   '<li>' + (_('blockab.manual_item_install') || 'Installation &amp; configuration') + '</li>'
            +   '<li>' + (_('blockab.manual_item_migx') || 'MIGX field configuration') + '</li>'
            +   '<li>' + (_('blockab.manual_item_template') || 'Template integration (Fenom)') + '</li>'
            +   '<li>' + (_('blockab.manual_item_snippets') || 'Snippet documentation (BlockAB, BlockABConversion)') + '</li>'
            +   '<li>' + (_('blockab.manual_item_troubleshooting') || 'Troubleshooting') + '</li>'
            + '</ul>'
            + '<div class="blockab-manual-link">'
            +   '<a href="https://github.com/dimitrihilverda/blockab" target="_blank" class="blockab-manual-btn">'
            +     '&#128196;&nbsp; github.com/dimitrihilverda/blockab'
            +   '</a>'
            + '</div>'
            + '</div>';

        BlockAB._manualWindow = new Ext.Window({
            title: _('blockab.manual') || 'Manual',
            width: 400,
            autoHeight: true,
            modal: true,
            resizable: false,
            closable: true,
            bodyStyle: 'padding: 0; background: #fff;',
            html: html,
            buttons: [{
                text: _('blockab.close') || 'Close',
                handler: function() { BlockAB._manualWindow.hide(); }
            }],
            buttonAlign: 'center'
        });
    }
    BlockAB._manualWindow.show();
};

BlockAB.renderPageHeader = function(opts) {
    opts = opts || {};
    var subtitle = opts.subtitle
        ? ' <span class="blockab-page-subtitle">&mdash; ' + opts.subtitle + '</span>'
        : '';
    var back = opts.backUrl
        ? '<div class="blockab-back-link"><a href="' + opts.backUrl + '">&#8592; ' + (_('blockab.back_to_overview') || 'Terug naar Test Overzicht') + '</a></div>'
        : '';

    var assetsUrl = (BlockAB.config && BlockAB.config.assetsUrl)
        ? BlockAB.config.assetsUrl
        : MODx.config.assets_url + 'components/blockab/';

    return '<div class="blockab-page-header-wrap">'
        + '<div class="blockab-header-left">'
        +   '<h2 class="blockab-header-title">'
        +     '<span class="blockab-title-wordmark">'
        +       '<img src="' + assetsUrl + 'img/blockab-icon.svg" class="blockab-title-icon" alt="">'
        +       '<span class="blockab-title-block">Block</span><span class="blockab-title-ab">AB</span>'
        +     '</span>'
        +     subtitle
        +   '</h2>'
        +   back
        + '</div>'
        + '<div class="blockab-header-actions">'
        +   '<img src="' + assetsUrl + 'img/moving-it.svg" class="blockab-header-logo" alt="Moving-IT">'
        +   '<div class="blockab-header-btns">'
        +     '<button class="blockab-header-btn" onclick="BlockAB.showAbout()">' + (_('blockab.about') || 'Over BlockAB') + '</button>'
        +     '<button class="blockab-header-btn" onclick="BlockAB.showManual()">' + (_('blockab.manual') || 'Handleiding') + '</button>'
        +   '</div>'
        + '</div>'
        + '</div>';
};
