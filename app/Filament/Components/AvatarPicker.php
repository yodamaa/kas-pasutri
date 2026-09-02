<?php

namespace App\Filament\Components;

use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\ViewField;
use Filament\Schemas\Components\Tabs;
use Illuminate\Support\HtmlString;

class AvatarPicker extends FileUpload
{
    protected function setUp(): void
    {
        parent::setUp();

        $actionId = 'hidden-avatar-action-btn';

        $roundStyle = <<<'CSS'
.avatar-round-field .fi-fo-file-upload-input-ctn{width:fit-content}
.avatar-round-field .filepond--root{position:relative!important;width:128px!important;height:128px!important;border-radius:9999px!important;overflow:hidden!important;cursor:pointer}
.avatar-round-field .filepond--drop-label{display:none!important}
.avatar-round-field .filepond--root::before{content:'';position:absolute;inset:0;z-index:1;pointer-events:none;background:#f1f5f9 url('data:image/svg+xml,%3Csvg%20xmlns=%27http://www.w3.org/2000/svg%27%20viewBox=%270%200%2024%2024%27%20fill=%27%23cbd5e1%27%3E%3Cpath%20d=%27M12%2012a4%204%200%201%200%200-8%204%204%200%200%200%200%208Zm0%202c-4%200-8%202-8%205v1h16v-1c0-3-4-5-8-5Z%27/%3E%3C/svg%3E') center / 42% no-repeat}
.avatar-round-field .filepond--root:has(.filepond--item)::before{display:none}
.avatar-round-field .filepond--root::after{content:'Ubah';position:absolute;left:50%;top:6px;transform:translateX(-50%);z-index:2;pointer-events:none;background:rgba(15,23,42,.55);color:#fff;font-size:11px;font-weight:600;padding:2px 10px;border-radius:9999px}
CSS;

        $roundStyle = trim(str_replace(["\n", "\r"], ' ', $roundStyle));
        $roundStyleForJs = addslashes($roundStyle);

        $this
            ->avatar()
            ->disk('public')
            ->directory('avatars')
            ->imagePreviewHeight(160)
            ->helperText(__('Klik lingkaran untuk memilih atau mengganti foto profil'))
            ->extraAttributes([
                'class' => 'avatar-round-field',
                'x-init' => "if (! document.getElementById('avatar-round-style')) { let s = document.createElement('style'); s.id = 'avatar-round-style'; s.innerHTML = '{$roundStyleForJs}'; document.head.appendChild(s); } if (! window.__avatarRoundBound) { window.__avatarRoundBound = 1; document.addEventListener('click', function (ev) { if (! ev.target.closest('.avatar-round-field')) { return; } if (ev.target.closest('#{$actionId}') || ev.target.closest('.filepond--action-remove-item, .filepond--action-revert-item-processing, .filepond--action-abort-item-load, .filepond--action-retry-item-load')) { return; } ev.preventDefault(); ev.stopPropagation(); var b = document.getElementById('{$actionId}'); if (b) { b.click(); } }, true); }",
            ])
            ->hintAction(
                Action::make('chooseAvatar')
                    ->label('Pilih Avatar')
                    ->extraAttributes(['style' => 'display: none !important;', 'id' => $actionId])
                    ->extraModalWindowAttributes(['style' => 'border-radius: 16px; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1); border: 1px solid rgba(0,0,0,0.05); overflow: hidden;'])
                    ->modalHeading(new HtmlString('<div class="text-center pt-2 sm:pt-4 pb-1 sm:pb-2"><h2 class="text-gray-900 dark:text-white" style="font-size: clamp(1.1rem, 3vw, 1.4rem); font-weight: 700; line-height: 1.2;">'.__('filament-avatar-picker::avatar.title').'</h2><p class="text-gray-500 dark:text-gray-400" style="font-size: clamp(0.8rem, 1.5vw, 0.9rem); font-weight: 400; margin-top: 8px;">'.__('filament-avatar-picker::avatar.description').'</p></div>'))
                    ->modalWidth('2xl')
                    ->modalSubmitActionLabel(__('filament-avatar-picker::avatar.apply_button'))
                    ->form([
                        Tabs::make('Tabs')
                            ->tabs([
                                Tabs\Tab::make(__('filament-avatar-picker::avatar.gallery_tab'))
                                    ->icon('heroicon-m-photo')
                                    ->schema([
                                        ViewField::make('preset_avatar')
                                            ->hiddenLabel()
                                            ->view('filament-avatar-picker::components.avatar-gallery'),
                                    ]),
                                Tabs\Tab::make(__('filament-avatar-picker::avatar.upload_tab'))
                                    ->icon('heroicon-m-arrow-up-tray')
                                    ->schema([
                                        FileUpload::make('custom_upload')
                                            ->hiddenLabel()
                                            ->disk('public')
                                            ->directory('custom-avatars')
                                            ->placeholder(new HtmlString(__('filament-avatar-picker::avatar.upload_prompt')))
                                            ->extraAttributes([
                                                'x-on:filepond-processfile' => "setTimeout(() => \$el.closest('.fi-modal').querySelector('button[type=submit]').click(), 300)",
                                            ]),
                                    ]),
                            ]),
                    ])
                    ->action(function (array $data, self $component) {
                        if (! empty($data['custom_upload'])) {
                            $component->state($data['custom_upload']);
                        } elseif (! empty($data['preset_avatar'])) {
                            $component->state($data['preset_avatar']);
                        }
                    })
            );
    }
}
