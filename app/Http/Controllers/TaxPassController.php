<?php

namespace App\Http\Controllers;

use App\Enums\GrantType;
use App\Models\Profile;
use App\Models\Scopes\ProfileScope;
use App\Models\User;
use App\Services\UserTokenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\Rule;
use Laravel\Sanctum\PersonalAccessToken;
use SimpleXMLElement;

class TaxPassController extends Controller
{
    /**
     * @throws \Exception
     */
    private function arrayToXml($data, $rootElement = null, $xml = null)
    {
        if ($xml === null) {
            $xml = new SimpleXMLElement($rootElement ? "<$rootElement/>" : '<root/>');
        }

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                if (is_numeric($key)) {
                    $this->arrayToXml($value, $key, $xml->addChild('item'));
                } else {
                    $this->arrayToXml($value, $key, $xml->addChild($key));
                }
            } else {
                $xml->addChild($key, htmlspecialchars($value));
            }
        }

        return $xml->asXML();
    }

    private function responseXml($content)
    {
        return Response::make($content, 200, [
            'Content-Type' => 'application/xml',
        ]);
    }

    private function errorResponse($error, $description)
    {
        $xml = <<<XML
<ErrorResponse xmlns:i="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://schemas.datacontract.org/2004/07/PetzAuth.Models">
    <error>{$error}</error>
    <error_description>{$description}</error_description>
    <hint />
    <inner_description />
</ErrorResponse>
XML;

        return $this->responseXml($xml);
    }

    public function getAccessToken(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
            'client_id' => 'required|integer',
            'grant_type' => ['required', Rule::in(GrantType::values())],
        ]);

        $username = $validated['username'];
        $password = $validated['password'];
        $clientId = $validated['client_id'];
        $grantType = $validated['grant_type'];

        if ($grantType !== GrantType::PASSWORD->value) {
            return $this->errorResponse('unsupported_grant_type', 'Only password grant type is supported');
        }

        $user = User::where('id', $clientId)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            return $this->errorResponse('invalid_client', 'User authentication failed');
        }

        $tokens = UserTokenService::getTokens($user);

        $xml = <<<XML
<TokenDTO xmlns:i="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://schemas.datacontract.org/2004/07/AppServices.Models.DTO">
  <access_token>{$tokens['access_token']}</access_token>
  <expires_in>1800</expires_in>
  <refresh_token>{$tokens['refresh_token']}</refresh_token>
  <token_type>{$grantType}</token_type>
</TokenDTO>
XML;

        return $this->responseXml($xml);
    }

    public function getReturnsForImportList(Request $request)
    {
        $accessToken = $request->input('access_token');
        $season = $request->input('season');
        $userId = $request->input('user_id');
        $eroId = $request->input('eroId');

        if (! $accessToken) {
            return $this->errorResponse('invalid_grant', 'Login session has timed out');
        }

        $tokenModel = PersonalAccessToken::findToken($accessToken);
        if (! $tokenModel || $tokenModel->cant('access-token')) {
            return $this->errorResponse('invalid_grant', 'Invalid or expired access token');
        }

        /** @var Profile $profiles */
        $profiles = Profile::query()
            ->withoutGlobalScope(ProfileScope::class)
            ->with(['legal'])
            ->whereYear('created_at', $season)
            ->where('user_id', $userId)
            ->get();

        $resultsXml = '';
        foreach ($profiles as $profile) {
            $filingStatus = $profile->legal?->filing_status ?? '';
            $dateImported = $profile->created_at?->toIso8601String() ?? '';
            $dateSubmitted = $profile->date_submitted?->toIso8601String() ?? '';
            $newFlag = $profile->new_flag ? 'Y' : 'N';

            $resultsXml .= "<TaxReturnMetaData>
      <dateImported>{$dateImported}</dateImported>
      <dateSubmitted>{$dateSubmitted}</dateSubmitted>
      <filingStatus>{$filingStatus}</filingStatus>
      <firstName>{$profile->first_name}</firstName>
      <lastName>{$profile->last_name}</lastName>
      <message></message>
      <newFlag>{$newFlag}</newFlag>
      <ssn>{$profile->ssn}</ssn>
    </TaxReturnMetaData>";
        }

        $totalCount = $profiles->count();
        $xml = <<<XML
<ImportReturnListResponse xmlns:i="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://schemas.datacontract.org/2004/07/PetzAuth.Models.Responses">
  <totalCount>{$totalCount}</totalCount>
  <results>
    {$resultsXml}
  </results>
</ImportReturnListResponse>
XML;

        return $this->responseXml($xml);
    }

    public function getReturnForImport(Request $request)
    {
        $accessToken = $request->input('access_token');
        $ssn = $request->input('ssn');
        $season = $request->input('season');
        $userId = $request->input('user_id');
        $login = $request->input('login');
        $eroId = $request->input('eroId');

        if (! $accessToken) {
            return $this->errorResponse('invalid_grant', 'Login session has timed out');
        }

        $tokenModel = PersonalAccessToken::findToken($accessToken);
        if (! $tokenModel || $tokenModel->cant('access-token')) {
            return $this->errorResponse('invalid_grant', 'Invalid or expired access token');
        }

        $profile = Profile::query()
            ->withoutGlobalScope(ProfileScope::class)
            ->with([
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
            ->whereYear('created_at', $season)
            ->where('user_id', $userId)
            ->first();

        if (! $profile) {
            return $this->errorResponse('not_found', 'Tax return not found');
        }

        $val = fn ($v) => htmlspecialchars($v ?? '');

        $dependentsXml = '';
        if ($profile->dependants) {
            foreach ($profile->dependants as $dep) {
                $dob = $dep->date_of_birth ?: '';
                $dependentsXml .= "<DependentInfoReturn>
      <birthdate>{$val($dob)}</birthdate>
      <firstName>{$val($dep->first_name)}</firstName>
      <lastName>{$val($dep->last_name)}</lastName>
      <relation>{$val($dep->relationship)}</relation>
      <ssn>{$val($dep->social_security_number)}</ssn>
    </DependentInfoReturn>";
            }
        }

        $scannedDocsXml = '';
        $addDoc = function ($attachment, $docType) use (&$scannedDocsXml, $val) {
            if ($attachment) {
                $guid = $val($attachment->guid);
                $mediaItem = $attachment->getFirstMedia($attachment->collection_name->value ?? 'default');
                $fileName = $mediaItem ? $mediaItem->file_name : 'document.pdf';
                $modifiedDate = $attachment->updated_at ? $attachment->updated_at->toIso8601String() : '';

                $scannedDocsXml .= "<ScannedDocumentReturn>
      <docType>{$docType}</docType>
      <docDescription>{$val($fileName)}</docDescription>
      <guid>{$guid}</guid>
      <ftpFileName>{$val($fileName)}</ftpFileName>
      <modifiedDate>{$modifiedDate}</modifiedDate>
    </ScannedDocumentReturn>";
            }
        };

        if ($profile->documents) {
            $d = $profile->documents;
            $addDoc($d->w2, '04');
            $addDoc($d->misc1099, '05');
            $addDoc($d->mortgageStatement, '07');
            $addDoc($d->tuitionStatement, '06');
            $addDoc($d->sharedRiders, '03');
            $addDoc($d->misc, '03');
        }

        $dobP = $profile->date_of_birth ?: '';

        $filingStatusId = $profile->legal?->filing_status ?? '';

        $addr = $profile->address;
        $address = $addr ? $addr->address : '';
        $city = $addr ? $addr->city : '';
        $state = $addr ? $addr->state : '';
        $zip = $profile->zip_code;

        $xml = <<<XML
<ImportTaxpayerReturnResponse xmlns:i="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://schemas.datacontract.org/2004/07/PetzAuth.Models.Responses">
  <FilingStatusId>{$val($filingStatusId)}</FilingStatusId>
  <Primary_FirstName>{$val($profile->first_name)}</Primary_FirstName>
  <Primary_LastName>{$val($profile->last_name)}</Primary_LastName>
  <Primary_SSN>{$val($profile->ssn)}</Primary_SSN>
  <Primary_EmailAddress>{$val($profile->user?->email)}</Primary_EmailAddress>
  <Primary_Birthdate>{$val($dobP)}</Primary_Birthdate>
  <Primary_CellPhone>{$val($profile->phone)}</Primary_CellPhone>
  <Primary_Occupation>{$val($profile->occupation)}</Primary_Occupation>

  <Address>{$val($address)}</Address>
  <City>{$val($city)}</City>
  <State>{$val($state)}</State>
  <ZipCode>{$val($zip)}</ZipCode>

  <Dependents>
    {$dependentsXml}
  </Dependents>
  <W2Documents>
  </W2Documents>
  <ScannedDocuments>
    {$scannedDocsXml}
  </ScannedDocuments>
</ImportTaxpayerReturnResponse>
XML;

        return $this->responseXml($xml);
    }

    public function postNewFlagClear(Request $request)
    {
        $accessToken = $request->input('access_token');
        $ssn = $request->input('ssn');

        if (! $accessToken) {
            return $this->errorResponse('invalid_grant', 'Login session has timed out');
        }

        $tokenModel = PersonalAccessToken::findToken($accessToken);
        if (! $tokenModel) {
            return $this->errorResponse('invalid_grant', 'Invalid or expired access token');
        }

        $profile = Profile::query()
            ->withoutGlobalScope(ProfileScope::class)
            ->where('ssn', $ssn)
            ->first();

        if (! $profile) {
            return $this->errorResponse('not_found', 'Tax return not found');
        }

        $profile->new_flag = false;
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
