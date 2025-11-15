<?php

namespace App\Services;

use App\Enums\TaxDocType;
use App\Exceptions\TaxPassException;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;

class TaxPassService
{
    protected string $baseUrl;

    protected int $timeout;

    protected string $ftp;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('taxpass.base_url'), '/');
        $this->timeout = (int) config('taxpass.timeout', 10);
        $this->ftp = config('taxpass.ftp');
    }

    /**
     * @throws TaxPassException
     */
    public function getAccessToken(
        string $year,
        string $username,
        string $clientId,
        string $password,
        string $grantType = 'password'
    ): array {
        $url = "{$this->baseUrl}/{$year}/Auth/GetToken/";

        $form = [
            'username' => $username,
            'client_id' => $clientId,
            'grant_type' => $grantType,
            'password' => $password,
        ];

        try {
            $response = Http::timeout($this->timeout)
                ->asForm()
                ->accept('application/xml')
                ->post($url, $form);
        } catch (\Throwable $e) {
            throw new TaxPassException('TaxPass request failed: '.$e->getMessage());
        }

        $body = $response->body();

        if (! $body) {
            throw new TaxPassException('TaxPass returned an empty response');
        }

        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($body);

        if ($xml === false) {
            throw new TaxPassException('Invalid XML response from TaxPass', 0, [
                'body' => $body,
                'errors' => libxml_get_errors(),
            ]);
        }

        if ($xml->getName() === 'ErrorResponse') {
            $error = (string) $xml->error;
            $desc = (string) $xml->error_description;

            throw new TaxPassException(
                "TaxPass error: {$error} — {$desc}",
                0,
                ['xml' => $xml]
            );
        }

        if ($xml->getName() !== 'TokenDTO') {
            throw new TaxPassException("Unexpected XML root node: {$xml->getName()}", 0, ['xml' => $xml]);
        }

        $accessToken = (string) $xml->access_token;
        $expiresIn = (int) $xml->expires_in;
        $refreshToken = (string) $xml->refresh_token;
        $tokenType = (string) $xml->token_type;

        if (! $accessToken) {
            throw new TaxPassException('access_token not found', 0, ['xml' => $xml]);
        }

        return [
            'access_token' => $accessToken,
            'expires_in' => $expiresIn,
            'expires_at' => Carbon::now()->addSeconds($expiresIn),
            'refresh_token' => $refreshToken ?: null,
            'token_type' => $tokenType ?: null,
        ];
    }

    /**
     * @throws TaxPassException
     */
    public function getReturnsForImportList(
        string $accessToken,
        string $season,
        string $userId,
        ?string $eroId = null,
        ?string $passwd = null
    ): array {
        $url = "{$this->baseUrl}/GetReturnsForImportList";

        $form = [
            'access_token' => $accessToken,
            'season' => $season,
            'userId' => $userId,
        ];

        if ($eroId !== null) {
            $form['eroId'] = $eroId;
        }
        if ($passwd !== null) {
            $form['passwd'] = $passwd;
        }

        try {
            $response = Http::timeout($this->timeout)
                ->asForm()
                ->accept('application/xml, text/xml, */*')
                ->post($url, $form);
        } catch (\Throwable $e) {
            throw new TaxPassException('TaxPass GetReturnsForImportList request failed: '.$e->getMessage(), 0, ['url' => $url]);
        }

        $body = $response->body();

        if ($body === '') {
            throw new TaxPassException('TaxPass GetReturnsForImportList returned an empty response', $response->status());
        }

        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($body);

        if ($xml === false) {
            $errors = libxml_get_errors();
            libxml_clear_errors();
            throw new TaxPassException('Invalid XML from GetReturnsForImportList', 0, ['body' => $body, 'errors' => $errors]);
        }

        if ($xml->getName() === 'ErrorResponse' || isset($xml->error)) {
            $err = (string) ($xml->error ?? '');
            $desc = (string) ($xml->error_description ?? '');
            throw new TaxPassException("TaxPass error: {$err} — {$desc}", 0, ['xml' => $xml]);
        }

        $totalCount = isset($xml->totalCount) ? (int) $xml->totalCount : 0;

        $results = [];
        $resultsNode = $xml->results ?? null;

        if ($resultsNode !== null) {
            foreach ($resultsNode->TaxReturnMetaData as $meta) {
                $results[] = $this->mapTaxReturnMetaData($meta);
            }
        } else {
            $found = $xml->xpath('//TaxReturnMetaData');
            if ($found !== false) {
                foreach ($found as $meta) {
                    $results[] = $this->mapTaxReturnMetaData($meta);
                }
            }
        }

        return [
            'totalCount' => $totalCount,
            'results' => $results,
        ];
    }

    protected function mapTaxReturnMetaData(\SimpleXMLElement $meta): array
    {
        return [
            'dateImported' => (string) ($meta->dateImported ?? ''),
            'dateSubmitted' => (string) ($meta->dateSubmitted ?? ''),
            'filingStatus' => (string) ($meta->filingStatus ?? ''),
            'firstName' => (string) ($meta->firstName ?? ''),
            'lastName' => (string) ($meta->lastName ?? ''),
            'message' => (string) ($meta->message ?? ''),
            'newFlag' => (string) ($meta->newFlag ?? ''),
            'ssn' => (string) ($meta->ssn ?? ''),
        ];
    }

    /**
     * @throws TaxPassException
     */
    public function getReturnForImport(
        string $accessToken,
        string $ssn,
        string $season,
        string $userId,
        string $login = 'ADMIN',
        ?string $eroId = null,
        ?string $passwd = null
    ): array {
        $url = "{$this->baseUrl}/Resource/GetReturnForImport";

        $form = [
            'access_token' => $accessToken,
            'ssn' => $ssn,
            'season' => $season,
            'userId' => $userId,
            'login' => $login,
        ];
        if ($eroId !== null) {
            $form['eroId'] = $eroId;
        }
        if ($passwd !== null) {
            $form['passwd'] = $passwd;
        }

        try {
            $response = Http::timeout($this->timeout)
                ->asForm()
                ->accept('application/xml, text/xml, */*')
                ->post($url, $form);
        } catch (\Throwable $e) {
            throw new TaxPassException(
                'TaxPass GetReturnForImport HTTP request failed: '.$e->getMessage(),
                0,
                ['url' => $url]
            );
        }

        $body = (string) $response->body();
        if ($body === '') {
            throw new TaxPassException('TaxPass GetReturnForImport returned an empty response', $response->status());
        }

        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($body);
        if ($xml === false) {
            $errors = libxml_get_errors();
            libxml_clear_errors();
            throw new TaxPassException('Invalid XML from GetReturnForImport', 0, ['body' => $body, 'errors' => $errors]);
        }

        if ($xml->getName() === 'ErrorResponse' || isset($xml->error)) {
            $err = (string) ($xml->error ?? '');
            $desc = (string) ($xml->error_description ?? '');
            throw new TaxPassException("TaxPass error: {$err} — {$desc}", 0, ['xml' => $xml]);
        }

        $strCast = function ($node) {
            return isset($node) ? (string) $node : '';
        };

        $mapped = [
            'Primary' => [
                'Birthdate' => $strCast($xml->Primary_Birthdate),
                'Carrier' => $strCast($xml->Primary_Carrier),
                'CellPhone' => $strCast($xml->Primary_CellPhone),
                'Contact' => $strCast($xml->Primary_Contact),
                'EmailAddress' => $strCast($xml->Primary_EmailAddress),
                'FirstName' => $strCast($xml->Primary_FirstName),
                'HomePhone' => $strCast($xml->Primary_HomePhone),
                'WorkPhone' => $strCast($xml->Primary_WorkPhone),
                'IdExpDate' => $strCast($xml->Primary_IdExpDate),
                'IdIssDate' => $strCast($xml->Primary_IdIssDate),
                'IdNumber' => $strCast($xml->Primary_IdNumber),
                'IdState' => $strCast($xml->Primary_IdState),
                'IdType' => $strCast($xml->Primary_IdType),
                'Language' => $strCast($xml->Primary_Language),
                'LastName' => $strCast($xml->Primary_LastName),
                'MiddleInitial' => $strCast($xml->Primary_MiddleInitial),
                'Occupation' => $strCast($xml->Primary_Occupation),
                'SSN' => $strCast($xml->Primary_SSN),
                'Text' => $strCast($xml->Primary_Text),
                'isBlind' => $strCast($xml->Primary_isBlind),
                'isDependent' => $strCast($xml->Primary_isDependent),
                'isDisabled' => $strCast($xml->Primary_isDisabled),
                'isUSCitizen' => $strCast($xml->Primary_isUSCitizen),
            ],

            'Secondary' => [
                'Birthdate' => $strCast($xml->Secondary_Birthdate),
                'Carrier' => $strCast($xml->Secondary_Carrier),
                'CellPhone' => $strCast($xml->Secondary_CellPhone),
                'Contact' => $strCast($xml->secondary_contact),
                'EmailAddress' => $strCast($xml->Secondary_EmailAddress),
                'FirstName' => $strCast($xml->Secondary_FirstName),
                'HomePhone' => $strCast($xml->Secondary_HomePhone),
                'WorkPhone' => $strCast($xml->Secondary_WorkPhone),
                'IdExpDate' => $strCast($xml->Secondary_IdExpDate),
                'IdIssDate' => $strCast($xml->Secondary_IdIssDate),
                'IdNumber' => $strCast($xml->Secondary_IdNumber),
                'IdState' => $strCast($xml->Secondary_IdState),
                'IdType' => $strCast($xml->Secondary_IdType),
                'Language' => $strCast($xml->Secondary_Language),
                'LastName' => $strCast($xml->Secondary_LastName),
                'MiddleInitial' => $strCast($xml->Secondary_MiddleInitial),
                'Occupation' => $strCast($xml->Secondary_Occupation),
                'SSN' => $strCast($xml->Secondary_SSN),
                'Text' => $strCast($xml->Secondary_Text),
                'isBlind' => $strCast($xml->Secondary_isBlind),
                'isDependent' => $strCast($xml->Secondary_isDependent),
                'isDisabled' => $strCast($xml->Secondary_isDisabled),
                'isUSCitizen' => $strCast($xml->Secondary_isUSCitizen),
            ],

            'LastYearFiled' => (int) ($strCast($xml->LastYearFiled) ?: 0),
            'HearAbout' => (int) ($strCast($xml->HearAbout) ?: 0),
            'BankName' => $strCast($xml->BankName),
            'RoutingNumber' => $strCast($xml->RoutingNumber),
            'AccountNumber' => $strCast($xml->AccountNumber),
            'AccountType' => $strCast($xml->AccountType),
            'CheckingAccount' => $strCast($xml->CheckingAccount),
            'ReferralDescription' => $strCast($xml->ReferralDescription),
            'ReferralType' => $strCast($xml->ReferralType),
            'Address' => $strCast($xml->Address),
            'AddressCont' => $strCast($xml->AddressCont),
            'City' => $strCast($xml->City),
            'State' => $strCast($xml->State),
            'ZipCode' => $strCast($xml->ZipCode),
        ];

        $mapped['W2Documents'] = [];
        if (isset($xml->W2Documents->W2DocumentReturn)) {
            foreach ($xml->W2Documents->W2DocumentReturn as $w2) {
                $mapped['W2Documents'][] = [
                    'Primary_ssn' => $strCast($w2->Primary_ssn),
                    'Secondary_ssn' => $strCast($w2->Secondary_ssn),
                    'allocatedTipsAmt' => $strCast($w2->allocatedTipsAmt),
                    'box12aAmt' => $strCast($w2->box12aAmt),
                    'box12aCode' => $strCast($w2->box12aCode),
                    'box12bAmt' => $strCast($w2->box12bAmt),
                    'box12bCode' => $strCast($w2->box12bCode),
                    'box12cAmt' => $strCast($w2->box12cAmt),
                    'box12cCode' => $strCast($w2->box12cCode),
                    'box12dAmt' => $strCast($w2->box12dAmt),
                    'box12dCode' => $strCast($w2->box12dCode),
                    'box14Amt' => $strCast($w2->box14Amt),
                    'box14OtherExplain' => $strCast($w2->box14OtherExplain),
                    'box15State' => $strCast($w2->box15State),
                    'box15State2' => $strCast($w2->box15State2),
                    'box15StateId' => $strCast($w2->box15StateId),
                    'box15StateId2' => $strCast($w2->box15StateId2),
                    'box16StateWagesTip' => $strCast($w2->box16StateWagesTip),
                    'box16StateWagesTip2' => $strCast($w2->box16StateWagesTip2),
                    'box17StateIncomeTax' => $strCast($w2->box17StateIncomeTax),
                    'box17StateIncomeTax2' => $strCast($w2->box17StateIncomeTax2),
                    'box18LocalWagesTips' => $strCast($w2->box18LocalWagesTips),
                    'box18LocalWagesTips2' => $strCast($w2->box18LocalWagesTips2),
                    'box19LocalIncomeTax' => $strCast($w2->box19LocalIncomeTax),
                    'box19LocalIncomeTax2' => $strCast($w2->box19LocalIncomeTax2),
                    'box20LocalityName' => $strCast($w2->box20LocalityName),
                    'box20LocalityName2' => $strCast($w2->box20LocalityName2),
                    'dependentCareBenefitsAmt' => $strCast($w2->dependentCareBenefitsAmt),
                    'ein' => $strCast($w2->ein),
                    'empAddress1' => $strCast($w2->empAddress1),
                    'empAddress2' => $strCast($w2->empAddress2),
                    'empCity' => $strCast($w2->empCity),
                    'empName' => $strCast($w2->empName),
                    'empName2' => $strCast($w2->empName2),
                    'empState' => $strCast($w2->empState),
                    'empZip' => $strCast($w2->empZip),
                    'medicareTaxWithheldAmt' => $strCast($w2->medicareTaxWithheldAmt),
                    'medicareWagesTipsAmt' => $strCast($w2->medicareWagesTipsAmt),
                    'modifiedDate' => $strCast($w2->modifiedDate),
                    'nonQualPlans' => $strCast($w2->nonQualPlans),
                    'retirementPlan' => $strCast($w2->retirementPlan),
                    'season' => $strCast($w2->season),
                    'ssTaxAmt' => $strCast($w2->ssTaxAmt),
                    'ssTipsAmt' => $strCast($w2->ssTipsAmt),
                    'ssWagesAmt' => $strCast($w2->ssWagesAmt),
                    'statutoryEmp' => $strCast($w2->statutoryEmp),
                    'thirdPartySickPay' => $strCast($w2->thirdPartySickPay),
                    'tors' => $strCast($w2->tors),
                    'verificationCode' => $strCast($w2->verificationCode),
                    'wagesAmt' => $strCast($w2->wagesAmt),
                    'withholdingAmt' => $strCast($w2->withholdingAmt),
                ];
            }
        }

        $mapped['Dependents'] = [];
        if (isset($xml->Dependents->DependentInfoReturn)) {
            foreach ($xml->Dependents->DependentInfoReturn as $d) {
                $mapped['Dependents'][] = [
                    'birthdate' => $strCast($d->birthdate),
                    'firstName' => $strCast($d->firstName),
                    'lastName' => $strCast($d->lastName),
                    'relation' => $strCast($d->relation),
                    'ssn' => $strCast($d->ssn),
                ];
            }
        }

        $mapped['ScannedDocuments'] = [];
        if (isset($xml->ScannedDocuments->ScannedDocumentReturn)) {
            foreach ($xml->ScannedDocuments->ScannedDocumentReturn as $sd) {
                $docType = TaxDocType::from($strCast($sd->docType))->value;

                $mapped['ScannedDocuments'][] = [
                    'docType' => $docType,
                    'docDescription' => $strCast($sd->docDescription),
                    'guid' => $strCast($sd->guid),
                    'ftpFileName' => $strCast($sd->ftpFileName),
                    'modifiedDate' => $strCast($sd->modifiedDate),
                ];
            }
        }

        return $mapped;
    }

    /**
     * @throws TaxPassException
     */
    public function postNewFlagClear(string $accessToken, string $ssn): bool
    {
        $url = "{$this->baseUrl}/Resource/PostNewFlagClear";

        $form = [
            'access_token' => $accessToken,
            'ssn' => $ssn,
        ];

        try {
            $response = Http::timeout($this->timeout)
                ->asForm()
                ->accept('application/xml, text/xml, */*')
                ->post($url, $form);
        } catch (\Throwable $e) {
            throw new TaxPassException(
                'TaxPass PostNewFlagClear HTTP request failed: '.$e->getMessage(),
                0,
                ['url' => $url, 'ssn' => $ssn]
            );
        }

        $body = $response->body();
        if ($body === '') {
            throw new TaxPassException('TaxPass PostNewFlagClear returned empty response', $response->status());
        }

        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($body);
        if ($xml === false) {
            $errors = libxml_get_errors();
            libxml_clear_errors();
            throw new TaxPassException('Invalid XML from PostNewFlagClear', 0, ['body' => $body, 'errors' => $errors]);
        }

        if ($xml->getName() === 'ErrorResponse' || isset($xml->error)) {
            $err = (string) ($xml->error ?? '');
            $desc = (string) ($xml->error_description ?? '');
            throw new TaxPassException("TaxPass PostNewFlagClear error: {$err} — {$desc}", 0, ['xml' => $xml]);
        }

        return true;
    }

    /**
     * @throws Exception
     */
    public function downloadScannedDocument(string $fileName, string $localPath): void
    {
        $ftpUrl = "ftp://{$this->ftp['username']}:{$this->ftp['password']}@{$this->ftp['host']}/{$fileName}";

        $remoteStream = @fopen($ftpUrl, 'r');
        if (! $remoteStream) {
            throw new Exception("Unable to open FTP file: {$fileName}");
        }

        $meta = stream_get_meta_data($remoteStream);
        $size = $meta['unread_bytes'] ?? null;
        if ($size !== null && $size > 3 * 1024 * 1024) {
            fclose($remoteStream);
            throw new Exception("FTP file {$fileName} exceeds maximum size of 3MB");
        }

        $localStream = fopen($localPath, 'w');
        if (! $localStream) {
            fclose($remoteStream);
            throw new Exception("Unable to open local path: {$localPath}");
        }

        stream_copy_to_stream($remoteStream, $localStream);

        fclose($remoteStream);
        fclose($localStream);
    }

    /**
     * @throws TaxPassException
     */
    public function logout(string $accessToken): bool
    {
        $url = "{$this->baseUrl}/Auth/InvalidateAccess";

        $form = [
            'access_token' => $accessToken,
        ];

        try {
            $response = Http::timeout($this->timeout)
                ->asForm()
                ->accept('application/xml, text/xml, */*')
                ->post($url, $form);
        } catch (\Throwable $e) {
            throw new TaxPassException(
                'TaxPass logout HTTP request failed: '.$e->getMessage(),
                0,
                ['url' => $url, 'access_token' => $accessToken]
            );
        }

        $body = $response->body();
        if ($body === '') {
            throw new TaxPassException('TaxPass logout returned empty response', $response->status());
        }

        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($body);
        if ($xml === false) {
            $errors = libxml_get_errors();
            libxml_clear_errors();
            throw new TaxPassException('Invalid XML from logout', 0, ['body' => $body, 'errors' => $errors]);
        }

        if ($xml->getName() === 'ErrorResponse' || isset($xml->error)) {
            $err = (string) ($xml->error ?? '');
            $desc = (string) ($xml->error_description ?? '');
            throw new TaxPassException("TaxPass logout error: {$err} — {$desc}", 0, ['xml' => $xml]);
        }

        return true;
    }
}
