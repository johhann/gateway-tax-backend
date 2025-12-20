<?php

namespace Database\Seeders;

use App\Enums\DependantRelationship;
use App\Enums\DirectDepositAccountType;
use App\Enums\FilingStatus;
use App\Enums\LicenseType;
use App\Enums\RefundMethod;
use App\Enums\RefundType;
use App\Enums\StateEnum;
use App\Models\Branch;
use App\Models\LegalCity;
use App\Models\Profile;
use Illuminate\Database\Seeder;

class ProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $legalCities = LegalCity::with('branches')->inRandomOrder()->first();
        $branches = Branch::pluck('id');
        $profiles = Profile::factory(50)->create();

        foreach ($profiles as $profile) {
            // create address
            $profile->address()->create([
                'address' => fake()->streetAddress(),
                'apt' => 'Apt '.$profile->id,
                'city' => fake()->city(),
                'state' => fake()->streetName(),
                'zip_code' => fake()->postcode(),
            ]);

            // identification
            $profile->identification()->create([
                'license_type' => fake()->randomElement(LicenseType::cases()),
                'issuing_state' => fake()->randomElement(StateEnum::cases()),
                'license_number' => fake()->unique()->numerify('##########'),
                'license_issue_date' => fake()->dateTimeBetween('-5 years', '-1 years'),
                'license_expiration_date' => fake()->dateTimeBetween('+1 years', '+5 years'),
            ]);

            // business
            if ($profile->self_employment_income) {
                $profile->business()->create([
                    'name' => fake()->company(),
                    'description' => fake()->realText(),
                    'address_line_one' => fake()->streetAddress(),
                    'address_line_two' => 'Apt '.$profile->id,
                    'city' => fake()->city(),
                    'state' => fake()->streetName(),
                    'zip_code' => fake()->postcode(),
                    'work_phone' => fake()->phoneNumber(),
                    'home_phone' => fake()->phoneNumber(),
                    'website' => fake()->url(),
                    'has_1099_misc' => fake()->boolean(),
                    'is_license_requirement' => fake()->boolean(),
                    'has_business_license' => fake()->boolean(),
                    'file_taxed_for_tax_year' => fake()->boolean(),
                    'business_advertisement' => fake()->realText(),
                    'advertise_through' => fake()->randomElements([
                        'Newspapers',
                        'Flyers',
                        'Personal Computers',
                        'Radio',
                        'Television',
                        'Internet',
                    ]),
                    'records' => fake()->randomElements([
                        'Accounting Records',
                        'Computer Records',
                        'Business Bank Accounts',
                        'Paid Invoices/Receipts',
                        'Business Stationery',
                        'Insurance',
                        'Advertising',
                        'Car/Truck Expense',
                        'Rental Expense',
                    ]),
                ]);
            }

            // legals
            $legal = $profile->legal()->create([
                'legal_city_id' => $legalCities->id,
                'branch_id' => fake()->randomElement($branches) ?? null,
                'social_security_number' => fake()->unique()->numerify('###-##-####'),
                // 'address' => fake()->streetAddress(),
                'filing_status' => $filingStatus = fake()->randomElement(FilingStatus::cases()),
                'number_of_dependant' => $dependant = fake()->randomElement([0, 1, 2, 3, 4]),
                'spouse_information' => in_array($filingStatus, [FilingStatus::MarriedFilingJointly, FilingStatus::MarriedFilingSeparately]) ? [
                    'first_name' => fake()->firstName(),
                    'middle_name' => fake()->optional()->firstName(),
                    'last_name' => fake()->lastName(),
                    'date_of_birth' => fake()->dateTimeBetween('-60 years', '-18 years'),
                    'social_security_number' => fake()->unique()->numerify('###-##-####'),
                ] : null,
            ]);

            // dependant
            for ($i = 0; $i < $dependant; $i++) {
                $profile->dependants()->create([
                    'first_name' => fake()->firstName(),
                    'middle_name' => fake()->optional()->firstName(),
                    'last_name' => fake()->lastName(),
                    'date_of_birth' => fake()->dateTimeBetween('-60 years', '-18 years'),
                    'social_security_number' => fake()->unique()->numerify('###-##-####'),
                    'relationship' => fake()->randomElement(DependantRelationship::cases()),
                    'occupation' => fake()->jobTitle(),
                ]);
            }

            // payment
            $profile->payment()->create([
                'type' => fake()->randomElements(RefundType::cases(), 2),
                'refund_method' => $refundMethod = fake()->randomElement(RefundMethod::cases()),
                'data' => $refundMethod === RefundMethod::DirectDeposit ? [
                    'bank_name' => fake()->company(),
                    'account_type' => fake()->randomElement(DirectDepositAccountType::cases()),
                    'account_number' => fake()->numerify('##########'),
                    'name_of_account_holder' => fake()->name(),
                    'routing_number' => fake()->numerify('##########'),
                    'branch_code' => fake()->numerify('###'),
                ] : null,
            ]);
            // then, upload file for payment

        }
    }
}
