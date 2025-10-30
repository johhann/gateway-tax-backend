<?php

namespace App\Filament\Resources\Profiles\Schemas;

use App\Models\Profile;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
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

                        // TextEntry::make('user.name')
                        //     ->label('User'),
                        TextEntry::make('taxStation.name')
                            ->label('Tax station'),
                        TextEntry::make('first_name'),
                        TextEntry::make('middle_name')
                            ->placeholder('-'),
                        TextEntry::make('last_name'),
                        TextEntry::make('phone'),
                        TextEntry::make('date_of_birth')
                            ->date(),
                    ])
                        ->columns(3)
                        ->columnSpan(3),
                    Section::make([
                        TextEntry::make('zip_code'),
                        TextEntry::make('hear_from'),
                        TextEntry::make('occupation'),
                        IconEntry::make('self_employment_income')
                            ->boolean(),
                        // TextEntry::make('created_at')
                        //     ->dateTime()
                        //     ->placeholder('-'),
                        TextEntry::make('updated_at')
                            ->label('Last updated at')
                            ->dateTime('M d, Y h:i A')
                            ->placeholder('-'),
                        // TextEntry::make('deleted_at')
                        //     ->dateTime()
                        //     ->visible(fn (Profile $record): bool => $record->trashed()),
                    ])
                        ->columns(3)
                        ->columnSpan(3),
                ])
                    ->from('md')
                    ->columnSpanFull(),

                Tabs::make('Tabs')
                    ->tabs([
                        Tab::make('Address')
                            ->schema([
                                TextEntry::make('address.address'),
                                TextEntry::make('address.apt'),
                                TextEntry::make('address.city'),
                                TextEntry::make('address.state'),
                                TextEntry::make('address.zip_code'),
                            ])
                            ->columns(4),
                        Tab::make('Identification')
                            ->schema([
                                TextEntry::make('identification.license_type'),
                                TextEntry::make('identification.issuing_state'),
                                TextEntry::make('identification.license_number'),
                                TextEntry::make('identification.license_issue_date')
                                    ->date(),
                                TextEntry::make('identification.license_expiration_date')
                                    ->date()
                                    ->color(Color::Red),
                            ])
                            ->columns(4),
                        Tab::make('Legal')
                            ->schema([
                                TextEntry::make('legal.city.name'),
                                TextEntry::make('legal.social_security_number'),
                                TextEntry::make('legal.address'),
                                TextEntry::make('legal.filing_status'),
                                TextEntry::make('legal.number_of_dependant'),
                                RepeatableEntry::make('legal.spouse_information'),
                            ])
                            ->columns(4),

                        Tab::make('Business')
                            ->visible(fn (Profile $record) => $record->self_employment_income)
                            ->schema([
                                TextEntry::make('business.name'),
                                TextEntry::make('business.description'),
                                TextEntry::make('business.address_line_one'),
                                TextEntry::make('business.address_line_two'),
                                TextEntry::make('business.city'),
                                TextEntry::make('business.state'),
                                TextEntry::make('business.zip_code'),
                                TextEntry::make('business.work_phone'),
                                TextEntry::make('business.home_phone'),
                                TextEntry::make('business.website'),
                                IconEntry::make('business.has_1099_misc')
                                    ->boolean(),
                                IconEntry::make('business.is_license_requirement')
                                    ->boolean(),
                                IconEntry::make('business.has_business_license')
                                    ->boolean(),
                                IconEntry::make('business.file_taxed_for_file_year')
                                    ->boolean(),
                                TextEntry::make('business.business_advertisement'),
                            ])
                            ->columns(4),

                        Tab::make('Dependant')
                            ->badge(fn (Profile $record): ?string => $record->dependant()->count())
                            ->schema([
                                RepeatableEntry::make('dependant')
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
                                        TextEntry::make('dependant.occupation')
                                            ->state(fn ($record) => $record->occupation),
                                        TextEntry::make('dependant.relationship')
                                            ->state(fn ($record) => $record->relationship),
                                    ])
                                    ->columns(3)
                                    ->columnSpan(6)
                                    ->grid(2),
                            ])
                            ->columns(2)
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull()
                    ->activeTab(4),
            ]);
    }
}
