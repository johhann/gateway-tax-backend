<?php

namespace App\Filament\Resources\Profiles\Schemas;

use App\Models\Profile;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;

class ProfileInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Flex::make([
                    Section::make([
                        Fieldset::make('Basic Information')
                            ->schema([
                                TextEntry::make('first_name'),
                                TextEntry::make('middle_name')
                                    ->placeholder('-'),
                                TextEntry::make('last_name'),
                                TextEntry::make('taxStation.name')
                                    ->label('Tax station'),
                                TextEntry::make('phone'),
                                TextEntry::make('date_of_birth')
                                    ->date(),
                                TextEntry::make('identification.license_type'),
                                TextEntry::make('identification.issuing_state'),
                                TextEntry::make('identification.license_number'),
                                TextEntry::make('identification.license_issue_date')
                                    ->date(),
                                TextEntry::make('identification.license_expiration_date')
                                    ->date()
                                    ->color(Color::Red),
                            ])
                            ->columns(4)
                            ->columnSpan(6),
                        Fieldset::make('Additional Information')
                            ->schema([
                                TextEntry::make('zip_code'),
                                TextEntry::make('hear_from'),
                                TextEntry::make('occupation'),
                                IconEntry::make('self_employment_income')
                                    ->label('Self Employed')
                                    ->boolean(),
                                TextEntry::make('updated_at')
                                    ->label('Last updated at')
                                    ->dateTime('M d, Y h:i A')
                                    ->placeholder('-'),
                            ])
                            ->columnSpan(3),
                        Fieldset::make('Address')
                            ->schema([
                                TextEntry::make('address.address'),
                                TextEntry::make('address.apt'),
                                TextEntry::make('address.city'),
                                TextEntry::make('address.state'),
                                TextEntry::make('address.zip_code'),
                            ])
                            ->columnSpan(3),
                    ])
                        ->columns()
                        ->grow(true)
                        ->columnSpan(4),
                ])
                    ->from('md')
                    ->columnSpanFull(),

                Fieldset::make('Business')
                    ->visible(fn (Profile $record) => $record->self_employment_income)
                    ->extraAttributes([
                        'class' => 'bg-white rounded-xl p-4 shadow-sm border border-gray-200',
                    ])
                    ->schema([
                        TextEntry::make('business.name'),
                        TextEntry::make('business.city'),
                        TextEntry::make('business.state'),
                        TextEntry::make('business.zip_code'),
                        TextEntry::make('business.work_phone'),
                        TextEntry::make('business.home_phone'),
                        TextEntry::make('business.address_line_one'),
                        TextEntry::make('business.address_line_two'),
                        TextEntry::make('business.website')
                            ->url(fn ($record) => $record->business['website'])
                            ->color(Color::Blue),
                        IconEntry::make('business.has_1099_misc')
                            ->boolean(),
                        IconEntry::make('business.is_license_requirement')
                            ->boolean(),
                        IconEntry::make('business.has_business_license')
                            ->boolean(),
                        TextEntry::make('business.description'),
                        TextEntry::make('business.business_advertisement'),
                        IconEntry::make('business.file_taxed_for_file_year')
                            ->boolean(),
                    ])
                    ->columns(4)
                    ->columnSpanFull(),
                Fieldset::make('Legal')
                    ->schema([
                        Section::make([
                            TextEntry::make('legal.city.name'),
                            TextEntry::make('legal.social_security_number'),
                            TextEntry::make('legal.address'),
                            TextEntry::make('legal.filing_status'),
                            TextEntry::make('legal.number_of_dependant'),
                        ])
                            ->columns(3)
                            ->columnSpan(4),
                        Section::make([
                            KeyValueEntry::make('legal.spouse_information')
                                ->label('Spouse Information')
                                ->visible(fn ($record) => ! empty($record->legal['spouse_information']))
                                ->state(function ($record) {
                                    $spouse = $record->legal['spouse_information'] ?? null;

                                    if (! $spouse) {
                                        return null;
                                    }

                                    return [
                                        'First Name' => $spouse['first_name'] ?? '-',
                                        'Middle Name' => $spouse['middle_name'] ?? '-',
                                        'Last Name' => $spouse['last_name'] ?? '-',
                                        'Date of Birth' => isset($spouse['date_of_birth']['date'])
                                            ? \Carbon\Carbon::parse($spouse['date_of_birth']['date'])->format('Y-m-d')
                                            : '-',
                                        'SSN' => $spouse['social_security_number'] ?? '-',
                                    ];
                                }),
                        ])
                            ->columnSpanFull()
                            ->grow(false),
                    ])
                    ->columnSpanFull(),
                RepeatableEntry::make('dependants')
                    ->visible(fn (Profile $record) => $record->legal->number_of_dependant > 0)

                    ->schema([
                        TextEntry::make('dependant.first_name')
                            ->state(fn ($record) => $record->first_name),
                        TextEntry::make('dependant.middle_name')
                            ->state(fn ($record) => $record->middle_name),
                        TextEntry::make('dependant.last_name')
                            ->state(fn ($record) => $record->last_name),
                        TextEntry::make('dependant.social_security_number')
                            ->state(fn ($record) => $record->social_security_number),
                        TextEntry::make('dependant.date_of_birth')
                            ->date()
                            ->state(fn ($record) => $record->date_of_birth),
                        TextEntry::make('dependant.relationship')
                            ->state(fn ($record) => $record->relationship),
                        TextEntry::make('dependant.occupation')
                            ->state(fn ($record) => $record->occupation),
                    ])
                    ->columns(3)
                    ->columnSpan(3)
                    ->grow(false),

                // Tabs::make('Tabs')
                //     ->tabs([
                //         Tab::make('Legal')
                //             ->schema([

                //             ]),
                //     ])
                //     ->columnSpanFull()
                //     ->activeTab(1),
            ]);
    }
}
