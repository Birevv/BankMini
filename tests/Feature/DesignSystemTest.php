<?php

namespace Tests\Feature;

use Tests\TestCase;

class DesignSystemTest extends TestCase
{
    public function testCustomThemeUsesSolidSurfacesWithoutGlassmorphism(): void
    {
        $stylesheets = [
            (string) file_get_contents(public_path('css/bank-mini.css')),
            (string) file_get_contents(resource_path('css/filament-solid.css')),
        ];

        foreach ($stylesheets as $stylesheet) {
            $stylesheet = strtolower($stylesheet);

            $this->assertStringNotContainsString('gradient', $stylesheet);
            $this->assertStringNotContainsString('backdrop-filter', $stylesheet);
            $this->assertStringNotContainsString('rgba(', $stylesheet);
        }

        $this->assertStringContainsString(
            "@import '../../vendor/filament/filament/resources/css/theme.css';",
            (string) file_get_contents(resource_path('css/filament-solid.css')),
        );

        $this->assertStringContainsString(
            "'resources/css/filament-solid.css'",
            (string) file_get_contents(base_path('vite.config.js')),
        );

        $panelTheme = (string) file_get_contents(resource_path('css/filament-solid.css'));

        $this->assertStringContainsString('.fi-body .fi-main.fi-width-full', $panelTheme);
        $this->assertStringContainsString('.fi-body .fi-sidebar-item-label', $panelTheme);
        $this->assertStringContainsString('.fi-body .fi-ta-table', $panelTheme);
        $this->assertStringContainsString('.fi-body .bank-data-table .fi-ta-header-toolbar', $panelTheme);
        $this->assertStringContainsString('.fi-pagination-records-per-page-select', $panelTheme);
    }

    public function testPublicPagesUseTheAdminPanelFont(): void
    {
        $publicTheme = (string) file_get_contents(public_path('css/bank-mini.css'));
        $applicationTheme = (string) file_get_contents(resource_path('css/app.css'));
        $publicFontHead = (string) file_get_contents(resource_path('views/partials/public-font.blade.php'));
        $viteConfig = (string) file_get_contents(base_path('vite.config.js'));

        $this->assertStringContainsString('inter-latin-wght-normal-NRMW37G5.woff2', $publicFontHead);
        $this->assertStringContainsString('rel="preload"', $publicFontHead);
        $this->assertStringNotContainsString('@import url(', $publicTheme);
        $this->assertStringContainsString('"Inter Variable", Inter', $publicTheme);
        $this->assertStringContainsString("'Inter Variable', Inter", $applicationTheme);
        $this->assertStringNotContainsString('Instrument Sans', $publicTheme.$applicationTheme.$viteConfig);
        $this->assertStringContainsString('body, button, input, select, textarea { font-family: inherit; }', $publicTheme);
    }

    public function testHomepageProgramCardUsesReadableResponsiveSizing(): void
    {
        $publicTheme = (string) file_get_contents(public_path('css/bank-mini.css'));

        $this->assertStringContainsString('grid-template-columns: minmax(0, 1.25fr) minmax(340px, .75fr);', $publicTheme);
        $this->assertStringContainsString('font-size: clamp(32px, 3vw, 38px);', $publicTheme);
        $this->assertStringContainsString('@media (max-width: 1020px)', $publicTheme);
        $this->assertStringContainsString('.program-values > div { grid-template-columns: auto minmax(0, 1fr);', $publicTheme);
    }

    public function testPublicPagesUseAccessiblePageTransitions(): void
    {
        $transitionScript = (string) file_get_contents(public_path('js/page-transitions.js'));
        $publicTheme = (string) file_get_contents(public_path('css/bank-mini.css'));

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('js/page-transitions.js', false);

        $this->get(route('internal.login'))
            ->assertOk()
            ->assertSee('js/page-transitions.js', false);

        $this->get(route('nasabah.login'))
            ->assertOk()
            ->assertSee('js/page-transitions.js', false);

        $this->assertStringContainsString("window.matchMedia('(prefers-reduced-motion: reduce)')", $transitionScript);
        $this->assertStringContainsString("link.hasAttribute('download')", $transitionScript);
        $this->assertStringContainsString('destination.origin !== window.location.origin', $transitionScript);
        $this->assertStringNotContainsString('transform: translateY(6px)', $publicTheme);
        $this->assertStringNotContainsString('transform: translateY(-4px)', $publicTheme);
        $this->assertStringContainsString('transition: opacity 140ms ease;', $publicTheme);
        $this->assertStringContainsString('scroll-behavior: smooth', $publicTheme);
        $this->assertStringContainsString('@media (prefers-reduced-motion: reduce)', $publicTheme);
    }

    public function testHomepageRendersClearRoleBasedEntryPoints(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee(route('internal.login'), false)
            ->assertSee(route('nasabah.login'), false)
            ->assertSee('Menabung di sekolah, lebih mudah dan aman.')
            ->assertSee('Sederhana untuk nasabah, terkontrol untuk sekolah.');
    }

    public function testBothLoginPagesUseTheSharedSplitLayout(): void
    {
        $this->get(route('internal.login'))
            ->assertOk()
            ->assertSee('auth-layout', false)
            ->assertSee('Operasional bank sekolah, lebih aman dan tertib.');

        $this->get(route('nasabah.login'))
            ->assertOk()
            ->assertSee('auth-layout', false)
            ->assertSee('Pantau tabunganmu dengan tenang.');
    }
}
