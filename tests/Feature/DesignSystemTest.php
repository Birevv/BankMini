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
