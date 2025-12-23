<?php

namespace App\Http\Controllers;

use App\Enums\FilingStatus;
use App\Enums\GrantType;
use App\Enums\InformationSource;
use App\Enums\LicenseType;
use App\Models\Attachment;
use App\Models\Profile;
use App\Models\Scopes\ProfileScope;
use App\Models\User;
use App\Services\UserTokenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Laravel\Sanctum\PersonalAccessToken;
use Spatie\ArrayToXml\ArrayToXml;

class TaxPassController extends Controller
{
    private function responseXml($content)
    {
        return Response::make($content, 200, [
            'Content-Type' => 'application/xml',
        ]);
    }

    private function errorResponse($error, $description)
    {
        $data = [
            '_attributes' => [
                'xmlns:i' => 'http://www.w3.org/2001/XMLSchema-instance',
                'xmlns' => 'http://schemas.datacontract.org/2004/07/PetzAuth.Models',
            ],
            'error' => $error,
            'error_description' => $description,
            'hint' => '',
            'inner_description' => '',
        ];

        $xml = ArrayToXml::convert($data, 'ErrorResponse');

        return $this->responseXml($xml);
    }

    public function getAccessToken(Request $request)
    {
        $validated = $request->validate([
            'username' => 'string',
            'email' => 'required|string|email',
            'password' => 'required|string',
            'client_id' => 'integer',
            'grant_type' => [Rule::in(GrantType::values())],
        ]);

        $email = $validated['email'];
        //        $username = $validated['username'];
        $password = $validated['password'];
        //        $clientId = $validated['client_id'];
        $grantType = $validated['grant_type'] ?? GrantType::PASSWORD->value;

        if ($grantType !== GrantType::PASSWORD->value) {
            return $this->errorResponse('unsupported_grant_type', 'Only password grant type is supported');
        }

        $user = User::where('email', $email)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            return $this->errorResponse('invalid_client', 'User authentication failed');
        }

        $tokens = UserTokenService::createGrantTokens($user);

        $data = [
            '_attributes' => [
                'xmlns:i' => 'http://www.w3.org/2001/XMLSchema-instance',
                'xmlns' => 'http://schemas.datacontract.org/2004/07/AppServices.Models.DTO',
            ],
            'access_token' => $tokens['access_token'],
            'expires_in' => (int) config('sanctum.access_token'),
            'refresh_token' => $tokens['refresh_token'],
            'token_type' => $grantType,
        ];

        $xml = ArrayToXml::convert($data, 'TokenDTO');

        return $this->responseXml($xml);
    }

    public function getAccessTokenJson(Request $request)
    {
        $validated = $request->validate([
            'username' => 'string',
            'email' => 'required|string|email',
            'password' => 'required|string',
            'client_id' => 'integer',
            'grant_type' => [Rule::in(GrantType::values())],
        ]);

        $email = $validated['email'];
        //        $username = $validated['username'];
        $password = $validated['password'];
        //        $clientId = $validated['client_id'];
        $grantType = $validated['grant_type'] ?? GrantType::PASSWORD->value;

        if ($grantType !== GrantType::PASSWORD->value) {
            return response()->json([
                'error' => 'unsupported_grant_type',
                'error_description' => 'Only password grant type is supported',
            ], 400);
        }

        $user = User::where('email', $email)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            return response()->json([
                'error' => 'invalid_client',
                'error_description' => 'User authentication failed',
            ], 401);
        }

        $tokens = UserTokenService::createGrantTokens($user);

        $data = [
            'access_token' => $tokens['access_token'],
            //            'expires_in'    => (int) config('sanctum.access_token'),
            //            'refresh_token' => $tokens['refresh_token'],
            //            'token_type'    => $grantType,
        ];

        //        $xml = ArrayToXml::convert($data, 'TokenDTO');

        return response()->json($data);
    }

    public function getReturnsForImportList(Request $request)
    {
        //        $accessToken = $request->input('access_token');
        //        $season = $request->input('season');
        //        $userId = $request->input('user_id');
        //        $eroId = $request->input('eroId');
        $efin = $request->input('efin');

        //        if (! $accessToken) {
        //            return $this->errorResponse('invalid_grant', 'Login session has timed out');
        //        }
        //
        //        $tokenModel = PersonalAccessToken::findToken($accessToken);
        //        if (! $tokenModel || $tokenModel->cant('access-token')) {
        //            return $this->errorResponse('invalid_grant', 'Invalid or expired access token');
        //        }

        /** @var Profile[] $profiles */
        $profiles = Profile::query()
            ->withoutGlobalScope(ProfileScope::class)
            ->with(['legal'])
            ->where('date_submitted', '!=', null)
            ->whereHas('legal', function ($query) use ($efin) {
                $query->whereHas('branch', function ($q) use ($efin) {
                    $q->where('efin', $efin);
                });
            })
            ->get();

        $results = [];
        foreach ($profiles as $profile) {
            $filingStatus = FilingStatus::tryFrom($profile->legal?->filing_status)?->getInt() ?? '';
            $dateImported = $profile->date_imported?->format('Y-m-d') ?? '';
            $dateSubmitted = $profile->date_submitted?->format('Y-m-d') ?? '';
            $newFlag = $profile->new_flag ? 1 : 0;

            $results[] = [
                'dateImported' => $dateImported,
                'dateSubmitted' => $dateSubmitted,
                'filingStatus' => $filingStatus,
                'firstName' => $profile->first_name,
                'lastName' => $profile->last_name,
                'message' => 0,
                'newFlag' => $newFlag,
                'ssn' => $profile->ssn,
            ];
        }

        $data = [
            '_attributes' => [
                'xmlns:i' => 'http://www.w3.org/2001/XMLSchema-instance',
                'xmlns' => 'https://github.com/spatie/array-to-xml',
            ],
            'results' => [
                'TaxReturnMetaData' => $results,
            ],
            'totalCount' => count($profiles),
        ];

        $xml = ArrayToXml::convert($data, 'ImportReturnListResponse');

        return $this->responseXml($xml);
    }

    public function getReturnForImport(Request $request)
    {
        //        $accessToken = $request->input('access_token');
        $ssn = $request->input('ssn');
        //        $season = $request->input('season');
        //        $userId = $request->input('user_id');
        //        $login = $request->input('login');
        //        $eroId = $request->input('eroId');

        //        if (! $accessToken) {
        //            return $this->errorResponse('invalid_grant', 'Login session has timed out');
        //        }
        //
        //        $tokenModel = PersonalAccessToken::findToken($accessToken);
        //        if (! $tokenModel || $tokenModel->cant('access-token')) {
        //            return $this->errorResponse('invalid_grant', 'Invalid or expired access token');
        //        }

        $profile = Profile::query()
            ->withoutGlobalScope(ProfileScope::class)
            ->with([
                'business',
                'identification',
                'payment',
                'legal',
                'address',
                'dependants',
                'documents',
                'documents.w2',
                'documents.misc1099',
                'documents.mortgageStatement',
                'documents.tuitionStatement',
                'documents.sharedRiders',
                'documents.misc',
            ])
            ->where('ssn', $ssn)
//            ->whereYear('created_at', $season)
//            ->where('user_id', $userId)
            ->first();

        if (! $profile) {
            return $this->errorResponse('not_found', 'Please provide Valid ssn');
        }

        $val = fn ($v) => htmlspecialchars($v ?? '');

        $dependents = [];
        if ($profile->dependants) {
            foreach ($profile->dependants as $dep) {
                $dependents[] = [
                    'firstName' => $val($dep->first_name),
                    'lastName' => $val($dep->last_name),
                    'birthdate' => $val(date('Y-m-d', strtotime($dep->date_of_birth))),
                    'relation' => $val($dep->relationship),
                    'ssn' => $val($dep->social_security_number),
                ];
            }
        }

        $scannedDocs = [];
        $addDoc = function (?Attachment $attachment, $docType) use (&$scannedDocs, $val) {
            if ($attachment) {
                $guid = $val($attachment->guid);

                $mediaItem = $attachment->getFirstMedia($attachment->collection_name->value);
                $base64 = '';
                if (! is_null($mediaItem)) {
                    $disk = $mediaItem->disk;
                    $path = $mediaItem->getPathRelativeToRoot();

                    $fileContents = Storage::disk($disk)->get($path);
                    $base64 = base64_encode($fileContents);
                }
                $modifiedDate = $attachment->updated_at ? $attachment->updated_at->format('Y-m-d') : '';

                $scannedDocs[] = [
                    'docType' => $docType,
                    'guid' => $guid,
                    'imageData' => $base64,
                    'modifiedDate' => $modifiedDate,
                ];
            }
        };

        if ($profile->documents) {
            $d = $profile->documents;
            $addDoc($d->w2, 'w2');
            $addDoc($d->misc1099, 'misc_1099');
            $addDoc($d->mortgageStatement, 'mortgage_statement');
            $addDoc($d->tuitionStatement, 'tuition_statement');
            $addDoc($d->sharedRiders, 'shared_riders');
            $addDoc($d->misc, 'misc');
        }

        $dobP = $profile->date_of_birth ?: '';

        $filingStatusId = FilingStatus::tryFrom($profile->legal?->filing_status)?->getInt() ?? '';

        $data = $profile->payment?->data;

        \Log::debug($data);

        $null = [
            '_attributes' => [
                'i:nil' => 'true',
            ],
        ];

        $data = [
            '_attributes' => [
                'xmlns:i' => 'http://www.w3.org/2001/XMLSchema-instance',
                'xmlns' => 'https://github.com/spatie/array-to-xml',
            ],
            'FilingStatusId' => $val($filingStatusId),
            'newFlag' => $val($profile->new_flag ? 1 : 0),
            'Primary_Carrier' => $null,
            'Primary_EmailAddress' => $val($profile->user?->email),
            'Primary_Contact' => $null,
            'Primary_FirstName' => $val($profile->first_name),
            'Primary_MiddleInitial' => $profile->middle_name ? $val(substr($profile->middle_name, 0, 1)) : $null,
            'Primary_LastName' => $val($profile->last_name),
            'Primary_Birthdate' => $val($dobP),
            'Primary_SSN' => $val($profile->ssn),
            'Primary_CellPhone' => $val($profile->phone),
            'Primary_HomePhone' => $profile->business?->home_phone ? $val($profile->business->home_phone) : $null,
            'Primary_WorkPhone' => $profile->business?->work_phone ? $val($profile->business->work_phone) : $null,
            'Primary_IdExpDate' => $profile->identification?->license_expiration_date ? $val($profile->identification->license_expiration_date->toDateString()) : '',
            'Primary_IdIssDate' => $profile->identification?->license_issue_date ? $val($profile->identification->license_issue_date->toDateString()) : '',
            'Primary_IdNumber' => $val($profile->identification?->license_number),
            'Primary_IdState' => $val($profile->identification?->issuing_state->value),
            'Primary_IdType' => LicenseType::tryFrom($profile->identification?->license_type->value)?->getInt() ?? '',
            'Primary_Occupation' => $val($profile->occupation),
            'Primary_Language' => $null,
            'Primary_Text' => $null,
            'Primary_isBlind' => $null,
            'Primary_isDependent' => $null,
            'Primary_isDisabled' => $null,
            'Primary_isUSCitizen' => $null,
            'ReferralDescription' => $null,
            'ReferralType' => $null,
            'Secondary_Carrier' => $null,
            'Secondary_CellPhone' => $null,
            'Secondary_Contact' => $null,
            'Secondary_EmailAddress' => $null,
            'Secondary_FirstName' => $null,
            'Secondary_MiddleInitial' => $null,
            'Secondary_LastName' => $null,
            'Secondary_Birthdate' => $null,
            'Secondary_SSN' => $null,
            'Secondary_Occupation' => $null,
            'Secondary_HomePhone' => $null,
            'Secondary_IdExpDate' => $null,
            'Secondary_IdIssDate' => $null,
            'Secondary_IdNumber' => $null,
            'Secondary_IdState' => $null,
            'Secondary_IdType' => $null,
            'Secondary_Language' => $null,
            'Secondary_Text' => $null,
            'Secondary_WorkPhone' => $null,
            'Secondary_isBlind' => $null,
            'Secondary_isDependent' => $null,
            'Secondary_isDisabled' => $null,
            'Secondary_isUSCitizen' => $null,
            'Address' => $val($profile->address?->address),
            'AddressCont' => $profile->address?->apt ? $val($profile->business?->address_line_two) : $null,
            'City' => $val($profile->address?->city),
            'State' => $val($profile->address?->state),
            'ZipCode' => $val($profile->address?->zip_code),
            'LastYearFiled' => $val($profile->business?->file_taxed_for_tax_year ? 1 : 0),
            'HearAbout' => InformationSource::tryFrom($profile->hear_from)?->getInt() ?? '',
            'BankName' => $data['bank_name'] ?? $null,
            'RoutingNumber' => $data && isset($data['routing_number']) ? $data['routing_number'] : $null,
            'AccountNumber' => $data && isset($data['account_number']) ? $data['account_number'] : $null,
            'AccountType' => $data && isset($data['account_type']) ? $data['account_type'] : $null,
            'CheckingAccount' => $data && isset($data['account_type']) && $data['account_type'] === 'checking' ? 'X' : $null,
            'Dependents' => [
                'DependentInfoReturn' => $dependents,
            ],
            'ScannedDocuments' => [
                'ScannedDocumentReturn' => $scannedDocs,
            ],
        ];

        $xml = ArrayToXml::convert($data, 'ImportTaxpayerReturnResponse');

        return $this->responseXml($xml);
    }

    public function postNewFlagClear(Request $request)
    {
        //        $accessToken = $request->input('access_token');
        $ssn = $request->input('ssn');
        $statusId = $request->input('statusId');

        //        if (! $accessToken) {
        //            return $this->errorResponse('invalid_grant', 'Login session has timed out');
        //        }

        //        $tokenModel = PersonalAccessToken::findToken($accessToken);
        //        if (! $tokenModel) {
        //            return $this->errorResponse('invalid_grant', 'Invalid or expired access token');
        //        }

        $profile = Profile::query()
            ->withoutGlobalScope(ProfileScope::class)
            ->where('ssn', $ssn)
            ->first();

        if (! $profile) {
            return $this->errorResponse('not_found', 'Please provide Valid ssn');
        }

        $profile->new_flag = false;
        $profile->date_imported = now();
        $profile->save();

        return response('', 200);
    }

    public function invalidateAccess(Request $request)
    {
        $accessToken = $request->input('access_token');

        if ($accessToken) {
            $tokenModel = PersonalAccessToken::findToken($accessToken);
            if ($tokenModel) {
                $tokenModel->delete();
            } else {
                return $this->errorResponse('invalid_grant', 'Invalid or expired access token');
            }
        }

        return response('', 200);
    }
}
